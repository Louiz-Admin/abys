<?php
$page_title = 'Paiement confirmé · ABYS AI';
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

<style>
.ok-hero { position: relative; min-height: calc(100vh - 96px); display: flex; align-items: center; justify-content: center;
  padding: 56px 24px; overflow: hidden; }
.ok-hero::before { content: ''; position: absolute; inset: 0;
  background: #041712 url('/assets/img/success-bg.jpg') center/cover no-repeat; }
.ok-hero::after { content: ''; position: absolute; inset: 0;
  background: linear-gradient(180deg, rgba(4,20,16,0.28), rgba(3,13,10,0.52)); }
.ok-card { position: relative; z-index: 2; width: 100%; max-width: 560px; text-align: center;
  background: rgba(255,255,255,0.97); border: 1px solid rgba(255,255,255,0.5); border-radius: 26px;
  padding: 44px 40px 40px; box-shadow: 0 30px 80px -20px rgba(0,0,0,0.55); backdrop-filter: blur(4px); }
.ok-logo { display: inline-flex; align-items: center; gap: 11px; margin-bottom: 26px; }
.ok-logo .name { font-size: 20px; letter-spacing: 0.16em; color: #0A1F1A; }
.ok-logo .name strong { font-weight: 800; }
.ok-logo .name em { font-style: normal; font-weight: 400; color: #10B981; }
.ok-check { width: 54px; height: 54px; border-radius: 50%; margin: 0 auto 20px;
  background: linear-gradient(135deg,#10B981,#059669); display: flex; align-items: center; justify-content: center;
  box-shadow: 0 10px 26px -6px rgba(16,185,129,0.6); }
.ok-check svg { width: 27px; height: 27px; }
.ok-milo { width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden; position: relative;
  border: 3px solid #10B981; background: #052E16; box-shadow: 0 0 0 6px rgba(16,185,129,.12), 0 16px 36px -10px rgba(16,185,129,.55); }
.ok-milo img { width: 100%; height: 100%; object-fit: cover; display: block; }
.ok-milo .tag { position: absolute; bottom: -1px; left: 50%; transform: translateX(-50%); background: #0A1F1A; color: #6EE7B7;
  font-size: 9px; font-weight: 700; letter-spacing: .08em; padding: 2px 9px; border-radius: 20px; border: 1px solid rgba(52,211,153,.4); }
.ok-card h1 { font-size: 31px; font-weight: 300; letter-spacing: -0.04em; line-height: 1.18; margin: 0 0 12px; color: #0A1F1A; }
.ok-card h1 strong { font-weight: 700; }
.ok-card p.sub { color: #4B5563; font-size: 15.5px; line-height: 1.6; margin: 0 auto 26px; max-width: 460px; }
.ok-loader { display: flex; flex-direction: column; align-items: center; gap: 14px; margin: 8px 0 4px; }
.ok-spin { width: 46px; height: 46px; border-radius: 50%; border: 3px solid rgba(16,185,129,0.16); border-top-color: #10B981;
  animation: okspin 900ms linear infinite; }
@keyframes okspin { to { transform: rotate(360deg); } }
.ok-loader small { color: #6B7280; font-size: 13.5px; }
.ok-btn { display: inline-block; padding: 14px 30px; border-radius: 13px; font-weight: 600; font-size: 15.5px;
  text-decoration: none; background: #10B981; color: #fff; box-shadow: 0 12px 28px -10px rgba(16,185,129,0.7); transition: background 150ms; }
.ok-btn:hover { background: #059669; }
.ok-btn.ghost { background: transparent; color: #059669; border: 1px solid rgba(16,185,129,0.4); box-shadow: none; }
.ok-steps { text-align: left; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 14px; padding: 20px 22px; margin: 4px 0 24px; }
.ok-steps .lbl { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #6B7280; margin-bottom: 14px; }
.ok-steps .row { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 12px; }
.ok-steps .row:last-child { margin-bottom: 0; }
.ok-steps .num { width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg,#10B981,#0EA5E9); color: #fff;
  display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
.ok-steps .row div:last-child { font-size: 13.5px; color: #4B5563; line-height: 1.5; }
.ok-account { background: linear-gradient(135deg,#0A1F1A,#064E3B); border-radius: 16px; padding: 24px; text-align: left; margin: 4px 0 18px; }
.ok-account .t { font-size: 14px; font-weight: 700; color: #6EE7B7; margin-bottom: 6px; }
.ok-account p { font-size: 13px; color: rgba(255,255,255,.65); margin: 0 0 14px; line-height: 1.5; }
.ok-account input { padding: 11px 14px; border-radius: 10px; border: 1px solid rgba(255,255,255,.2);
  background: rgba(255,255,255,.08); color: #fff; font-size: 14px; font-family: inherit; width: 100%; box-sizing: border-box; margin-bottom: 10px; }
.ok-account button { width: 100%; padding: 12px; background: #10B981; color: #fff; border: none; border-radius: 10px;
  font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; }
.ok-link { display: inline-block; margin-top: 14px; color: #6B7280; font-size: 13.5px; text-decoration: none; }
.ok-link:hover { color: #374151; }
</style>

<div class="ok-hero">
  <div class="ok-card">
    <div class="ok-logo">
      <svg width="34" height="34" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <rect width="32" height="32" rx="9" fill="#052E16"/>
        <path d="M16 7L24.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/>
        <path d="M16 7L7.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/>
        <line x1="10.5" y1="19" x2="21.5" y2="19" stroke="#10B981" stroke-width="2" stroke-linecap="round"/>
        <circle cx="16" cy="7" r="2" fill="#34D399"/>
      </svg>
      <span class="name"><strong>ABYS</strong><em> AI</em></span>
    </div>

    <?php $milo_moment = in_array($plan, ['mission','lancement','assistant','seo'], true); ?>
    <?php if ($milo_moment): ?>
    <div class="ok-milo"><img src="/assets/img/milo-avatar.jpg" alt="Milo, votre copilote IA"></div>
    <?php else: ?>
    <div class="ok-check">
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 13l4 4L19 7" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <?php endif; ?>

  <?php if ($report): ?>
    <h1>Votre rapport IA se <strong>prépare</strong></h1>
    <p class="sub">Notre IA rédige votre plan d'action personnalisé, outil par outil. Cela prend généralement moins d'une minute.</p>

    <div id="loading-report" class="ok-loader">
      <div class="ok-spin"></div>
      <small id="loading-msg">Génération en cours…</small>
    </div>
    <div id="report-ready" style="display:none">
      <a href="/rapport.php?token=<?= htmlspecialchars($token) ?>" class="ok-btn">Accéder à mon rapport</a>
    </div>

  <?php elseif ($plan === 'mission' || $plan === 'lancement'): ?>
    <h1>Votre mission est <strong>activée</strong></h1>
    <p class="sub">
      <strong>Milo</strong>, votre copilote IA, vous attend dans votre espace pour démarrer la mise en action
      <?= $plan === 'lancement' ? 'de vos 3 outils, un par un,' : 'de votre outil' ?> jusqu'au premier résultat concret.
      Vous recevez aussi un email avec votre accès.
    </p>
    <div class="ok-account">
      <div class="t">Créez votre espace pour parler à Milo</div>
      <p>Un mot de passe, et la mission démarre immédiatement.</p>
      <input type="password" id="account-pass" placeholder="Choisissez un mot de passe (8 car. min)">
      <button id="btn-create-account" onclick="createAccount()">Démarrer ma mission</button>
      <div id="account-error" style="color:#FCA5A5;font-size:12px;margin-top:8px;display:none"></div>
    </div>
    <a href="/" class="ok-link">Retour à l'accueil</a>

  <?php elseif ($plan): ?>
    <?php
    $plan_label = ($plan === 'seo') ? 'SEO & Visibilité IA' : 'Assistant IA';
    $price      = ($plan === 'seo') ? '49€' : '29€';
    ?>
    <h1>Abonnement <strong>activé</strong></h1>
    <p class="sub">Votre abonnement <strong><?= htmlspecialchars($plan_label) ?> · <?= $price ?>/mois</strong> est actif. Vous allez recevoir un email de confirmation d'ici quelques minutes.</p>
    <div class="ok-steps">
      <div class="lbl">Prochaines étapes</div>
      <div class="row"><div class="num">1</div><div>Vérifiez votre email : confirmation et instructions dans les prochaines minutes.</div></div>
      <div class="row"><div class="num">2</div><div>Milo vous accueille dans votre espace pour vos premières questions IA.</div></div>
      <div class="row"><div class="num">3</div><div>Posez toutes vos questions : réponses adaptées à votre secteur.</div></div>
    </div>
    <div class="ok-account">
      <div class="t">Accédez à votre assistant IA</div>
      <p>Créez votre espace pour parler à votre assistant, disponible 24h/24.</p>
      <input type="password" id="account-pass" placeholder="Choisissez un mot de passe (8 car. min)">
      <button id="btn-create-account" onclick="createAccount()">Créer mon espace</button>
      <div id="account-error" style="color:#FCA5A5;font-size:12px;margin-top:8px;display:none"></div>
    </div>
    <a href="/" class="ok-link">Retour à l'accueil</a>

  <?php else: ?>
    <h1>Merci</h1>
    <p class="sub">Votre paiement a bien été confirmé.</p>
    <a href="/" class="ok-btn">Retour à l'accueil</a>
  <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php if (!empty($_tracking['ga4_id'])): ?>
<!-- ── Conversion tracking GA4 + Google Ads ── -->
<script>
(function() {
  var plan   = '<?= htmlspecialchars($_plan, ENT_QUOTES) ?>';
  var amount = <?= (int)$_amount ?>;
  var txId   = '<?= htmlspecialchars($_GET['token'] ?? uniqid('tx_'), ENT_QUOTES) ?>';
  if (typeof gtag === 'function') {
    gtag('event', 'purchase', {
      transaction_id: txId, value: amount, currency: 'EUR',
      items: [{ item_id: plan, item_name: 'ABYS ' + plan, price: amount, quantity: 1 }]
    });
    <?php if (!empty($_tracking['gads_id']) && !empty($_tracking['gads_conversion_label'])): ?>
    gtag('event', 'conversion', {
      send_to: '<?= htmlspecialchars($_tracking['gads_id']) ?>/<?= htmlspecialchars($_tracking['gads_conversion_label']) ?>',
      value: amount, currency: 'EUR', transaction_id: txId
    });
    <?php endif; ?>
  }
})();
</script>
<?php endif; ?>

<script>
// ── Génération rapport : on déclenche puis on POLL un endpoint léger ──
// (aucune connexion longue maintenue côté navigateur → pas de 504)
(function () {
  var token = '<?= htmlspecialchars($token, ENT_QUOTES) ?>';
  if (!token) return;

  // Déclenche la génération côté serveur (best-effort, on n'attend pas la réponse)
  try {
    fetch('/api/generate-report.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ token: token })
    }).catch(function () {});
  } catch (e) {}

  function showReady() {
    var l = document.getElementById('loading-report');
    var r = document.getElementById('report-ready');
    if (l) l.style.display = 'none';
    if (r) r.style.display = 'block';
  }

  var tries = 0, MAX = 80; // ~4 min max
  function poll() {
    tries++;
    fetch('/api/report-status.php?token=' + encodeURIComponent(token))
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (j && j.ready) { showReady(); return; }
        if (tries < MAX) { setTimeout(poll, 3000); }
        else {
          var m = document.getElementById('loading-msg');
          if (m) m.innerHTML = "Cela prend un peu plus de temps que prévu. Votre lien reste valable : <a href='/rapport.php?token=" + encodeURIComponent(token) + "'>ouvrir mon rapport</a> ou réessayez dans une minute.";
        }
      })
      .catch(function () { if (tries < MAX) setTimeout(poll, 3000); });
  }
  setTimeout(poll, 2500);
})();

// ── Création de compte après paiement ──
async function createAccount() {
  const pass  = document.getElementById('account-pass')?.value;
  const errEl = document.getElementById('account-error');
  const btn   = document.getElementById('btn-create-account');
  if (!pass || pass.length < 8) {
    errEl.textContent = 'Mot de passe trop court (8 caractères minimum)';
    errEl.style.display = 'block'; return;
  }
  btn.disabled = true; btn.textContent = 'Création…';
  try {
    const email = ABYS.get('lead_email') || '';
    const r = await ABYS.api('auth-client.php', {
      action: 'register', email, password: pass,
      lead_id: parseInt(ABYS.get('lead_id') || 0)
    });
    if (r.success) window.location.href = '/compte/assistant.php';
  } catch (e) {
    errEl.textContent = e.message || 'Erreur, réessayez';
    errEl.style.display = 'block';
    btn.disabled = false; btn.textContent = 'Créer mon espace';
  }
}
</script>
