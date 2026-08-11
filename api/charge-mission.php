<?php
// Fichier: abys-ai/api/charge-mission.php
// Paiement 1 CLIC des missions (79€ / 199€) sur la carte enregistrée du client.
// Auth : le token du rapport payé (lien secret 64 hex) identifie le client.
//
//  POST {action:'info',   token}                  -> {card:{brand,last4}} ou {card:null}
//  POST {action:'charge', token, plan, tool}      -> {success:true, amount, last4}
//     · carte absente ou authentification bancaire requise -> {fallback:'checkout'}
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

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
$token  = trim($input['token'] ?? '');
if (!$token) { http_response_code(400); die(json_encode(['error' => 'Token manquant'])); }

$db = get_db();
$settings = $db->query("SELECT `key`, value FROM settings WHERE `key` IN ('stripe_sk','payments_enabled')")->fetchAll(PDO::FETCH_KEY_PAIR);
if (($settings['payments_enabled'] ?? '1') !== '1') {
    http_response_code(503); die(json_encode(['error' => 'Paiement momentanément indisponible.']));
}

// Le token d'un rapport PAYÉ identifie le lead (lien secret)
$stmt = $db->prepare("
    SELECT l.id AS lead_id, l.email, l.url,
           l.stripe_customer_id, l.stripe_pm_id, l.card_last4, l.card_brand
    FROM reports r JOIN leads l ON r.lead_id = l.id
    WHERE r.token = ? AND r.paid_at IS NOT NULL LIMIT 1
");
try { $stmt->execute([$token]); $lead = $stmt->fetch(); }
catch (Exception $e) { $lead = false; } // colonnes pas encore migrées => pas de carte

if (!$lead) { http_response_code(404); die(json_encode(['error' => 'Client non trouvé'])); }

$has_card = !empty($lead['stripe_customer_id']) && !empty($lead['stripe_pm_id']);

// ── INFO : la carte enregistrée (pour afficher le bouton 1 clic) ──
if ($action === 'info') {
    echo json_encode(['card' => $has_card
        ? ['brand' => $lead['card_brand'] ?: 'carte', 'last4' => $lead['card_last4'] ?: '????']
        : null]);
    exit;
}

if ($action !== 'charge') { http_response_code(400); die(json_encode(['error' => 'Action inconnue'])); }
if (!$has_card) { echo json_encode(['fallback' => 'checkout']); exit; }

$mplan  = ($input['plan'] ?? 'mission') === 'lancement' ? 'lancement' : 'mission';
$tool   = substr(trim($input['tool'] ?? ''), 0, 80);
$amount = $mplan === 'lancement' ? 19900 : 7900;
$label  = $mplan === 'lancement'
    ? 'Forfait Lancement ABYS · 3 outils mis en action'
    : 'Mission lancement ABYS' . ($tool ? ' · ' . $tool : '');

$sk = decrypt_value($settings['stripe_sk'] ?? '');
if (!$sk) { http_response_code(500); die(json_encode(['error' => 'Stripe non configuré'])); }
\Stripe\Stripe::setApiKey($sk);

// Verrou anti double-clic (2 clics rapides = 1 seul débit)
$lockName = 'abyschg_' . preg_replace('/[^a-zA-Z0-9]/', '', substr($token, 0, 40));
$got = (int) $db->query("SELECT GET_LOCK(" . $db->quote($lockName) . ", 0)")->fetchColumn();
if (!$got) { echo json_encode(['error' => 'Paiement déjà en cours, patientez.']); exit; }

try {
    $pi = \Stripe\PaymentIntent::create([
        'amount'         => $amount,
        'currency'       => 'eur',
        'customer'       => $lead['stripe_customer_id'],
        'payment_method' => $lead['stripe_pm_id'],
        'off_session'    => true,
        'confirm'        => true,
        'description'    => $label,
        'metadata'       => ['lead_id' => $lead['lead_id'], 'plan' => $mplan, 'mission_tool' => $tool, 'one_click' => '1'],
    ]);

    if ($pi->status !== 'succeeded') {
        throw new Exception('Statut inattendu : ' . $pi->status);
    }

    $db->prepare("INSERT INTO payments (lead_id, stripe_payment_intent, amount, type, status) VALUES (?,?,?,?,'succeeded')")
       ->execute([$lead['lead_id'], $pi->id, $amount / 100, 'mission']);

    // Email client + notification admin (mêmes messages que via Checkout)
    if (!empty($lead['email'])) {
        send_email($lead['email'], 'Votre mission de lancement ABYS est activée', "
            <div style='text-align:center;margin:0 0 16px'><img src='https://abys.ai/assets/img/milo-avatar.jpg' alt='Milo' width='76' height='76' style='width:76px;height:76px;border-radius:50%;border:2px solid #10B981;object-fit:cover'></div>
            <h2>Votre mission est activée</h2>
            <p>Merci pour votre confiance. Milo, votre copilote IA, vous attend dans votre espace pour démarrer la mise en action" . ($tool ? " de <strong>" . htmlspecialchars($tool) . "</strong>" : " de vos outils") . ".</p>
            <a class='btn' href='https://abys.ai/compte/'>Démarrer avec Milo</a>
            <p style='font-size:13px;color:#6B7280'>Débit de " . ($amount / 100) . "€ sur votre carte •••• {$lead['card_last4']} · Satisfait ou remboursé.</p>
        ");
    }
    notify_admin("Mission 1 clic " . ($amount / 100) . "€ · {$lead['email']}", "
        <p>Mission payée en 1 clic (carte enregistrée).</p>
        <div class='info-box'>
          Email : <strong>{$lead['email']}</strong><br>
          Site : {$lead['url']}<br>
          Formule : {$mplan}" . ($tool ? "<br>Outil : " . htmlspecialchars($tool) : "") . "<br>
          Montant : " . ($amount / 100) . "€ · carte •••• {$lead['card_last4']}
        </div>
    ");

    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lockName) . ")");
    echo json_encode(['success' => true, 'amount' => $amount / 100, 'last4' => $lead['card_last4']]);

} catch (\Stripe\Exception\CardException $e) {
    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lockName) . ")");
    // Banque exige une authentification (3DS) ou carte refusée -> parcours Checkout classique
    error_log('[ABYS 1clic] CardException: ' . $e->getError()->code);
    echo json_encode(['fallback' => 'checkout', 'reason' => $e->getError()->code]);
} catch (\Exception $e) {
    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lockName) . ")");
    error_log('[ABYS 1clic] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Paiement impossible : ' . $e->getMessage()]);
}
