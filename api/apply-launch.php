<?php
// Fichier: abys-ai/api/apply-launch.php
// Script a usage UNIQUE : passe le prix du rapport a 99 (offre de lancement).
// Se supprime automatiquement apres execution.
header('Content-Type: application/json');

$TOKEN = 'abys-launch-x4p8w2';
if (($_GET['k'] ?? '') !== $TOKEN) { http_response_code(404); die('{}'); }

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$db = get_db();
$before = $db->query("SELECT value FROM settings WHERE `key`='price_report'")->fetchColumn();

$stmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES ('price_report', '99')
                      ON DUPLICATE KEY UPDATE value = '99'");
$stmt->execute();

$after = $db->query("SELECT value FROM settings WHERE `key`='price_report'")->fetchColumn();

$deleted = @unlink(__FILE__);

echo json_encode([
    'price_report_avant' => $before,
    'price_report_apres' => $after,
    'script_supprime'    => $deleted,
], JSON_PRETTY_PRINT);
