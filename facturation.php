<?php
$plan = $_GET['plan'] ?? 'report'; // report | assistant | seo | pack | mission | lancement
$valid_plans = ['report', 'assistant', 'seo', 'pack', 'mission', 'lancement'];
if (!in_array($plan, $valid_plans)) $plan = 'report';
$tool = trim(substr($_GET['tool'] ?? '', 0, 80));

$plan_labels = [
    'report'    => 'Rapport Premium',
    'assistant' => 'Assistant IA',
    'seo'       => 'SEO & Visibilité IA',
    'pack'      => 'Forfait Intégral',
    'mission'   => 'Mission lancement' . ($tool ? ' — ' . $tool : ''),
    'lancement' => 'Forfait Lancement',
];
$plan_prices = [
    'report'    => '99€',
    'assistant' => '29€/mois',
    'seo'       => '49€/mois',
    'pack'      => '499€',
    'mission'   => '79€',
    'lancement' => '199€',
];
$plan_label = $plan_labels[$plan];
$plan_price = $plan_prices[$plan];

$page_title = "Facturation — {$plan_label} — ABYS AI";
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>
.fact-wrap { max-width: 780px; margin: 0 auto; padding: 56px 24px 80px; }
.fact-header { margin-bottom: 40px; }
.fact-header h1 { font-size: 32px; font-weight: 300; letter-spacing: -0.04em; margin-bottom: 8px; }
.fact-header h1 strong { font-weight: 700; }
.fact-header p { font-size: 15px; color: var(--ink-3); }

.fact-grid { display: grid; grid-template-columns: 1fr 320px; gap: 32px; align-items: start; }
@media(max-width: 768px) { .fact-grid { grid-template-columns: 1fr; } }

/* Formulaire */
.fact-form { background: var(--white); border: 1px solid var(--border); border-radius: var(--r-xl); padding: 32px; }
.fact-section { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--ink-4); margin: 24px 0 16px; padding-top: 24px; border-top: 1px solid var(--border); }
.fact-section:first-child { margin-top: 0; padding-top: 0; border-top: none; }
.fact-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.fact-row.full { grid-template-columns: 1fr; }
@media(max-width: 540px) { .fact-row { grid-template-columns: 1fr; } }
.fact-field { display: flex; flex-direction: column; gap: 5px; }
.fact-field label { font-size: 12px; font-weight: 500; color: var(--ink-3); }
.fact-field label .req { color: var(--green); margin-left: 2px; }
.fact-field label .opt { color: var(--ink-4); font-weight: 400; font-size: 11px; margin-left: 4px; }
.fact-field input, .fact-field select {
  padding: 10px 12px; border: 1px solid var(--border-2); border-radius: var(--r-md);
  font-family: var(--font); font-size: 14px; color: var(--ink); background: var(--bg);
  transition: border-color 150ms; outline: none; width: 100%; box-sizing: border-box;
}
.fact-field input:focus, .fact-field select:focus { border-color: var(--green); }
.fact-field input.error { border-color: #EF4444; }

.fact-error { background: rgba(239,68,68,.07); border: 1px solid rgba(239,68,68,.2); color: #DC2626;
  padding: 12px 16px; border-radius: var(--r-md); font-size: 13px; margin-bottom: 20px; display: none; }
.fact-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #059669, #064E3B);
  color: #fff; border: none; border-radius: var(--r-md); font-size: 16px; font-weight: 600;
  cursor: pointer; font-family: var(--font); transition: opacity 150ms; margin-top: 8px; }
.fact-btn:hover { opacity: .9; }
.fact-btn:disabled { opacity: .5; cursor: not-allowed; }

/* Récap commande */
.order-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--r-xl); padding: 24px; position: sticky; top: 80px; }
.order-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--ink-4); margin-bottom: 20px; }
.order-product { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid var(--border); margin-bottom: 14px; }
.order-product-name { font-size: 15px; font-weight: 600; color: var(--ink-2); }
.order-product-price { font-size: 22px; font-weight: 700; color: var(--green-deep); }
.order-includes { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
.order-line { display: flex; gap: 8px; font-size: 13px; color: var(--ink-3); }
.order-line svg { flex-shrink: 0; margin-top: 1px; }
.order-secure { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--ink-4); padding-top: 16px; border-top: 1px solid var(--border); }
</style>

