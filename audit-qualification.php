<?php
// Fichier: abys-ai/audit-qualification.php
$plan = isset($_GET['plan']) && $_GET['plan'] === 'premium' ? 'premium' : 'essential';
$plan_label = $plan === 'premium' ? 'Premium' : 'Essentiel';
$plan_price = $plan === 'premium' ? 499 : 249;

$page_title       = 'Qualification · Audit IA ' . $plan_label . ' · ABYS AI';
$page_description = 'Complétez votre profil pour accéder à votre audit IA ' . $plan_label . ' et découvrez les aides financement disponibles pour votre entreprise.';
include __DIR__ . '/includes/head.php';
?>

<style>
/* ── Hide nav links, only show logo ───────────────────────── */
.nav-links,
.nav > a.btn {
  display: none !important;
}
.mif-bar { display: none !important; }

/* ── Page-level layout ────────────────────────────────────── */
.qual-page {
  min-height: 100vh;
  background: #f0fdf4;
  padding: 0 0 80px;
}

/* ── Progress header ──────────────────────────────────────── */
.qual-header {
  background: #fff;
  border-bottom: 1px solid var(--border, #E2E8F0);
  padding: 20px 0;
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 1px 12px rgba(10,31,26,0.06);
}
.qual-header-inner {
  max-width: 860px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  align-items: center;
  gap: 20px;
}
.qual-plan-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #052E16, #065F46);
  color: #fff;
  padding: 8px 18px;
  border-radius: 100px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.02em;
  white-space: nowrap;
  flex-shrink: 0;
}
.qual-plan-badge em {
  font-style: normal;
  color: #6EE7B7;
}
.qual-progress-wrap {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.qual-steps {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  font-weight: 600;
  color: #64748B;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.qual-steps span.active { color: #10B981; }
.qual-progress-bar {
  height: 4px;
  background: #E2E8F0;
  border-radius: 100px;
  overflow: hidden;
}
.qual-progress-fill {
  height: 100%;
  width: 50%;
  background: linear-gradient(90deg, #10B981, #0EA5E9);
  border-radius: 100px;
  transition: width 600ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* ── Container ────────────────────────────────────────────── */
.qual-container {
  max-width: 860px;
  margin: 0 auto;
  padding: 40px 24px 0;
}

/* ── Intro headline ───────────────────────────────────────── */
.qual-intro {
  text-align: center;
  margin-bottom: 36px;
}
.qual-intro h1 {
  font-family: 'Plus Jakarta Sans', 'Rubik', sans-serif;
  font-size: 30px;
  font-weight: 800;
  color: #0A1F1A;
  letter-spacing: -0.04em;
  line-height: 1.2;
  margin-bottom: 10px;
}
.qual-intro h1 span {
  background: linear-gradient(135deg, #10B981, #0EA5E9);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.qual-intro p {
  font-size: 15px;
  color: #475569;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

/* ── Form card ────────────────────────────────────────────── */
.qual-form-card {
  background: #fff;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  box-shadow: 0 4px 24px rgba(10,31,26,0.07), 0 1px 4px rgba(10,31,26,0.04);
  padding: 40px;
  margin-bottom: 28px;
}

.form-section-title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #10B981;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.form-section-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, #E2E8F0, transparent);
}

.form-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
.form-grid-1 {
  margin-bottom: 16px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.field-group label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  letter-spacing: 0.01em;
}
.field-group label .req {
  color: #10B981;
  margin-left: 2px;
}
.field-group label .opt {
  font-weight: 400;
  color: #9CA3AF;
  font-size: 12px;
  margin-left: 4px;
}

.qual-input,
.qual-select,
.qual-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1.5px solid #E2E8F0;
  border-radius: 10px;
  font-family: 'Rubik', sans-serif;
  font-size: 14px;
  color: #0A1F1A;
  background: #FAFAFA;
  transition: border-color 200ms, box-shadow 200ms, background 200ms;
  outline: none;
  box-sizing: border-box;
  -webkit-appearance: none;
  appearance: none;
}
.qual-select {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6' stroke='%2310B981' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 40px;
}
.qual-input:focus,
.qual-select:focus,
.qual-textarea:focus {
  border-color: #10B981;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
  background: #fff;
}
.qual-input::placeholder,
.qual-textarea::placeholder {
  color: #B0BEC5;
}
.qual-textarea {
  resize: vertical;
  line-height: 1.6;
}

/* ── Pill buttons ─────────────────────────────────────────── */
.pill-group {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
}
.pill-btn {
  display: inline-flex;
  align-items: center;
  padding: 9px 16px;
  border: 1.5px solid #E2E8F0;
  border-radius: 100px;
  font-family: 'Rubik', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: #475569;
  background: #FAFAFA;
  cursor: pointer;
  transition: all 180ms ease;
  user-select: none;
  line-height: 1;
}
.pill-btn:hover {
  border-color: #10B981;
  color: #10B981;
  background: rgba(16,185,129,0.05);
}
.pill-btn.selected {
  border-color: #10B981;
  background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(14,165,233,0.08));
  color: #065F46;
  font-weight: 600;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
}

/* ── Aid simulator ────────────────────────────────────────── */
.aid-simulator {
  background: #052E16;
  border-radius: 20px;
  padding: 36px 40px;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
}
.aid-simulator::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 40% at 80% 20%, rgba(16,185,129,0.12) 0%, transparent 70%),
    radial-gradient(ellipse 40% 60% at 10% 80%, rgba(14,165,233,0.08) 0%, transparent 70%);
  pointer-events: none;
}
.aid-sim-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
  flex-wrap: wrap;
  gap: 12px;
  position: relative;
}
.aid-sim-eyebrow {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #6EE7B7;
}
.aid-sim-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  background: rgba(16,185,129,0.15);
  border: 1px solid rgba(16,185,129,0.25);
  border-radius: 100px;
  font-size: 11px;
  font-weight: 600;
  color: #6EE7B7;
}
.aid-sim-badge::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #10B981;
  animation: pulse-dot 2s ease infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.7); }
}
.aid-sim-subtitle {
  font-size: 14px;
  color: rgba(255,255,255,0.55);
  margin-bottom: 24px;
  position: relative;
}
.aid-sim-title {
  font-family: 'Plus Jakarta Sans', 'Rubik', sans-serif;
  font-size: 22px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.03em;
  line-height: 1.25;
  margin-bottom: 6px;
  position: relative;
}

