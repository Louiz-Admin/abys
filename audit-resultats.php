<?php
$page_title       = 'Vos résultats · ABYS AI';
$page_description = 'Découvrez votre score IA et vos opportunités d\'automatisation personnalisées.';
$extra_js         = ['/assets/js/audit.js'];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<style>
/* ── Page résultats ─────────────────────────────────────── */
.results-page {
  max-width: 1120px;
  margin: 0 auto;
  padding: 48px 40px 80px;
}

/* ── Header hero ────────────────────────────────────────── */
.results-header {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 48px;
  align-items: center;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r-xl);
  padding: 40px 48px;
  box-shadow: var(--shadow-md);
  margin-bottom: 48px;
}

.gauge-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex-shrink: 0;
}

.header-right {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.sector-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 18px;
  background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(14,165,233,0.08));
  border: 1px solid rgba(16,185,129,0.3);
  border-radius: var(--r-pill);
  font-size: 13px;
  font-weight: 600;
  color: var(--green-deep);
  letter-spacing: 0.01em;
  width: fit-content;
}

.results-h1 {
  font-size: 44px;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.04em;
  line-height: 1.05;
}

.results-summary {
  font-size: 17px;
  color: var(--ink-3);
  line-height: 1.65;
  max-width: 560px;
}

.results-stats {
  display: flex;
  gap: 40px;
  padding-top: 24px;
  border-top: 1px solid var(--border-green);
  margin-top: 4px;
}

.result-stat {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.result-stat-value {
  font-size: 64px;
  font-weight: 700;
  color: var(--green);
  letter-spacing: -0.04em;
  line-height: 1;
}

.result-stat-label {
  font-size: 13px;
  color: var(--ink-4);
  font-weight: 400;
  letter-spacing: 0.01em;
}

/* ── Section opportunités ───────────────────────────────── */
.opps-section {
  margin-bottom: 40px;
}

.opps-header {
  display: flex;
  align-items: baseline;
  gap: 16px;
  margin-bottom: 28px;
}

.opps-title {
  font-size: 30px;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.03em;
}

.opps-subtitle {
  display: inline-flex;
  padding: 5px 14px;
  background: rgba(16,185,129,0.08);
  border: 1px solid rgba(16,185,129,0.2);
  border-radius: var(--r-pill);
  font-size: 14px;
  font-weight: 500;
  color: var(--ink-4);
}

.opps-grid {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* ── Opportunité card ───────────────────────────────────── */
.opp-card {
  display: grid;
  grid-template-columns: 72px 1fr auto;
  gap: 24px;
  align-items: center;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 28px 32px;
  box-shadow: var(--shadow-sm);
  transition: transform 200ms var(--ease), box-shadow 200ms var(--ease), border-color 200ms var(--ease);
}

.opp-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
  border-color: rgba(16,185,129,0.3);
}

.opp-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.opp-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
}