<div class="fact-wrap">
  <div class="fact-header">
    <div class="badge" style="margin-bottom: 16px"><?= htmlspecialchars($plan_label) ?></div>
    <h1>Informations de <strong>facturation</strong></h1>
    <p>Ces informations serviront à établir votre facture officielle.</p>
  </div>

  <div class="fact-grid">
    <!-- Formulaire -->
    <div class="fact-form">
      <div class="fact-error" id="fact-error"></div>

      <div class="fact-section">Contact</div>
      <div class="fact-row">
        <div class="fact-field">
          <label>Prénom <span class="req">*</span></label>
          <input type="text" id="f_first_name" placeholder="Marie" autocomplete="given-name" required>
        </div>
        <div class="fact-field">
          <label>Nom <span class="req">*</span></label>
          <input type="text" id="f_last_name" placeholder="Dupont" autocomplete="family-name" required>
        </div>
      </div>
      <div class="fact-row">
        <div class="fact-field">
          <label>Poste / Fonction <span class="opt">(optionnel)</span></label>
          <input type="text" id="f_job_title" placeholder="Directrice générale" autocomplete="organization-title">
        </div>
        <div class="fact-field">
          <label>Téléphone <span class="opt">(optionnel)</span></label>
          <input type="tel" id="f_phone" placeholder="06 12 34 56 78" autocomplete="tel">
        </div>
      </div>
      <div class="fact-row full">
        <div class="fact-field">
          <label>Email professionnel <span class="req">*</span></label>
          <input type="email" id="f_email" placeholder="marie@monentreprise.fr" autocomplete="email" required>
        </div>
      </div>

      <div class="fact-section">Entreprise</div>
      <div class="fact-row full">
        <div class="fact-field">
          <label>Raison sociale <span class="req">*</span></label>
          <input type="text" id="f_company" placeholder="Mon Entreprise SAS" autocomplete="organization" required>
        </div>
      </div>
      <div class="fact-row full">
        <div class="fact-field">
          <label>Adresse <span class="opt">(optionnel — demandée au paiement)</span></label>
          <input type="text" id="f_address" placeholder="12 rue de la Paix" autocomplete="street-address">
        </div>
      </div>
      <div class="fact-row">
        <div class="fact-field">
          <label>Code postal <span class="opt">(optionnel)</span></label>
          <input type="text" id="f_postal" placeholder="75001" autocomplete="postal-code" maxlength="10">
        </div>
        <div class="fact-field">
          <label>Ville <span class="opt">(optionnel)</span></label>
          <input type="text" id="f_city" placeholder="Paris" autocomplete="address-level2">
        </div>
      </div>

      <div class="fact-section">Informations légales</div>
      <div class="fact-row">
        <div class="fact-field">
          <label>SIRET <span class="opt">(optionnel)</span></label>
          <input type="text" id="f_siret" placeholder="123 456 789 00012" maxlength="20">
        </div>
        <div class="fact-field">
          <label>N° TVA intracom. <span class="opt">(si applicable)</span></label>
          <input type="text" id="f_vat" placeholder="FR12345678901" maxlength="20">
        </div>
      </div>

      <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border)">
        <button class="fact-btn" id="fact-submit" onclick="submitFacturation()">
          Continuer vers le paiement sécurisé →
        </button>
        <p style="font-size: 12px; color: var(--ink-4); text-align: center; margin-top: 12px">
          🔒 Paiement sécurisé Stripe · Vos données ne sont jamais revendues
        </p>
      </div>
    </div>

    <!-- Récap -->
    <div class="order-card">
      <div class="order-title">Votre commande</div>
      <div class="order-product">
        <div class="order-product-name"><?= htmlspecialchars($plan_label) ?></div>
        <div class="order-product-price"><?= htmlspecialchars($plan_price) ?></div>
      </div>
      <div class="order-includes">
        <?php
        $features = [
            'report'    => ['7+ opportunités IA', 'Tutoriels personnalisés', 'Plan 12 mois', 'Milo (assistant IA) inclus 30 jours', 'Accès à vie'],
            'assistant' => ['Questions illimitées', 'Milo, votre copilote IA 24h/24', 'Contexte de votre audit', 'Résiliable à tout moment'],
            'seo'       => ['Audit présence IA', 'Optimisation citations', 'Rapport mensuel', 'Sans engagement'],
            'pack'      => ['Rapport premium inclus', 'Toutes vos missions de lancement', 'Chaque outil installé et actif', 'Milo (IA) pendant 6 mois', 'Suivi mensuel automatique'],
            'mission'   => ['Un outil installé et paramétré', 'Guidage pas à pas par Milo (IA)', 'Jusqu\'au premier résultat concret', 'Satisfait ou remboursé'],
            'lancement' => ['3 outils de votre choix mis en action', 'Guidage pas à pas par Milo (IA)', '90 jours d\'assistance incluse', 'Satisfait ou remboursé'],
        ];
        foreach($features[$plan] as $f): ?>
        <div class="order-line">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-top:2px"><polyline points="20 6 9 17 4 12"/></svg>
          <?= htmlspecialchars($f) ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if ($plan === 'report' || $plan === 'pack'): ?>
      <div style="background: rgba(16,185,129,.06); border: 1px solid rgba(16,185,129,.15); border-radius: var(--r-md); padding: 12px; margin-bottom: 16px; font-size: 12px; color: var(--green-deep); line-height: 1.5">
        <?= $plan === 'report' ? '✅ Garantie satisfait ou remboursé 14 jours — sans justification' : '💡 Éligible aux aides à la transition numérique (OPCO, France Num) — on vous guide' ?>
      </div>
      <?php endif; ?>
      <div class="order-secure">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Paiement sécurisé via Stripe
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
const plan = '<?= htmlspecialchars($plan, ENT_QUOTES) ?>';
const missionTool = '<?= htmlspecialchars($tool, ENT_QUOTES) ?>';

