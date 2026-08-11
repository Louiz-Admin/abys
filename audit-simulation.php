<?php
$page_title = 'Simulez vos gains IA — ABYS AI';
$page_description = 'Calculez en temps réel le temps libéré, les économies et le ROI que l\'IA peut générer pour votre entreprise.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<style>
/* ── Page simulation ── */
.sim-header {
  text-align: center;
  padding: 72px 0 48px;
}
.sim-header .badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(16,185,129,0.1);
  color: var(--green-deep);
  border: 1px solid var(--border-green);
  border-radius: var(--r-pill);
  padding: 5px 14px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 20px;
}
.sim-header h1 {
  font-size: 42px;
  font-weight: 300;
  letter-spacing: -0.04em;
  line-height: 1.1;
  margin-bottom: 14px;
  color: var(--ink);
}
.sim-header h1 strong {
  font-weight: 700;
  background: var(--gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.sim-header p {
  font-size: 17px;
  color: var(--ink-3);
  max-width: 520px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ── Résultats en 3 colonnes ── */
.sim-results {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 56px;
}
.sim-result-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r-lg);
  padding: 28px 24px;
  text-align: center;
  box-shadow: var(--shadow-sm);
  transition: box-shadow 200ms var(--ease), transform 200ms var(--ease);
  position: relative;
  overflow: hidden;
}
.sim-result-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
}
.sim-result-card.green::before { background: var(--gradient); }
.sim-result-card.blue::before  { background: linear-gradient(135deg, #0EA5E9, #38BDF8); }
.sim-result-card.deep::before  { background: linear-gradient(135deg, #059669, #0369A1); }
.sim-result-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}
.sim-result-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--ink-4);
  margin-bottom: 10px;
}
.sim-result-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}
.sim-result-value {
  font-size: 36px;
  font-weight: 700;
  letter-spacing: -0.04em;
  line-height: 1;
  margin-bottom: 6px;
}
.sim-result-card.green .sim-result-value { color: var(--green-deep); }
.sim-result-card.blue  .sim-result-value { color: var(--blue-deep); }
.sim-result-card.deep  .sim-result-value { color: var(--ink-2); }
.sim-result-sub {
  font-size: 12px;
  color: var(--ink-4);
}

/* ── Questions en grille ── */
.sim-questions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  margin-bottom: 56px;
}
.sim-question-block {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r-lg);
  padding: 28px;
  box-shadow: var(--shadow-sm);
}
/* Q4 (multi-select) span 2 colonnes */
.sim-question-block.full-width {
  grid-column: 1 / -1;
}
.sim-question-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--gradient);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  margin-bottom: 10px;
}
.sim-question-label {
  font-size: 15px;
  font-weight: 600;
  color: var(--ink);
  margin-bottom: 18px;
  line-height: 1.4;
}
.sim-options {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap: 10px;
}
.sim-options.options-lg {
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
}
.sim-option {
  border: 2px solid var(--border);
  border-radius: var(--r-lg);
  padding: 16px 10px;
  text-align: center;
  cursor: pointer;
  background: var(--white);
  transition: border-color 180ms var(--ease), background 180ms var(--ease), box-shadow 180ms var(--ease), transform 120ms var(--ease);
  user-select: none;
  -webkit-user-select: none;
}
.sim-option:hover {
  border-color: rgba(16,185,129,0.4);
  background: rgba(16,185,129,0.03);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}
.sim-option.selected {
  border-color: var(--green);
  background: rgba(16,185,129,0.06);
  box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
}
.sim-option.selected .opt-label {
  font-weight: 600;
  color: var(--green-deep);
}
.opt-ico-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 8px;
}
.opt-label {
  font-size: 12px;
  color: var(--ink-2);
  line-height: 1.35;
  font-weight: 400;
  transition: font-weight 180ms, color 180ms;
}

/* ── Barre de progression ── */
.sim-progress-bar {
  background: var(--border);
  border-radius: var(--r-pill);
  height: 4px;
  margin-bottom: 40px;
  overflow: hidden;
}
.sim-progress-fill {
  height: 100%;
  background: var(--gradient);
  border-radius: var(--r-pill);
  transition: width 400ms var(--ease);
}

