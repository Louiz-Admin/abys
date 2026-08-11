<?php
// Fichier: abys-ai/api/email-check.php
// MILO répond aux emails entrants (contact@abys.ai) : IMAP en PHP pur (imap-lite),
// aucune dépendance à ext-imap. Appelé par le déclencheur automatique du site
// (poor-man's cron dans head.php) ou par un cron classique.
//
// Phase 1 : lit les non-lus -> génère la réponse de Milo -> file d'attente (2-10 min)
// Phase 2 : envoie les réponses dont le délai est écoulé
//
// URL : https://abys.ai/api/email-check.php?key=<imap_cron_key>

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';
require_once __DIR__ . '/imap-lite.php';

header('Content-Type: application/json');
ignore_user_abort(true);
@set_time_limit(120);

// ── Sécurité ─────────────────────────────────────────────────────────────────
$db       = get_db();
$settings = [];
foreach ($db->query("SELECT `key`, value FROM settings")->fetchAll() as $r) {
    $settings[$r['key']] = $r['value'];
}
$cron_key = $settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p';
if (($_GET['key'] ?? '') !== $cron_key) { http_response_code(403); exit('Forbidden'); }

$imap_host = $settings['imap_host'] ?? 'imap.ionos.fr';
$imap_port = (int)($settings['imap_port'] ?? 993);
$imap_user = $settings['imap_user'] ?? 'contact@abys.ai';
$imap_pass = decrypt_value($settings['imap_pass'] ?? '') ?: '';
$api_key   = decrypt_value($settings['claude_key'] ?? '') ?: '';

if (!$imap_pass || !$api_key) {
    http_response_code(200);
    exit(json_encode(['skipped' => 'config manquante', 'imap_pass' => $imap_pass ? 'ok' : 'MANQUANT (php api/update-imap-pass.php sur le VPS)', 'claude_key' => $api_key ? 'ok' : 'MANQUANT']));
}

$log = ['queued' => 0, 'sent' => 0, 'skipped' => 0, 'escalated' => 0, 'errors' => 0];

// ── Garde-fou : plafond quotidien de réponses automatiques ──────────────────
$daily_cap  = (int)($settings['milo_email_daily_cap'] ?? 40);
$sent_today = (int)$db->query("SELECT COUNT(*) FROM email_inbound_log WHERE replied=1 AND replied_at >= CURDATE()")->fetchColumn();

// ── Template : réponse signée Milo ───────────────────────────────────────────
function milo_reply_html(string $text): string {
    $body = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    return <<<HTML
<div style="margin-bottom:18px">
  <img src="https://abys.ai/assets/img/milo-avatar.jpg" alt="Milo" width="52" height="52"
       style="width:52px;height:52px;border-radius:50%;border:2px solid #10B981;object-fit:cover;vertical-align:middle">
  <span style="font-size:13px;color:#6B7280;margin-left:10px"><strong style="color:#111827">Milo</strong> · copilote IA d'ABYS · disponible 24h/24</span>
</div>
<p>{$body}</p>
HTML;
}