// Pré-remplir email depuis sessionStorage si disponible
window.addEventListener('DOMContentLoaded', () => {
  const email = ABYS.get('lead_email') || '';
  if (email) document.getElementById('f_email').value = email;
});

function val(id) { return document.getElementById(id)?.value?.trim() || ''; }

function showErr(msg) {
  const el = document.getElementById('fact-error');
  el.textContent = msg;
  el.style.display = 'block';
  el.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

async function submitFacturation() {
  document.getElementById('fact-error').style.display = 'none';
  const btn = document.getElementById('fact-submit');

  // Validation
  const required = ['f_first_name','f_last_name','f_email','f_company'];
  for (const id of required) {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      el.classList.add('error');
      el.addEventListener('input', () => el.classList.remove('error'), { once: true });
      showErr('Merci de remplir tous les champs obligatoires (*).');
      el.focus();
      return;
    }
  }

  const email = val('f_email');
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    document.getElementById('f_email').classList.add('error');
    showErr('Adresse email invalide.');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Enregistrement…';

  const billing = {
    first_name:   val('f_first_name'),
    last_name:    val('f_last_name'),
    job_title:    val('f_job_title'),
    phone:        val('f_phone'),
    email:        email,
    company_name: val('f_company'),
    address:      val('f_address'),
    postal_code:  val('f_postal'),
    city:         val('f_city'),
    siret:        val('f_siret'),
    vat_number:   val('f_vat'),
    plan,
    lead_id:      parseInt(ABYS.get('lead_id') || 0),
  };

  try {
    // Sauvegarder les infos de facturation
    const saveRes = await ABYS.api('billing.php', { action: 'save', ...billing });

    // Stocker email pour la création de compte après paiement
    ABYS.store('lead_email', email);

    // Appel Stripe selon le plan
    let stripeRes;
    btn.textContent = 'Redirection vers le paiement…';

    if (plan === 'report') {
      stripeRes = await ABYS.api('stripe.php', {
        action:   'create_checkout_report',
        lead_id:  billing.lead_id,
        audit_id: parseInt(ABYS.get('audit_id') || 0),
        email,
        billing,
      });
    } else if (plan === 'mission' || plan === 'lancement') {
      stripeRes = await ABYS.api('stripe.php', {
        action:  'create_checkout_mission',
        plan,
        tool:    missionTool,
        lead_id: billing.lead_id,
        email,
        billing,
      });
    } else if (plan === 'pack') {
      stripeRes = await ABYS.api('stripe.php', {
        action:  'create_checkout_pack',
        lead_id: billing.lead_id,
        email,
        billing,
      });
    } else {
      stripeRes = await ABYS.api('stripe.php', {
        action:  'create_checkout_subscription',
        plan:    plan === 'seo' ? 'seo' : 'assistant',
        lead_id: billing.lead_id,
        email,
        billing,
      });
    }

    if (stripeRes.url) {
      window.location.href = stripeRes.url;
    } else {
      throw new Error('Erreur Stripe');
    }
  } catch(e) {
    showErr(e.message || 'Une erreur est survenue. Veuillez réessayer.');
    btn.disabled = false;
    btn.textContent = 'Continuer vers le paiement sécurisé →';
  }
}
</script>