.aid-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 28px;
  position: relative;
}
.aid-item {
  display: grid;
  grid-template-columns: 24px 1fr auto;
  gap: 12px;
  align-items: start;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  padding: 14px 16px;
  transition: background 250ms;
}
.aid-item:hover {
  background: rgba(255,255,255,0.08);
}
.aid-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 800;
  flex-shrink: 0;
  margin-top: 1px;
}
.aid-icon.eligible {
  background: rgba(16,185,129,0.2);
  color: #34D399;
}
.aid-icon.probable {
  background: rgba(251,191,36,0.18);
  color: #FCD34D;
}
.aid-icon.variable {
  background: rgba(148,163,184,0.18);
  color: #94A3B8;
}
.aid-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.aid-name {
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
}
.aid-desc {
  font-size: 12px;
  color: rgba(255,255,255,0.5);
  line-height: 1.4;
}
.aid-conditions {
  font-size: 11px;
  color: rgba(255,255,255,0.3);
  margin-top: 2px;
}
.aid-montant {
  font-size: 14px;
  font-weight: 800;
  color: #34D399;
  white-space: nowrap;
  text-align: right;
  flex-shrink: 0;
}

.aid-result-bar {
  border-top: 1px solid rgba(255,255,255,0.1);
  padding-top: 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
  position: relative;
}
.aid-result-label {
  font-size: 13px;
  color: rgba(255,255,255,0.5);
  line-height: 1.5;
}
.aid-result-label strong {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(255,255,255,0.35);
  margin-bottom: 2px;
}
.aid-net-price {
  text-align: right;
}
.aid-net-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: rgba(255,255,255,0.4);
  margin-bottom: 4px;
}
.aid-net-amount {
  font-family: 'Plus Jakarta Sans', 'Rubik', sans-serif;
  font-size: 34px;
  font-weight: 900;
  letter-spacing: -0.04em;
  line-height: 1;
  background: linear-gradient(135deg, #34D399, #67E8F9);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.aid-net-note {
  font-size: 11px;
  color: rgba(255,255,255,0.3);
  margin-top: 3px;
}

.aid-empty {
  text-align: center;
  padding: 24px;
  color: rgba(255,255,255,0.3);
  font-size: 14px;
  font-style: italic;
}

/* ── Submit button ────────────────────────────────────────── */
.qual-submit-wrap {
  margin-top: 8px;
}
.btn-submit-qual {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  padding: 18px 32px;
  border: none;
  border-radius: 14px;
  font-family: 'Plus Jakarta Sans', 'Rubik', sans-serif;
  font-size: 17px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #fff;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #10B981 0%, #0EA5E9 50%, #10B981 100%);
  background-size: 200% 200%;
  animation: shimmer-bg 4s ease infinite;
  box-shadow: 0 8px 32px rgba(16,185,129,0.35), 0 2px 8px rgba(14,165,233,0.2);
  transition: transform 150ms, box-shadow 150ms;
}
.btn-submit-qual:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 40px rgba(16,185,129,0.45), 0 4px 12px rgba(14,165,233,0.3);
}
.btn-submit-qual:active {
  transform: translateY(0);
}
.btn-submit-qual::after {
  content: '';
  position: absolute;
  top: 0; left: -100%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
  animation: btn-shine 3s ease infinite;
}
@keyframes shimmer-bg {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}
@keyframes btn-shine {
  0% { left: -100%; }
  60%, 100% { left: 150%; }
}

