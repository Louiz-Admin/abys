<?php
// Fichier: abys-ai/api/stripe.php
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); die(json_encode(['error' => 'Method not allowed']));
}

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../lib/stripe/init.php')) {
    require_once __DIR__ . '/../lib/stripe/init.php';
} else {
    http_response_code(500); die(json_encode(['error' => 'SDK Stripe non installé']));
}

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$db     = get_db();

$settings = $db->query("SELECT `key`, value FROM settings WHERE `key` IN ('stripe_sk','stripe_pk','price_report','price_assistant','payments_enabled')")->fetchAll(PDO::FETCH_KEY_PAIR);

// Interrupteur global des paiements (bascule de compte Stripe en cours)
if (($settings['payments_enabled'] ?? '1') !== '1') {
    http_response_code(503);
    die(json_encode(['error' => 'Paiement momentanément indisponible · réessayez dans quelques heures.']));
}
$sk = decrypt_value($settings['stripe_sk'] ?? '');
if (!$sk) { http_response_code(500); die(json_encode(['error' => 'Stripe non configuré'])); }

\Stripe\Stripe::setApiKey($sk);

// Helper : construire les infos client Stripe depuis les données de facturation
function build_customer_details(array $billing): array {
    $details = [];
    if (!empty($billing['email'])) $details['customer_email'] = $billing['email'];
    $name = trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
    if ($name) {
        $details['customer_creation'] = 'always';
        // On passe les données via metadata pour les utiliser dans les factures
    }
    return $details;
}

// Helper : métadonnées billing pour Stripe
function billing_meta(array $billing): array {
    return [
        'client_name'    => trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')),
        'client_company' => $billing['company_name'] ?? '',
        'client_siret'   => $billing['siret'] ?? '',
        'client_phone'   => $billing['phone'] ?? '',
        'client_address' => trim(($billing['address'] ?? '') . ' ' . ($billing['postal_code'] ?? '') . ' ' . ($billing['city'] ?? '')),
    ];
}

// ── RAPPORT PREMIUM 249€ ────────────────────────────────────────────────────
if ($action === 'create_checkout_report') {
    $lead_id  = intval($input['lead_id']  ?? 0);
    $audit_id = intval($input['audit_id'] ?? 0);
    $email    = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $billing  = $input['billing'] ?? [];
    $price    = intval($settings['price_report'] ?? 249) * 100;

    $token = bin2hex(random_bytes(32));
    $stmt  = $db->prepare("INSERT INTO reports (audit_id, lead_id, token, amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([$audit_id, $lead_id, $token, $settings['price_report'] ?? 249]);
    $report_id = $db->lastInsertId();

    $client_name = trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));

    $session_params = [
        'line_items' => [[
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => [
                    'name'        => 'Rapport ABYS Premium · Audit IA complet',
                    'description' => '7+ opportunités IA · Tutoriels personnalisés · Plan 12 mois · Accès à vie',
                ],
                'unit_amount'  => $price,
            ],
            'quantity' => 1,
        ]],
        'mode'        => 'payment',
        'billing_address_collection' => 'required',
        'success_url' => SITE_URL . '/paiement-succes.php?token=' . $token . '&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => SITE_URL . '/facturation.php?plan=report&cancelled=1',
        'metadata'    => array_merge(
            ['report_id' => $report_id, 'lead_id' => $lead_id, 'token' => $token],
            billing_meta($billing)
        ),
    ];

    if ($email) $session_params['customer_email'] = $email;

    $session = \Stripe\Checkout\Session::create($session_params);
    echo json_encode(['url' => $session->url]);