.opp-top {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.opp-tool-name {
  font-size: 20px;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.02em;
}

.opp-category {
  display: inline-flex;
  padding: 4px 12px;
  border-radius: var(--r-pill);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.03em;
}

.opp-desc {
  font-size: 15px;
  color: var(--ink-3);
  line-height: 1.6;
}

.opp-gains {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
  flex-shrink: 0;
  white-space: nowrap;
}

.opp-euros {
  font-size: 24px;
  font-weight: 700;
  color: var(--green);
  letter-spacing: -0.02em;
}

.opp-hours {
  font-size: 13px;
  color: var(--ink-4);
  font-weight: 500;
}

.opp-cost {
  display: inline-block;
  font-size: 11px;
  color: var(--ink-4);
  font-weight: 500;
  padding: 2px 8px;
  background: rgba(0,0,0,0.04);
  border-radius: 6px;
  white-space: nowrap;
  margin-top: 6px;
}

.opp-intro {
  margin-bottom: 32px;
  padding: 28px 32px;
  background: linear-gradient(135deg, rgba(10,31,26,0.96) 0%, rgba(9,28,43,0.96) 100%);
  border: 1px solid rgba(16,185,129,0.2);
  border-radius: 20px;
  backdrop-filter: blur(8px);
}
@media (max-width: 768px) {
  .opp-intro { padding: 20px 18px; }
}

.tool-logo {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  object-fit: contain;
  background: #f8f9fa;
  padding: 10px;
  border: 1px solid var(--border);
}


.opp-cost {
  font-size: 11px;
  color: var(--ink-4);
  font-weight: 500;
  padding: 3px 8px;
  background: rgba(0,0,0,0.04);
  border-radius: 6px;
  white-space: nowrap;
  margin-top: 2px;
}

/* .opp-intro styles moved to first occurrence above */

.tool-logo {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  object-fit: contain;
  background: #f8f9fa;
  padding: 10px;
  border: 1px solid var(--border);
}

/* ── Email capture card ─────────────────────────────────── */
.email-capture-card {
  margin-top: 28px;
  background: #fff;
  border: 1.5px solid rgba(16,185,129,0.25);
  border-radius: 20px;
  padding: 28px 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-shadow: 0 2px 16px rgba(16,185,129,0.06);
}
.ec-left {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}
.ec-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(16,185,129,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ec-title {
  font-size: 17px;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.02em;
  margin-bottom: 4px;
}
.ec-sub {
  font-size: 14px;
  color: var(--ink-3);
  line-height: 1.5;
}
.ec-form { display: flex; flex-direction: column; gap: 12px; }
.ec-fields {
  display: grid;
  grid-template-columns: 1fr 1fr 1.4fr;
  gap: 10px;
}
.ec-input {
  padding: 12px 16px;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  font-size: 14px;
  font-family: var(--font);
  color: var(--ink);
  background: #FAFAFA;
  transition: border-color 150ms, box-shadow 150ms;
  outline: none;
}
.ec-input:focus {
  border-color: #10B981;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
  background: #fff;
}
.ec-input.ec-error { border-color: #EF4444; }
.ec-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 28px;
  background: linear-gradient(90deg, #059669 0%, #0EA5E9 50%, #059669 100%);
  background-size: 200% 100%;
  animation: btn-shine 3s linear infinite;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  font-family: var(--font);
  cursor: pointer;
  align-self: flex-start;
  transition: transform 150ms, box-shadow 150ms;
  box-shadow: 0 4px 14px rgba(16,185,129,0.35);
}
.ec-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,0.45); }
.ec-btn:disabled { opacity: 0.65; cursor: wait; transform: none; }
.ec-success {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  font-weight: 600;
  color: #065F46;
  background: rgba(16,185,129,0.08);
  border: 1px solid rgba(16,185,129,0.25);
  border-radius: 10px;
  padding: 14px 18px;
}
.ec-rgpd {
  font-size: 12px;
  color: var(--ink-4);
  margin: 0;
}
@media (max-width: 768px) {
  .email-capture-card { padding: 20px 18px; }
  .ec-fields { grid-template-columns: 1fr; }
  .ec-btn { width: 100%; justify-content: center; }
}

/* ── Bouton simulation (inline avec opps-header) ────────── */
.btn-simulate {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  background: var(--white);
  border: 2px solid var(--green);
  border-radius: var(--r-pill);
  font-size: 14px;
  font-weight: 600;
  color: var(--green-deep);
  letter-spacing: -0.01em;
  transition: all 200ms var(--ease);
  box-shadow: var(--shadow-sm);
  white-space: nowrap;
  flex-shrink: 0;
}
.btn-simulate:hover {
  background: var(--green);
  color: #fff;
  box-shadow: var(--shadow-glow);
  transform: translateY(-1px);
}
.btn-simulate:hover svg { transform: translateX(3px); }
.btn-simulate svg { transition: transform 200ms var(--ease); }

/* ── Offres premium · section double ───────────────────── */
.offers-section {
  position: relative;
  background: linear-gradient(160deg, #060F12 0%, #071A14 40%, #091C2B 100%);
  border-radius: 28px;
  padding: 64px 52px 72px;
  overflow: hidden;
}
.offers-section::before {
  content: '';
  position: absolute;
  top: -120px; right: -120px;
  width: 480px; height: 480px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 65%);
  pointer-events: none;
}
.offers-section::after {
  content: '';
  position: absolute;
  bottom: -80px; left: -80px;
  width: 320px; height: 320px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(14,165,233,0.10) 0%, transparent 65%);
  pointer-events: none;
}
.offers-header {
  text-align: center;
  margin-bottom: 48px;
  position: relative; z-index: 1;
}
.offers-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px;
  background: rgba(16,185,129,0.15);
  border: 1px solid rgba(16,185,129,0.35);
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
  color: #34D399;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  margin-bottom: 20px;
}
.offers-title {
  font-size: 38px;
  font-weight: 300;
  color: #fff;
  letter-spacing: -0.04em;
  line-height: 1.1;
  margin: 0 0 12px;
}
.offers-title strong { font-weight: 800; color: #34D399; }
.offers-sub {
  font-size: 16px;
  color: rgba(255,255,255,0.45);
  margin: 0;
}

/* Grid deux colonnes */
.offers-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  position: relative; z-index: 1;
}

/* Carte de base */
.offer-card {
  border-radius: 22px;
  padding: 36px 32px 40px;
  display: flex;
  flex-direction: column;
  gap: 0;
  position: relative;
  transition: transform 220ms ease, box-shadow 220ms ease;
}
.offer-card:hover { transform: translateY(-4px); }

/* Carte Essentiel (249€) */
.offer-essential {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
}
.offer-essential:hover {
  box-shadow: 0 20px 60px rgba(0,0,0,0.4);
}

/* Carte Premium (499€) · mise en avant */
.offer-premium {
  background: linear-gradient(145deg, rgba(16,185,129,0.12) 0%, rgba(14,165,233,0.08) 100%);
  border: 1.5px solid rgba(16,185,129,0.45);
  box-shadow: 0 0 0 1px rgba(16,185,129,0.1), 0 16px 50px rgba(16,185,129,0.15);
}
.offer-premium:hover {
  box-shadow: 0 0 0 1px rgba(16,185,129,0.2), 0 24px 70px rgba(16,185,129,0.25);
}
/* Glow animé sur la carte premium */
.offer-premium::before {
  content: '';
  position: absolute;
  inset: -1px;
  border-radius: 23px;
  background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(14,165,233,0.15), transparent 60%);
  z-index: -1;
  opacity: 0.6;
}

