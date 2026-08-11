<?php
// Urgence : cherche une sauvegarde de config sur le serveur. A SUPPRIMER apres.
$T = 'abys-exp-3j8w1z';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }
header('Content-Type: application/json');

$root = dirname(__DIR__);
$out = ['racine' => $root, 'fichiers' => []];

$scan_dirs = [$root, $root . '/api', $root . '/setup', $root . '/includes', dirname($root)];
foreach ($scan_dirs as $d) {
    if (!is_dir($d)) continue;
    foreach (scandir($d) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = $d . '/' . $f;
        if (is_file($p)) {
            $out['fichiers'][] = [
                'chemin' => str_replace(dirname($root), '', $p),
                'taille' => filesize($p),
                'modifie' => date('Y-m-d H:i', filemtime($p)),
            ];
        }
    }
}

// Recherche ciblee de sauvegardes de config
$patterns = ['config*', '*.bak', '*.old', '*.save', '*.backup', '*~'];
$found = [];
foreach ($scan_dirs as $d) {
    if (!is_dir($d)) continue;
    foreach ($patterns as $pat) {
        foreach (glob($d . '/' . $pat) ?: [] as $p) {
            if (is_file($p)) $found[] = str_replace(dirname($root), '', $p);
        }
    }
}
$out['candidats_config'] = array_values(array_unique($found));
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