// ── ABONNEMENT ASSISTANT / SEO ───────────────────────────────────────────────
} elseif ($action === 'create_checkout_subscription') {
    $lead_id = intval($input['lead_id'] ?? 0);
    $plan    = $input['plan'] ?? 'assistant';
    $email   = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $billing = $input['billing'] ?? [];

    $price_amount = match($plan) { 'seo' => 4900, default => 2900 };
    $plan_label   = match($plan) { 'seo' => 'SEO & Visibilité IA ABYS', default => 'Assistant IA ABYS' };

    $stripe_price = \Stripe\Price::create([
        'currency'     => 'eur',
        'unit_amount'  => $price_amount,
        'recurring'    => ['interval' => 'month'],
        'product_data' => ['name' => $plan_label],
    ]);

    $session_params = [
        'line_items'           => [['price' => $stripe_price->id, 'quantity' => 1]],
        'mode'                 => 'subscription',
        'billing_address_collection' => 'required',
        'success_url'          => SITE_URL . '/paiement-succes.php?plan=' . $plan . '&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'           => SITE_URL . '/facturation.php?plan=' . $plan . '&cancelled=1',
        'metadata'             => array_merge(
            ['lead_id' => $lead_id, 'plan' => $plan],
            billing_meta($billing)
        ),
    ];

    if ($email) $session_params['customer_email'] = $email;

    $session = \Stripe\Checkout\Session::create($session_params);
    echo json_encode(['url' => $session->url]);

// ── MISSION LANCEMENT 79€ / FORFAIT LANCEMENT 199€ ──────────────────────────
} elseif ($action === 'create_checkout_mission') {
    $lead_id = intval($input['lead_id'] ?? 0);
    $mplan   = ($input['plan'] ?? 'mission') === 'lancement' ? 'lancement' : 'mission';
    $tool    = substr(trim($input['tool'] ?? ''), 0, 80);
    $email   = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $billing = $input['billing'] ?? [];

    $amount = $mplan === 'lancement' ? 19900 : 7900;
    $name   = $mplan === 'lancement'
        ? 'Forfait Lancement ABYS · 3 outils mis en action'
        : 'Mission lancement ABYS' . ($tool ? ' · ' . $tool : '');
    $desc   = $mplan === 'lancement'
        ? '3 outils installés et actifs avec Milo (IA) · 90 jours d\'assistance incluse'
        : 'Outil installé, paramétré et actif, guidé par Milo (IA) · Satisfait ou remboursé';

    $session_params = [
        'line_items' => [[
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => ['name' => $name, 'description' => $desc],
                'unit_amount'  => $amount,
            ],
            'quantity' => 1,
        ]],
        'mode'        => 'payment',
        'billing_address_collection' => 'required',
        'success_url' => SITE_URL . '/paiement-succes.php?plan=' . $mplan . '&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => SITE_URL . '/facturation.php?plan=' . $mplan . '&cancelled=1',
        'metadata'    => array_merge(
            ['lead_id' => $lead_id, 'plan' => $mplan, 'mission_tool' => $tool],
            billing_meta($billing)
        ),
    ];
    if ($email) $session_params['customer_email'] = $email;

    $session = \Stripe\Checkout\Session::create($session_params);
    echo json_encode(['url' => $session->url]);

// ── FORFAIT INTÉGRAL 499€ (100% IA, 6 mois) ─────────────────────────────────
} elseif ($action === 'create_checkout_pack') {
    $lead_id = intval($input['lead_id'] ?? 0);
    $email   = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $billing = $input['billing'] ?? [];

    $token = bin2hex(random_bytes(32));
    $stmt  = $db->prepare("INSERT INTO reports (lead_id, token, amount) VALUES (?, ?, 499)");
    $stmt->execute([$lead_id, $token]);
    $report_id = $db->lastInsertId();

    $session_params = [
        'line_items' => [[
            'price_data' => [
                'currency'     => 'eur',
                'product_data' => [
                    'name'        => 'Forfait Intégral ABYS',
                    'description' => 'Rapport premium · Toutes vos missions de lancement · Milo (IA) pendant 6 mois',
                ],
                'unit_amount' => 49900,
            ],
            'quantity' => 1,
        ]],
        'mode'        => 'payment',
        'billing_address_collection' => 'required',
        'success_url' => SITE_URL . '/paiement-succes.php?plan=pack&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => SITE_URL . '/facturation.php?plan=pack&cancelled=1',
        'metadata'    => array_merge(
            ['lead_id' => $lead_id, 'plan' => 'pack', 'report_id' => $report_id],
            billing_meta($billing)
        ),
    ];

    if ($email) $session_params['customer_email'] = $email;

    $session = \Stripe\Checkout\Session::create($session_params);
    echo json_encode(['url' => $session->url]);

} else {
    http_response_code(400); echo json_encode(['error' => 'Action inconnue']);
}
