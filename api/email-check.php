<?php
// Fichier: abys-ai/api/email-check.php
// Appelé par cron toutes les 5 minutes.
// Phase 1 : détecte les emails non lus → les met en file d'attente avec délai aléatoire 20-180 min
// Phase 2 : envoie les réponses dont le délai est écoulé
//
// URL cron : https://abys.ai/api/email-check.php?key=abys_cron_2026_x7k9p

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

// ── Sécurité ──────────────────────────────────────────────────────────────────
$db       = get_db();
$settings = [];
foreach ($db->query("SELECT `key`, value FROM settings")->fetchAll() as $r) {
    $settings[$r['key']] = $r['value'];
}

$cron_key = $settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p';
if (($_GET['key'] ?? '') !== $cron_key) {
    http_response_code(403);
    exit('Forbidden');
}

$imap_host = $settings['imap_host'] ?? 'imap.ionos.fr';
$imap_port = (int)($settings['imap_port'] ?? 993);
$imap_user = $settings['imap_user'] ?? 'contact@abys.ai';
$imap_pass = decrypt_value($settings['imap_pass'] ?? '') ?: '';
$api_key   = decrypt_value($settings['claude_key'] ?? '') ?: '';

if (!$imap_pass || !$api_key) {
    http_response_code(500);
    exit(json_encode(['error' => 'Missing IMAP or Claude config']));
}

$log = ['queued' => 0, 'sent' => 0, 'skipped' => 0, 'errors' => 0];

