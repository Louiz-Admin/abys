<?php
// Fichier: abys-ai/api/diag.php
// Sonde de diagnostic ABYS. Ne revele AUCUN secret : uniquement des booleens,
// des longueurs et des compteurs. Protegee par jeton. A SUPPRIMER apres usage.
header('Content-Type: application/json');

$TOKEN = 'abys-diag-7f3k9q2m';
if (($_GET['k'] ?? '') !== $TOKEN) { http_response_code(404); die('{}'); }

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$out = ['time' => date('c'), 'site_url' => defined('SITE_URL') ? SITE_URL : null];

try {
    $db = get_db();
    $out['db'] = 'ok';
} catch (Throwable $e) {
    $out['db'] = 'FAIL';
    die(json_encode($out));
}

// Etat des cles de configuration (presence + longueur, jamais la valeur)
$keys = ['stripe_sk','stripe_pk','stripe_webhook','price_report','price_assistant',
         'smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from','contact_email',
         'ai_provider','claude_api_key','openai_api_key',
         'ga4_id','gads_id','gads_conversion_label','meta_pixel_id'];
$settings = [];
try {
    foreach ($db->query("SELECT `key`, value FROM settings")->fetchAll() as $r) {
        $settings[$r['key']] = $r['value'];
    }
} catch (Throwable $e) { $out['settings_table'] = 'FAIL: ' . $e->getMessage(); }

$state = [];
foreach ($keys as $k) {
    $v = $settings[$k] ?? null;
    $state[$k] = ($v === null || $v === '') ? 'ABSENT' : 'present (len ' . strlen($v) . ')';
}
// Cles presentes en base mais pas dans ma liste
$state['_autres_cles'] = array_values(array_diff(array_keys($settings), $keys));
$out['settings'] = $state;

// La cle Stripe se dechiffre-t-elle et est-elle live ou test ?
if (!empty($settings['stripe_sk'])) {
    $sk = decrypt_value($settings['stripe_sk']);
    if ($sk === false || $sk === '') {
        $out['stripe_sk_decrypt'] = 'FAIL (cle de chiffrement differente ?)';
    } else {
        $out['stripe_sk_decrypt'] = 'ok';
        $out['stripe_mode'] = str_starts_with($sk, 'sk_live_') ? 'LIVE'
                            : (str_starts_with($sk, 'sk_test_') ? 'TEST' : 'inconnu');
        // Validation reelle de la cle aupres de Stripe
        if (file_exists(__DIR__ . '/../lib/stripe/init.php')) {
            require_once __DIR__ . '/../lib/stripe/init.php';
            try {
                \Stripe\Stripe::setApiKey($sk);
                $acct = \Stripe\Account::retrieve();
                $out['stripe_key_valid'] = true;
                $out['stripe_charges_enabled'] = (bool)($acct->charges_enabled ?? false);
            } catch (Throwable $e) {
                $out['stripe_key_valid'] = false;
                $out['stripe_error'] = substr($e->getMessage(), 0, 120);
            }
        }
    }
}

// Compteurs des tables metier
foreach (['leads','audits','reports','payments','subscriptions','billing_info','clients'] as $t) {
    try { $out['count_' . $t] = (int)$db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); }
    catch (Throwable $e) { $out['count_' . $t] = 'table absente'; }
}

// Derniers leads (dates uniquement, pas de donnees perso)
try {
    $out['derniers_leads'] = $db->query("SELECT id, created_at, source FROM leads ORDER BY id DESC LIMIT 5")->fetchAll();
} catch (Throwable $e) {}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
