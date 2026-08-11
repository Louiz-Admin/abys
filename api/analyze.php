<?php
// Fichier: abys-ai/api/analyze.php
set_time_limit(90);
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

$input   = json_decode(file_get_contents('php://input'), true);
$domain  = trim($input['domain']   ?? '');
$scrape  = $input['scrape_data']   ?? [];
$answers = $input['answers']       ?? [];
$lead_id = intval($input['lead_id'] ?? 0);

if (!$domain && !$answers) {
    http_response_code(400); die(json_encode(['error' => 'Données manquantes']));
}

$db       = get_db();
$settings = get_settings($db);
$provider = $settings['ai_provider'] ?? 'claude';
$prompt   = build_audit_prompt($domain, $scrape, $answers, true); // fast=true → Haiku

try {
    $result = call_ai($provider, $prompt, $settings, true); // fast=true → Haiku 3-5s
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Erreur IA : ' . $e->getMessage()]));
}

$audit_id = 0;
if ($lead_id) {
    $audit_id = save_audit($db, $lead_id, $result, $provider, !empty($scrape));
}

echo json_encode(['success' => true, 'audit' => $result, 'audit_id' => $audit_id]);