// ════════════════════════════════════════════════════════════════════
// PHASE 2 · Envoyer les réponses dont le délai est écoulé
// (fait en premier pour ne pas bloquer sur la connexion IMAP si inutile)
// ════════════════════════════════════════════════════════════════════
$pending = $db->query(
    "SELECT * FROM email_inbound_log WHERE replied = 0 AND reply_after <= NOW() LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

foreach ($pending as $row) {
    try {
        $reply_subject = preg_match('/^Re\s*:/i', $row['subject']) ? $row['subject'] : 'Re : ' . $row['subject'];
        $reply_html    = '<p>' . nl2br(htmlspecialchars($row['ai_reply'], ENT_QUOTES, 'UTF-8')) . '</p>';
        $sent = send_email($row['from_email'], $reply_subject, $reply_html);

        $db->prepare("UPDATE email_inbound_log SET replied=1, replied_at=NOW() WHERE id=?")
           ->execute([$row['id']]);

        // Copie admin
        $delay_min = round((strtotime($row['reply_after']) - strtotime($row['queued_at'])) / 60);
        $admin_html = '<h2>📬 Réponse auto envoyée</h2>'
            . '<div class="info-box">'
            . '<strong>De :</strong> ' . htmlspecialchars($row['from_name']) . ' &lt;' . htmlspecialchars($row['from_email']) . '&gt;<br>'
            . '<strong>Objet :</strong> ' . htmlspecialchars($row['subject']) . '<br>'
            . '<strong>Délai simulé :</strong> ' . $delay_min . ' minutes<br>'
            . '<strong>Statut envoi :</strong> ' . ($sent ? '✅ OK' : '❌ Échec') . '</div>'
            . '<h3>Message reçu</h3><p>' . nl2br(htmlspecialchars($row['body_excerpt'])) . '</p>'
            . '<h3>Réponse envoyée</h3><p>' . nl2br(htmlspecialchars($row['ai_reply'])) . '</p>';
        notify_admin("Email auto-répondu · {$row['subject']}", $admin_html);

        $log['sent']++;
    } catch (Exception $e) {
        error_log("[ABYS email-check] Send error: " . $e->getMessage());
        $log['errors']++;
    }
}

// ════════════════════════════════════════════════════════════════════
// PHASE 1 · Lire les nouveaux emails IMAP et les mettre en file
// ════════════════════════════════════════════════════════════════════
$mailbox = "{{$imap_host}:{$imap_port}/imap/ssl/novalidate-cert}INBOX";
$imap = @imap_open($mailbox, $imap_user, $imap_pass, 0, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);

if (!$imap) {
    $err = imap_last_error();
    error_log("[ABYS email-check] IMAP failed: $err");
    echo json_encode(array_merge($log, ['imap_error' => $err]));
    exit;
}

$uids = imap_search($imap, 'UNSEEN') ?: [];
$uids = array_slice($uids, 0, 10);

foreach ($uids as $uid) {
    try {
        $header    = imap_headerinfo($imap, $uid);
        $from_raw  = $header->from[0] ?? null;
        if (!$from_raw) { $log['skipped']++; continue; }

        $from_email = strtolower($from_raw->mailbox . '@' . $from_raw->host);
        $from_name  = isset($from_raw->personal) ? imap_utf8($from_raw->personal) : $from_email;
        $subject    = imap_utf8($header->subject ?? '(sans objet)');
        $message_id = trim($header->message_id ?? uniqid('abys_', true));

        // Anti-boucle
        if (preg_match('/noreply|no-reply|mailer-daemon|postmaster|abys\.ai$/i', $from_email)) {
            imap_setflag_full($imap, (string)$uid, '\\Seen');
            $log['skipped']++;
            continue;
        }

        // Doublon ?
        $exists = $db->prepare("SELECT id FROM email_inbound_log WHERE message_id=?");
        $exists->execute([$message_id]);
        if ($exists->fetchColumn()) {
            imap_setflag_full($imap, (string)$uid, '\\Seen');
            $log['skipped']++;
            continue;
        }

        // Corps du message
        $body_text = extract_body($imap, $uid);

        // ── Génération IA immédiate (avant le délai) ──────────────────────────
        $system_prompt = <<<PROMPT
Tu es l'assistant email d'ABYS AI (abys.ai), entreprise française qui aide les PME et artisans à adopter les outils IA simplement.

RÈGLES :
- Réponse courte (3-5 paragraphes max), ton humain et bienveillant
- Aucun jargon ("workflow", "CRM", "B2B", "pipeline", "leads")
- Commence par le prénom si dispo, sinon "Bonjour,"
- Termine toujours par : "L'équipe ABYS AI\nabys.ai"
- Tarifs : Audit gratuit | Rapport Premium 249€ | Assistant IA 29€/mois | Pack Accompagné 499€
- Problème complexe → expert rappelle sous 24h ouvrées
- Ne jamais promettre ce qu'ABYS ne peut pas tenir
PROMPT;

        $user_prompt = "Email de : {$from_name} <{$from_email}>\nObjet : {$subject}\n\nMessage :\n" . mb_substr($body_text, 0, 1500);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $api_key,
                'anthropic-version: 2023-06-01',
                'content-type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model'      => 'claude-haiku-4-5',
                'max_tokens' => 600,
                'system'     => $system_prompt,
                'messages'   => [['role' => 'user', 'content' => $user_prompt]],
            ]),
            CURLOPT_TIMEOUT => 30,
        ]);
        $ai_raw   = curl_exec($ch);
        curl_close($ch);
        $ai_data  = json_decode($ai_raw, true);
        $ai_reply = trim($ai_data['content'][0]['text'] ?? '');

        if (empty($ai_reply)) {
            $ai_reply = "Bonjour,\n\nMerci pour votre message. Notre équipe vous répondra dans les plus brefs délais.\n\nL'équipe ABYS AI\nabys.ai";
        }

        // ── Délai aléatoire 20-180 min ────────────────────────────────────────
        $delay_minutes = rand(20, 180);
        $reply_after   = date('Y-m-d H:i:s', time() + $delay_minutes * 60);

        // Stocker en file d'attente
        $db->prepare(
            "INSERT IGNORE INTO email_inbound_log
             (message_id, from_email, from_name, subject, body_excerpt, ai_reply, queued_at, reply_after, replied)
             VALUES (?,?,?,?,?,?,NOW(),?,0)"
        )->execute([
            $message_id, $from_email, $from_name, $subject,
            mb_substr($body_text, 0, 500),
            mb_substr($ai_reply, 0, 1000),
            $reply_after,
        ]);

        // Marquer comme lu dans IMAP (ne sera pas re-traité)
        imap_setflag_full($imap, (string)$uid, '\\Seen');

        error_log("[ABYS email-check] Queued reply to {$from_email} · réponse dans {$delay_minutes} min");
        $log['queued']++;

    } catch (Exception $e) {
        error_log("[ABYS email-check] Error uid {$uid}: " . $e->getMessage());
        $log['errors']++;
    }
}

imap_close($imap);
echo json_encode(array_merge($log, ['timestamp' => date('Y-m-d H:i:s')]));

// ── Helper : extrait le corps texte d'un message IMAP ────────────────────────
function extract_body($imap, int $uid): string {
    $structure = imap_fetchstructure($imap, $uid);
    if ($structure->type === 0) {
        return trim(decode_part(imap_body($imap, $uid), $structure->encoding ?? 0));
    }
    $plain = $html = '';
    foreach ($structure->parts ?? [] as $i => $part) {
        $sub = strtolower($part->subtype ?? '');
        $raw = imap_fetchbody($imap, $uid, (string)($i + 1));
        $txt = decode_part($raw, $part->encoding ?? 0);
        if ($sub === 'plain') { $plain = $txt; break; }
        if ($sub === 'html' && !$plain) { $html = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $txt)); }
    }
    return trim($plain ?: $html);
}

function decode_part(string $raw, int $encoding): string {
    return match($encoding) {
        3 => base64_decode($raw),
        4 => quoted_printable_decode($raw),
        default => $raw,
    };
}
