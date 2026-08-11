<?php
// Supprime les sondes temporaires de migration, puis se supprime.
header('Content-Type: application/json');
$T = 'abys-cl2-3v9x';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }
$dir = __DIR__;
$cibles = ['migrate.php','leads-stats.php','cleanup2.php'];
$res = [];
foreach ($cibles as $f) {
    $p = $dir . '/' . $f;
    $res[$f] = file_exists($p) ? (@unlink($p) ? 'supprime' : 'ECHEC') : 'absent';
}
echo json_encode($res, JSON_PRETTY_PRINT);