.qual-security-note {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin-top: 16px;
  font-size: 12px;
  color: #94A3B8;
  flex-wrap: wrap;
}
.qual-security-note span {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* ── Validation error ─────────────────────────────────────── */
.field-error {
  font-size: 12px;
  color: #EF4444;
  margin-top: 4px;
  display: none;
}
.field-group.has-error .qual-input,
.field-group.has-error .qual-select,
.field-group.has-error .qual-textarea {
  border-color: #EF4444;
  box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
}
.field-group.has-error .field-error {
  display: block;
}

/* ── Mobile responsiveness ────────────────────────────────── */
@media (max-width: 640px) {
  .qual-form-card,
  .aid-simulator {
    padding: 24px 20px;
  }
  .form-grid-2 {
    grid-template-columns: 1fr;
  }
  .qual-intro h1 {
    font-size: 24px;
  }
  .aid-sim-title {
    font-size: 18px;
  }
  .aid-net-amount {
    font-size: 28px;
  }
  .aid-item {
    grid-template-columns: 24px 1fr;
  }
  .aid-montant {
    grid-column: 2;
    text-align: left;
  }
  .qual-header-inner {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .qual-steps {
    font-size: 10px;
  }
  .btn-submit-qual {
    font-size: 15px;
    padding: 16px 20px;
  }
}
</style>

<?php include __DIR__ . '/includes/nav.php'; ?>

<div class="qual-page">

  <!-- Progress header -->
  <div class="qual-header">
    <div class="qual-header-inner">
      <div class="qual-plan-badge">
        Audit <em><?= htmlspecialchars($plan_label) ?></em> · <?= $plan_price ?>€
      </div>
      <div class="qual-progress-wrap">
        <div class="qual-steps">
          <span class="active">Étape 1/2 · Votre profil</span>
          <span>Étape 2/2 · Paiement sécurisé</span>
        </div>
        <div class="qual-progress-bar">
          <div class="qual-progress-fill" id="progressFill"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="qual-container">

    <!-- Intro -->
    <div class="qual-intro">
      <h1>Parlez-nous de votre <span>entreprise</span></h1>
      <p>Ces informations nous permettent de personnaliser votre audit IA et d'identifier les aides financières auxquelles vous êtes éligible.</p>
    </div>

    <!-- Form -->
    <form id="qualForm" novalidate>
      <div class="qual-form-card">

        <!-- Coordonnées -->
        <div class="form-section-title">Vos coordonnées</div>

        <div class="form-grid-2">
          <div class="field-group" id="fg-prenom">
            <label for="prenom">Prénom <span class="req">*</span></label>
            <input class="qual-input" type="text" id="prenom" name="prenom" placeholder="Marie" autocomplete="given-name" required>
            <div class="field-error">Veuillez saisir votre prénom.</div>
          </div>
          <div class="field-group" id="fg-nom">
            <label for="nom">Nom <span class="req">*</span></label>
            <input class="qual-input" type="text" id="nom" name="nom" placeholder="Dupont" autocomplete="family-name" required>
            <div class="field-error">Veuillez saisir votre nom.</div>
          </div>
        </div>

        <div class="form-grid-2">
          <div class="field-group" id="fg-email">
            <label for="email">Email professionnel <span class="req">*</span></label>
            <input class="qual-input" type="email" id="email" name="email" placeholder="marie@entreprise.fr" autocomplete="email" required>
            <div class="field-error">Veuillez saisir un email valide.</div>
          </div>
          <div class="field-group" id="fg-telephone">
            <label for="telephone">Téléphone <span class="req">*</span></label>
            <input class="qual-input" type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" autocomplete="tel" required>
            <div class="field-error">Veuillez saisir votre numéro de téléphone.</div>
          </div>
        </div>

        <div class="form-grid-1 field-group" id="fg-poste">
          <label for="poste">Poste / Fonction <span class="req">*</span></label>
          <input class="qual-input" type="text" id="poste" name="poste" placeholder="Gérant, Directeur commercial, Responsable opérations…" required>
          <div class="field-error">Veuillez indiquer votre fonction.</div>
        </div>

        <!-- Entreprise -->
        <div class="form-section-title" style="margin-top:28px">Votre entreprise</div>

        <div class="form-grid-2">
          <div class="field-group" id="fg-entreprise">
            <label for="entreprise">Nom de l'entreprise <span class="req">*</span></label>
            <input class="qual-input" type="text" id="entreprise" name="entreprise" placeholder="Ma Belle Boutique SAS" autocomplete="organization" required>
            <div class="field-error">Veuillez saisir le nom de votre entreprise.</div>
          </div>
          <div class="field-group">
            <label for="siret">SIRET <span class="opt">(optionnel)</span></label>
            <input class="qual-input" type="text" id="siret" name="siret" placeholder="362 521 879 00034" maxlength="18" autocomplete="off">
          </div>
        </div>

        <div class="form-grid-1 field-group" id="fg-secteur">
          <label for="secteur">Secteur d'activité <span class="req">*</span></label>
          <select class="qual-select" id="secteur" name="secteur" required>
            <option value="">-- Choisissez votre secteur --</option>
            <option value="Commerce">Commerce</option>
            <option value="Restauration">Restauration</option>
            <option value="Artisan/BTP">Artisan / BTP</option>
            <option value="Santé">Santé</option>
            <option value="Services/Conseil">Services / Conseil</option>
            <option value="Immobilier">Immobilier</option>
            <option value="Transport">Transport</option>
            <option value="Beauté/Bien-être">Beauté / Bien-être</option>
            <option value="Agriculture">Agriculture</option>
            <option value="Sport/Loisirs">Sport / Loisirs</option>
            <option value="Comptabilité/Finance">Comptabilité / Finance</option>
            <option value="Juridique">Juridique</option>
            <option value="Communication/Marketing">Communication / Marketing</option>
            <option value="Autre">Autre</option>
          </select>
          <div class="field-error">Veuillez choisir votre secteur d'activité.</div>
        </div>

        <!-- Taille & CA -->
        <div class="form-section-title" style="margin-top:28px">Taille & activité</div>

        <div class="form-grid-1 field-group" id="fg-salaries">
          <label>Nombre de salariés <span class="req">*</span></label>
          <div class="pill-group" id="pillarSalaries">
            <button type="button" class="pill-btn" data-value="0">Solo</button>
            <button type="button" class="pill-btn" data-value="1-5">2–5</button>
            <button type="button" class="pill-btn" data-value="6-20">6–20</button>
            <button type="button" class="pill-btn" data-value="21-50">21–50</button>
            <button type="button" class="pill-btn" data-value="51-250">51–250</button>
            <button type="button" class="pill-btn" data-value=">250">&gt; 250</button>
          </div>
          <input type="hidden" id="salaries" name="salaries">
          <div class="field-error" id="err-salaries">Veuillez sélectionner une tranche de salariés.</div>
        </div>

        <div class="form-grid-1 field-group" style="margin-top:16px" id="fg-ca">
          <label>Chiffre d'affaires annuel <span class="req">*</span></label>
          <div class="pill-group" id="pillarCA">
            <button type="button" class="pill-btn" data-value="<100k">&lt; 100K€</button>
            <button type="button" class="pill-btn" data-value="100-500k">100K–500K€</button>
            <button type="button" class="pill-btn" data-value="500k-2m">500K–2M€</button>
            <button type="button" class="pill-btn" data-value="2m-10m">2M–10M€</button>
            <button type="button" class="pill-btn" data-value=">10m">&gt; 10M€</button>
          </div>
          <input type="hidden" id="ca" name="ca">
          <div class="field-error" id="err-ca">Veuillez sélectionner une tranche de chiffre d'affaires.</div>
        </div>

        <!-- Défis & outils -->
        <div class="form-section-title" style="margin-top:28px">Vos défis & outils</div>

        <div class="form-grid-1 field-group" id="fg-defis">
          <label for="defis">Quels sont vos principaux défis ? <span class="req">*</span></label>
          <textarea class="qual-textarea" id="defis" name="defis" rows="4" placeholder="Ex: manque de temps, difficultés à fidéliser les clients, gestion administrative lourde, suivi des devis, communication digitale…" required></textarea>
          <div class="field-error">Décrivez brièvement vos défis (au moins 10 caractères).</div>
        </div>

        <div class="form-grid-1 field-group" style="margin-top:16px">
          <label for="outils">Quels outils numériques utilisez-vous déjà ? <span class="opt">(optionnel)</span></label>
          <textarea class="qual-textarea" id="outils" name="outils" rows="3" placeholder="Ex: Excel, Google Workspace, Sage, Mailchimp, Shopify…"></textarea>
        </div>

      </div><!-- /.qual-form-card -->

      <!-- Aid simulator -->
      <div class="aid-simulator">
        <div class="aid-sim-header">
          <div class="aid-sim-eyebrow">Simulateur d'aides financières</div>
          <div class="aid-sim-badge">Mise à jour en temps réel</div>
        </div>
        <div class="aid-sim-title">Vos aides potentielles</div>
        <div class="aid-sim-subtitle">Basé sur votre profil, vous êtes potentiellement éligible à&nbsp;:</div>

        <div class="aid-list" id="aidList">
          <div class="aid-empty" id="aidEmpty">
            Sélectionnez votre nombre de salariés et votre CA pour voir vos aides estimées.
          </div>
        </div>

        <div class="aid-result-bar">
          <div class="aid-result-label">
            <strong>Économies potentielles totales</strong>
            <span id="aidTotalSavings">-</span>
          </div>
          <div class="aid-net-price">
            <div class="aid-net-label">Votre reste à charge estimé</div>
            <div class="aid-net-amount" id="aidNetAmount">-</div>
            <div class="aid-net-note" id="aidNetNote">Renseignez votre profil pour calculer</div>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div class="qual-submit-wrap">
        <button type="submit" class="btn-submit-qual" id="submitBtn">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          Procéder au paiement sécurisé →
        </button>
        <div class="qual-security-note">
          <span>
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2L4 7v6c0 4.4 3.4 8.5 8 9.5 4.6-1 8-5.1 8-9.5V7l-8-5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Paiement sécurisé Stripe
          </span>
          <span>
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg>
            Données chiffrées SSL
          </span>
          <span>
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M3 10h18M7 15h.01M11 15h2M15 15h.01M21 6H3a1 1 0 00-1 1v12a1 1 0 001 1h18a1 1 0 001-1V7a1 1 0 00-1-1z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            CB, virement, Paypal acceptés
          </span>
        </div>
      </div>

    </form>

  </div><!-- /.qual-container -->
</div><!-- /.qual-page -->

<script>
(function() {
  'use strict';

  /* ─── Plan data from PHP ─────────────────────────── */
  var PLAN      = <?= json_encode($plan) ?>;
  var PLAN_PRICE = <?= $plan_price ?>;

  /* ─── Pill button groups ─────────────────────────── */
  function initPillGroup(containerId, hiddenId) {
    var container = document.getElementById(containerId);
    var hidden    = document.getElementById(hiddenId);
    if (!container || !hidden) return;

    container.querySelectorAll('.pill-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        container.querySelectorAll('.pill-btn').forEach(function(b) {
          b.classList.remove('selected');
        });
        btn.classList.add('selected');
        hidden.value = btn.dataset.value;
        updateSimulator();
        updateProgress();
        // Clear error state
        var fg = document.getElementById(hiddenId === 'salaries' ? 'fg-salaries' : 'fg-ca');
        if (fg) {
          fg.classList.remove('has-error');
          var errEl = document.getElementById(hiddenId === 'salaries' ? 'err-salaries' : 'err-ca');
          if (errEl) errEl.style.display = 'none';
        }
      });
    });
  }

  initPillGroup('pillarSalaries', 'salaries');
  initPillGroup('pillarCA', 'ca');

  /* ─── Parse helpers ──────────────────────────────── */
  function parseEmployees(code) {
    if (!code) return -1;
    switch (code) {
      case '0':     return 0;
      case '1-5':   return 3;
      case '6-20':  return 13;
      case '21-50': return 35;
      case '51-250':return 150;
      case '>250':  return 300;
      default:      return 0;
    }
  }

  function parseCA(code) {
    // returns numeric value in K€
    if (!code) return -1;
    switch (code) {
      case '<100k':   return 50;
      case '100-500k':return 300;
      case '500k-2m': return 1250;
      case '2m-10m':  return 6000;
      case '>10m':    return 15000;
      default:        return 0;
    }
  }

  /* ─── Aid calculator ─────────────────────────────── */
  function calcAides(plan, employees_code, ca_code) {
    var price = plan === 'premium' ? 499 : 249;
    var emp   = parseEmployees(employees_code);
    var ca    = parseCA(ca_code);

    if (emp < 0 || ca < 0) return null; // not enough info yet

    var isPME        = emp < 250 && ca < 50000; // < 50M€
    var hasEmployees = emp >= 1;
    var aides        = [];

    // 1. Chèque France Num (TPE/PME < 250 salariés)
    if (isPME) {
      aides.push({
        name:       'Chèque France Num',
        montant:    Math.min(500, price),
        desc:       'Subvention État pour la transformation numérique des TPE/PME',
        conditions: '< 250 salariés · programme gouvernemental actif',
        certainty:  'eligible'
      });
    }

    // 2. OPCO / Financement formation (si salariés)
    if (hasEmployees) {
      aides.push({
        name:       'Financement OPCO',
        montant:    Math.min(price, 499),
        desc:       'Prise en charge de la formation IA via votre OPCO de branche',
        conditions: 'Salariés cotisant · dossier formation à constituer',
        certainty:  'probable'
      });
    }

    // 3. Crédit d'Impôt Innovation (< 250 salariés, < 50M€ CA)
    if (isPME) {
      aides.push({
        name:       "Crédit d'Impôt Innovation",
        montant:    Math.round(price * 0.30),
        desc:       'Remboursement sur votre déclaration fiscale annuelle',
        conditions: "< 250 salariés · dépenses d'innovation éligibles",
        certainty:  'eligible'
      });
    }

    // 4. BPI France IA Booster (France 2030, CA > 500K€)
    if (ca >= 500) {
      aides.push({
        name:       'BPI France · Programme IA Booster',
        montant:    Math.round(price * 0.5),
        desc:       "Subvention France 2030 pour les projets d'IA en entreprise",
        conditions: 'CA > 500 K€ · dossier BPI accompagné par ABYS',
        certainty:  ca >= 2000 ? 'eligible' : 'probable'
      });
    }

    // 5. Aide régionale numérique (always)
    aides.push({
      name:       'Aide régionale numérique',
      montant:    Math.round(price * 0.2),
      desc:       'Programme de votre région pour la transition numérique (variable selon région)',
      conditions: 'Variable selon région · ABYS vous aide à identifier la bonne aide',
      certainty:  'variable'
    });

    // Net price: best non-cumulative + CII
    var maxSingleAide = 0;
    var ciiAide       = 0;
    aides.forEach(function(a) {
      if (a.name.includes('Impôt')) {
        ciiAide = a.montant;
      } else if (a.montant > maxSingleAide) {
        maxSingleAide = a.montant;
      }
    });
    var netPrice = Math.max(0, price - maxSingleAide - ciiAide);

    return { aides: aides, netPrice: netPrice, price: price };
  }

  /* ─── Render simulator ───────────────────────────── */
  var CERTAINTY_META = {
    eligible: { symbol: '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9.5 17 4 11.5"/></svg>', cssClass: 'eligible', label: 'Éligible'  },
    probable: { symbol: '~', cssClass: 'probable', label: 'Probable'  },
    variable: { symbol: '?', cssClass: 'variable', label: 'Variable'  }
  };

  function euros(n) {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency', currency: 'EUR', maximumFractionDigits: 0
    }).format(n);
  }

  function updateSimulator() {
    var empCode = document.getElementById('salaries').value;
    var caCode  = document.getElementById('ca').value;
    var result  = calcAides(PLAN, empCode, caCode);

    var listEl     = document.getElementById('aidList');
    var emptyEl    = document.getElementById('aidEmpty');
    var totalEl    = document.getElementById('aidTotalSavings');
    var netAmtEl   = document.getElementById('aidNetAmount');
    var netNoteEl  = document.getElementById('aidNetNote');

    if (!result) {
      listEl.innerHTML = '';
      listEl.appendChild(emptyEl);
      emptyEl.style.display = '';
      totalEl.textContent  = '-';
      netAmtEl.textContent = '-';
      netNoteEl.textContent = 'Renseignez votre profil pour calculer';
      return;
    }

    emptyEl.style.display = 'none';

    // Render aids
    var html = '';
    var totalSavings = 0;
    result.aides.forEach(function(a) {
      var meta = CERTAINTY_META[a.certainty] || CERTAINTY_META.variable;
      totalSavings += a.montant;
      html += '<div class="aid-item">'
        + '<div class="aid-icon ' + meta.cssClass + '">' + meta.symbol + '</div>'
        + '<div class="aid-info">'
        +   '<div class="aid-name">' + escapeHtml(a.name) + '</div>'
        +   '<div class="aid-desc">' + escapeHtml(a.desc) + '</div>'
        +   '<div class="aid-conditions">' + escapeHtml(a.conditions) + '</div>'
        + '</div>'
        + '<div class="aid-montant">jusqu\'à ' + euros(a.montant) + '</div>'
        + '</div>';
    });

    listEl.innerHTML = html;
    listEl.appendChild(emptyEl);

    totalEl.textContent  = 'Jusqu\'à ' + euros(totalSavings) + ' d\'aides mobilisables';
    if (result.netPrice === 0) {
      netAmtEl.textContent  = '0 €';
      netNoteEl.textContent = 'Prise en charge totale possible';
    } else {
      netAmtEl.textContent  = '≈ ' + euros(result.netPrice);
      netNoteEl.textContent = 'Après application des principales aides';
    }
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  /* ─── Progress bar ───────────────────────────────── */
  function updateProgress() {
    var fields = ['prenom','nom','email','telephone','poste','entreprise','secteur'];
    var filled = fields.filter(function(id) {
      var el = document.getElementById(id);
      return el && el.value.trim().length > 0;
    }).length;
    var salariesOk = document.getElementById('salaries').value ? 1 : 0;
    var caOk       = document.getElementById('ca').value ? 1 : 0;
    var defisOk    = document.getElementById('defis').value.trim().length >= 10 ? 1 : 0;
    var total      = fields.length + 3; // fields + salaries + ca + defis
    var done       = filled + salariesOk + caOk + defisOk;
    var pct        = Math.round((done / total) * 50); // 0-50%
    document.getElementById('progressFill').style.width = pct + '%';
  }

  /* ─── Live progress on text inputs ──────────────── */
  ['prenom','nom','email','telephone','poste','entreprise','secteur','defis'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', updateProgress);
  });

  /* ─── Form validation & submit ───────────────────── */
  document.getElementById('qualForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var valid = true;

    // Text/select fields
    var requiredFields = [
      { id: 'prenom',     fg: 'fg-prenom',    check: function(v) { return v.length >= 1; } },
      { id: 'nom',        fg: 'fg-nom',       check: function(v) { return v.length >= 1; } },
      { id: 'email',      fg: 'fg-email',     check: function(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); } },
      { id: 'telephone',  fg: 'fg-telephone', check: function(v) { return v.replace(/\D/g,'').length >= 9; } },
      { id: 'poste',      fg: 'fg-poste',     check: function(v) { return v.length >= 2; } },
      { id: 'entreprise', fg: 'fg-entreprise',check: function(v) { return v.length >= 1; } },
      { id: 'secteur',    fg: 'fg-secteur',   check: function(v) { return v !== ''; } },
      { id: 'defis',      fg: 'fg-defis',     check: function(v) { return v.trim().length >= 10; } }
    ];

    requiredFields.forEach(function(f) {
      var el = document.getElementById(f.id);
      var fg = document.getElementById(f.fg);
      if (!el || !fg) return;
      if (!f.check(el.value.trim())) {
        fg.classList.add('has-error');
        valid = false;
      } else {
        fg.classList.remove('has-error');
      }
    });

    // Pill fields
    if (!document.getElementById('salaries').value) {
      document.getElementById('fg-salaries').classList.add('has-error');
      document.getElementById('err-salaries').style.display = 'block';
      valid = false;
    } else {
      document.getElementById('fg-salaries').classList.remove('has-error');
    }

    if (!document.getElementById('ca').value) {
      document.getElementById('fg-ca').classList.add('has-error');
      document.getElementById('err-ca').style.display = 'block';
      valid = false;
    } else {
      document.getElementById('fg-ca').classList.remove('has-error');
    }

    if (!valid) {
      // Scroll to first error
      var firstErr = document.querySelector('.has-error');
      if (firstErr) {
        firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }

    // Save to sessionStorage
    var data = {
      prenom:     document.getElementById('prenom').value.trim(),
      nom:        document.getElementById('nom').value.trim(),
      email:      document.getElementById('email').value.trim(),
      telephone:  document.getElementById('telephone').value.trim(),
      poste:      document.getElementById('poste').value.trim(),
      entreprise: document.getElementById('entreprise').value.trim(),
      siret:      document.getElementById('siret').value.trim(),
      secteur:    document.getElementById('secteur').value,
      salaries:   document.getElementById('salaries').value,
      ca:         document.getElementById('ca').value,
      defis:      document.getElementById('defis').value.trim(),
      outils:     document.getElementById('outils').value.trim(),
      plan:       PLAN,
      price:      PLAN_PRICE
    };

    try {
      sessionStorage.setItem('abys_qualification', JSON.stringify(data));
    } catch(err) {
      // sessionStorage may be unavailable; continue anyway
    }

    // Update progress to 100% then redirect
    document.getElementById('progressFill').style.width = '100%';
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Redirection…';

    setTimeout(function() {
      window.location.href = '/checkout.php?plan=' + encodeURIComponent(PLAN) + '&qualified=1';
    }, 350);
  });

  // Initial render
  updateProgress();

})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
