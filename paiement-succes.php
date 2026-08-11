<?php
$page_title = 'Paiement confirmé — ABYS AI';
require_once __DIR__ . '/api/db.php';

// ── Récupérer plan + montant pour le tracking ─────────────────────────────
$_plan   = $_GET['plan']  ?? 'report';
$_amount = match($_plan) {
    'assistant' => 29,
    'seo'       => 49,
    'pack'      => 499,
    'mission'   => 79,
    'lancement' => 199,
    default     => 0,
};
if ($_plan === 'report') {
    try {
        $_amount = (int)(get_db()->query("SELECT value FROM settings WHERE `key`='price_report'")->fetchColumn() ?: 99);
    } catch (Exception $e) { $_amount = 99; }
}

// Récupérer les IDs tracking
$_tracking = [];
try {
    foreach (get_db()->query("SELECT `key`,value FROM settings WHERE `key` IN ('ga4_id','gads_id','gads_conversion_label')")->fetchAll() as $r) {
        $_tracking[$r['key']] = $r['value'];
    }
} catch (Exception $e) {}

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';

$token = $_GET['token'] ?? '';
$plan  = $_GET['plan']  ?? '';
$report = null;

if ($token) {
    $stmt = get_db()->prepare("SELECT * FROM reports WHERE token = ? AND paid_at IS NOT NULL LIMIT 1");
    $stmt->execute([$token]);
    $report = $stmt->fetch();
}
?>

<div style="min-height:80vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:60px 24px">
  <div style="font-size:64px;margin-bottom:24px">🎉</div>

  <?php if ($report): ?>
    <h1 style="font-size:36px;font-weight:300;letter-spacing:-0.04em;margin-bottom:12px">
      Votre rapport est en cours de <strong style="font-weight:700">génération</strong>
    </h1>
    <p style="color:var(--ink-3);font-size:16px;margin-bottom:32px">Notre IA prépare votre plan d'action personnalisé. Environ 30 secondes.</p>

    <div id="loading-report" style="margin-bottom:24px">
      <div style="width:50px;height:50px;border-radius:50%;border:3px solid rgba(14,165,233,0.15);border-top-color:#10B981;animation:spin 900ms linear infinite;margin:0 auto 16px"></div>
      <p style="color:var(--ink-4);font-size:14px">Génération en cours…</p>
    </div>
    <div id="report-ready" style="display:none">
      <a href="/rapport.php?token=<?= htmlspecialchars($token) ?>" class="btn btn-primary btn-lg">Accéder à mon rapport →</a>
    </div>

  <?php elseif ($plan === 'mission' || $plan === 'lancement'): ?>
    <h1 style="font-size:36px;font-weight:300;letter-spacing:-0.04em;margin-bottom:12px">
      Votre mission est <strong style="font-weight:700">activée !</strong>
    </h1>
    <p style="color:var(--ink-3);font-size:16px;margin-bottom:24px;max-width:520px">
      <strong>Milo</strong>, votre copilote IA, vous attend dans votre espace pour démarrer la mise en action
      <?= $plan === 'lancement' ? 'de vos 3 outils, un par un,' : 'de votre outil' ?> jusqu'au premier résultat concret.
      Vous recevez aussi un email avec votre accès.
    </p>
    <div style="max-width:440px;margin:0 auto 16px;background:linear-gradient(135deg,#0A1F1A,#064E3B);border-radius:var(--r-xl);padding:28px;text-align:left;box-shadow:var(--shadow-lg)">
      <div style="font-size:14px;font-weight:700;color:#6EE7B7;margin-bottom:6px">Créez votre espace pour parler à Milo →</div>
      <p style="font-size:13px;color:rgba(255,255,255,.65);margin-bottom:16px;line-height:1.5">Un mot de passe, et la mission démarre immédiatement.</p>
      <div style="display:flex;flex-direction:column;gap:10px">
        <input type="password" id="account-pass" placeholder="Choisissez un mot de passe (8 car. min)"
          style="padding:11px 14px;border-radius:var(--r-md);border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:#fff;font-size:14px;font-family:inherit;width:100%;box-sizing:border-box">
        <button id="btn-create-account" onclick="createAccount()"
          style="padding:12px;background:#10B981;color:#fff;border:none;border-radius:var(--r-md);font-size:15px;font-weight:600;cursor:pointer;font-family:inherit">
          Démarrer ma mission →
        </button>
      </div>
      <div id="account-error" style="color:#FCA5A5;font-size:12px;margin-top:8px;display:none"></div>
    </div>
    <a href="/" class="btn btn-secondary">Retour à l'accueil</a>

  <?php elseif ($plan): ?>
    <?php
    $plan_label = ($plan === 'seo') ? 'SEO & Visibilité IA' : 'Assistant IA';
    $price      = ($plan === 'seo') ? '49€' : '29€';
    ?>
    <h1 style="font-size:36px;font-weight:300;letter-spacing:-0.04em;margin-bottom:12px">
      Abonnement <strong style="font-weight:700">activé !</strong>
    </h1>
    <p style="color:var(--ink-3);font-size:16px;margin-bottom:24px">
      Votre abonnement <strong><?= htmlspecialchars($plan_label) ?> — <?= $price ?>/mois</strong> est actif.<br>
      Vous allez recevoir un email de confirmation d'ici quelques minutes.
    </p>
    <div style="max-width:440px;margin:0 auto 32px;background:var(--white);border:1px solid var(--border);border-radius:var(--r-xl);padding:28px;text-align:left;box-shadow:var(--shadow-sm)">
      <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--ink-4);margin-bottom:16px">Prochaines étapes</div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;gap:12px;align-items:flex-start">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">1</div>
          <div style="font-size:14px;color:var(--ink-3);line-height:1.5">Vérifiez votre email — confirmation + instructions dans les prochaines minutes</div>
        </div>
        <div style="display:flex;gap:12px;align-items:flex-start">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">2</div>
          <div style="font-size:14px;color:var(--ink-3);line-height:1.5">Notre équipe vous contacte sous <strong>24h ouvrées</strong> pour vous accueillir</div>
        </div>
        <div style="display:flex;gap:12px;align-items:flex-start">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">3</div>
          <div style="font-size:14px;color:var(--ink-3);line-height:1.5">Posez toutes vos questions IA — réponses personnalisées selon votre secteur</div>
        </div>
      </div>
    </div>
    <!-- Création de compte -->
    <div style="max-width:440px;margin:0 auto 16px;background:linear-gradient(135deg,#0A1F1A,#064E3B);border-radius:var(--r-xl);padding:28px;text-align:left;box-shadow:var(--shadow-lg)">
      <div style="font-size:14px;font-weight:700;color:#6EE7B7;margin-bottom:6px">Accédez à votre assistant IA →</div>
      <p style="font-size:13px;color:rgba(255,255,255,.65);margin-bottom:16px;line-height:1.5">Créez votre espace pour accéder à votre assistant IA personnel — disponible 24h/24.</p>
      <div style="display:flex;flex-direction:column;gap:10px">
        <input type="password" id="account-pass" placeholder="Choisissez un mot de passe (8 car. min)"
          style="padding:11px 14px;border-radius:var(--r-md);border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);color:#fff;font-size:14px;font-family:inherit;width:100%;box-sizing:border-box"
          oninput="this.style.borderColor='rgba(255,255,255,.4)'">
        <button id="btn-create-account" onclick="createAccount()"
          style="padding:12px;background:#10B981;color:#fff;border:none;border-radius:var(--r-md);font-size:15px;font-weight:600;cursor:pointer;font-family:inherit">
          Créer mon espace →
        </button>
      </div>
      <div id="account-error" style="color:#FCA5A5;font-size:12px;margin-top:8px;display:none"></div>
    </div>
    <a href="/" class="btn btn-secondary">Retour à l'accueil</a>

  <?php else: ?>
    <h1 style="font-size:36px;font-weight:300;margin-bottom:20px">Merci !</h1>
    <a href="/" class="btn btn-primary">Retour à l'accueil →</a>
  <?php endif; ?>
