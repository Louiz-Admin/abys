<?php
// Recopie le config.php restaure par IONOS (_ProviderRestore) vers api/config.php.
// Standalone : ne require PAS config.php. Cible uniquement _ProviderRestore. Auto-suppression.
header('Content-Type: application/json');
$T = 'abys-mv-6r3q9';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }

$api_dir  = __DIR__;                      // .../abys/api
$abys_dir = dirname($api_dir);            // .../abys
$web_root = dirname($abys_dir);           // .../htdocs
$target   = $api_dir . '/config.php';

$out = ['abys_dir' => $abys_dir, 'web_root' => $web_root];

// Emplacements possibles du dossier restaure
$pr_roots = array_values(array_filter([
    $web_root . '/_ProviderRestore',
    $abys_dir . '/_ProviderRestore',
], 'is_dir'));
$out['provider_restore_dirs'] = $pr_roots;

if (!$pr_roots) {
    $out['resultat'] = 'Dossier _ProviderRestore introuvable — restauration pas encore terminee ?';
    echo json_encode($out, JSON_PRETTY_PRINT); exit;
}

// Chemins directs probables
$direct = [];
foreach ($pr_roots as $r) {
    $direct[] = $r . '/abys/api/config.php';
    $direct[] = $r . '/api/config.php';
}

// + scan limite AU SEUL _ProviderRestore (profondeur faible)
$found = [];
$seen = [];
function scan_pr($dir, &$found, &$seen, $depth = 0) {
    if ($depth > 6 || !is_dir($dir)) return;
    $real = realpath($dir);
    if ($real === false || isset($seen[$real])) return;
    $seen[$real] = true;
    foreach (@scandir($dir) ?: [] as $e) {
        if ($e === '.' || $e === '..') continue;
        $p = $dir . '/' . $e;
        if (is_dir($p)) scan_pr($p, $found, $seen, $depth + 1);
        elseif ($e === 'config.php') $found[] = $p;
    }
}
foreach ($pr_roots as $r) scan_pr($r, $found, $seen);

$all = array_values(array_unique(array_merge(array_filter($direct, 'is_file'), $found)));
$out['config_trouves'] = $all;

// Choisir un config avec de VRAIES valeurs (pas le placeholder)
$best = null;
foreach ($all as $f) {
    $c = @file_get_contents($f);
    if ($c === false) continue;
    if (strpos($c, 'VOTRE_MOT_DE_PASSE_ICI') !== false) continue;
    if (strpos($c, 'ENCRYPTION_KEY') !== false && strpos($c, 'DB_PASS') !== false) { $best = $f; break; }
}

if (!$best) {
    $out['resultat'] = 'Config restaure pas encore present ou seulement le placeholder. Reessayer dans 1-2 min.';
    echo json_encode($out, JSON_PRETTY_PRINT); exit;
}

$out['source_choisie'] = $best;
@copy($target, $target . '.broken-' . date('YmdHis'));
$ok = @copy($best, $target);
$out['copie_ok'] = $ok;
if ($ok) {
    $c = file_get_contents($target);
    $out['nouveau_len'] = strlen($c);
    $out['contient_placeholder'] = strpos($c, 'VOTRE_MOT_DE_PASSE_ICI') !== false;
    $out['mover_supprime'] = @unlink(__FILE__);
}
echo json_encode($out, JSON_PRETTY_PRINT);
