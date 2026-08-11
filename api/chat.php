<?php
// Fichier: abys-ai/api/chat.php
// SSE streaming chat avec contexte d'audit complet
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Auth obligatoire
if (empty($_SESSION['client_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Non authentifié']));
}

$client_id = (int)$_SESSION['client_id'];
$input     = json_decode(file_get_contents('php://input'), true);
$message   = trim($input['message'] ?? '');

if (!$message || strlen($message) > 4000) {
    http_response_code(400);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Message invalide']));
}

$db = get_db();

// ── Charger le contexte client ─────────────────────────────────────────────
$client = $db->prepare("SELECT * FROM client_accounts WHERE id=?");
$client->execute([$client_id]);
$client = $client->fetch();

$context = '';
if ($client['lead_id']) {
    // Audit et opportunités
    $audit = $db->prepare("
        SELECT a.*, l.url, l.secteur, l.email
        FROM audits a
        JOIN leads l ON l.id = a.lead_id
        WHERE a.lead_id = ?
        ORDER BY a.created_at DESC LIMIT 1
    ");
    $audit->execute([$client['lead_id']]);
    $audit = $audit->fetch();

    if ($audit) {
        $opps = json_decode($audit['recommendations'] ?? '{}', true);
        $opportunities = $opps['opportunities'] ?? [];

        $opps_txt = '';
        foreach ($opportunities as $o) {
            $opps_txt .= "- {$o['tool']} : {$o['description']} (économie: {$o['money_saved_eur_month']}€/mois, {$o['time_saved_h_week']}h/sem)\n";
        }

        $context = "
=== CONTEXTE CLIENT ===
Site web : {$audit['url']}
Secteur d'activité : {$audit['secteur']}
Score IA actuel : {$audit['score']}/100
Email : {$audit['email']}

=== OPPORTUNITÉS IA IDENTIFIÉES ===
{$opps_txt}
=== FIN DU CONTEXTE ===
";

        // Rapport premium si disponible
        $report = $db->prepare("
            SELECT content FROM reports WHERE lead_id=? AND paid_at IS NOT NULL ORDER BY paid_at DESC LIMIT 1
        ");
        $report->execute([$client['lead_id']]);
        $report_row = $report->fetch();
        if ($report_row && $report_row['content']) {
            $rc = json_decode($report_row['content'], true);
            $context .= "\n=== RAPPORT PREMIUM ===\nRésumé exécutif : " . ($rc['executive_summary'] ?? '') . "\n";
            if (!empty($rc['action_plan']['month_1'])) {
                $context .= "Actions prioritaires mois 1 : " . implode(', ', $rc['action_plan']['month_1']) . "\n";
            }
        }
    }
}

// ── Historique de conversation (10 derniers messages) ─────────────────────
$history_rows = $db->prepare("
    SELECT role, content FROM chat_messages WHERE client_id=? ORDER BY created_at DESC LIMIT 20
");
$history_rows->execute([$client_id]);
$history = array_reverse($history_rows->fetchAll());

$messages = [];
foreach ($history as $h) {
    $messages[] = ['role' => $h['role'], 'content' => $h['content']];
}
$messages[] = ['role' => 'user', 'content' => $message];

// Sauvegarder message user
$db->prepare("INSERT INTO chat_messages (client_id, role, content) VALUES (?,?,?)")
   ->execute([$client_id, 'user', $message]);

// ── System prompt ──────────────────────────────────────────────────────────
$system = "Tu es MILO, le copilote IA d'ABYS (abys.ai). Tu es ouvertement une intelligence artificielle : tu l'assumes avec fierté, c'est un argument (disponible 24h/24, jamais pressé, toujours à jour). Tu connais parfaitement l'entreprise de ce client, son secteur et les outils IA recommandés pour lui. Ton rôle : le guider concrètement, pas à pas, jusqu'au premier résultat réel.

Ton style :
- Tu parles à la première personne en tant que Milo. Chaleureux, direct, zéro jargon.
- Réponses courtes et actionnables (pas d'intro, pas de blabla)
- Tutoiement si le client tutoie, vouvoiement sinon
- Toujours proposer UNE prochaine étape concrète
- Si tu guides vers un outil, donne les étapes précises d'installation/configuration (clique ici, va dans ce menu, copie ce code)
- Tu peux demander des précisions pour mieux aider

Tes limites (STRICTES) :
- Jamais de promesse d'argent, de remboursement, de geste commercial ou d'engagement contractuel : réponds « je transmets à l'équipe, réponse sous 24h ouvrées » et invite à écrire à contact@abys.ai
- Jamais de conseil juridique, fiscal ou comptable engageant : oriente vers un professionnel
- Ne jamais dénigrer un concurrent, ne jamais inventer des chiffres sur ABYS
- Si le client est mécontent ou évoque un litige : excuse sincère, escalade vers contact@abys.ai, ton calme

Tarifs que tu peux rappeler : Audit gratuit · Rapport Premium 99€ (offre de lancement) · Mission lancement 79€ · Forfait Lancement 199€ (3 outils, 90 j) · Assistant IA 29€/mois · Forfait Intégral 499€ (6 mois).

{$context}";

// ── Streaming Claude API ───────────────────────────────────────────────────
$settings = get_settings($db);
$api_key  = decrypt_value($settings['claude_key'] ?? '');

if (!$api_key) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'IA non configurée']));
}

// Headers SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
ob_implicit_flush(true);
ob_end_flush();

$payload = json_encode([
    'model'      => 'claude-haiku-4-5',
    'max_tokens' => 1024,
    'system'     => $system,
    'messages'   => $messages,
    'stream'     => true,
]);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . $api_key,
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_WRITEFUNCTION  => function($curl, $data) use ($db, $client_id, &$full_response) {
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line || !str_starts_with($line, 'data:')) continue;
            $json = trim(substr($line, 5));
            if ($json === '[DONE]') {
                echo "data: [DONE]\n\n";
                flush();
                continue;
            }
            $ev = json_decode($json, true);
            if (!$ev) continue;

            if (($ev['type'] ?? '') === 'content_block_delta') {
                $text = $ev['delta']['text'] ?? '';
                if ($text !== '') {
                    $full_response .= $text;
                    $chunk = json_encode(['text' => $text]);
                    echo "data: {$chunk}\n\n";
                    flush();
                }
            }
        }
        return strlen($data);
    },
]);

$full_response = '';
curl_exec($ch);
curl_close($ch);

// Sauvegarder réponse assistant
if ($full_response) {
    $db->prepare("INSERT INTO chat_messages (client_id, role, content) VALUES (?,?,?)")
       ->execute([$client_id, 'assistant', $full_response]);
}