</div>

<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

<?php if ($report && $token): ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php if (!empty($_tracking['ga4_id'])): ?>
<!-- ── Conversion tracking GA4 + Google Ads ── -->
<script>
(function() {
  var plan   = '<?= htmlspecialchars($_plan, ENT_QUOTES) ?>';
  var amount = <?= (int)$_amount ?>;
  var txId   = '<?= htmlspecialchars($_GET['token'] ?? uniqid('tx_'), ENT_QUOTES) ?>';

  // GA4 purchase event
  if (typeof gtag === 'function') {
    gtag('event', 'purchase', {
      transaction_id: txId,
      value: amount,
      currency: 'EUR',
      items: [{ item_id: plan, item_name: 'ABYS ' + plan, price: amount, quantity: 1 }]
    });

    <?php if (!empty($_tracking['gads_id']) && !empty($_tracking['gads_conversion_label'])): ?>
    // Google Ads conversion
    gtag('event', 'conversion', {
      send_to: '<?= htmlspecialchars($_tracking['gads_id']) ?>/<?= htmlspecialchars($_tracking['gads_conversion_label']) ?>',
      value: amount,
      currency: 'EUR',
      transaction_id: txId
    });
    <?php endif; ?>
  }
})();
</script>
<?php endif; ?>

<script>
// Génération rapport
(async () => {
  const token = '<?= htmlspecialchars($token, ENT_QUOTES) ?>';
  if (!token) return;
  try {
    const res = await ABYS.api('generate-report.php', { token });
    if (res.success) {
      ABYS.store('premium_report', res.report);
      document.getElementById('loading-report').style.display = 'none';
      document.getElementById('report-ready').style.display   = 'block';
    }
  } catch (e) {
    const el = document.querySelector('#loading-report p');
    if (el) el.textContent = 'Erreur de génération — contactez support@abys.ai';
  }
})();

// Création de compte après paiement
async function createAccount() {
  const pass = document.getElementById('account-pass')?.value;
  const errEl = document.getElementById('account-error');
  const btn   = document.getElementById('btn-create-account');
  if (!pass || pass.length < 8) {
    errEl.textContent = 'Mot de passe trop court (8 caractères minimum)';
    errEl.style.display = 'block'; return;
  }
  btn.disabled = true; btn.textContent = 'Création…';
  try {
    // Récupérer email depuis sessionStorage ou champ
    const email = ABYS.get('lead_email') || '';
    const r = await ABYS.api('auth-client.php', {
      action: 'register',
      email,
      password: pass,
      lead_id: parseInt(ABYS.get('lead_id') || 0)
    });
    if (r.success) window.location.href = '/compte/assistant.php';
  } catch(e) {
    errEl.textContent = e.message || 'Erreur, réessayez';
    errEl.style.display = 'block';
    btn.disabled = false; btn.textContent = 'Créer mon espace →';
  }
}
</script>