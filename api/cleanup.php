<?php
// Supprime tous les scripts temporaires de diagnostic/sauvetage, puis se supprime.
header('Content-Type: application/json');
$T = 'abys-clean-8w4z1';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }

$dir = __DIR__; // .../abys/api
$cibles = [
    $dir . '/diag.php',
    $dir . '/recover.php',
    $dir . '/explorer.php',
    $dir . '/apply-config.php',
    $dir . '/apply-launch.php',
    $dir . '/mover.php',
    $dir . '/config-values.json',
];
$res = [];
foreach ($cibles as $f) {
    if (file_exists($f)) {
        $res[basename($f)] = @unlink($f) ? 'supprime' : 'ECHEC';
    } else {
        $res[basename($f)] = 'absent';
    }
}
// Supprime aussi les eventuelles sauvegardes .broken-*
foreach (glob($dir . '/config.php.broken-*') ?: [] as $b) {
    $res[basename($b)] = @unlink($b) ? 'supprime' : 'ECHEC';
}
$res['cleanup.php'] = @unlink(__FILE__) ? 'supprime' : 'ECHEC';

echo json_encode($res, JSON_PRETTY_PRINT);