/* ── CTA final ── */
.sim-cta {
  text-align: center;
  padding: 56px 0 80px;
}
.sim-cta-box {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r-xl);
  padding: 48px 40px;
  max-width: 640px;
  margin: 0 auto;
  box-shadow: var(--shadow-md);
  position: relative;
  overflow: hidden;
}
.sim-cta-box::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: var(--gradient);
}
.sim-cta-box h2 {
  font-size: 26px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 12px;
}
.sim-cta-box p {
  color: var(--ink-3);
  font-size: 15px;
  margin-bottom: 28px;
  line-height: 1.6;
}
.btn-primary-lg {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 16px 32px;
  background: var(--gradient);
  color: #fff;
  border-radius: var(--r-pill);
  font-size: 16px;
  font-weight: 600;
  box-shadow: var(--shadow-glow);
  transition: opacity 150ms var(--ease), transform 150ms var(--ease), box-shadow 150ms var(--ease);
  cursor: pointer;
  border: none;
  text-decoration: none;
}
.btn-primary-lg:hover {
  opacity: 0.92;
  transform: translateY(-2px);
  box-shadow: 0 8px 32px rgba(16,185,129,0.35);
}
.sim-note {
  margin-top: 16px;
  font-size: 12px;
  color: var(--ink-4);
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
@keyframes ico-pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
  50% { box-shadow: 0 0 12px 2px rgba(16,185,129,0.18); }
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .sim-header h1 { font-size: 30px; }
  .sim-results { grid-template-columns: 1fr; }
  .sim-questions { grid-template-columns: 1fr; }
  .sim-question-block.full-width { grid-column: auto; }
  .sim-options { grid-template-columns: repeat(2, 1fr); }
  .sim-options.options-lg { grid-template-columns: repeat(2, 1fr); }
  .sim-cta-box { padding: 32px 24px; }
}
@media (max-width: 480px) {
  .sim-result-value { font-size: 28px; }
  .sim-options { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="container">

  <!-- ── Spotlight entreprise (injecté par JS) ── -->
  <div id="sim-company-bar" style="display:none;margin:40px auto 0;max-width:720px">
    <div style="
      background:linear-gradient(135deg,#0A1F1A 0%,#064E3B 45%,#091C2B 100%);
      border-radius:24px;padding:40px 40px 36px;text-align:center;position:relative;overflow:hidden;
      box-shadow:0 20px 60px rgba(0,0,0,0.22)">
      <!-- Glow circle déco -->
      <div style="position:absolute;top:-60px;left:50%;transform:translateX(-50%);width:280px;height:280px;
        background:radial-gradient(ellipse,rgba(16,185,129,0.15) 0%,transparent 70%);pointer-events:none"></div>

      <!-- Badge -->
      <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(16,185,129,0.12);
        border:1px solid rgba(16,185,129,0.3);border-radius:40px;padding:5px 14px;
        font-size:11px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#6EE7B7;margin-bottom:24px">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        Simulation personnalisée
      </div>

      <!-- Logo entreprise -->
      <div id="sim-company-logo" style="display:flex;justify-content:center;margin-bottom:20px">
        <!-- injecté par JS -->
      </div>

      <!-- Nom entreprise -->
      <h2 id="sim-company-name" style="font-size:26px;font-weight:700;color:#fff;letter-spacing:-0.03em;margin-bottom:6px;line-height:1.2"></h2>
      <div id="sim-company-sector" style="font-size:14px;color:rgba(255,255,255,0.5);margin-bottom:0"></div>
    </div>
  </div>

  <!-- Header -->
  <div class="sim-header">
    <div class="badge" id="sim-header-badge" style="display:none">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      Simulateur avancé
    </div>
    <div class="badge" id="sim-badge-default">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      Simulateur avancé
    </div>
    <h1 id="sim-main-title">Simulez vos <strong>gains IA</strong></h1>
    <p id="sim-main-sub">Répondez aux questions ci-dessous et obtenez une estimation personnalisée du temps et de l'argent que l'IA peut vous faire gagner.</p>
  </div>

  <!-- Barre de progression -->
  <div class="sim-progress-bar">
    <div class="sim-progress-fill" id="progressFill" style="width:0%"></div>
  </div>

  <!-- Questions interactives en grille 2 colonnes -->
  <div class="sim-questions">

    <!-- Q1 : Employés -->
    <div class="sim-question-block">
      <div class="sim-question-num">1</div>
      <div class="sim-question-label">Combien d'employés dans votre entreprise ?</div>
      <div class="sim-options" data-question="q1" data-multi="false">
        <div class="sim-option" data-value="1">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span></div>
          <span class="opt-label">1 (solo)</span>
        </div>
        <div class="sim-option" data-value="3">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
          <span class="opt-label">2 – 5</span>
        </div>
        <div class="sim-option" data-value="12">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
          <span class="opt-label">6 – 20</span>
        </div>
        <div class="sim-option" data-value="35">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="2"/><line x1="14" y1="22" x2="14" y2="2"/></svg></span></div>
          <span class="opt-label">21 – 50</span>
        </div>
        <div class="sim-option" data-value="75">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="2"/><line x1="14" y1="22" x2="14" y2="2"/></svg></span></div>
          <span class="opt-label">50+</span>
        </div>
      </div>
    </div>

    <!-- Q2 : Heures répétitives -->
    <div class="sim-question-block">
      <div class="sim-question-num">2</div>
      <div class="sim-question-label">Heures par semaine passées sur des tâches répétitives ?</div>
      <div class="sim-options" data-question="q2" data-multi="false">
        <div class="sim-option" data-value="3">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
          <span class="opt-label">&lt; 5h</span>
        </div>
        <div class="sim-option" data-value="7">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
          <span class="opt-label">5 – 10h</span>
        </div>
        <div class="sim-option" data-value="15">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
          <span class="opt-label">10 – 20h</span>
        </div>
        <div class="sim-option" data-value="25">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
          <span class="opt-label">20 – 30h</span>
        </div>
        <div class="sim-option" data-value="35">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
          <span class="opt-label">&gt; 30h</span>
        </div>
      </div>
    </div>

    <!-- Q3 : CA annuel -->
    <div class="sim-question-block">
      <div class="sim-question-num">3</div>
      <div class="sim-question-label">Quel est votre chiffre d'affaires annuel ?</div>
      <div class="sim-options" data-question="q3" data-multi="false">
        <div class="sim-option" data-value="50000">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span></div>
          <span class="opt-label">&lt; 100k€</span>
        </div>
        <div class="sim-option" data-value="200000">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span></div>
          <span class="opt-label">100 – 300k€</span>
        </div>
        <div class="sim-option" data-value="650000">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span></div>
          <span class="opt-label">300k – 1M€</span>
        </div>
        <div class="sim-option" data-value="2000000">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span></div>
          <span class="opt-label">&gt; 1M€</span>
        </div>
      </div>
    </div>

    <!-- Q5 : Budget mensuel -->
    <div class="sim-question-block">
      <div class="sim-question-num">5</div>
      <div class="sim-question-label">Quel budget mensuel envisagez-vous pour l'IA ?</div>
      <div class="sim-options" data-question="q5" data-multi="false">
        <div class="sim-option" data-value="25">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span></div>
          <span class="opt-label">&lt; 50€</span>
        </div>
        <div class="sim-option" data-value="125">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span></div>
          <span class="opt-label">50 – 200€</span>
        </div>
        <div class="sim-option" data-value="350">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span></div>
          <span class="opt-label">200 – 500€</span>
        </div>
        <div class="sim-option" data-value="750">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span></div>
          <span class="opt-label">&gt; 500€</span>
        </div>
      </div>
    </div>

    <!-- Q4 : Processus (multi-select, pleine largeur) -->
    <div class="sim-question-block full-width">
      <div class="sim-question-num">4</div>
      <div class="sim-question-label">Quels processus souhaitez-vous automatiser en priorité ? <span style="font-size:12px;font-weight:400;color:var(--ink-4)">(choix multiples)</span></div>
      <div class="sim-options options-lg" data-question="q4" data-multi="true">
        <div class="sim-option" data-value="1.2">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span></div>
          <span class="opt-label">Emails &amp; devis</span>
        </div>
        <div class="sim-option" data-value="1.15">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span></div>
          <span class="opt-label">Comptabilité</span>
        </div>
        <div class="sim-option" data-value="1.1">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></span></div>
          <span class="opt-label">Marketing</span>
        </div>
        <div class="sim-option" data-value="1.1">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span></div>
          <span class="opt-label">SAV</span>
        </div>
        <div class="sim-option" data-value="1.05">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
          <span class="opt-label">Recrutement</span>
        </div>
        <div class="sim-option" data-value="1.1">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span></div>
          <span class="opt-label">Planification</span>
        </div>
        <div class="sim-option" data-value="1.15">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span></div>
          <span class="opt-label">Facturation</span>
        </div>
        <div class="sim-option" data-value="1.08">
          <div class="opt-ico-wrap"><span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span></div>
          <span class="opt-label">Reporting</span>
        </div>
      </div>
    </div>

  </div><!-- /.sim-questions -->

  <!-- Résultats dynamiques en 3 colonnes -->
  <div class="sim-results" style="margin-top:40px">
    <div class="sim-result-card green">
      <div class="sim-result-icon">
        <span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
      </div>
      <div class="sim-result-label">Heures libérées / semaine</div>
      <div class="sim-result-value" id="valHeures">—</div>
      <div class="sim-result-sub" id="subHeures">Complétez les questions</div>
    </div>
    <div class="sim-result-card blue">
      <div class="sim-result-icon">
        <span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
      </div>
      <div class="sim-result-label">Économies / mois</div>
      <div class="sim-result-value" id="valEconomies">—</div>
      <div class="sim-result-sub" id="subEconomies">Complétez les questions</div>
    </div>
    <div class="sim-result-card deep">
      <div class="sim-result-icon">
        <span class="ico ico-sm"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
      </div>
      <div class="sim-result-label">ROI sur 12 mois</div>
      <div class="sim-result-value" id="valROI">—</div>
      <div class="sim-result-sub" id="subROI">Basé sur votre budget mensuel</div>
    </div>
  </div>

  <!-- CTA -->
  <div class="sim-cta">
    <div class="sim-cta-box">
      <h2>Prêt à libérer votre potentiel ?</h2>
      <p id="ctaText">Complétez les questions ci-dessus pour obtenir votre estimation personnalisée, puis accédez à votre rapport détaillé.</p>
      <a href="/audit-rapport-premium.php" class="btn-primary-lg" id="ctaBtn">
        Voir mon rapport détaillé →
      </a>
      <p class="sim-note">Rapport complet · Feuille de route · Outils recommandés</p>
    </div>
  </div>

</div><!-- /.container -->

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
(function () {
  'use strict';

  /* ── État ── */
  const state = {
    q1: null,   // nombre d'employés (valeur numérique)
    q2: null,   // heures répétitives/semaine
    q3: null,   // CA annuel
    q4: [],     // multiplicateurs processus (multi)
    q5: null,   // budget mensuel
  };

  /* ── Taux d'automatisation IA par défaut ── */
  const AI_RATE = 0.65; // 65% des tâches répétitives automatisables
  const COST_PER_HOUR = 35; // coût moyen d'une heure de travail (€)

  /* ── Initialisation depuis sessionStorage ── */
  function loadSessionData () {
    try {
      const raw = sessionStorage.getItem('audit_result') || sessionStorage.getItem('abys_audit');
      if (!raw) return;
      const d = JSON.parse(raw);
      // Pré-remplir si données disponibles
      if (d.employees)    preselectByValue('q1', closestEmployeeVal(d.employees));
      if (d.hoursLost)    preselectByValue('q2', closestHourVal(d.hoursLost));
      if (d.revenue)      preselectByValue('q3', closestRevenueVal(d.revenue));
      if (d.budget)       preselectByValue('q5', closestBudgetVal(d.budget));
    } catch (e) { /* ignore */ }
  }

  function closestEmployeeVal (n) {
    const map = [[1,1],[5,3],[20,12],[50,35],[Infinity,75]];
    return map.find(([max]) => n <= max)[1];
  }
  function closestHourVal (h) {
    const map = [[5,3],[10,7],[20,15],[30,25],[Infinity,35]];
    return map.find(([max]) => h <= max)[1];
  }
  function closestRevenueVal (r) {
    const map = [[100000,50000],[300000,200000],[1000000,650000],[Infinity,2000000]];
    return map.find(([max]) => r <= max)[1];
  }
  function closestBudgetVal (b) {
    const map = [[50,25],[200,125],[500,350],[Infinity,750]];
    return map.find(([max]) => b <= max)[1];
  }

  function preselectByValue (q, numVal) {
    const container = document.querySelector(`[data-question="${q}"]`);
    if (!container) return;
    const opt = [...container.querySelectorAll('.sim-option')].find(el => parseFloat(el.dataset.value) === numVal);
    if (opt) opt.click();
  }

  /* ── Calcul ── */
  function compute () {
    if (!state.q2) return null; // pas assez de données

    const employees      = state.q1 || 1;
    const hoursRepetitif = state.q2;
    const caAnnuel       = state.q3 || 0;
    const budgetMensuel  = state.q5 || 0;

    // Multiplicateur processus (produit des facteurs sélectionnés, plafonné)
    const procMult = state.q4.reduce((acc, v) => acc * v, 1);
    const cappedMult = Math.min(procMult, 3.0);

    // Heures libérées par semaine (pour 1 employé × nb employés impactés)
    const employeesImpacted = Math.max(1, Math.round(employees * 0.7));
    const heuresParEmploye  = Math.round(hoursRepetitif * AI_RATE * cappedMult * 10) / 10;
    const heuresTotal       = Math.round(heuresParEmploye * employeesImpacted * 10) / 10;

    // Économies mensuelles
    const heuresMois   = heuresTotal * 4.33;
    const economieBrut = Math.round(heuresMois * COST_PER_HOUR);
    const bonusCA      = caAnnuel > 0 ? Math.round(caAnnuel * 0.015 / 12) : 0;
    const economieTot  = economieBrut + bonusCA;

    // ROI 12 mois
    let roi = null;
    if (budgetMensuel > 0) {
      const investissement = budgetMensuel * 12;
      const gainAnnuel     = economieTot * 12;
      roi = Math.round(((gainAnnuel - investissement) / investissement) * 100);
    }

    return { heuresTotal, economieTot, roi, budgetMensuel };
  }

  /* ── Formatage ── */
  function fmtEuro (n) {
    if (n >= 10000) return Math.round(n / 1000) + 'k€';
    return n.toLocaleString('fr-FR') + '€';
  }

  /* ── Mise à jour UI ── */
  function updateUI () {
    const result = compute();
    const answeredCount = [state.q1, state.q2, state.q3, state.q5].filter(Boolean).length
                        + (state.q4.length > 0 ? 1 : 0);
    const total = 5;
    const pct = Math.round((answeredCount / total) * 100);

    // Barre de progression
    document.getElementById('progressFill').style.width = pct + '%';

    if (!result) {
      // État vide
      document.getElementById('valHeures').textContent   = '—';
      document.getElementById('subHeures').textContent   = 'Répondez aux questions';
      document.getElementById('valEconomies').textContent = '—';
      document.getElementById('subEconomies').textContent = 'Répondez aux questions';
      document.getElementById('valROI').textContent      = '—';
      document.getElementById('subROI').textContent      = 'Indiquez votre budget';
      return;
    }

    // Heures
    document.getElementById('valHeures').textContent  = result.heuresTotal + 'h';
    document.getElementById('subHeures').textContent  = 'récupérées chaque semaine';

    // Économies
    document.getElementById('valEconomies').textContent  = fmtEuro(result.economieTot);
    document.getElementById('subEconomies').textContent  = 'économisés chaque mois';

    // ROI
    if (result.roi !== null) {
      const sign = result.roi >= 0 ? '+' : '';
      document.getElementById('valROI').textContent = sign + result.roi + '%';
      document.getElementById('subROI').textContent = result.roi >= 0
        ? 'retour sur investissement'
        : 'coût net (ajustez le budget)';
    } else {
      document.getElementById('valROI').textContent = '—';
      document.getElementById('subROI').textContent = 'Indiquez votre budget mensuel';
    }

    // Texte CTA
    if (answeredCount >= 3) {
      document.getElementById('ctaText').textContent =
        'Votre simulation est prête. Obtenez maintenant votre feuille de route personnalisée avec les outils et actions concrètes.';
    }

    // Sauvegarder en session
    try {
      const existing = JSON.parse(sessionStorage.getItem('audit_result') || '{}');
      sessionStorage.setItem('audit_result', JSON.stringify({
        ...existing,
        simulation: {
          heuresLibereesParSemaine: result.heuresTotal,
          economiesMensuelles: result.economieTot,
          roiPct: result.roi,
          budgetMensuel: result.budgetMensuel,
        }
      }));
    } catch (e) { /* ignore */ }
  }

  /* ── Gestionnaire de clic sur options ── */
  function bindOptions () {
    document.querySelectorAll('[data-question]').forEach(function (container) {
      const qKey = container.dataset.question;
      const isMulti = container.dataset.multi === 'true';

      container.addEventListener('click', function (e) {
        const opt = e.target.closest('.sim-option');
        if (!opt || !container.contains(opt)) return;

        const val = parseFloat(opt.dataset.value);

        if (isMulti) {
          // Toggle multi-select
          if (opt.classList.contains('selected')) {
            opt.classList.remove('selected');
            state[qKey] = state[qKey].filter(v => v !== val);
            // Remettre si plusieurs fois la même valeur (comptabilité + facturation = même mult 1.15)
            // On gère par index plutôt que valeur pour éviter les doublons
          } else {
            opt.classList.add('selected');
            state[qKey].push(val);
          }
        } else {
          // Single select
          container.querySelectorAll('.sim-option').forEach(o => o.classList.remove('selected'));
          opt.classList.add('selected');
          state[qKey] = val;
        }

        updateUI();
      });
    });
  }

  /* ── Personnalisation depuis l'audit ── */
  function loadCompanyContext () {
    var domain       = (typeof ABYS !== 'undefined' && ABYS.get) ? ABYS.get('audit_url')    : null;
    var audit        = (typeof ABYS !== 'undefined' && ABYS.get) ? ABYS.get('audit_result') : null;
    var scrapeTitle  = (typeof ABYS !== 'undefined' && ABYS.get) ? ABYS.get('scrape_title') : null;
    var scrapeH1     = (typeof ABYS !== 'undefined' && ABYS.get) ? ABYS.get('scrape_h1')    : null;
    if (!domain && !audit) return;

    var bar      = document.getElementById('sim-company-bar');
    var logoWrap = document.getElementById('sim-company-logo');
    var nameEl   = document.getElementById('sim-company-name');
    var secEl    = document.getElementById('sim-company-sector');

    /* Nom exact : scrape title > company_name > domaine formaté */
    var displayName = extractCompanyNameSim(scrapeTitle, scrapeH1, domain)
                   || (audit && audit.company_name)
                   || formatDomain(domain);

    /* Logo premium */
    if (domain && logoWrap) {
      var logoOuter = document.createElement('div');
      logoOuter.style.cssText = 'width:80px;height:80px;border-radius:20px;background:rgba(255,255,255,0.08);' +
        'border:1px solid rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;' +
        'overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.3);margin:0 auto';
      var img = document.createElement('img');
      img.src    = 'https://www.google.com/s2/favicons?domain=' + domain + '&sz=128';
      img.width  = 54; img.height = 54;
      img.style.cssText = 'object-fit:contain;border-radius:12px';
      img.onerror = function () { logoOuter.style.display = 'none'; };
      logoOuter.appendChild(img);
      logoWrap.appendChild(logoOuter);
      logoWrap.style.display = 'flex';
    }

    if (displayName && nameEl) nameEl.textContent = displayName;
    var sector = (audit && (audit.sector_label || audit.sector)) || '';
    if (sector && secEl) secEl.textContent = sector;
    if (bar) bar.style.display = 'block';

    /* Masque le badge par défaut, affiche le badge du header */
    var defBadge = document.getElementById('sim-badge-default');
    if (defBadge) defBadge.style.display = 'none';

    /* Titre & sous-titre : on garde les valeurs par défaut de la page */
  }

  /* Extrait le nom d'entreprise depuis le titre de page */
  function extractCompanyNameSim(title, h1, domain) {
    var candidates = [];
    if (title) {
      var parts = title.split(/\s*[-|–—·•\/]\s*/);
      var stop  = /^(accueil|home|bienvenue|contact|actualit[eé]s?|news|blog|[àa] propos|about|site officiel|page d'accueil)$/i;
      parts.forEach(function (p) { p = p.trim(); if (p && !stop.test(p) && p.length > 1 && p.length < 60) candidates.push(p); });
    }
    if (h1 && h1.trim().length > 1 && h1.trim().length < 60) candidates.push(h1.trim());
    if (candidates.length === 0) return null;
    candidates.sort(function (a, b) { return a.length - b.length; });
    return candidates[0];
  }

  // Formate "boulangerie-martin.fr" → "Boulangerie Martin"
  function formatDomain(domain) {
    if (!domain) return '';
    var d = domain.replace(/^www\./i, '').replace(/\.[a-z]{2,}$/i, '');
    return d.charAt(0).toUpperCase() + d.slice(1).replace(/[-_]/g, ' ');
  }

  /* ── Init ── */
  document.addEventListener('DOMContentLoaded', function () {
    bindOptions();
    loadSessionData();
    loadCompanyContext();
    updateUI();
  });

})();
</script>