// ════════════════════════════════════════════════════════════════════
// PHASE 2 · Envoyer les réponses dont le délai est écoulé
// ════════════════════════════════════════════════════════════════════
$pending = $db->query(
    "SELECT * FROM email_inbound_log WHERE replied = 0 AND reply_after <= NOW() LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

foreach ($pending as $row) {
    if ($sent_today >= $daily_cap) { $log['skipped']++; continue; }
    try {
        $reply_subject = preg_match('/^Re\s*:/i', $row['subject']) ? $row['subject'] : 'Re : ' . $row['subject'];
        $sent = send_email($row['from_email'], $reply_subject, milo_reply_html($row['ai_reply']));
        $db->prepare("UPDATE email_inbound_log SET replied=1, replied_at=NOW() WHERE id=?")->execute([$row['id']]);
        $sent_today++;

        $delay_min = round((strtotime($row['reply_after']) - strtotime($row['queued_at'])) / 60);
        notify_admin("Milo a répondu · {$row['subject']}",
            '<div class="info-box">'
            . '<strong>De :</strong> ' . htmlspecialchars($row['from_name']) . ' &lt;' . htmlspecialchars($row['from_email']) . '&gt;<br>'
            . '<strong>Objet :</strong> ' . htmlspecialchars($row['subject']) . '<br>'
            . '<strong>Délai :</strong> ' . $delay_min . ' min · <strong>Envoi :</strong> ' . ($sent ? 'OK' : 'ÉCHEC') . '</div>'
            . '<h3>Message reçu</h3><p>' . nl2br(htmlspecialchars($row['body_excerpt'])) . '</p>'
            . '<h3>Réponse de Milo</h3><p>' . nl2br(htmlspecialchars($row['ai_reply'])) . '</p>');
        $log['sent']++;
    } catch (Exception $e) {
        error_log('[ABYS milo-email] send: ' . $e->getMessage());
        $log['errors']++;
    }
}

// ════════════════════════════════════════════════════════════════════
// PHASE 1 · Lire les nouveaux emails (IMAP pur PHP) et mettre en file
// ════════════════════════════════════════════════════════════════════
try {
    $imap = new ImapLite($imap_host, $imap_port, $imap_user, $imap_pass);
    $imap->selectInbox();
    $nums = array_slice($imap->searchUnseen(), 0, 10);
} catch (Exception $e) {
    error_log('[ABYS milo-email] IMAP: ' . $e->getMessage());
    echo json_encode(array_merge($log, ['imap_error' => $e->getMessage()]));
    exit;
}

foreach ($nums as $num) {
    try {
        $msg = imaplite_parse($imap->fetchRaw($num));
        $from_email = $msg['from_email'];
        if (!$from_email) { $imap->markSeen($num); $log['skipped']++; continue; }

        // Anti-boucle : jamais répondre aux automates ni à soi-même
        if (preg_match('/noreply|no-reply|mailer-daemon|postmaster|abys\.ai$/i', $from_email)) {
            $imap->markSeen($num); $log['skipped']++; continue;
        }
        // Doublon
        $exists = $db->prepare("SELECT id FROM email_inbound_log WHERE message_id=?");
        $exists->execute([$msg['message_id']]);
        if ($exists->fetchColumn()) { $imap->markSeen($num); $log['skipped']++; continue; }

        $body_text = mb_substr($msg['body'], 0, 1500);

        // ── Escalade : sujets sensibles -> pas de réponse engageante, admin prévenu ──
        $sensible = preg_match('/rembours|avocat|juridique|litige|rgpd|plainte|arnaque|résili|resili|mise en demeure/iu',
                               $msg['subject'] . ' ' . $body_text);

        if ($sensible) {
            $prenom = ($msg['from_name'] && $msg['from_name'] !== $from_email) ? ' ' . explode(' ', $msg['from_name'])[0] : '';
            $ai_reply = "Bonjour{$prenom},\n\n"
                . "Merci pour votre message, je l'ai bien transmis à l'équipe : votre demande mérite une réponse humaine et attentive. "
                . "Vous recevrez une réponse sous 24h ouvrées.\n\n"
                . "Milo, copilote IA d'ABYS\nabys.ai";
            notify_admin("ESCALADE · email sensible de {$from_email}",
                '<p><strong>Sujet :</strong> ' . htmlspecialchars($msg['subject']) . '</p><p>' . nl2br(htmlspecialchars($body_text)) . '</p>'
                . '<p><strong>Action requise :</strong> répondre personnellement sous 24h.</p>');
            $log['escalated']++;
        } else {
            // ── Réponse de Milo (Haiku : rapide et économique) ──
            $system_prompt = <<<PROMPT
Tu es MILO, le copilote IA d'ABYS (abys.ai), qui aide les PME et artisans français à adopter l'IA simplement. Tu es ouvertement une IA et tu l'assumes : disponible 24h/24, réponse rapide, c'est un avantage.

RÈGLES STRICTES :
- Réponse courte (3-5 paragraphes max), chaleureuse, concrète, zéro jargon ("workflow", "CRM", "B2B", "pipeline", "leads" interdits)
- Commence par le prénom si disponible, sinon "Bonjour,"
- Termine toujours exactement par : "Milo, copilote IA d'ABYS\nabys.ai"
- Tarifs : Audit gratuit sur abys.ai | Rapport Premium 99€ (offre de lancement) | Mission lancement 79€ | Forfait Lancement 199€ | Assistant IA 29€/mois
- JAMAIS de promesse d'argent, de remboursement ou d'engagement contractuel : si on te le demande, dis que tu transmets à l'équipe (réponse sous 24h ouvrées)
- Question complexe ou technique pointue : propose l'audit gratuit ou indique qu'un complément arrivera sous 24h ouvrées
- Ne promets jamais ce qu'ABYS ne peut pas tenir
PROMPT;
            $user_prompt = "Email de : {$msg['from_name']} <{$from_email}>\nObjet : {$msg['subject']}\n\nMessage :\n" . $body_text;

            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['x-api-key: ' . $api_key, 'anthropic-version: 2023-06-01', 'content-type: application/json'],
                CURLOPT_POSTFIELDS     => json_encode([
                    'model' => 'claude-haiku-4-5', 'max_tokens' => 600,
                    'system' => $system_prompt,
                    'messages' => [['role' => 'user', 'content' => $user_prompt]],
                ]),
                CURLOPT_TIMEOUT => 30,
            ]);
            $ai_raw = curl_exec($ch); curl_close($ch);
            $ai_reply = trim(json_decode($ai_raw, true)['content'][0]['text'] ?? '');
            if ($ai_reply === '') {
                $ai_reply = "Bonjour,\n\nMerci pour votre message. Je reviens vers vous très vite avec une réponse complète.\n\nMilo, copilote IA d'ABYS\nabys.ai";
            }
        }

        // Milo est une IA assumée : réponse rapide (2 à 10 minutes), c'est un argument
        $delay_minutes = rand(2, 10);
        $db->prepare(
            "INSERT IGNORE INTO email_inbound_log
             (message_id, from_email, from_name, subject, body_excerpt, ai_reply, queued_at, reply_after, replied)
             VALUES (?,?,?,?,?,?,NOW(),?,0)"
        )->execute([
            $msg['message_id'], $from_email, $msg['from_name'], $msg['subject'],
            mb_substr($body_text, 0, 500), mb_substr($ai_reply, 0, 1000),
            date('Y-m-d H:i:s', time() + $delay_minutes * 60),
        ]);

        $imap->markSeen($num);
        $log['queued']++;
    } catch (Exception $e) {
        error_log("[ABYS milo-email] msg {$num}: " . $e->getMessage());
        $log['errors']++;
    }
}

$imap->close();
echo json_encode(array_merge($log, ['sent_today' => $sent_today, 'cap' => $daily_cap, 'timestamp' => date('Y-m-d H:i:s')]));
