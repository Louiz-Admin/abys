<?php
// Export SQL de la base ABYS (lecture seule). Protege par jeton. A SUPPRIMER apres migration.
$T = 'abys-mig-Kp7Rx2q9';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
header('Content-Type: text/plain; charset=utf-8');

$db = get_db();

// En-tete : cle de chiffrement + site url, en commentaires SQL (le VPS les extrait)
echo "-- ABYS export " . date('c') . "\n";
echo "-- ENC_KEY_B64: " . base64_encode(ENCRYPTION_KEY) . "\n";
echo "-- SITE_URL: " . SITE_URL . "\n";
echo "SET FOREIGN_KEY_CHECKS=0;\n";
echo "SET NAMES utf8mb4;\n\n";

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    // Structure
    $create = $db->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);
    $ddl = $create['Create Table'] ?? ($create['Create View'] ?? null);
    if (!$ddl) continue;
    echo "DROP TABLE IF EXISTS `$t`;\n";
    echo $ddl . ";\n\n";

    // Donnees
    $rows = $db->query("SELECT * FROM `$t`");
    $buffer = [];
    $cols = null;
    $flush = function () use (&$buffer, &$cols, $t) {
        if (!$buffer || $cols === null) return;
        echo "INSERT INTO `$t` (`$cols`) VALUES\n" . implode(",\n", $buffer) . ";\n";
        $buffer = [];
    };
    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        if ($cols === null) $cols = implode("`,`", array_keys($row));
        $vals = array_map(function ($v) use ($db) {
            return $v === null ? 'NULL' : $db->quote($v);
        }, array_values($row));
        $buffer[] = "(" . implode(",", $vals) . ")";
        if (count($buffer) >= 200) $flush();
    }
    $flush();
    echo "\n";
}
echo "SET FOREIGN_KEY_CHECKS=1;\n";
