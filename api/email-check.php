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

// ── Nettoyage anti-« style IA » : appliqué à CHAQUE réponse avant envoi ──────
// Supprime le markdown, les tirets longs, et toute proposition de rendez-vous
// (Milo est une IA écrite : pas d'agenda, pas de téléphone, pas de visio).
function milo_sanitize(string $t): string {
    // Markdown : gras/italique/titres/puces
    $t = preg_replace('/\*{1,3}([^*]+)\*{1,3}/u', '$1', $t);
    $t = preg_replace('/^#{1,6}\s*/mu', '', $t);
    $t = preg_replace('/^\s*[-•]\s+/mu', '', $t);
    // Tirets cadratins et demi-cadratins : interdits (règle absolue)
    $t = str_replace([' — ', ' – ', '—', '–'], [' : ', ' : ', ', ', ', '], $t);
    // Aucune proposition de créneau/rendez-vous/appel : on retire la phrase entière
    $t = preg_replace('/[^.!?\n]*(cr[ée]neau|rendez-?vous|\brdv\b|appel(er)?\b|t[ée]l[ée]phon|visio)[^.!?\n]*[.!?]?/iu', '', $t);
    // Aucune auto-presentation en machine : « je suis Milo, l'IA d'ABYS » degage.
    // Milo reste honnete si on lui pose la question, mais il ne l'amene jamais lui-meme.
    $t = preg_replace('/[^.!?\n]*\b(je suis|c\x27est moi|moi c\x27est)\b[^.!?\n]{0,60}\b(l\x27\s*IA|une\s+IA|intelligence artificielle|assistant virtuel|robot|agent (?:IA|conversationnel))\b[^.!?\n]*[.!?]?/iu', '', $t);
    // Ponctuation : pas de rafales d'exclamations
    $t = preg_replace('/!{2,}/', '!', $t);
    // Espaces propres
    $t = preg_replace("/[ \t]+\n/", "\n", $t);
    $t = preg_replace('/^[ \t]+/mu', '', $t);
    $t = preg_replace("/\n{3,}/", "\n\n", $t);
    return trim($t);
}

// ── Template : en-tête Milo AVANT le message (table : compatible tous clients) ──
function milo_reply_html(string $text): string {
    $body = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    return <<<HTML
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px">
  <tr>
    <td style="vertical-align:middle;padding-right:12px">
      <img src="https://abys.ai/assets/img/milo-avatar.jpg" alt="Milo" width="48" height="48"
           style="display:block;width:48px;height:48px;border-radius:50%;border:2px solid #10B981">
    </td>
    <td style="vertical-align:middle;font-size:13px;color:#6B7280;line-height:1.4">
      <strong style="color:#111827">Milo</strong>, copilote IA d'ABYS<br>abys.ai
    </td>
  </tr>
</table>
<p style="margin:0">{$body}</p>
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
        $sent = send_email_perso($row['from_email'], $reply_subject, milo_reply_html($row['ai_reply']));
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

        // ── Sujets sensibles : Milo repond lui-meme, sans jamais s'engager ──
        // REGLE ABSOLUE : rien n'est renvoye vers un humain. Milo traite tout.
        $sensible = preg_match('/rembours|avocat|juridique|litige|rgpd|plainte|arnaque|résili|resili|mise en demeure/iu',
                               $msg['subject'] . ' ' . $body_text);

        $consigne_sensible = $sensible
                ? "ATTENTION, ce message touche a un sujet sensible (remboursement, litige, donnees personnelles, resiliation). Tu reponds toi-meme, avec calme et respect. Tu accuses reception precisement, tu rappelles les faits que tu connais, tu n'inventes rien, tu ne t'engages sur AUCUN montant, AUCUN remboursement, AUCUN delai contractuel. Tu ne renvoies vers personne d'autre. Tu proposes la prochaine etape concrete que tu peux tenir toi-meme.\n\n"
                : '';
        // ── Réponse de Milo (Haiku : rapide et économique) ──
            $system_prompt = <<<PROMPT
{$consigne_sensible}Tu es MILO, le copilote IA d'ABYS (abys.ai), qui aide les PME et artisans français à adopter l'IA simplement. Tu ne te presentes jamais spontanement comme une intelligence artificielle : personne n'aime ecrire a une machine. Tu reponds simplement, a la premiere personne. Si on te pose franchement la question, tu dis la verite sans detour.

STYLE (STRICT) :
- Écris comme un humain sobre et direct. AUCUN markdown : pas de **, pas de titres, pas de listes à puces.
- INTERDIT ABSOLU : le tiret long (—) et le tiret demi-long (–). Utilise une virgule ou deux points.
- Réponse courte (3-4 paragraphes max), chaleureuse, concrète, zéro jargon ("workflow", "CRM", "B2B", "pipeline", "leads" interdits)
- Un seul point d'exclamation maximum dans tout l'email
- Commence par le prénom si disponible, sinon "Bonjour,"
- Termine par une phrase simple puis "Milo" seul sur la dernière ligne (rien d'autre : la signature complète est ajoutée automatiquement)

LIMITES (STRICTES) :
- Tu n'as NI agenda, NI téléphone, NI visio. Ne propose JAMAIS de créneau, rendez-vous, appel ou rencontre. Si on t'en demande un, explique avec le sourire que tout se passe par écrit, immédiatement, 24h/24 : par email avec toi, ou via l'audit gratuit sur abys.ai (2 minutes, résultat immédiat). Si la personne tient à parler à un humain, explique simplement que c'est toi qui traites sa demande, maintenant, et traite-la. Ne parle JAMAIS d'une équipe, d'un collègue, d'un transfert ou d'un rappel.
- JAMAIS de promesse d'argent, de remboursement ou d'engagement contractuel : tu le dis franchement et tu expliques ce que tu peux faire à la place. Tu ne renvoies vers personne.
- Question complexe ou technique pointue : tu réponds au mieux avec ce que tu sais, et tu proposes l'audit gratuit pour aller plus loin. Jamais de renvoi vers quelqu'un d'autre.
- Ne promets jamais ce qu'ABYS ne peut pas tenir

Tarifs : Audit gratuit sur abys.ai | Rapport Premium 99€ (offre de lancement) | Mission lancement 79€ | Forfait Lancement 199€ | Assistant IA 29€/mois
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
            $ai_reply = milo_sanitize(trim(json_decode($ai_raw, true)['content'][0]['text'] ?? ''));
            // Aucune reponse ne part sans salutation, quoi qu'ait produit le modele
            $prenom_exp = ($msg['from_name'] && $msg['from_name'] !== $from_email)
                ? explode(' ', trim($msg['from_name']))[0] : '';
            $ai_reply = milo_saluer($ai_reply, $prenom_exp);
            if (mb_strlen($ai_reply) < 40) {
                $ai_reply = "Bonjour,\n\nMerci pour votre message. Je le reprends en detail et je vous reponds dans la foulee.\n\nMilo";
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
