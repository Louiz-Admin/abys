<?php
// Fichier: abys-ai/api/stripe-webhook.php
// Configurer dans Dashboard Stripe → Développeurs → Webhooks
// URL : https://abys.ai/api/stripe-webhook.php
// Événements : checkout.session.completed, customer.subscription.deleted

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../lib/stripe/init.php')) {
    require_once __DIR__ . '/../lib/stripe/init.php';
}

$payload   = file_get_contents('php://input');
$sig       = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$db        = get_db();
$wh_secret = decrypt_value($db->query("SELECT value FROM settings WHERE `key`='stripe_webhook'")->fetchColumn() ?: '');

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig, $wh_secret);
} catch (\Exception $e) {
    http_response_code(400); die('Webhook invalide');
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $meta    = $session->metadata;
    $mode    = $session->mode;

    if ($mode === 'payment' && isset($meta->report_id)) {
        $db->prepare("UPDATE reports SET stripe_payment_id=?, paid_at=NOW(), expires_at=DATE_ADD(NOW(), INTERVAL 90 DAY) WHERE id=?")
           ->execute([$session->payment_intent, $meta->report_id]);

        $db->prepare("INSERT INTO payments (lead_id, stripe_payment_intent, amount, type, reference_id, status) VALUES (?,?,?,?,?,'succeeded')")
           ->execute([$meta->lead_id, $session->payment_intent, $session->amount_total / 100, 'report', $meta->report_id]);

        // Email client · confirmation + lien rapport
        $customer_email = $session->customer_details->email ?? $session->customer_email ?? '';
        $report_row = $db->prepare("SELECT r.token, l.url FROM reports r JOIN leads l ON r.lead_id=l.id WHERE r.id=?");
        $report_row->execute([$meta->report_id]);
        $rdata = $report_row->fetch();
        if ($customer_email && $rdata) {
            email_report_paid($customer_email, $rdata['url'], $rdata['token']);
        }
        // Notif admin
        notify_admin("Nouveau paiement rapport " . ($session->amount_total / 100) . "€ · {$customer_email}", "
            <p>Nouveau paiement rapport premium reçu.</p>
            <div class='info-box'>
              Email : <strong>{$customer_email}</strong><br>
              Site : {$rdata['url']}<br>
              Montant : " . ($session->amount_total / 100) . "€
            </div>
        ");
    }

    // Missions de lancement (79€ / 199€) · accompagnement Milo
    if ($mode === 'payment' && isset($meta->plan) && in_array($meta->plan, ['mission', 'lancement'], true)) {
        $customer_email = $session->customer_details->email ?? ($session->customer_email ?? '');
        $tool = $meta->mission_tool ?? '';
        $db->prepare("INSERT INTO payments (lead_id, stripe_payment_intent, amount, type, status) VALUES (?,?,?,?,'succeeded')")
           ->execute([intval($meta->lead_id ?? 0), $session->payment_intent, $session->amount_total / 100, 'mission']);
        notify_admin("Nouvelle mission " . ($session->amount_total / 100) . "€ · {$customer_email}", "
            <p>Nouvelle mission de lancement payée.</p>
            <div class='info-box'>
              Email : <strong>{$customer_email}</strong><br>
              Formule : {$meta->plan}" . ($tool ? "<br>Outil : {$tool}" : "") . "<br>
              Montant : " . ($session->amount_total / 100) . "€
            </div>
        ");
        if ($customer_email) {
            send_email($customer_email, 'Votre mission de lancement ABYS est activée', "
                <h2>Votre mission est activée ✅</h2>
                <p>Merci pour votre confiance. Milo, votre copilote IA, vous attend dans votre espace pour démarrer la mise en action" . ($tool ? " de <strong>" . htmlspecialchars($tool) . "</strong>" : " de vos outils") . ".</p>
                <a class='btn' href='https://abys.ai/compte/'>Démarrer avec Milo →</a>
                <p style='font-size:13px;color:#6B7280'>Objectif : outil installé, paramétré, premier résultat. Satisfait ou remboursé.</p>
            ");
        }
    }

    if ($mode === 'subscription' && isset($meta->lead_id)) {
        $plan = $meta->plan ?? 'assistant';
        $db->prepare("
            INSERT INTO subscriptions (lead_id, stripe_subscription_id, stripe_customer_id, plan, price, status)
            VALUES (?, ?, ?, ?, ?, 'active')
            ON DUPLICATE KEY UPDATE status='active', stripe_subscription_id=VALUES(stripe_subscription_id)
        ")->execute([$meta->lead_id, $session->subscription, $session->customer, $plan, $session->amount_total / 100]);

        $db->prepare("INSERT INTO payments (lead_id, amount, type, status) VALUES (?,?,'subscription','succeeded')")
           ->execute([$meta->lead_id, $session->amount_total / 100]);

        // Email client · bienvenue abonnement
        $customer_email = $session->customer_details->email ?? $session->customer_email ?? '';
        if ($customer_email) {
            email_subscription_welcome($customer_email, $plan);
        }
        // Notif admin
        $plan_label = $plan === 'seo' ? 'SEO 49€/mois' : 'Assistant 29€/mois';
        notify_admin("Nouvel abonnement {$plan_label} · {$customer_email}", "
            <p>Nouvel abonnement activé.</p>
            <div class='info-box'>
              Email : <strong>{$customer_email}</strong><br>
              Plan : {$plan_label}<br>
              lead_id : {$meta->lead_id}
            </div>
            <p><strong>Action requise :</strong> contacter ce client sous 24h.</p>
        ");
    }
}

if ($event->type === 'customer.subscription.deleted') {
    $sub = $event->data->object;
    $db->prepare("UPDATE subscriptions SET status='cancelled', cancelled_at=NOW() WHERE stripe_subscription_id=?")
       ->execute([$sub->id]);
}

http_response_code(200);
echo json_encode(['received' => true]);
