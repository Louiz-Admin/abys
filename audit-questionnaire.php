<?php
$page_title = 'Questionnaire · ABYS AI';
$extra_js   = ['/assets/js/audit.js'];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>
.q-hero { text-align:center; padding:52px 0 32px; }
.q-hero h1 { font-size:40px; font-weight:300; letter-spacing:-0.04em; margin-bottom:12px; }
.q-hero p { font-size:16px; color:var(--ink-3); max-width:520px; margin:0 auto; line-height:1.6; }

/* Progress bar */
.q-progress-wrap { max-width:800px; margin:0 auto 28px; padding:0 24px; }
.q-progress-bar-bg { height:4px; background:var(--border); border-radius:4px; overflow:hidden; }
.q-progress-bar { height:4px; background:var(--green); border-radius:4px; transition:width 400ms ease; }
.q-progress-label { font-size:12px; color:var(--ink-4); margin-top:6px; text-align:right; }

/* Section titles */
.q-section-title {
  font-size:13px; font-weight:700; color:var(--green-deep);
  letter-spacing:0.08em; text-transform:uppercase;
  margin:36px 0 16px; display:flex; align-items:center; gap:8px;
}
.q-section-title::after {
  content:''; flex:1; height:1px; background:var(--border);
}

.q-block { background:#fff; border:1px solid var(--border); border-radius:20px; padding:32px; margin-bottom:20px; box-shadow:var(--shadow-sm); }
.q-block h3 { font-size:17px; font-weight:600; color:var(--ink-2); margin-bottom:20px; display:flex; align-items:center; gap:10px; line-height:1.4; }
.q-num { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; background:var(--green); color:#fff; border-radius:50%; font-size:13px; font-weight:700; flex-shrink:0; }

.opt-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
.opt-grid.col2 { grid-template-columns:repeat(2,1fr); }
.opt-grid.col4 { grid-template-columns:repeat(4,1fr); }
@media(max-width:640px){ .opt-grid,.opt-grid.col2,.opt-grid.col4 { grid-template-columns:repeat(2,1fr); } }
@media(max-width:420px){ .opt-grid,.opt-grid.col2,.opt-grid.col4 { grid-template-columns:1fr 1fr; } }

.opt-box {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  gap:8px; padding:16px 10px; border:2px solid var(--border); border-radius:14px;
  cursor:pointer; text-align:center; font-size:13px; font-weight:500; color:var(--ink-2);
  background:#fff; transition:border-color 150ms,background 150ms,transform 100ms;
  user-select:none; min-height:80px;
}
.opt-box:hover { border-color:#10B981; background:#F0FDF4; }
.opt-box.selected { border-color:#10B981; background:#F0FDF4; color:#065F46; }
.opt-box.selected .ico { transform:scale(1.08); }

/* Compact option (no icon) */
.opt-box.compact {
  min-height:unset; padding:12px 14px; flex-direction:row;
  justify-content:flex-start; gap:10px; text-align:left;
}

.multi-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; }
@media(max-width:500px){ .multi-grid { grid-template-columns:1fr; } }
.multi-box {
  display:flex; align-items:center; gap:10px; padding:12px 14px;
  border:2px solid var(--border); border-radius:12px; cursor:pointer;
  font-size:13px; font-weight:500; color:var(--ink-2); background:#fff;
  transition:border-color 150ms,background 150ms;
}
.multi-box:hover { border-color:#10B981; background:#F0FDF4; }
.multi-box.selected { border-color:#10B981; background:#F0FDF4; color:#065F46; }

/* Range slider */
.q-slider-wrap { padding:4px 0; }
.q-slider { -webkit-appearance:none; appearance:none; width:100%; height:6px; border-radius:6px; background:var(--border); outline:none; margin:16px 0 8px; }
.q-slider::-webkit-slider-thumb { -webkit-appearance:none; appearance:none; width:22px; height:22px; border-radius:50%; background:var(--green); cursor:pointer; border:2px solid #fff; box-shadow:0 2px 8px rgba(16,185,129,0.4); }
.q-slider-labels { display:flex; justify-content:space-between; font-size:12px; color:var(--ink-4); }
.q-slider-val { font-size:18px; font-weight:700; color:var(--green-deep); text-align:center; margin:4px 0; }

/* Text input */
.q-input {
  width:100%; padding:14px 16px; border:2px solid var(--border); border-radius:12px;
  font-size:15px; color:var(--ink-1); outline:none; transition:border-color 150ms;
  box-sizing:border-box; font-family:inherit; background:var(--bg);
}
.q-input:focus { border-color:#10B981; }
.q-input::placeholder { color:var(--ink-4); }

/* Textarea */
.q-textarea {
  width:100%; padding:14px 16px; border:2px solid var(--border); border-radius:12px;
  font-size:14px; color:var(--ink-1); outline:none; transition:border-color 150ms;
  box-sizing:border-box; font-family:inherit; resize:vertical; min-height:90px; background:var(--bg);
}
.q-textarea:focus { border-color:#10B981; }

/* Email */
.email-input {
  width:100%; padding:14px 16px; border:2px solid var(--border); border-radius:12px;
  font-size:15px; color:var(--ink-1); outline:none; transition:border-color 150ms;
  box-sizing:border-box; font-family:inherit;
}
.email-input:focus { border-color:#10B981; }

.submit-btn {
  width:100%; padding:18px; background:linear-gradient(90deg,#059669 0%,#0EA5E9 30%,#10B981 50%,#0EA5E9 70%,#059669 100%);
  background-size:300% 100%; animation:btn-shine 3s linear infinite;
  color:#fff; border:none; border-radius:14px;
  font-size:16px; font-weight:700; cursor:pointer;
  box-shadow:0 4px 20px rgba(16,185,129,0.35);
  position:relative; overflow:hidden;
}
.submit-btn:disabled { opacity:0.6; cursor:not-allowed; animation:none; }
@keyframes btn-shine { 0%{background-position:0% 0%} 100%{background-position:300% 0%} }

.loading-state { display:none; text-align:center; padding:60px 20px; }

/* Hint */
.q-hint { font-size:12px; color:var(--ink-4); margin-top:8px; line-height:1.5; }

/* Icon system */
.ico {
  display:inline-flex; align-items:center; justify-content:center;
  width:44px; height:44px; border-radius:12px;
  background:linear-gradient(135deg,rgba(16,185,129,0.08),rgba(14,165,233,0.08));
  border:1px solid rgba(16,185,129,0.15);
  animation:ico-pulse 3s ease-in-out infinite;
  flex-shrink:0; transition:transform 100ms;
}
.ico svg { width:22px; height:22px; stroke:#10B981; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.ico-sm { width:32px; height:32px; border-radius:8px; }
.ico-sm svg { width:16px; height:16px; }
@keyframes ico-pulse {
  0%,100% { box-shadow:0 0 0 0 rgba(16,185,129,0); }
  50% { box-shadow:0 0 12px 2px rgba(16,185,129,0.18); }
}
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<div class="q-hero">
  <div class="container">
    <div class="badge" style="margin:0 auto 16px">Audit personnalisé</div>
    <h1>Quelques questions pour<br><strong>affiner votre audit</strong></h1>
    <p>Plus on en sait sur vous et votre entreprise, plus les recommandations seront pertinentes. 3 à 4 minutes suffisent.</p>
  </div>
</div>

<!-- Progress -->
<div class="q-progress-wrap">
  <div class="q-progress-bar-bg">
    <div class="q-progress-bar" id="q-progress" style="width:0%"></div>
  </div>
  <div class="q-progress-label" id="q-progress-label">0 / 12 questions</div>
</div>

<div class="container" style="max-width:800px;padding-bottom:80px">

  <div id="questionnaire">

    <!-- ════════════════════════════════════════
         SECTION 1 · L'ENTREPRISE
         ════════════════════════════════════════ -->
    <div class="q-section-title">🏢 Votre entreprise</div>

    <!-- Q1 : Secteur -->
    <div class="q-block" data-q="1">
      <h3><span class="q-num">1</span> Quel est votre secteur d'activité ?</h3>
      <div class="opt-grid" id="grid-sector">
        <div class="opt-box" data-group="sector" data-value="Artisan / BTP">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
          Artisan / BTP
        </div>
        <div class="opt-box" data-group="sector" data-value="Commerce de détail">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></span>
          Commerce
        </div>
        <div class="opt-box" data-group="sector" data-value="Restauration">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg></span>
          Restauration
        </div>
        <div class="opt-box" data-group="sector" data-value="Santé / Bien-être">
          <span class="ico"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
          Santé / Bien-être
        </div>
        <div class="opt-box" data-group="sector" data-value="Services / Conseil">
          <span class="ico"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span>
          Services / Conseil
        </div>
        <div class="opt-box" data-group="sector" data-value="Immobilier">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
          Immobilier
        </div>
        <div class="opt-box" data-group="sector" data-value="Transport / Logistique">
          <span class="ico"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
          Transport
        </div>
        <div class="opt-box" data-group="sector" data-value="Juridique / Comptabilité">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
          Juridique / Compta
        </div>
        <div class="opt-box" data-group="sector" data-value="Beauté / Esthétique">
          <span class="ico"><svg viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/></svg></span>
          Beauté
        </div>
        <div class="opt-box" data-group="sector" data-value="Agriculture">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M12 22V12"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/><path d="M8 5.07A9.92 9.92 0 0 1 12 4c1.68 0 3.26.42 4.65 1.16"/><path d="M12 4V2"/></svg></span>
          Agriculture
        </div>
        <div class="opt-box" data-group="sector" data-value="E-commerce">
          <span class="ico"><svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></span>
          E-commerce
        </div>
        <div class="opt-box" data-group="sector" data-value="Autre">
          <span class="ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
          Autre
        </div>
      </div>
    </div>

    <!-- Q2 : Taille -->
    <div class="q-block" data-q="2">
      <h3><span class="q-num">2</span> Combien de personnes dans votre entreprise ?</h3>
      <div class="opt-grid col2">
        <div class="opt-box" data-group="size" data-value="Juste moi (auto-entrepreneur)">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
          Juste moi
        </div>
        <div class="opt-box" data-group="size" data-value="2 à 5 personnes">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          2 – 5
        </div>
        <div class="opt-box" data-group="size" data-value="6 à 20 personnes">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          6 – 20
        </div>
        <div class="opt-box" data-group="size" data-value="Plus de 20 personnes">
          <span class="ico"><svg viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="2"/><line x1="14" y1="22" x2="14" y2="2"/></svg></span>
          20+
        </div>
      </div>
    </div>

    <!-- Q3 : Ancienneté -->
    <div class="q-block" data-q="3">
      <h3><span class="q-num">3</span> Depuis combien de temps existe votre entreprise ?</h3>
      <div class="opt-grid col2">
        <div class="opt-box compact" data-group="age" data-value="Moins d'1 an">🌱 Moins d'un an</div>
        <div class="opt-box compact" data-group="age" data-value="1 à 3 ans">🌿 1 à 3 ans</div>
        <div class="opt-box compact" data-group="age" data-value="3 à 10 ans">🌳 3 à 10 ans</div>
        <div class="opt-box compact" data-group="age" data-value="Plus de 10 ans">🏛 Plus de 10 ans</div>
      </div>
    </div>

    <!-- Q4 : CA / budget -->
    <div class="q-block" data-q="4">
      <h3><span class="q-num">4</span> Quel est l'ordre de grandeur de votre chiffre d'affaires annuel ?</h3>
      <p class="q-hint" style="margin-bottom:16px">Cette information nous aide à calibrer le ROI de vos automatisations · elle reste confidentielle.</p>
      <div class="opt-grid col2">
        <div class="opt-box compact" data-group="revenue" data-value="Moins de 50 000€">Moins de 50 000€</div>
        <div class="opt-box compact" data-group="revenue" data-value="50 000 à 150 000€">50 000 à 150 000€</div>
        <div class="opt-box compact" data-group="revenue" data-value="150 000 à 500 000€">150 000 à 500 000€</div>
        <div class="opt-box compact" data-group="revenue" data-value="Plus de 500 000€">Plus de 500 000€</div>
      </div>
    </div>

    <!-- ════════════════════════════════════════
         SECTION 2 · LE DIRIGEANT
         ════════════════════════════════════════ -->
    <div class="q-section-title">👤 Vous, le dirigeant</div>

    <!-- Q5 : Votre rôle réel -->
    <div class="q-block" data-q="5">
      <h3><span class="q-num">5</span> Au quotidien, quelle est votre principale casquette ?</h3>
      <div class="multi-grid" id="grid-role">
        <div class="multi-box" data-group="role" data-value="Commercial / prospection">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
          Commercial / prospection
        </div>
        <div class="multi-box" data-group="role" data-value="Opérationnel / terrain">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
          Opérationnel / terrain
        </div>
        <div class="multi-box" data-group="role" data-value="Administratif / gestion">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
          Administratif / gestion
        </div>
        <div class="multi-box" data-group="role" data-value="Recrutement / RH">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
          Recrutement / RH
        </div>
        <div class="multi-box" data-group="role" data-value="Stratégie / pilotage">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          Stratégie / pilotage
        </div>
        <div class="multi-box" data-group="role" data-value="Communication / marketing">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></span>
          Communication / marketing
        </div>
      </div>
      <p class="q-hint">Sélectionnez une ou plusieurs réponses.</p>
    </div>

    <!-- Q6 : Temps perdu en admin -->
    <div class="q-block" data-q="6">
      <h3><span class="q-num">6</span> Combien d'heures par semaine passez-vous sur des tâches administratives ou répétitives dont vous vous passeriez volontiers ?</h3>
      <div class="q-slider-wrap">
        <div class="q-slider-val" id="admin-time-val">5h / semaine</div>
        <input type="range" class="q-slider" id="admin-time-slider" min="1" max="30" step="1" value="5">
        <div class="q-slider-labels">
          <span>1h</span>
          <span>15h</span>
          <span>30h+</span>
        </div>
      </div>
    </div>

    <!-- Q7 : Appétence pour le numérique -->
    <div class="q-block" data-q="7">
      <h3><span class="q-num">7</span> Comment vous sentez-vous avec les outils numériques ?</h3>
      <div class="opt-grid col2">
        <div class="opt-box" data-group="digital" data-value="Je préfère le papier et les habitudes existantes">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>
          Je préfère le papier
        </div>
        <div class="opt-box" data-group="digital" data-value="J'utilise quelques outils basiques (Excel, email…)">
          <span class="ico"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span>
          Quelques outils basiques
        </div>
        <div class="opt-box" data-group="digital" data-value="J'utilise plusieurs logiciels métier">
          <span class="ico"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
          Plusieurs logiciels métier
        </div>
        <div class="opt-box" data-group="digital" data-value="Je suis curieux des nouvelles technologies">
          <span class="ico"><svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span>
          Curieux des technos
        </div>
      </div>
    </div>

    <!-- Q8 : Objectif principal -->
    <div class="q-block" data-q="8">
      <h3><span class="q-num">8</span> Si l'IA vous libérait du temps, qu'en feriez-vous en priorité ?</h3>
      <div class="multi-grid">
        <div class="multi-box" data-group="goal" data-value="Développer mon activité commerciale">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          Développer mon activité
        </div>
        <div class="multi-box" data-group="goal" data-value="Améliorer la qualité de mon service">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
          Améliorer mon service
        </div>
        <div class="multi-box" data-group="goal" data-value="Retrouver du temps personnel / vie de famille">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
          Temps perso / famille
        </div>
        <div class="multi-box" data-group="goal" data-value="Réduire mon stress et ma charge mentale">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
          Réduire mon stress
        </div>
        <div class="multi-box" data-group="goal" data-value="Embaucher ou déléguer plus facilement">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          Embaucher / déléguer
        </div>
        <div class="multi-box" data-group="goal" data-value="Réduire mes coûts de fonctionnement">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/></svg></span>
          Réduire mes coûts
        </div>
      </div>
    </div>

    <!-- ════════════════════════════════════════
         SECTION 3 · L'ÉQUIPE & LES TÂCHES
         ════════════════════════════════════════ -->
    <div class="q-section-title">👥 Votre équipe &amp; vos tâches</div>

    <!-- Q9 : Pain points -->
    <div class="q-block" data-q="9">
      <h3><span class="q-num">9</span> Quelles tâches prennent le plus de temps dans votre entreprise ?<br><small style="font-size:13px;font-weight:400;color:var(--ink-3)">Sélectionnez tout ce qui s'applique</small></h3>
      <div class="multi-grid" id="grid-pain">
        <div class="multi-box" data-group="pain" data-value="Emails et communication">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
          Emails et communication
        </div>
        <div class="multi-box" data-group="pain" data-value="Devis et facturation">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
          Devis et facturation
        </div>
        <div class="multi-box" data-group="pain" data-value="Comptabilité et saisie">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          Comptabilité et saisie
        </div>
        <div class="multi-box" data-group="pain" data-value="Réseaux sociaux et marketing">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></span>
          Réseaux sociaux
        </div>
        <div class="multi-box" data-group="pain" data-value="Prise de rendez-vous">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          Prise de rendez-vous
        </div>
        <div class="multi-box" data-group="pain" data-value="Gestion des commandes / stocks">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></span>
          Commandes / stocks
        </div>
        <div class="multi-box" data-group="pain" data-value="Rédaction de documents">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></span>
          Rédaction
        </div>
        <div class="multi-box" data-group="pain" data-value="Support client / SAV">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
          Support client / SAV
        </div>
        <div class="multi-box" data-group="pain" data-value="Recrutement et onboarding">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
          Recrutement / onboarding
        </div>
        <div class="multi-box" data-group="pain" data-value="Reporting et tableaux de bord">
          <span class="ico ico-sm"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg></span>
          Reporting / tableaux
        </div>
      </div>
    </div>

    <!-- Q10 : Résistance de l'équipe -->
    <div class="q-block" data-q="10">
      <h3><span class="q-num">10</span> Si vous avez des collaborateurs · comment perçoivent-ils les nouveaux outils numériques ?</h3>
      <div class="opt-grid col2">
        <div class="opt-box compact" data-group="team_adoption" data-value="Très réticents · ils préfèrent les habitudes actuelles">😬 Très réticents</div>
        <div class="opt-box compact" data-group="team_adoption" data-value="Prudents · à convaincre avec des preuves concrètes">🤔 Prudents à convaincre</div>
        <div class="opt-box compact" data-group="team_adoption" data-value="Ouverts · prêts à essayer si c'est simple">🙂 Ouverts si c'est simple</div>
        <div class="opt-box compact" data-group="team_adoption" data-value="Enthousiastes · demandeurs de nouveautés">🚀 Enthousiastes</div>
        <div class="opt-box compact" data-group="team_adoption" data-value="Je suis seul(e)">👤 Je suis seul(e)</div>
      </div>
    </div>

    <!-- Q11 : Outils déjà utilisés -->
    <div class="q-block" data-q="11">
      <h3><span class="q-num">11</span> Quels outils / logiciels utilisez-vous déjà ?<br><small style="font-size:13px;font-weight:400;color:var(--ink-3)">Sélectionnez tout ce qui s'applique</small></h3>
      <div class="multi-grid">
        <div class="multi-box" data-group="tools" data-value="Suite Office / Google Workspace">📊 Office / Google Workspace</div>
        <div class="multi-box" data-group="tools" data-value="Logiciel de comptabilité (Sage, QuickBooks…)">📒 Logiciel compta</div>
        <div class="multi-box" data-group="tools" data-value="CRM (Salesforce, HubSpot, Pipedrive…)">🤝 CRM</div>
        <div class="multi-box" data-group="tools" data-value="E-commerce (Shopify, WooCommerce…)">🛒 E-commerce</div>
        <div class="multi-box" data-group="tools" data-value="Outil de réservation (Calendly, Doctolib…)">📅 Réservation en ligne</div>
        <div class="multi-box" data-group="tools" data-value="Logiciel RH (Lucca, Payfit…)">👥 Logiciel RH</div>
        <div class="multi-box" data-group="tools" data-value="Outil marketing (Mailchimp, Brevo…)">📧 Emailing / marketing</div>
        <div class="multi-box" data-group="tools" data-value="Aucun outil particulier">❌ Aucun outil particulier</div>
      </div>
    </div>

    <!-- Q12 : Email -->
    <div class="q-block" data-q="12">
      <h3><span class="q-num">12</span> Vos coordonnées pour recevoir vos résultats</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
          <label style="font-size:13px;font-weight:600;color:var(--ink-2);display:block;margin-bottom:6px">Prénom</label>
          <input type="text" id="q-prenom" class="q-input" placeholder="Marie" autocomplete="given-name">
        </div>
        <div>
          <label style="font-size:13px;font-weight:600;color:var(--ink-2);display:block;margin-bottom:6px">Nom</label>
          <input type="text" id="q-nom" class="q-input" placeholder="Dupont" autocomplete="family-name">
        </div>
      </div>
      <label style="font-size:13px;font-weight:600;color:var(--ink-2);display:block;margin-bottom:6px">Email professionnel</label>
      <input type="email" id="q-email" class="email-input" placeholder="marie@monentreprise.fr" autocomplete="email"/>
      <p class="q-hint">Utilisé uniquement pour votre audit personnalisé. Pas de spam, désinscription en 1 clic.</p>
    </div>

    <button class="submit-btn" id="q-submit" onclick="submitQuestionnaire()">
      Lancer mon audit personnalisé →
    </button>
  </div>

  <!-- Loading -->
  <div class="loading-state" id="q-loading">
    <div style="width:60px;height:60px;border-radius:50%;border:4px solid rgba(16,185,129,0.15);border-top-color:#10B981;animation:spin 900ms linear infinite;margin:0 auto 20px"></div>
    <p style="font-size:18px;font-weight:600;color:var(--ink-2);margin-bottom:8px">Analyse en cours…</p>
    <p style="color:var(--ink-3);font-size:14px">Notre IA prépare vos recommandations personnalisées</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script>
/* ── Sélection radio (1 seul choix par groupe) ── */
document.querySelectorAll('.opt-box[data-group]').forEach(box => {
  box.addEventListener('click', () => {
    const group = box.dataset.group;
    document.querySelectorAll(`.opt-box[data-group="${group}"]`).forEach(b => b.classList.remove('selected'));
    box.classList.add('selected');
    updateProgress();
  });
});

/* ── Sélection multi ── */
document.querySelectorAll('.multi-box').forEach(box => {
  box.addEventListener('click', () => {
    box.classList.toggle('selected');
    updateProgress();
  });
});

/* ── Slider admin time ── */
const slider    = document.getElementById('admin-time-slider');
const sliderVal = document.getElementById('admin-time-val');
if (slider) {
  slider.addEventListener('input', () => {
    const v = parseInt(slider.value);
    sliderVal.textContent = v + 'h / semaine';
    updateProgress();
  });
}

/* ── Progress bar ── */
const TOTAL_Q = 12;
function updateProgress() {
  const answered = countAnswered();
  const pct = Math.round(answered / TOTAL_Q * 100);
  document.getElementById('q-progress').style.width = pct + '%';
  document.getElementById('q-progress-label').textContent = answered + ' / ' + TOTAL_Q + ' questions';
}
function countAnswered() {
  const radios  = ['sector','size','age','revenue','digital','team_adoption'];
  const multis  = ['role','goal','pain','tools'];
  let count = 0;
  radios.forEach(g  => { if (document.querySelector(`.opt-box[data-group="${g}"].selected`)) count++; });
  multis.forEach(g  => { if (document.querySelectorAll(`.multi-box[data-group="${g}"].selected`).length) count++; });
  if (slider && parseInt(slider.value) > 1) count++;  // slider
  const email = (document.getElementById('q-email')?.value || '').trim();
  if (email.includes('@')) count++;
  return Math.min(count, TOTAL_Q);
}

/* ── Soumission ── */
async function submitQuestionnaire() {
  const email  = document.getElementById('q-email').value.trim();
  const prenom = document.getElementById('q-prenom').value.trim();
  const nom    = document.getElementById('q-nom').value.trim();

  if (!email || !email.includes('@')) {
    document.getElementById('q-email').focus();
    document.getElementById('q-email').style.borderColor = '#EF4444';
    return;
  }

  const getRadio  = g => document.querySelector(`.opt-box[data-group="${g}"].selected`)?.dataset.value || '';
  const getMulti  = g => [...document.querySelectorAll(`.multi-box[data-group="${g}"].selected`)].map(b => b.dataset.value).join(', ');

  const sector      = getRadio('sector');
  const size        = getRadio('size');
  const age         = getRadio('age');
  const revenue     = getRadio('revenue');
  const digital     = getRadio('digital');
  const teamAdopt   = getRadio('team_adoption');
  const pain        = getMulti('pain');
  const role        = getMulti('role');
  const goal        = getMulti('goal');
  const tools       = getMulti('tools');
  const adminTime   = slider ? slider.value + 'h/semaine' : '';

  if (!sector) {
    document.getElementById('grid-sector').scrollIntoView({behavior:'smooth', block:'center'});
    document.getElementById('grid-sector').style.outline = '2px solid #EF4444';
    setTimeout(() => document.getElementById('grid-sector').style.outline = '', 2000);
    return;
  }

  document.getElementById('questionnaire').style.display = 'none';
  document.getElementById('q-loading').style.display = 'block';

  const answers = {
    'Secteur'                          : sector,
    'Taille'                           : size,
    'Ancienneté entreprise'            : age,
    'Chiffre d\'affaires'              : revenue,
    'Rôles du dirigeant'               : role,
    'Temps admin/semaine'              : adminTime,
    'Appétence numérique'              : digital,
    'Objectifs prioritaires'           : goal,
    'Tâches chronophages'              : pain,
    'Adoption équipe'                  : teamAdopt,
    'Outils déjà utilisés'            : tools,
  };

  try {
    const lead = await ABYS.api('leads.php', {
      action: 'create', url: '', email,
      sector, source: 'questionnaire'
    });
    ABYS.store('lead_id', lead.lead_id);
    if (prenom) ABYS.store('prenom', prenom);
    if (nom)    ABYS.store('nom', nom);
    await Audit.runFromQuestionnaire(answers);
  } catch (err) {
    document.getElementById('questionnaire').style.display = 'block';
    document.getElementById('q-loading').style.display = 'none';
    alert('Erreur : ' + err.message);
  }
}
</script>