/* Badge Recommandé */
.offer-badge {
  position: absolute;
  top: -14px; left: 50%; transform: translateX(-50%);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 18px;
  background: linear-gradient(90deg, #059669, #0EA5E9);
  border-radius: 100px;
  font-size: 11px;
  font-weight: 700;
  color: #fff;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  white-space: nowrap;
  box-shadow: 0 4px 16px rgba(16,185,129,0.5);
}

/* Plan name + price */
.offer-plan {
  font-size: 13px;
  font-weight: 600;
  color: rgba(255,255,255,0.45);
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.offer-price-row {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 6px;
}
.offer-price {
  font-size: 58px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.05em;
  line-height: 1;
}
.offer-price-suffix {
  font-size: 15px;
  color: rgba(255,255,255,0.35);
  font-weight: 400;
}
.offer-tagline {
  font-size: 15px;
  color: rgba(255,255,255,0.7);
  line-height: 1.5;
  margin-bottom: 28px;
  min-height: 44px;
}

/* Séparateur */
.offer-divider {
  height: 1px;
  background: rgba(255,255,255,0.08);
  margin-bottom: 24px;
}

/* Feature list */
.offer-features {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}
.offer-feature {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 14px;
  color: rgba(255,255,255,0.75);
  line-height: 1.45;
}
.offer-feature-icon {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}
.offer-essential .offer-feature-icon {
  background: rgba(255,255,255,0.08);
}
.offer-premium .offer-feature-icon {
  background: rgba(16,185,129,0.2);
}
.offer-feature strong { color: #fff; font-weight: 600; }

/* CTA boutons */
.offer-cta {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 0 24px;
  height: 52px;
  margin-top: auto;           /* colle le bouton en bas : les CTA des 2 cartes s'alignent */
  border-radius: 14px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: all 200ms ease;
  width: 100%;
  box-sizing: border-box;
}
.offer-cta-essential {
  background: rgba(255,255,255,0.07);
  border: 1.5px solid rgba(255,255,255,0.18);
  color: #fff;
}
.offer-cta-essential:hover {
  background: rgba(255,255,255,0.12);
  border-color: rgba(255,255,255,0.3);
  transform: translateY(-1px);
}
.offer-cta-premium {
  background: linear-gradient(90deg, #059669 0%, #0EA5E9 50%, #059669 100%);
  background-size: 200% 100%;
  animation: btn-shine 3s linear infinite;
  border: none;
  color: #fff;
  box-shadow: 0 4px 24px rgba(16,185,129,0.45);
}
.offer-cta-premium:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 36px rgba(16,185,129,0.6);
}
.offer-reassurance {
  text-align: center;
  font-size: 12px;
  color: rgba(255,255,255,0.25);
  margin-top: 14px;
}

@media (max-width: 768px) {
  .offers-section { padding: 40px 20px 48px; }
  .offers-title { font-size: 28px; }
  .offers-grid { grid-template-columns: 1fr; }
  .offer-premium { order: -1; }
  .offer-price { font-size: 48px; }
}

/* ── Icon system ── */
.ico {
  display: inline-flex; align-items: center; justify-content: center;
  width: 44px; height: 44px; border-radius: 12px;
  background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(14,165,233,0.08));
  border: 1px solid rgba(16,185,129,0.15);
  animation: ico-pulse 3s ease-in-out infinite;
  flex-shrink: 0;
}
.ico svg { width: 22px; height: 22px; stroke: #10B981; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.ico-sm { width: 32px; height: 32px; border-radius: 8px; }
.ico-sm svg { width: 16px; height: 16px; }
.ico-lg { width: 56px; height: 56px; border-radius: 16px; }
.ico-lg svg { width: 28px; height: 28px; }
@keyframes ico-pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
  50% { box-shadow: 0 0 12px 2px rgba(16,185,129,0.18); }
}

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 900px) {
  .results-page { padding: 24px 16px 60px; }

  .results-header {
    grid-template-columns: 1fr;
    gap: 32px;
    padding: 32px 28px;
  }

  .gauge-wrap { margin: 0 auto; }
  .results-h1 { font-size: 34px; }
  .results-stats { gap: 24px; }
  .result-stat-value { font-size: 48px; }

  .opp-card {
    grid-template-columns: 60px 1fr;
  }

  .opp-gains { display: none; }

  .premium-banner { padding: 48px 28px; }
  .premium-title { font-size: 30px; }
  .premium-price { font-size: 48px; }
}

@media (max-width: 640px) {
  .opps-header { flex-direction: column; gap: 10px; align-items: flex-start; }
  .results-stats { flex-wrap: wrap; gap: 20px; }
  .premium-includes { flex-direction: column; gap: 14px; align-items: center; }
}
</style>

<main class="results-page" id="results-page" style="display:none">

  <!-- ── Header résultats ──────────────────────────────── -->
  <div class="results-header reveal">

    <div class="gauge-wrap">
      <!-- Logo entreprise au-dessus de la jauge -->
      <div id="company-logo-above" style="display:none;flex-direction:column;align-items:center;gap:8px;margin-bottom:20px">
        <div style="width:80px;height:80px;border-radius:20px;background:var(--white);border:1px solid var(--border);box-shadow:0 4px 20px rgba(0,0,0,0.08);display:flex;align-items:center;justify-content:center;overflow:hidden">
          <img id="company-logo-img" src="" alt="" width="60" height="60" style="object-fit:contain">
        </div>
        <div style="text-align:center">
          <div id="company-domain-label" style="font-size:13px;font-weight:600;color:var(--ink-2);line-height:1.3"></div>
          <div id="company-sector-label" style="font-size:11px;color:var(--ink-4);margin-top:2px"></div>
        </div>
      </div>
      <canvas id="gauge" width="260" height="220"></canvas>
    </div>

    <div class="header-right">

      <div class="sector-badge" id="sector-badge">
        <span>✦</span>
        <span id="sector-label">Votre secteur</span>
      </div>

      <h1 class="results-h1">Votre potentiel IA</h1>

      <p class="results-summary" id="results-summary">
        Analyse de votre activité en cours…
      </p>

      <div class="results-stats">
        <div class="result-stat">
          <div class="result-stat-value" id="stat-time">-</div>
          <div class="result-stat-label">heures récupérées / sem.</div>
        </div>
        <div class="result-stat">
          <div class="result-stat-value" id="stat-money">-</div>
          <div class="result-stat-label">euros économisés / mois</div>
        </div>
        <div class="result-stat">
          <div class="result-stat-value" id="stat-tools">-</div>
          <div class="result-stat-label">outils identifiés pour vous</div>
        </div>
      </div>

    </div>
  </div>

  <!-- ── Section opportunités ─────────────────────────── -->
  <section class="opps-section">
    <div class="opps-header" style="justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div style="display:flex;align-items:baseline;gap:14px;flex-wrap:wrap">
        <h2 class="opps-title">Vos opportunités IA</h2>
        <span class="opps-subtitle" id="opps-subtitle">3 premières gratuites</span>
      </div>
      <a href="/simulation.php" class="btn-simulate">
        Simuler mes gains
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
        </svg>
      </a>
    </div>
    <div class="opp-intro" id="opps-intro" style="display:none"></div>
    <div class="opps-grid" id="opps-grid">
      <!-- Cards injectées par JS -->
    </div>
    <!-- Vignette récapitulative injectée par JS -->
    <div id="opps-summary-card" style="display:none;margin-top:20px;border-radius:20px;background:linear-gradient(135deg,#064E3B 0%,#065F46 50%,#0A2315 100%);border:1px solid rgba(16,185,129,0.35);padding:22px 28px;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between"></div>
  </section>

  <!-- ── Capture email · recevoir les résultats ─────────── -->
  <div class="email-capture-card reveal" id="email-capture">
    <div class="ec-left">
      <div class="ec-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      </div>
      <div>
        <div class="ec-title">Recevez vos résultats par email</div>
        <div class="ec-sub">Un récapitulatif gratuit de vos opportunités IA, directement dans votre boîte mail.</div>
      </div>
    </div>
    <form class="ec-form" id="ec-form" novalidate>
      <div class="ec-fields">
        <input class="ec-input" type="text"  id="ec-prenom" placeholder="Prénom" autocomplete="given-name"  required>
        <input class="ec-input" type="text"  id="ec-nom"    placeholder="Nom"    autocomplete="family-name" required>
        <input class="ec-input" type="email" id="ec-email"  placeholder="Email professionnel" autocomplete="email" required>
      </div>
      <button class="ec-btn" type="submit" id="ec-submit">
        <span id="ec-btn-label">Recevoir mes résultats</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>
    <div class="ec-success" id="ec-success" style="display:none">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <span>Rapport envoyé ! Vérifiez votre boîte mail.</span>
    </div>
    <p class="ec-rgpd">Aucun spam · Données protégées · Désinscription en 1 clic</p>
  </div>

  <!-- ── Section offres ──────────────────────────────────── -->
  <section class="offers-section reveal">

    <div class="offers-header">
      <div class="offers-eyebrow">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Passez à l'action
      </div>
      <h2 class="offers-title">Débloquez votre <strong>plan complet</strong></h2>
      <p class="offers-sub">Choisissez le niveau d'accompagnement qui vous correspond.</p>
    </div>

    <div class="offers-grid">

      <!-- ── Carte Essentiel 249€ ── -->
      <div class="offer-card offer-essential reveal">
        <div class="offer-plan">Rapport Essentiel</div>
        <div class="offer-price-row">
          <div class="offer-price"><span style="font-size:18px;color:var(--ink-4);text-decoration:line-through;font-weight:500;margin-right:8px">249€</span>99€</div>
          <div class="offer-price-suffix">paiement unique · offre de lancement</div>
        </div>
        <p class="offer-tagline">Le rapport complet pour comprendre et choisir vos outils IA.</p>
        <div class="offer-divider"></div>
        <div class="offer-features">
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span>Rapport PDF complet · <strong>tous vos outils IA identifiés</strong>, outil par outil</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span><strong>Explication d'usage concrète</strong> pour chaque outil : à quoi ça sert, comment l'utiliser au quotidien</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span>Comparatif des solutions avec <strong>nos recommandations personnalisées</strong></span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span>Simulation ROI sur 12 mois · <strong>temps et argent économisés</strong></span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span>Support par email · réponse sous 48h</span>
          </div>
        </div>
        <a href="/facturation.php?plan=report" class="offer-cta offer-cta-essential">
          Obtenir le rapport · 99€
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <p class="offer-reassurance">Paiement sécurisé · Satisfait ou remboursé 14 jours</p>
        <!-- Aides financement -->
        <div style="margin-top:16px;padding:12px 14px;background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.18);border-radius:10px">
          <div style="font-size:12px;font-weight:700;color:#065F46;margin-bottom:6px">💡 Financement possible</div>
          <div style="font-size:12px;color:var(--ink-3);line-height:1.6">Ce rapport peut être <strong>pris en charge partiellement</strong> par votre OPCO ou via <strong>France Num</strong> (aide transition numérique). Mentionnez-le à la commande · notre équipe vous guide.</div>
        </div>
      </div>

      <!-- ── Carte Premium 499€ ── -->
      <div class="offer-card offer-premium reveal">
        <div class="offer-badge">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Recommandé
        </div>
        <div class="offer-plan">ABYS Premium</div>
        <div class="offer-price-row">
          <div class="offer-price">499€</div>
          <div class="offer-price-suffix">paiement unique</div>
        </div>
        <p class="offer-tagline">Rapport + déploiement complet de vos outils, piloté par Milo, notre copilote IA.</p>
        <div class="offer-divider"></div>
        <div class="offer-features">
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span><strong>Tout le Rapport Essentiel</strong> · rapport PDF, explications, comparatif, ROI</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span><strong>Toutes vos missions de lancement</strong> · chaque outil installé, paramétré et actif</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span><strong>Guidage pas à pas par Milo (IA)</strong> · de la création du compte au premier résultat</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span>Accès à l'<strong>espace client ABYS</strong> · suivi de vos automatisations en temps réel</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span><strong>Milo disponible 24h/24 pendant 6 mois</strong> · réponses immédiates, contexte de votre audit</span>
          </div>
          <div class="offer-feature">
            <div class="offer-feature-icon"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <span>Suivi automatique <strong>pendant 6 mois</strong> · on s'assure que tout tourne</span>
          </div>
        </div>
        <a href="/audit-qualification.php?plan=premium" class="offer-cta offer-cta-premium">
          Démarrer avec ABYS Premium
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <p class="offer-reassurance">Paiement sécurisé · Satisfait ou remboursé 14 jours</p>
        <!-- Aides financement premium -->
        <div style="margin-top:16px;padding:12px 14px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:10px">
          <div style="font-size:12px;font-weight:700;color:#10B981;margin-bottom:6px">💡 Jusqu'à 50% finançable</div>
          <div style="font-size:12px;color:rgba(255,255,255,0.7);line-height:1.6">L'accompagnement ABYS Premium est éligible aux aides <strong style="color:#fff">BPI France</strong>, <strong style="color:#fff">OPCO</strong> (formation) et <strong style="color:#fff">France Num</strong>. Certaines entreprises ne paient que 200–250€. Notre équipe vous aide à monter le dossier.</div>
        </div>
      </div>

    </div>
  </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
(function () {

  /* ── Fallback SVG icons par catégorie ───────────────────── */
  function getCategoryIcon (category) {
    var cat = (category || '').toLowerCase();
    if (/email|mail|communication|message/.test(cat))
      return '<svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    if (/factur|devis|compta|finance|fiscal|invoice/.test(cat))
      return '<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>';
    if (/market|social|r.seau|content|pub|advertis/.test(cat))
      return '<svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>';
    if (/sav|support|client|chat|service/.test(cat))
      return '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    if (/rh|recruit|ressource|human|employ|staff/.test(cat))
      return '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>';
    if (/plan|agenda|rendez|calendrier|schedul/.test(cat))
      return '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
    if (/automat|workflow|process|int.gration/.test(cat))
      return '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>';
    if (/analys|report|donn.e|analytics|insight/.test(cat))
      return '<svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>';
    if (/r.daction|contenu|texte|document|writing/.test(cat))
      return '<svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
    return '<svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
  }

  /* ── Extraire le domaine depuis tool_url ─────────────────── */
  function toolDomain(opp) {
    var d = opp.tool_domain || '';
    if (!d && opp.tool_url) {
      try { d = new URL(opp.tool_url).hostname.replace(/^www\./, ''); } catch(e) {}
    }
    if (!d) {
      d = (opp.tool || opp.name || '').toLowerCase()
            .replace(/\s+/g, '').replace(/[^a-z0-9]/g, '') + '.com';
    }
    return d;
  }

  /* ── Formater le coût mensuel ───────────────────────────── */
  function fmtCost(opp) {
    var hasFree = opp.has_free_plan === true || opp.has_free_plan === 'true';
    var cost    = opp.monthly_cost_eur;
    if (cost === 0 || cost === '0' || hasFree && !cost) return 'Gratuit';
    if (cost > 0)  return 'À partir de ' + cost + '€/mois' + (hasFree ? ' (plan gratuit dispo)' : '');
    if (hasFree)   return 'Plan gratuit disponible';
    return '';
  }

  /* ── Lire les données depuis sessionStorage ────────────── */
  var result = ABYS.get('audit_result');
  if (!result) {
    window.location.href = '/';
    return;
  }

  /* ── Afficher la page ─────────────────────────────────── */
  document.getElementById('results-page').style.display = 'block';

  /* ── Logo entreprise au-dessus de la jauge ──────────── */
  var auditDomain   = ABYS.get('audit_url')    || '';
  function decodeEntities(s){ if(!s) return s; var t=document.createElement('textarea'); t.innerHTML=s; return t.value; }
  var scrapeTitle   = decodeEntities(ABYS.get('scrape_title') || '');
  var scrapeH1      = decodeEntities(ABYS.get('scrape_h1')    || '');

  /* Extrait le nom exact de l'entreprise depuis le titre de la page */
  function extractCompanyName(title, h1, domain) {
    var candidates = [];
    if (title) {
      var parts = title.split(/\s*[-|\u2013\u2014\u00B7\u2022\/]\s*/);
      var stop = /^(accueil|home|bienvenue|welcome|contact|actualité|actualités|news|blog|à propos|about|page d'accueil|site officiel)$/i;
      parts.forEach(function(p) { p = p.trim(); if (p && !stop.test(p) && p.length > 1) candidates.push(p); });
    }
    if (h1) candidates.push(h1.trim());
    if (candidates.length === 0 && domain) {
      var d = domain.replace(/^www\./i, '').replace(/\.[a-z]{2,}$/i, '');
      return d.charAt(0).toUpperCase() + d.slice(1).replace(/[-_]/g, ' ');
    }
    /* Prend le candidat le plus court (souvent le nom de marque) */
    candidates.sort(function(a, b) { return a.length - b.length; });
    return candidates[0] || domain;
  }

  var companyName = extractCompanyName(scrapeTitle, scrapeH1, auditDomain);

  if (auditDomain) {
    var logoAbove  = document.getElementById('company-logo-above');
    var logoImg    = document.getElementById('company-logo-img');
    var domLbl     = document.getElementById('company-domain-label');
    if (logoImg) {
      logoImg.src = 'https://www.google.com/s2/favicons?domain=' + auditDomain + '&sz=128';
      logoImg.alt = companyName || auditDomain;
      logoImg.onerror = function() {
        if (logoAbove) logoAbove.style.display = 'none';
      };
    }
    if (domLbl) domLbl.textContent = companyName || auditDomain;
    if (logoAbove) logoAbove.style.display = 'flex';
  }

  /* ── Score & gauge ────────────────────────────────────── */
  var score = typeof result.score === 'number' ? result.score : 72;
  Audit.drawGauge('gauge', score);

  /* ── Secteur ──────────────────────────────────────────── */
  var sector = result.sector_label || result.sector || '';
  if (sector) {
    document.getElementById('sector-label').textContent = sector;
    var secLbl = document.getElementById('company-sector-label');
    if (secLbl) secLbl.textContent = sector;
  }

  /* ── Résumé ───────────────────────────────────────────── */
  var summaryEl = document.getElementById('results-summary');
  if (result.summary) summaryEl.textContent = result.summary;

  /* ── Helper : animer un stat avec suffixe ────────────── */
  function animateStat(el, numericVal, suffix, delay) {
    el.textContent = '0' + suffix;
    setTimeout(function () {
      var start = performance.now();
      var duration = 1400;
      (function step(now) {
        var p = Math.min(1, (now - start) / duration);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(numericVal * eased).toLocaleString('fr-FR') + suffix;
        if (p < 1) requestAnimationFrame(step);
      })(performance.now());
    }, delay);
  }

  /* ── Stat temps ───────────────────────────────────────── */
  var statTime  = document.getElementById('stat-time');
  var timeRaw   = result.time_saved_weekly !== undefined ? result.time_saved_weekly
                : result.total_time_saved_h_week;
  if (timeRaw !== undefined && timeRaw !== null) {
    var timeNum = parseInt(timeRaw, 10);
    if (!isNaN(timeNum)) animateStat(statTime, timeNum, 'h', 400);
    else statTime.textContent = timeRaw;
  } else {
    statTime.textContent = '-';
  }

  /* ── Stat euros ───────────────────────────────────────── */
  var statMoney = document.getElementById('stat-money');
  var moneyRaw  = result.monthly_savings !== undefined ? result.monthly_savings
                : result.total_money_saved_eur_month;
  if (moneyRaw !== undefined && moneyRaw !== null) {
    var moneyNum = parseInt(moneyRaw, 10);
    if (!isNaN(moneyNum)) animateStat(statMoney, moneyNum, '€', 500);
    else statMoney.textContent = moneyRaw;
  } else {
    statMoney.textContent = '-';
  }

  /* ── Stat outils ──────────────────────────────────────── */
  var statTools  = document.getElementById('stat-tools');
  var opps       = Array.isArray(result.opportunities) ? result.opportunities : [];

  /* Construire top3 · toujours exactement 3 si possible */
  var top3 = [];
  var top3Source = Array.isArray(result.top3_free) ? result.top3_free : null;
  if (top3Source) {
    top3 = top3Source.slice(0, 3).map(function (item) {
      return typeof item === 'number' ? opps[item] : item;
    }).filter(Boolean);
  }
  /* Si on n'a pas 3 éléments, compléter depuis opps */
  if (top3.length < 3) {
    opps.forEach(function(o) {
      if (top3.length < 3 && top3.indexOf(o) === -1) top3.push(o);
    });
  }

  var toolCount = opps.length || top3.length || 3;
  statTools.textContent = '0';
  setTimeout(function () { ABYS.animateCount(statTools, toolCount, 1200); }, 600);

  /* ── Mettre à jour le sous-titre des opportunités avec le vrai chiffre ── */
  var subtitleEl = document.getElementById('opps-subtitle');
  if (subtitleEl && toolCount > 3) {
    subtitleEl.textContent = toolCount + ' identifiés · 3 gratuits';
  } else if (subtitleEl && toolCount > 0) {
    subtitleEl.textContent = toolCount + ' identifi' + (toolCount > 1 ? 'és' : 'é') + ' · gratuits';
  }

  /* ── Palettes visuelles ───────────────────────────────── */
  var CAT_STYLES = [
    { bg: 'rgba(16,185,129,0.1)',  border: 'rgba(16,185,129,0.25)',  color: '#059669' },
    { bg: 'rgba(14,165,233,0.1)', border: 'rgba(14,165,233,0.25)', color: '#0369A1' },
    { bg: 'rgba(245,158,11,0.1)', border: 'rgba(245,158,11,0.25)', color: '#B45309' },
  ];

  /* ── Texte d’intro humain avant les opportunités ─────── */
  var introEl = document.getElementById('opps-intro');
  if (introEl && top3.length > 0) {
    var introTime  = result.total_time_saved_h_week     || result.time_saved_weekly || 0;
    var introMoney = result.total_money_saved_eur_month || result.monthly_savings   || 0;

    var timeStr  = introTime  > 0 ? '<strong>' + introTime + ' heures par semaine</strong>' : null;
    var moneyStr = introMoney > 0 ? '<strong>' + introMoney.toLocaleString('fr-FR') + ' € / mois</strong>' : null;

    var sentences = [];
    sentences.push('En examinant votre activité, nous avons repéré plusieurs tâches où l’IA peut vous soulager concrètement · des choses que vous faites probablement chaque jour sans y penser, mais qui vous coûtent un temps précieux.');
    if (timeStr && moneyStr) {
      sentences.push('Mis bout à bout, ces automatisations pourraient vous faire gagner ' + timeStr + ', soit jusqu’à ' + moneyStr + ' de valeur retrouvée · l’équivalent d’un collaborateur à temps partiel, sans l’embauche.');
    } else if (timeStr) {
      sentences.push('Mis bout à bout, ces automatisations pourraient vous faire gagner ' + timeStr + ' sur les tâches répétitives qui freinent votre quotidien.');
    } else {
      sentences.push('Ces automatisations vous libèrent des tâches répétitives pour vous concentrer sur ce qui crée vraiment de la valeur.');
    }
    sentences.push('Ces outils s’installent en quelques minutes, sans formation et sans technicien. Vous restez maître de votre activité : l’IA gère l’ingrat, vous gardez l’essentiel.');

    introEl.innerHTML = '<p style="font-size:15.5px;line-height:1.8;color:rgba(255,255,255,0.9);margin:0">' + sentences.join(' ') + '</p>';
    introEl.style.display = 'block';
  }

  /* ── Générer les cards d'opportunités ────────────────── */
  var grid = document.getElementById('opps-grid');

  if (top3.length === 0) {
    grid.innerHTML = '<p style="color:var(--ink-4);font-size:15px;padding:16px 0">Aucune opportunité identifiée.</p>';
  } else {
    top3.forEach(function (opp, i) {
      var catSt   = CAT_STYLES[i % CAT_STYLES.length];

      var name  = opp.tool || opp.name  || opp.title || 'Outil IA';
      var cat   = opp.category || opp.cat || 'Automatisation';
      var desc  = opp.description || opp.desc || '';

      var eurosRaw = opp.monthly_gain !== undefined ? opp.monthly_gain
                   : (opp.money_saved_eur_month !== undefined ? opp.money_saved_eur_month
                   : (opp.gain_euros || opp.euros));
      var hoursRaw = opp.hours_saved !== undefined ? opp.hours_saved
                   : (opp.time_saved_h_week !== undefined ? opp.time_saved_h_week
                   : (opp.gain_heures || opp.heures));

      var eurosStr = eurosRaw !== undefined && eurosRaw !== null
        ? '+' + parseInt(eurosRaw, 10).toLocaleString('fr-FR') + '€/mois' : '';
      var hoursStr = hoursRaw !== undefined && hoursRaw !== null
        ? '−' + parseInt(hoursRaw, 10) + 'h/sem' : '';

      var costStr  = fmtCost(opp);
      var domain   = toolDomain(opp);
      var logoSrc  = 'https://www.google.com/s2/favicons?domain=' + domain + '&sz=128';
      var svgIcon  = getCategoryIcon(cat);
      var fallbackId = 'ico-fallback-' + i;

      var card = document.createElement('div');
      card.className = 'opp-card reveal';
      card.innerHTML =
        '<div class="opp-icon-wrap" style="position:relative;flex-shrink:0;width:72px;height:72px">' +
          '<img class="tool-logo" src="' + logoSrc + '" alt="' + name + '"' +
            ' onerror="this.style.display=\'none\';document.getElementById(\'' + fallbackId + '\').style.display=\'flex\'">' +
          '<span class="ico ico-lg" id="' + fallbackId + '" style="display:none;position:absolute;inset:0;border-radius:18px">' + svgIcon + '</span>' +
        '</div>' +
        '<div class="opp-body">' +
          '<div class="opp-top">' +
            '<span class="opp-tool-name">' + name + '</span>' +
            '<span class="opp-category" style="background:' + catSt.bg + ';border:1px solid ' + catSt.border + ';color:' + catSt.color + '">' + cat + '</span>' +
          '</div>' +
          (desc ? '<p class="opp-desc">' + desc + '</p>' : '') +
          (costStr ? '<div class="opp-cost">' + costStr + '</div>' : '') +
        '</div>' +
        '<div class="opp-gains">' +
          (eurosStr ? '<div class="opp-euros">' + eurosStr + '</div>' : '') +
          (hoursStr ? '<div class="opp-hours">' + hoursStr + '</div>' : '') +
        '</div>';
      grid.appendChild(card);
    });
  }

  /* ── Vignette récapitulative (tous les outils identifiés) ── */
  var summaryEl = document.getElementById('opps-summary-card');
  if (summaryEl && result) {
    var allOpps    = result.opportunities || result.top_opportunities || top3;
    var totalTools = allOpps.length;
    var totalHours = result.total_time_saved_h_week     || result.time_saved_weekly || 0;
    var totalMoney = result.total_money_saved_eur_month || result.monthly_savings   || 0;

    if (totalTools > 3) {
      var statsHtml = '';
      if (totalTools > 0) statsHtml +=
        '<div style="display:flex;flex-direction:column;align-items:center;gap:4px">' +
          '<span style="font-size:32px;font-weight:800;color:#10B981;letter-spacing:-0.04em;line-height:1">' + totalTools + '</span>' +
          '<span style="font-size:12px;color:rgba(255,255,255,0.55);font-weight:500;text-align:center">outils<br>identifiés</span>' +
        '</div>';
      if (totalHours > 0) statsHtml +=
        '<div style="width:1px;height:48px;background:rgba(255,255,255,0.12);flex-shrink:0"></div>' +
        '<div style="display:flex;flex-direction:column;align-items:center;gap:4px">' +
          '<span style="font-size:32px;font-weight:800;color:#0EA5E9;letter-spacing:-0.04em;line-height:1">' + totalHours + 'h</span>' +
          '<span style="font-size:12px;color:rgba(255,255,255,0.55);font-weight:500;text-align:center">par semaine<br>économisées</span>' +
        '</div>';
      if (totalMoney > 0) statsHtml +=
        '<div style="width:1px;height:48px;background:rgba(255,255,255,0.12);flex-shrink:0"></div>' +
        '<div style="display:flex;flex-direction:column;align-items:center;gap:4px">' +
          '<span style="font-size:32px;font-weight:800;color:#10B981;letter-spacing:-0.04em;line-height:1">' + totalMoney.toLocaleString('fr-FR') + '€</span>' +
          '<span style="font-size:12px;color:rgba(255,255,255,0.55);font-weight:500;text-align:center">de plus<br>chaque mois</span>' +
        '</div>';

      summaryEl.innerHTML =
        '<div style="display:flex;flex-direction:column;gap:6px;flex:1;min-width:200px">' +
          '<span style="font-size:16px;font-weight:700;color:#fff;line-height:1.3">' +
            totalTools + ' outils IA identifiés, adaptés à votre activité' +
          '</span>' +
          '<span style="font-size:13.5px;color:rgba(255,255,255,0.55)">Accédez à votre rapport complet pour les débloquer tous.</span>' +
        '</div>' +
        '<div style="display:flex;align-items:center;gap:20px;flex-shrink:0">' + statsHtml + '</div>';
      summaryEl.style.display = 'flex';
    }
  }

  /* ── Relancer initReveal pour les nouvelles cards ──────── */
  ABYS.initReveal();

})();
</script>

<!-- ── Formulaire email capture ────────────────────────────── -->
<script>
(function () {
  var form    = document.getElementById('ec-form');
  var success = document.getElementById('ec-success');
  var btn     = document.getElementById('ec-submit');
  var label   = document.getElementById('ec-btn-label');
  if (!form) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    var prenom = document.getElementById('ec-prenom').value.trim();
    var nom    = document.getElementById('ec-nom').value.trim();
    var email  = document.getElementById('ec-email').value.trim();

    /* Validation basique */
    var ok = true;
    [['ec-prenom', prenom], ['ec-nom', nom], ['ec-email', email]].forEach(function (pair) {
      var el = document.getElementById(pair[0]);
      if (!pair[1] || (pair[0] === 'ec-email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(pair[1]))) {
        el.classList.add('ec-error'); ok = false;
      } else {
        el.classList.remove('ec-error');
      }
    });
    if (!ok) return;

    btn.disabled = true;
    label.textContent = 'Envoi en cours…';

    try {
      var leadId  = ABYS.get('lead_id')   || 0;
      var auditId = ABYS.get('audit_id')  || 0;
      var auditUrl = ABYS.get('audit_url') || '';

      var resp = await fetch('/api/send-audit-email.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ prenom, nom, email, lead_id: leadId, audit_id: auditId, url: auditUrl }),
      });
      var data = await resp.json();

      if (data.success) {
        form.style.display    = 'none';
        success.style.display = 'flex';
      } else {
        throw new Error(data.error || 'Erreur serveur');
      }
    } catch (err) {
      label.textContent = 'Réessayer';
      btn.disabled      = false;
      /* Affiche l'erreur sous le formulaire */
      var errEl = document.getElementById('ec-err-msg');
      if (!errEl) {
        errEl = document.createElement('p');
        errEl.id = 'ec-err-msg';
        errEl.style.cssText = 'color:#EF4444;font-size:13px;margin:4px 0 0';
        form.appendChild(errEl);
      }
      errEl.textContent = 'Une erreur est survenue, réessayez ou contactez-nous.';
    }
  });

  /* Retire la classe erreur à la frappe */
  ['ec-prenom','ec-nom','ec-email'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', function () { el.classList.remove('ec-error'); });
  });
})();
</script>
