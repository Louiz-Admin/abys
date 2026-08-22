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
        <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 1.9 5.8L19.7 11l-5.8 1.9L12 18.7l-1.9-5.8L4.3 11l5.8-2.2z"/></svg></span>
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

  <style>
  /* ══════ Concrètement, ce qui change ══════ */
  .tr-section { margin-top:56px; }
  .tr-head { display:flex; gap:18px; align-items:flex-start; margin-bottom:24px; }
  .tr-milo { width:64px; height:64px; border-radius:50%; border:3px solid #10B981; object-fit:cover; flex-shrink:0;
    box-shadow:0 0 0 6px rgba(16,185,129,.12); }
  .tr-eyebrow { font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:var(--green-deep,#059669); margin-bottom:6px; }
  .tr-title { font-size:clamp(22px,3vw,30px); font-weight:300; letter-spacing:-.03em; margin:0 0 6px; color:var(--ink,#0A1F1A); }
  .tr-title strong { font-weight:800; }
  .tr-sub { font-size:14.5px; color:var(--ink-3,#4B5563); margin:0; line-height:1.6; }

  .tr-row { display:grid; grid-template-columns:1fr 44px 1fr; align-items:stretch; margin-bottom:12px;
    opacity:0; transform:translateY(12px); transition:opacity .5s ease, transform .5s cubic-bezier(.3,1,.4,1); }
  .tr-row.in { opacity:1; transform:none; }
  @media(max-width:820px){ .tr-row{ grid-template-columns:1fr; } .tr-mid{ transform:rotate(90deg); margin:2px auto; } }
  .tr-cell { border-radius:16px; padding:18px 20px; }
  .tr-today { background:#fff; border:2px solid var(--border,#E5E7EB); }
  .tr-ai { background:linear-gradient(155deg,#0A1F1A,#064E3B); border:2px solid #10B981; }
  .tr-mid { display:flex; align-items:center; justify-content:center; color:#10B981; }
  .tr-task { display:flex; align-items:center; gap:9px; font-size:12px; font-weight:800; letter-spacing:.04em;
    text-transform:uppercase; margin-bottom:9px; }
  .tr-today .tr-task { color:var(--ink-4,#9CA3AF); }
  .tr-ai .tr-task { color:#6EE7B7; }
  .tr-hint { margin-left:auto; font-size:11px; font-weight:600; text-transform:none; letter-spacing:0;
    background:rgba(16,185,129,.14); color:#059669; border-radius:20px; padding:3px 9px; }
  .tr-ai .tr-hint { background:rgba(16,185,129,.2); color:#6EE7B7; }
  .tr-txt { font-size:14px; line-height:1.65; }
  .tr-today .tr-txt { color:var(--ink-3,#4B5563); }
  .tr-ai .tr-txt { color:rgba(255,255,255,.85); }
  .tr-verdict { margin-top:18px; background:linear-gradient(135deg,rgba(16,185,129,.09),rgba(14,165,233,.06));
    border:1px solid rgba(16,185,129,.25); border-radius:16px; padding:18px 22px; font-size:15.5px; line-height:1.65;
    color:var(--ink-2,#1F2937); text-align:center; font-weight:500; }

  /* ══════ Potentiel créatif ══════ */
  .crea-section { margin-top:56px; }
  .crea-head { text-align:center; margin-bottom:24px; }
  .crea-title { font-size:clamp(21px,2.8vw,28px); font-weight:300; letter-spacing:-.03em; margin:0 0 8px; color:var(--ink,#0A1F1A); }
  .crea-title strong { font-weight:800; }
  .crea-sub { font-size:14.5px; color:var(--ink-3,#4B5563); margin:0 auto; max-width:600px; line-height:1.65; }
  .crea-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
  @media(max-width:760px){ .crea-grid{ grid-template-columns:1fr; } }
  .crea-card { background:#fff; border:2px solid var(--border,#E5E7EB); border-radius:18px; padding:24px; }
  .crea-card .em { width:44px; height:44px; border-radius:13px; background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(14,165,233,.1));
    border:1px solid rgba(16,185,129,.22); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
  .crea-card h4 { font-size:16px; font-weight:800; margin:0 0 8px; color:var(--ink,#0A1F1A); }
  .crea-card p { font-size:13.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin:0; }

  /* ══════ Visibilité IA ══════ */
  .visia-section { position:relative; overflow:hidden; margin-top:56px; border-radius:24px;
    background:linear-gradient(160deg,#041712,#052E16 55%,#07231a); color:#fff; padding:44px 40px 40px; }
  @media(max-width:640px){ .visia-section{ padding:32px 22px; } }
  .visia-beams { position:absolute; inset:0; overflow:hidden; pointer-events:none; }
  .visia-beams span { position:absolute; top:-40%; left:var(--l); width:110px; height:190%; transform-origin:top center; transform:rotate(var(--a)); }
  .visia-beams span::before { content:''; position:absolute; inset:0;
    background:linear-gradient(to bottom, rgba(155,247,208,.22), rgba(58,206,231,.08) 55%, transparent 80%);
    -webkit-mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
            mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
    filter:blur(8px); mix-blend-mode:screen; transform-origin:top center;
    animation:visia-ray var(--d) ease-in-out var(--delay,0s) infinite alternate; }
  @keyframes visia-ray { from{ transform:rotate(calc(var(--s) * -1)); } to{ transform:rotate(var(--s)); } }
  @media (prefers-reduced-motion: reduce){ .visia-beams span::before{ animation:none; } }
  .visia-in { position:relative; z-index:2; max-width:780px; margin:0 auto; text-align:center; }
  .visia-eyebrow { display:inline-block; font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase;
    color:#6EE7B7; background:rgba(16,185,129,.14); border:1px solid rgba(16,185,129,.32); border-radius:30px; padding:6px 13px; margin-bottom:18px; }
  .visia-title { font-size:clamp(22px,3.1vw,32px); font-weight:300; letter-spacing:-.03em; line-height:1.25; margin:0 0 12px; }
  .visia-title strong { font-weight:800; }
  .visia-pitch { font-size:15px; line-height:1.75; color:rgba(255,255,255,.68); max-width:660px; margin:0 auto 26px; }

  .visia-demo { background:rgba(0,0,0,.32); border:1px solid rgba(255,255,255,.12); border-radius:16px; overflow:hidden;
    margin:0 auto 26px; text-align:left; }
  .visia-demo-bar { display:flex; align-items:center; gap:7px; padding:11px 16px; background:rgba(255,255,255,.05); border-bottom:1px solid rgba(255,255,255,.08); }
  .visia-dot { width:9px; height:9px; border-radius:50%; background:rgba(255,255,255,.18); }
  .visia-demo-title { font-size:11.5px; color:rgba(255,255,255,.45); margin-left:8px; }
  .visia-demo-body { padding:18px; }
  .visia-msg { font-size:14px; line-height:1.6; border-radius:14px; padding:11px 15px; margin-bottom:10px; max-width:88%; }
  .visia-msg-user { background:rgba(16,185,129,.16); color:#D1FAE5; margin-left:auto; border-bottom-right-radius:4px; }
  .visia-msg-ai { background:rgba(255,255,255,.07); color:rgba(255,255,255,.75); border-bottom-left-radius:4px; display:flex; align-items:center; gap:10px; }
  .visia-typing { display:inline-flex; gap:4px; }
  .visia-typing i { width:6px; height:6px; border-radius:50%; background:#6EE7B7; display:block; animation:visia-blink 1.2s infinite; }
  .visia-typing i:nth-child(2){ animation-delay:.2s } .visia-typing i:nth-child(3){ animation-delay:.4s }
  @keyframes visia-blink { 0%,60%,100%{ opacity:.25 } 30%{ opacity:1 } }
  .visia-answer { display:block; }
  .visia-rivals { display:flex; flex-direction:column; gap:7px; margin-top:11px; }
  .visia-rival { display:flex; align-items:center; gap:9px; font-size:13px; color:rgba(255,255,255,.6);
    background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:10px; padding:8px 12px;
    opacity:0; transform:translateX(-8px); transition:opacity .45s ease, transform .45s cubic-bezier(.3,1,.4,1); }
  .visia-rivals.in .visia-rival { opacity:1; transform:none; }
  .visia-rank { width:19px; height:19px; border-radius:6px; background:rgba(255,255,255,.1); color:rgba(255,255,255,.55);
    font-size:11px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .visia-verdict { margin-top:14px; padding:13px 15px; border-radius:12px; background:rgba(239,68,68,.1);
    border:1px solid rgba(239,68,68,.3); font-size:13.5px; line-height:1.6; color:#FCA5A5;
    opacity:0; transform:translateY(8px); transition:opacity .5s ease, transform .5s cubic-bezier(.3,1,.4,1); }
  .visia-verdict.in { opacity:1; transform:none; }
  .visia-verdict b { color:#FEE2E2; }

  .visia-actions { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:26px; }
  @media(max-width:760px){ .visia-actions{ grid-template-columns:1fr; } }
  .visia-act { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); border-radius:14px; padding:16px 18px;
    font-size:13.5px; line-height:1.6; color:rgba(255,255,255,.8); display:flex; gap:11px; align-items:flex-start; text-align:left; }
  .visia-act .n { width:24px; height:24px; border-radius:7px; background:linear-gradient(135deg,#10B981,#0EA5E9); color:#fff;
    font-size:12px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .visia-cta { display:inline-flex; align-items:center; justify-content:center; height:50px; padding:0 30px; border-radius:12px;
    background:linear-gradient(90deg,#059669,#0EA5E9 55%,#10B981); color:#fff; font-size:15px; font-weight:700; text-decoration:none;
    box-shadow:0 12px 30px -12px rgba(16,185,129,.8); transition:transform .15s; }
  .visia-cta:hover { transform:translateY(-2px); }
  </style>

  <style>
  /* ══════ Barre d'offre collante ══════ */
  .sticky-offer { position:fixed; left:0; right:0; bottom:0; z-index:60; transform:translateY(110%);
    transition:transform .4s cubic-bezier(.3,1,.4,1); pointer-events:none; }
  .sticky-offer.on { transform:none; pointer-events:auto; }
  .sticky-inner { max-width:1000px; margin:0 auto 14px; background:linear-gradient(150deg,#0A1F1A,#064E3B);
    border:1px solid rgba(16,185,129,.4); border-radius:18px; padding:14px 20px; display:flex; align-items:center;
    gap:18px; flex-wrap:wrap; box-shadow:0 18px 44px -14px rgba(0,0,0,.65); }
  .sticky-milo { width:42px; height:42px; border-radius:50%; border:2px solid #10B981; object-fit:cover; flex-shrink:0; }
  .sticky-txt { flex:1; min-width:200px; font-size:13.5px; line-height:1.5; color:rgba(255,255,255,.82); }
  .sticky-txt b { color:#fff; }
  .sticky-price { font-size:22px; font-weight:800; color:#34D399; letter-spacing:-.02em; white-space:nowrap; }
  .sticky-price s { font-size:13px; font-weight:500; color:rgba(255,255,255,.4); margin-right:7px; }
  .sticky-cta { height:46px; padding:0 24px; border-radius:12px; background:linear-gradient(90deg,#059669,#0EA5E9 55%,#10B981);
    color:#fff; font-size:14.5px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center;
    justify-content:center; white-space:nowrap; box-shadow:0 10px 26px -10px rgba(16,185,129,.8); transition:transform .15s; }
  .sticky-cta:hover { transform:translateY(-1px); }
  .sticky-close { background:none; border:none; color:rgba(255,255,255,.35); font-size:20px; line-height:1; cursor:pointer;
    padding:4px 6px; font-family:inherit; }
  .sticky-close:hover { color:rgba(255,255,255,.7); }
  @media(max-width:700px){
    .sticky-inner { gap:12px; padding:12px 14px; margin:0 10px 10px; }
    .sticky-txt { display:none; }
    .sticky-cta { flex:1; }
  }
  body.has-sticky { padding-bottom:96px; }
  @media print { .sticky-offer { display:none !important; } body.has-sticky{ padding-bottom:0; } }
  </style>

  <div class="sticky-offer" id="sticky-offer" aria-hidden="true">
    <div class="sticky-inner">
      <img class="sticky-milo" src="/assets/img/milo-avatar.jpg" alt="Milo">
      <div class="sticky-txt"><b>Votre plan d'action complet</b>, avec les tutoriels et mon accompagnement pendant 30 jours.</div>
      <div class="sticky-price"><s>249€</s>99€</div>
      <a class="sticky-cta" href="/facturation.php?plan=report">Obtenir mon rapport</a>
      <button class="sticky-close" id="sticky-close" aria-label="Masquer">&times;</button>
    </div>
  </div>

  <!-- ══════ CONCRÈTEMENT, CE QUI CHANGE ══════ -->
  <!-- ── Porte : la suite de l'analyse s'ouvre contre un email ───────── -->
  <style>
    .gate { position:relative; margin:44px 0 0; border-radius:22px; padding:26px 28px;
      background:linear-gradient(150deg,#052E24,#04211A 70%); border:1px solid rgba(16,185,129,.34);
      box-shadow:0 30px 70px -40px rgba(0,0,0,.8); display:flex; gap:22px; align-items:flex-start; flex-wrap:wrap; }
    .gate img { width:58px; height:58px; border-radius:50%; object-fit:cover; border:2px solid rgba(52,211,153,.7);
      box-shadow:0 0 0 6px rgba(16,185,129,.12); flex-shrink:0; }
    .gate-txt { flex:1; min-width:260px; }
    .gate-eyebrow { font-size:11px; font-weight:700; letter-spacing:.13em; text-transform:uppercase; color:#6EE7B7; margin-bottom:7px; }
    .gate h3 { font-size:clamp(19px,2.4vw,24px); font-weight:700; letter-spacing:-.025em; color:#F3FBF8; margin:0 0 9px; }
    .gate p { font-size:14px; line-height:1.65; color:rgba(255,255,255,.72); margin:0; max-width:620px; }
    .gate-form { display:flex; flex-direction:column; gap:10px; min-width:290px; flex:0 0 320px; }
    .gate-form input { padding:14px 15px; border-radius:12px; border:1px solid rgba(255,255,255,.18);
      background:rgba(255,255,255,.06); color:#F3FBF8; font-size:15px; font-family:inherit; outline:none;
      transition:border-color .16s, box-shadow .16s, background .16s; }
    .gate-form input::placeholder { color:rgba(255,255,255,.38); }
    .gate-form input:focus { border-color:#34D399; background:rgba(16,185,129,.10); box-shadow:0 0 0 3px rgba(16,185,129,.16); }
    .gate-form input.err { border-color:#F87171; box-shadow:0 0 0 3px rgba(248,113,113,.18); }
    .gate-btn { display:flex; align-items:center; justify-content:center; gap:9px; border:none; cursor:pointer;
      font-family:inherit; font-size:15px; font-weight:700; color:#03251B; border-radius:12px; padding:14px 20px;
      background:linear-gradient(90deg,#34D399,#5EEAD4 55%,#7DD3FC); box-shadow:0 18px 40px -18px rgba(52,211,153,.95);
      transition:transform .14s, filter .16s; }
    .gate-btn:hover { transform:translateY(-2px); filter:brightness(1.06); }
    .gate-btn:disabled { opacity:.5; cursor:wait; transform:none; }
    .gate-note { font-size:11.5px; color:rgba(255,255,255,.42); line-height:1.5; }
    .gate-ok { display:none; align-items:center; gap:10px; color:#6EE7B7; font-size:14.5px; font-weight:600; }

    /* Zone verrouillee */
    #zone-verrouillee { position:relative; }
    body:not(.debloque) #zone-verrouillee { filter:blur(9px); pointer-events:none; user-select:none;
      opacity:.55; max-height:520px; overflow:hidden; }
    body:not(.debloque) #zone-verrouillee::after { content:''; position:absolute; inset:auto 0 0 0; height:200px;
      background:linear-gradient(to bottom, transparent, var(--bg,#F0FDF8)); }
    #zone-verrouillee { transition:filter .7s ease, opacity .7s ease, max-height 1.1s ease; }
    @media(max-width:820px){ .gate { padding:22px 20px; } .gate-form { flex:1 1 100%; min-width:0; } }
  </style>

  <div class="gate reveal" id="gate">
    <img src="/assets/img/milo-avatar.jpg" alt="Milo">
    <div class="gate-txt">
      <div class="gate-eyebrow">Milo</div>
      <h3>La suite de votre analyse est prête</h3>
      <p>Vous venez de voir vos premières opportunités, et elles sont à vous, sans rien donner. En dessous, il reste ce que ça change concrètement dans votre semaine, ce que votre métier vous permettrait de créer, et surtout ce qui se passe aujourd'hui quand un client cherche votre activité dans ChatGPT ou Gemini. Je vous ouvre tout de suite, dites-moi juste où vous envoyer la copie.</p>
    </div>
    <form class="gate-form" id="gate-form" novalidate>
      <input type="text"  id="gate-prenom" placeholder="Votre prénom" autocomplete="given-name">
      <input type="email" id="gate-email"  placeholder="vous@votre-entreprise.fr" autocomplete="email" inputmode="email">
      <button class="gate-btn" type="submit" id="gate-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="10.5" rx="2.5"/><path d="M8 10.5V7a4 4 0 0 1 7.5-2"/></svg>
        <span id="gate-label">Ouvrir la suite de mon audit</span>
      </button>
      <div class="gate-ok" id="gate-ok">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9.5 17 4 11.5"/></svg>
        <span>C'est ouvert. La copie part dans votre boîte mail.</span>
      </div>
      <div class="gate-note">Gratuit, sans carte bancaire. Aucun spam, aucun appel commercial, désinscription en un clic.</div>
    </form>
  </div>

  <div id="zone-verrouillee">
  <section class="tr-section reveal" id="tr-section" style="display:none">
    <div class="tr-head">
      <img class="tr-milo" src="/assets/img/milo-avatar.jpg" alt="Milo">
      <div>
        <div class="tr-eyebrow">Analyse de Milo</div>
        <h2 class="tr-title">Concrètement, <strong>ce qui change</strong></h2>
        <p class="tr-sub">Tâche par tâche, ce que ces outils prennent en charge à votre place.</p>
      </div>
    </div>
    <div class="tr-list" id="tr-list"></div>
    <div class="tr-verdict" id="tr-verdict" style="display:none"></div>
  </section>

  <!-- ══════ CE QUE VOUS POURRIEZ ENFIN OSER ══════ -->
  <section class="crea-section reveal" id="crea-section" style="display:none">
    <div class="crea-head">
      <h2 class="crea-title">L'IA ne fait pas que vous <strong>faire gagner du temps</strong></h2>
      <p class="crea-sub">Elle vous permet aussi de créer et d'oser des choses que vous ne faites pas aujourd'hui, faute de temps ou de moyens.</p>
    </div>
    <div class="crea-grid" id="crea-grid"></div>
  </section>

  <!-- ══════ VISIBILITÉ IA · le levier ══════ -->
  <section class="visia-section reveal" id="visia-section" style="display:none">
    <div class="visia-beams" aria-hidden="true">
      <span style="--a:-16deg;--l:58%;--d:9s;--s:8deg;--delay:-2s"></span>
      <span style="--a:0deg;--l:64%;--d:7s;--s:10deg;--delay:-5s"></span>
      <span style="--a:16deg;--l:70%;--d:10.5s;--s:7deg;--delay:-1s"></span>
    </div>
    <div class="visia-in">
      <div class="visia-eyebrow">Le levier que vos concurrents ignorent</div>
      <h2 class="visia-title">Vos futurs clients ne demandent plus à Google.<br><strong>Ils demandent à une IA.</strong></h2>
      <p class="visia-pitch" id="visia-pitch"></p>

      <div class="visia-demo">
        <div class="visia-demo-bar">
          <span class="visia-dot"></span><span class="visia-dot"></span><span class="visia-dot"></span>
          <span class="visia-demo-title">Conversation avec une IA</span>
        </div>
        <div class="visia-demo-body">
          <div class="visia-msg visia-msg-user" id="visia-question"></div>
          <div class="visia-msg visia-msg-ai">
            <span class="visia-typing"><i></i><i></i><i></i></span>
            <span class="visia-answer">Voici les professionnels que je recommande…</span>
          </div>
          <div class="visia-verdict" id="visia-verdict"></div>
        </div>
      </div>

      <div class="visia-actions" id="visia-actions"></div>

      <a href="/visibilite-ia.php" class="visia-cta">Rendre mon entreprise visible par les IA</a>
    </div>
  </section>

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
          <div style="font-size:12px;font-weight:700;color:#065F46;margin-bottom:6px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px"><path d="M9 18h6"/><path d="M10 21.5h4"/><path d="M12 2a6.5 6.5 0 0 0-4 11.6c.6.5 1 1.2 1 2h6c0-.8.4-1.5 1-2A6.5 6.5 0 0 0 12 2z"/></svg>Financement possible</div>
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
          <div style="font-size:12px;font-weight:700;color:#10B981;margin-bottom:6px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px"><path d="M9 18h6"/><path d="M10 21.5h4"/><path d="M12 2a6.5 6.5 0 0 0-4 11.6c.6.5 1 1.2 1 2h6c0-.8.4-1.5 1-2A6.5 6.5 0 0 0 12 2z"/></svg>Jusqu'à 50% finançable</div>
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
    if (timeStr && moneyStr) {
      sentences.push('Mis bout à bout, ces outils représentent ' + timeStr + ', soit ' + moneyStr + ' de valeur retrouvée.');
    } else if (timeStr) {
      sentences.push('Mis bout à bout, ces outils représentent ' + timeStr + ' sur vos tâches répétitives.');
    } else {
      sentences.push('Ces outils vous libèrent des tâches répétitives.');
    }
    sentences.push('Installation en quelques minutes, sans technicien.');

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

  /* ══════ Transformations · potentiel créatif · visibilité IA ══════ */
  (function renderMiloSections(){
    function esc(s){ return String(s == null ? '' : s).replace(/[<>&]/g, function(c){ return {'<':'&lt;','>':'&gt;','&':'&amp;'}[c]; }); }
    var sector = decodeEntities(result.sector_label || result.sector || 'votre activité');

    var ICONS = {
      creation:'<path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/>',
      visibilite:'<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
      offre:'<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
      contenu:'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
      video:'<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>',
      relation:'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/>',
      organisation:'<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
      expertise:'<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'
    };
    function iconSvg(key){
      var body = ICONS[String(key || '').toLowerCase()] || ICONS.creation;
      return '<svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">' + body + '</svg>';
    }

    /* ── 1. Transformations (tâche par tâche, factuel) ── */
    var trs = Array.isArray(result.transformations) ? result.transformations : [];
    if (trs.length) {
      var host = document.getElementById('tr-list');
      host.innerHTML = '';
      trs.slice(0, 5).forEach(function(t){
        var task = esc(decodeEntities(t.task || ''));
        var hint = t.time_hint ? '<span class="tr-hint">' + esc(decodeEntities(t.time_hint)) + '</span>' : '';
        var row = document.createElement('div');
        row.className = 'tr-row';
        row.innerHTML =
          '<div class="tr-cell tr-today">' +
            '<div class="tr-task">Aujourd\'hui : ' + task + '</div>' +
            '<div class="tr-txt">' + esc(decodeEntities(t.today || '')) + '</div>' +
          '</div>' +
          '<div class="tr-mid" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>' +
          '<div class="tr-cell tr-ai">' +
            '<div class="tr-task">Avec l\'IA' + hint + '</div>' +
            '<div class="tr-txt">' + esc(decodeEntities(t.with_ai || '')) + '</div>' +
          '</div>';
        host.appendChild(row);
      });
      var trSec = document.getElementById('tr-section');
      trSec.style.display = 'block';

      var vd = decodeEntities(result.transformations_verdict || '');
      if (vd) { var vEl = document.getElementById('tr-verdict'); vEl.textContent = vd; vEl.style.display = 'block'; }

      var io = new IntersectionObserver(function(en){
        en.forEach(function(e){
          if (!e.isIntersecting) return;
          host.querySelectorAll('.tr-row').forEach(function(r,i){ setTimeout(function(){ r.classList.add('in'); }, i * 150); });
          io.unobserve(e.target);
        });
      }, { threshold: .15 });
      io.observe(trSec);
    }

    /* ── 2. Potentiel créatif ── */
    var crea = Array.isArray(result.creative_potential) ? result.creative_potential : [];
    if (crea.length) {
      var cg = document.getElementById('crea-grid');
      cg.innerHTML = '';
      crea.slice(0, 3).forEach(function(c){
        var d = document.createElement('div');
        d.className = 'crea-card';
        d.innerHTML = '<span class="em">' + iconSvg(c.icon) + '</span>' +
                      '<h4>' + esc(decodeEntities(c.title || '')) + '</h4>' +
                      '<p>' + esc(decodeEntities(c.text || '')) + '</p>';
        cg.appendChild(d);
      });
      document.getElementById('crea-section').style.display = 'block';
    }

    /* ── 3. Visibilité IA (conversation animée) ── */
    var vis = result.ai_visibility || null;
    if (vis && (vis.pitch || (vis.actions && vis.actions.length))) {
      document.getElementById('visia-pitch').textContent = decodeEntities(vis.pitch || '');

      var question = 'Tu peux me recommander un bon professionnel en ' + sector.toLowerCase() + ' près de chez moi ?';
      var qEl = document.getElementById('visia-question');
      var missed = parseInt(vis.missed_clients_month, 10);

      var acts = Array.isArray(vis.actions) ? vis.actions : [];
      var ag = document.getElementById('visia-actions');
      ag.innerHTML = '';
      acts.slice(0, 3).forEach(function(a, i){
        var d = document.createElement('div');
        d.className = 'visia-act';
        d.innerHTML = '<span class="n">' + (i + 1) + '</span><span>' + esc(decodeEntities(a)) + '</span>';
        ag.appendChild(d);
      });
      var visSec = document.getElementById('visia-section');
      visSec.style.display = 'block';

      /* Séquence animée : frappe de la question, réflexion, réponse, verdict */
      var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var answerEl = visSec.querySelector('.visia-answer');
      var typingEl = visSec.querySelector('.visia-typing');
      var aiMsg    = visSec.querySelector('.visia-msg-ai');
      var verdictEl = document.getElementById('visia-verdict');
      var verdictHtml = (missed > 0)
        ? 'L\'IA cite vos concurrents. Pas vous. C\'est environ <b>' + missed + ' clients potentiels par mois</b> qui ne vous voient jamais.'
        : 'L\'IA cite vos concurrents. Pas vous. <b>Ces clients ne vous voient jamais.</b>';
      verdictEl.innerHTML = verdictHtml;

      var RIVALS = ['Un concurrent à 3 km', 'Une entreprise que vous connaissez', 'Un autre professionnel du secteur'];
      function rivalsHtml(){
        return RIVALS.map(function(r, i){
          return '<span class="visia-rival" style="transition-delay:' + (i * 160) + 'ms"><span class="visia-rank">' + (i+1) + '</span>' + r + '</span>';
        }).join('');
      }

      function play(){
        qEl.textContent = '';
        aiMsg.classList.remove('done');
        typingEl.style.display = 'inline-flex';
        answerEl.innerHTML = '';
        verdictEl.classList.remove('in');

        if (reduce) {
          qEl.textContent = '« ' + question + ' »';
          typingEl.style.display = 'none';
          answerEl.innerHTML = 'Voici les professionnels que je recommande :<div class="visia-rivals in">' + rivalsHtml() + '</div>';
          verdictEl.classList.add('in');
          return;
        }

        var i = 0;
        qEl.textContent = '« ';
        var typer = setInterval(function(){
          qEl.textContent = '« ' + question.slice(0, ++i);
          if (i >= question.length) {
            clearInterval(typer);
            qEl.textContent = '« ' + question + ' »';
            setTimeout(function(){
              typingEl.style.display = 'none';
              answerEl.innerHTML = 'Voici les professionnels que je recommande :<div class="visia-rivals">' + rivalsHtml() + '</div>';
              setTimeout(function(){ visSec.querySelector('.visia-rivals').classList.add('in'); }, 40);
              setTimeout(function(){ verdictEl.classList.add('in'); }, 900);
            }, 1400);
          }
        }, 32);
      }

      var vio = new IntersectionObserver(function(en){
        en.forEach(function(e){ if (e.isIntersecting) { play(); vio.unobserve(e.target); } });
      }, { threshold: .35 });
      vio.observe(visSec);
    }
  })();

  /* ══════ Barre d'offre collante : visible dès la lecture des opportunités ══════ */
  (function stickyOffer(){
    var bar = document.getElementById('sticky-offer');
    if (!bar) return;
    var closed = false;
    document.getElementById('sticky-close').addEventListener('click', function(){
      closed = true; bar.classList.remove('on'); document.body.classList.remove('has-sticky');
    });
    var trigger = document.getElementById('opps-grid');
    var stop    = document.querySelector('.offers-section');
    function update(){
      if (closed) return;
      var t = trigger ? trigger.getBoundingClientRect() : null;
      var s2 = stop ? stop.getBoundingClientRect() : null;
      var started = t ? (t.top < window.innerHeight * 0.55) : false;
      var ended   = s2 ? (s2.top < window.innerHeight * 0.9) : false;
      var show = started && !ended;
      bar.classList.toggle('on', show);
      document.body.classList.toggle('has-sticky', show);
    }
    window.addEventListener('scroll', update, { passive:true });
    window.addEventListener('resize', update);
    setTimeout(update, 600);
  })();

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

<!-- ── La porte : l'email ouvre la suite de l'analyse ───────── -->
<script>
(function () {
  var form  = document.getElementById('gate-form');
  if (!form) return;
  var btn   = document.getElementById('gate-btn');
  var label = document.getElementById('gate-label');
  var ok    = document.getElementById('gate-ok');

  function ouvrir() {
    document.body.classList.add('debloque');
  }

  /* Deja identifie sur cette machine : on n'embete personne deux fois */
  if (ABYS.get('audit_debloque') === '1' || ABYS.get('lead_email')) {
    ouvrir();
    var g = document.getElementById('gate');
    if (g) g.style.display = 'none';
  }

  /* Rien a verrouiller (audit sans ces sections) : on efface la porte */
  setTimeout(function () {
    var z = document.getElementById('zone-verrouillee');
    if (!z) return;
    var visible = Array.prototype.filter.call(z.querySelectorAll('section'), function (el) {
      return el.offsetParent !== null || el.style.display !== 'none';
    });
    if (!visible.length) {
      ouvrir();
      var g = document.getElementById('gate');
      if (g) g.style.display = 'none';
    }
  }, 1400);

  function erreur(el) { el.classList.add('err'); el.focus(); }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    var elP = document.getElementById('gate-prenom');
    var elE = document.getElementById('gate-email');
    var prenom = elP.value.trim();
    var email  = elE.value.trim();

    elP.classList.remove('err'); elE.classList.remove('err');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { erreur(elE); return; }
    if (!prenom) { erreur(elP); return; }

    btn.disabled = true;
    label.textContent = 'J\'ouvre tout ça';

    /* L'ouverture ne depend pas de l'envoi du mail : on ne punit personne
       pour une panne SMTP. Le mail part en arriere-plan. */
    ABYS.store('lead_email', email);
    ABYS.store('prenom', prenom);
    ABYS.store('audit_debloque', '1');
    ouvrir();
    form.querySelector('.gate-btn').style.display = 'none';
    ok.style.display = 'flex';
    setTimeout(function () {
      var g = document.getElementById('gate');
      if (g) { g.style.transition = 'opacity .6s ease'; g.style.opacity = '.45'; }
    }, 2600);

    try {
      await fetch('/api/send-audit-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          prenom: prenom, nom: '', email: email,
          lead_id: ABYS.get('lead_id') || 0,
          audit_id: ABYS.get('audit_id') || 0,
          url: ABYS.get('audit_url') || ''
        })
      });
    } catch (err) { /* le contenu est deja ouvert a l'ecran */ }
  });

  ['gate-prenom', 'gate-email'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('input', function () { el.classList.remove('err'); });
  });
})();
</script>
