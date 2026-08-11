<?php
// Fichier: abys-ai/api/email-inbound.php
// Webhook Brevo Inbound — reçoit les emails envoyés à contact@abys.ai,
// génère une réponse par IA (Claude Haiku) et renvoie par mail().
//
// SETUP (une seule fois) :
//   1. Brevo → Inbound Parsing → ajouter votre domaine (mg.abys.ai)
//      → Endpoint webhook : https://abys.ai/api/email-inbound.php
//   2. IONOS email admin → Forwarder : contact@abys.ai → inbound@mg.abys.ai
//   3. Ajouter dans la DB : INSERT INTO settings (key,value) VALUES ('inbound_secret','CHANGEME_SECRET_FORT');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

// ── 1. Sécurité : vérification du secret partagé ─────────────────────────────
$db = get_db();
$secret = $db->query("SELECT value FROM settings WHERE `key`='inbound_secret'")->fetchColumn();

// Brevo envoie le secret en query-string ou header X-Webhook-Secret
$incoming_secret = $_GET['secret'] ?? ($_SERVER['HTTP_X_WEBHOOK_SECRET'] ?? '');
if ($secret && $incoming_secret !== $secret) {
    http_response_code(403);
    exit('Forbidden');
}

// ── 2. Parser le payload Brevo ────────────────────────────────────────────────
$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    // Brevo peut aussi envoyer en form-data
    $payload = $_POST;
}
if (empty($payload)) {
    http_response_code(400);
    exit('No payload');
}

// Champs Brevo Inbound standard
$from_email = $payload['From']   ?? ($payload['from_email'] ?? '');
$from_name  = $payload['FromFull']['Name'] ?? ($payload['from_name'] ?? $from_email);
$subject    = $payload['Subject'] ?? ($payload['subject'] ?? '(sans objet)');
$body_text  = $payload['Text']    ?? ($payload['text'] ?? ($payload['body_text'] ?? ''));
$body_html  = $payload['Html']    ?? ($payload['html'] ?? ($payload['body_html'] ?? ''));
$message_id = $payload['MessageId'] ?? ($payload['message_id'] ?? uniqid());

// Nettoie le corps
if (empty($body_text) && !empty($body_html)) {
    $body_text = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $body_html));
}
$body_text = trim($body_text);

// Évite les boucles auto-reply ↔ auto-reply
if (preg_match('/noreply|no-reply|mailer-daemon|postmaster|auto-reply|autoresponse/i', $from_email)) {
    http_response_code(200);
    exit('Ignored (auto-sender)');
}

// Évite les doublons (même message_id)
$already = $db->prepare("SELECT id FROM email_inbound_log WHERE message_id=?");
$already->execute([$message_id]);
if ($already->fetchColumn()) {
    http_response_code(200);
    exit('Already processed');
}

// ── 3. Générer la réponse IA ─────────────────────────────────────────────────
$settings = [];
foreach ($db->query("SELECT `key`, value FROM settings")->fetchAll() as $r) {
    $settings[$r['key']] = $r['value'];
}
$api_key = decrypt_value($settings['claude_key'] ?? '');
if (!$api_key) {
    http_response_code(500);
    exit('No API key');
}

// Truncate long emails
$body_excerpt = mb_substr($body_text, 0, 1500);

$system_prompt = <<<PROMPT
Tu es l'assistant email d'ABYS AI (abys.ai), une entreprise française qui aide les PME et artisans à adopter les outils IA.

Ta mission : répondre à l'email reçu de manière chaleureuse, professionnelle et utile — en français.

RÈGLES ABSOLUES :
- Réponse courte (3-5 paragraphes maximum)
- Ton humain, bienveillant, jamais condescendant
- Aucun jargon technique (pas de "workflow", "CRM", "B2B", "pipeline", "leads")
- Commence toujours par le prénom si tu le connais, sinon "Bonjour,"
- Termine par : "L'équipe ABYS AI\nabys.ai"
- Si la question concerne un sujet précis (tarifs, rapport, abonnement, problème technique) : donne une réponse directe et utile
- Si la demande est trop complexe ou nécessite une intervention humaine : explique que l'équipe va reprendre contact sous 24h
- NE JAMAIS promettre ce qu'ABYS AI ne peut pas tenir
- NE PAS répéter mot pour mot le contenu de l'email reçu

ABYS AI propose :
- Audit IA gratuit (résultats immédiats sur le site)
- Rapport Premium complet : 249€ paiement unique
- Assistant IA personnel : 29€/mois sans engagement
- Pack IA Accompagné (3 sessions + mise en place) : 499€

Contact : contact@abys.ai | Site : https://abys.ai
PROMPT;

$user_prompt = "Email reçu de : {$from_name} <{$from_email}>\nObjet : {$subject}\n\nMessage :\n{$body_excerpt}";

$response_text = '';
try {
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
            'messages'   => [
                ['role' => 'user', 'content' => $user_prompt],
            ],
        ]),
        CURLOPT_TIMEOUT => 30,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($raw, true);
    $response_text = $data['content'][0]['text'] ?? '';
} catch (Exception $e) {
    $response_text = '';
}

// Fallback si Claude échoue
if (empty(trim($response_text))) {
    $response_text = "Bonjour,\n\nMerci pour votre message. Notre équipe va vous répondre dans les plus brefs délais.\n\nL'équipe ABYS AI\nabys.ai";
}

// ── 4. Envoyer la réponse ─────────────────────────────────────────────────────
$reply_subject = preg_match('/^Re:/i', $subject) ? $subject : 'Re: ' . $subject;
$reply_html    = nl2br(htmlspecialchars($response_text, ENT_QUOTES, 'UTF-8'));
$sent          = send_email($from_email, $reply_subject, $reply_html);

// ── 5. Notifier l'admin (copie) ───────────────────────────────────────────────
$admin_html = '<h2>Email entrant auto-répondu</h2>'
    . '<div class="info-box">'
    . '<strong>De :</strong> ' . htmlspecialchars($from_name) . ' &lt;' . htmlspecialchars($from_email) . '&gt;<br>'
    . '<strong>Objet :</strong> ' . htmlspecialchars($subject) . '<br>'
    . '</div>'
    . '<h3>Message original</h3><p>' . nl2br(htmlspecialchars(mb_substr($body_text, 0, 800))) . '</p>'
    . '<h3>Réponse envoyée</h3><p>' . nl2br(htmlspecialchars($response_text)) . '</p>';
notify_admin('Email auto-répondu — ' . $subject, $admin_html);

// ── 6. Logger en base ─────────────────────────────────────────────────────────
try {
    $db->prepare("INSERT IGNORE INTO email_inbound_log (message_id, from_email, from_name, subject, body_excerpt, ai_reply, replied_at) VALUES (?,?,?,?,?,?,NOW())")
       ->execute([$message_id, $from_email, $from_name, $subject, mb_substr($body_text, 0, 500), mb_substr($response_text, 0, 1000)]);
} catch (Exception $e) { /* silently fail */ }

http_response_code(200);
echo json_encode(['ok' => true, 'sent' => $sent]);
