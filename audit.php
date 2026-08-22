<?php
$page_title = 'Analyse en cours · ABYS AI';
$extra_js   = ['/assets/js/audit.js'];
include __DIR__ . '/includes/head.php';
// PLEIN ÉCRAN VOLONTAIRE : pas de nav, pas de footer, aucun lien cliquable
// pendant le moment des faisceaux : on ne veut pas perdre les visiteurs impatients.
?>
<style>
  html, body { margin: 0; padding: 0; background: #F0FDF8; }
  body { overflow-x: hidden; }
</style>


<style>
/* ══════ Pile de blocs · chaque étape terminée monte d'un cran ══════ */
@keyframes ld-tour { to { --beam-angle: 360deg; } }

.ld-deck { position:relative; width:100%; height:132px;
  transition:height .65s cubic-bezier(.22,1,.36,1); }

.ld-panel { position:absolute; left:0; right:0; bottom:0; height:120px; z-index:1;
  background:#fff; border:1px solid #E5E7EB; border-radius:22px; padding:18px 22px;
  text-align:left; transform-origin:center bottom; will-change:transform, opacity;
  box-shadow:0 18px 40px -26px rgba(2,30,20,.5);
  opacity:0; transform:translateY(40px) scale(.95); pointer-events:none;
  transition:transform .68s cubic-bezier(.22,1,.36,1), opacity .5s ease,
             box-shadow .5s ease, border-color .4s ease; }

/* Rang 0 : le bloc en cours, en pleine lumière */
.ld-panel.on { opacity:1; transform:translateY(0) scale(1); z-index:6; height:170px;
  border-color:transparent; box-shadow:0 24px 50px -28px rgba(16,185,129,.75); }
.ld-panel.on::before { content:''; position:absolute; inset:-2px; border-radius:24px; z-index:3;
  padding:2px; pointer-events:none;
  background:conic-gradient(from var(--beam-angle),
    transparent 0deg, transparent 200deg, rgba(52,211,153,.35) 235deg,
    #34D399 258deg, #0EA5E9 272deg, #10B981 285deg, rgba(16,185,129,.25) 305deg, transparent 330deg);
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
          mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude;
  animation:ld-tour 2.8s linear infinite; }

/* Rangs supérieurs : la pile recule, seul le bandeau du haut dépasse */
.ld-panel[data-rang="1"] { opacity:1;   transform:translateY(-100px) scale(.968); }
.ld-panel[data-rang="2"] { opacity:.82; transform:translateY(-146px) scale(.936); }
.ld-panel[data-rang="3"] { opacity:.6;  transform:translateY(-192px) scale(.904); }
.ld-panel[data-rang="4"] { opacity:.36; transform:translateY(-238px) scale(.872); }
.ld-panel[data-rang="5"] { opacity:0;   transform:translateY(-284px) scale(.84); }
.ld-panel.pousse { animation:ld-pousse .55s cubic-bezier(.22,1,.36,1); }
@keyframes ld-pousse { 0%,100% { } 40% { margin-bottom:7px; } }

.ld-top { display:flex; align-items:center; gap:11px; }
.ld-ic { width:26px; height:26px; border-radius:50%; flex-shrink:0; display:grid; place-items:center;
  border:2px solid #D1D5DB; color:#9CA3AF; transition:border-color .35s, color .35s, background .35s; }
.ld-ic svg { width:13px; height:13px; }
.ld-panel.on .ld-ic { border-color:#10B981; color:#10B981; }
.ld-panel.fait .ld-ic { border-color:#10B981; background:#10B981; color:#fff; }
.ld-spin { width:12px; height:12px; border-radius:50%; border:2px solid rgba(16,185,129,.25);
  border-top-color:#10B981; animation:ld-spin .8s linear infinite; }
@keyframes ld-spin { to { transform:rotate(360deg); } }

.ld-t { flex:1; min-width:0; font-size:14.5px; font-weight:650; color:#0A1F1A; letter-spacing:-.01em; }
.ld-detail { font-size:12px; font-weight:650; color:#059669; background:rgba(16,185,129,.10);
  border:1px solid rgba(16,185,129,.22); border-radius:20px; padding:3px 10px;
  max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
  opacity:0; transform:translateX(6px); transition:opacity .4s ease .05s, transform .4s cubic-bezier(.22,1,.36,1) .05s; }
.ld-detail.vu { opacity:1; transform:none; }

.ld-main { overflow:hidden; max-height:0; opacity:0;
  transition:max-height .6s cubic-bezier(.22,1,.36,1), opacity .4s ease; }
.ld-panel.on .ld-main { max-height:130px; opacity:1; }
.ld-panel .ld-main { pointer-events:none; }
.ld-p { font-size:13.5px; line-height:1.6; color:#6B7280; margin:12px 0 0; }

.ld-lines { display:flex; flex-direction:column; gap:7px; margin-top:13px; }
.ld-lines i { display:block; height:7px; border-radius:4px;
  background:linear-gradient(90deg, #EDF6F2 25%, #D6EFE4 42%, #EDF6F2 60%);
  background-size:280% 100%; animation:ld-scan 1.5s ease-in-out infinite; }
.ld-lines i:nth-child(1) { width:92%; animation-delay:0s; }
.ld-lines i:nth-child(2) { width:76%; animation-delay:.18s; }
.ld-lines i:nth-child(3) { width:58%; animation-delay:.36s; }
@keyframes ld-scan { 0% { background-position:120% 0; } 100% { background-position:-40% 0; } }

@media (max-width:520px) {
  .ld-panel { height:112px; padding:16px 18px; border-radius:20px; }
  .ld-panel.on { height:172px; }
  .ld-detail { max-width:96px; }
}
@media (prefers-reduced-motion: reduce) {
  .ld-panel { transition:opacity .3s ease; }
  .ld-panel.pousse { animation:none; }
  .ld-lines i { animation:none; }
}
</style>

<!-- Canvas animation de fond -->
<canvas id="bg-canvas" style="position:fixed;inset:0;width:100%;height:100%;z-index:-1;display:block"></canvas>

<!-- Faisceaux infinis : halo de rayons qui tournent et sortent de tous les bords -->
<div class="beam-cosmos"></div>

<style>
/* ── Rotating emerald/turquoise beam around loading box ── */
@property --beam-angle {
  syntax: '<angle>';
  inherits: false;
  initial-value: 0deg;
}
.beam-ring {
  position: relative;
  max-width: 486px;
  width: 100%;
  border-radius: 27px;
  padding: 3px;
  background: transparent;
  /* Fallback static border */
  box-shadow: 0 0 0 2px rgba(16,185,129,0.25);
}
.beam-ring::before {
  content: '';
  position: absolute;
  top: 50%; left: 50%;
  width: 200%; height: 200%;
  transform: translate(-50%, -50%) rotate(0deg);
  background: conic-gradient(
    transparent 0deg,
    transparent 200deg,
    rgba(52,211,153,0.35) 235deg,
    #34D399 258deg,
    #0EA5E9 272deg,
    #10B981 285deg,
    rgba(16,185,129,0.25) 305deg,
    transparent 330deg,
    transparent 360deg
  );
  animation: beam-spin 2.8s linear infinite;
  z-index: 0;
  border-radius: 0;
}
@keyframes beam-spin {
  to { transform: translate(-50%, -50%) rotate(360deg); }
}
/* Second beam going the other way (subtle halo) */
.beam-ring::after {
  content: '';
  position: absolute;
  top: 50%; left: 50%;
  width: 200%; height: 200%;
  transform: translate(-50%, -50%) rotate(0deg);
  background: conic-gradient(
    transparent 0deg,
    transparent 20deg,
    rgba(14,165,233,0.15) 40deg,
    rgba(52,211,153,0.1) 55deg,
    transparent 70deg,
    transparent 360deg
  );
  animation: beam-spin-reverse 4.5s linear infinite;
  z-index: 0;
}
@keyframes beam-spin-reverse {
  to { transform: translate(-50%, -50%) rotate(-360deg); }
}
.beam-inner {
  position: relative;
  z-index: 1;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 24px;
  padding: 48px;
  text-align: center;
  box-shadow: 0 8px 40px rgba(0,0,0,0.10), 0 1px 3px rgba(0,0,0,0.06);
}
@keyframes spin { to { transform: rotate(360deg); } }

/* On retire les anciens faisceaux carrés autour de la boîte (source des bords droits) */
.beam-ring::before, .beam-ring::after { display: none !important; }

/* ── Faisceaux infinis : halo de rayons qui tournent et sortent de tous les bords ── */
.beam-cosmos {
  position: fixed;
  top: 50%; left: 50%;
  width: 200vmax; height: 200vmax;
  margin: -100vmax 0 0 -100vmax;      /* centrage sans translate : le translate reste libre pour l'animation GPU */
  border-radius: 50%;                  /* cercle : aucun bord droit possible */
  z-index: 0;
  pointer-events: none;
  transform-origin: center center;
  will-change: transform;
  backface-visibility: hidden;
  /* rayons doux obtenus par les transitions du dégradé (pas de filtre flou = fluide) */
  background: repeating-conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 20deg,
    rgba(52,211,153,0.20) 30deg,
    rgba(14,165,233,0.26) 36deg,
    rgba(52,211,153,0.20) 42deg,
    transparent 52deg,
    transparent 60deg
  );
  -webkit-mask-image: radial-gradient(circle at center, transparent 130px, #000 380px, #000 52%, transparent 82%);
  mask-image: radial-gradient(circle at center, transparent 130px, #000 380px, #000 52%, transparent 82%);
  animation: beam-spin-cosmos 26s linear infinite;
}
@keyframes beam-spin-cosmos {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}
@media (prefers-reduced-motion: reduce) {
  .beam-cosmos { animation: none; }
}
</style>

<!-- Contenu principal centré -->
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;position:relative;z-index:1">

  <!-- Phase chargement -->
  <div id="phase-loading" style="width:100%;max-width:486px">

    <div class="ld-head">
      <div style="position:relative;width:64px;height:64px;margin:0 auto 22px">
        <svg width="64" height="64" viewBox="0 0 72 72" style="animation:spin 1s linear infinite;display:block">
          <circle cx="36" cy="36" r="30" fill="none" stroke="rgba(16,185,129,0.15)" stroke-width="5"/>
          <circle cx="36" cy="36" r="30" fill="none" stroke="#10B981" stroke-width="5"
                  stroke-linecap="round" stroke-dasharray="60 130" stroke-dashoffset="0"/>
        </svg>
      </div>
      <h2 style="font-size:26px;font-weight:300;letter-spacing:-0.03em;margin:0 0 8px;line-height:1.3">
        Analyse de <strong id="display-url" style="font-weight:700;color:#10B981">votre site</strong> en cours…
      </h2>
      <p id="audit-subtitle" style="font-size:14.5px;color:var(--ink-3,#6B7280);margin:0 0 30px;transition:opacity 300ms ease;min-height:21px">
        Milo examine votre activité et calcule vos opportunités.
      </p>
    </div>

    <!-- Chaque étape est un bloc entier. Terminée, elle monte d'un cran. -->
    <div class="ld-deck" id="ld-deck" aria-live="polite">
      <div class="ld-panel" id="ld-0" data-titre="Lecture de votre site"
           data-texte="Je parcours vos pages pour comprendre ce que vous faites vraiment."></div>
      <div class="ld-panel" id="ld-1" data-titre="Identification de votre métier"
           data-texte="Je situe votre activité, pour ne pas vous proposer des outils hors sujet."></div>
      <div class="ld-panel" id="ld-2" data-titre="Sélection des outils"
           data-texte="Je croise votre profil avec plus de 300 outils et j'écarte ceux qui ne tiennent pas la route."></div>
      <div class="ld-panel" id="ld-3" data-titre="Calcul de vos gains"
           data-texte="Je chiffre le temps récupérable chaque semaine, puis je le convertis en euros."></div>
      <div class="ld-panel" id="ld-4" data-titre="Rédaction de votre plan"
           data-texte="J'ordonne les priorités et j'écris vos recommandations."></div>
    </div>

  </div>

  <!-- Phase erreur (masquée initialement) -->
  <div id="phase-error" style="display:none;background:rgba(255,255,255,0.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-radius:24px;padding:48px;max-width:480px;width:100%;text-align:center;box-shadow:0 8px 40px rgba(0,0,0,0.10),0 1px 3px rgba(0,0,0,0.06)">
    <div style="width:52px;height:52px;border-radius:50%;margin:0 auto 20px;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 8v5" stroke="#059669" stroke-width="2.2" stroke-linecap="round"/>
        <circle cx="12" cy="16.5" r="1.3" fill="#059669"/>
        <circle cx="12" cy="12" r="9" stroke="#10B981" stroke-width="2"/>
      </svg>
    </div>
    <h2 style="font-size:22px;font-weight:700;margin:0 0 12px;color:#111827">Analyse à finaliser</h2>
    <p style="font-size:15px;color:var(--ink-3,#6B7280);margin:0 0 28px;line-height:1.6">
      Nous n'avons pas pu terminer l'analyse automatique de votre site.<br>
      Répondez à quelques questions rapides pour obtenir votre audit.
    </p>
    <a href="/audit-questionnaire.php" class="btn btn-primary" style="display:inline-block;background:#10B981;color:#fff;padding:14px 32px;border-radius:12px;font-weight:600;font-size:15px;text-decoration:none;transition:background 150ms" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
      Continuer avec le questionnaire →
    </a>
  </div>

</div>

<!-- Plein écran : ni nav ni footer. On charge les scripts explicitement (ce que faisait footer.php). -->
<script src="<?= function_exists('abys_asset') ? abys_asset('/assets/js/app.js') : '/assets/js/app.js' ?>"></script>
<?php if (!empty($extra_js)): foreach ($extra_js as $js): ?>
<script src="<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; endif; ?>

<!-- Script inline APRÈS app.js/audit.js déjà chargés -->
<script>
/* ─── Canvas : particules réseau neuronal ─── */
(function () {
  const canvas = document.getElementById('bg-canvas');
  const ctx    = canvas.getContext('2d');

  const PARTICLE_COUNT  = 55;
  const CONNECTION_DIST = 140;
  const SPEED           = 0.4;

  let W, H, particles;

  function resize() {
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
  }

  function randomParticle() {
    return {
      x:  Math.random() * W,
      y:  Math.random() * H,
      vx: (Math.random() - 0.5) * SPEED * 2,
      vy: (Math.random() - 0.5) * SPEED * 2,
      r:  Math.random() * 2.2 + 1.2,
      /* teinte : vert #10B981 ou bleu #0EA5E9 */
      green: Math.random() > 0.42,
    };
  }

  function init() {
    resize();
    particles = Array.from({ length: PARTICLE_COUNT }, randomParticle);
  }

  function draw() {
    ctx.clearRect(0, 0, W, H);

    /* Fond très légèrement teinté */
    ctx.fillStyle = '#F0FDF8';
    ctx.fillRect(0, 0, W, H);

    /* Connexions */
    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const a = particles[i], b = particles[j];
        const dx = a.x - b.x, dy = a.y - b.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < CONNECTION_DIST) {
          const alpha = (1 - dist / CONNECTION_DIST) * 0.45;
          /* couleur de la ligne : mélange des deux noeuds */
          const color = (a.green && b.green)
            ? `rgba(16,185,129,${alpha})`
            : (!a.green && !b.green)
              ? `rgba(14,165,233,${alpha})`
              : `rgba(11,175,181,${alpha})`;
          ctx.beginPath();
          ctx.moveTo(a.x, a.y);
          ctx.lineTo(b.x, b.y);
          ctx.strokeStyle = color;
          ctx.lineWidth   = 1;
          ctx.stroke();
        }
      }
    }

    /* Particules */
    for (const p of particles) {
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = p.green ? 'rgba(16,185,129,0.75)' : 'rgba(14,165,233,0.75)';
      ctx.fill();
    }
  }

  function update() {
    for (const p of particles) {
      p.x += p.vx;
      p.y += p.vy;
      if (p.x < -20)  p.x = W + 20;
      if (p.x > W+20) p.x = -20;
      if (p.y < -20)  p.y = H + 20;
      if (p.y > H+20) p.y = -20;
    }
  }

  function loop() {
    update();
    draw();
    requestAnimationFrame(loop);
  }

  window.addEventListener('resize', resize);
  init();
  loop();
})();

/* ─── Audit logic ─── */
const params = new URLSearchParams(window.location.search);
const url    = params.get('url') || ABYS.get('audit_url') || '';
document.getElementById('display-url').textContent = url || 'votre site';

/* Messages rotatifs toutes les 3 s */
const subtitleEl = document.getElementById('audit-subtitle');
const messages = [
  'Notre IA examine votre activité et calcule vos opportunités.',
  'Identification de votre secteur et de vos processus clés…',
  'Calcul des gains de temps et d\'argent potentiels…',
  'Sélection des outils IA les plus adaptés à votre métier…',
  'Finalisation de votre rapport personnalisé…',
  'Presque prêt ! Vos résultats arrivent…',
];
let msgIdx = 0;
const msgInterval = setInterval(() => {
  msgIdx = (msgIdx + 1) % messages.length;
  subtitleEl.style.opacity = '0';
  setTimeout(() => {
    subtitleEl.textContent  = messages[msgIdx];
    subtitleEl.style.opacity = '1';
  }, 300);
}, 3000);

/* ══════ La pile de blocs ══════
   Une étape se termine, son bloc monte d'un cran, le suivant prend sa place.
   Les minuteurs donnent le rythme ; dès qu'un vrai résultat arrive, il prend la main. */
const CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9.5 17 4 11.5"/></svg>';
const PAS   = 44;
const BASE  = 132;
const deck    = document.getElementById('ld-deck');
const panneaux = [0, 1, 2, 3, 4].map(i => document.getElementById('ld-' + i));
let actif = -1;

panneaux.forEach(p => {
  if (!p) return;
  p.innerHTML =
    '<div class="ld-top">' +
      '<span class="ld-ic"><span class="ld-spin"></span></span>' +
      '<span class="ld-t">' + p.dataset.titre + '</span>' +
      '<span class="ld-detail"></span>' +
    '</div>' +
    '<div class="ld-main">' +
      '<p class="ld-p">' + p.dataset.texte + '</p>' +
      '<div class="ld-lines"><i></i><i></i><i></i></div>' +
    '</div>';
});

const PEEK = 46;   /* hauteur du bandeau qui depasse */
const ECART = 46;  /* ecart entre deux blocs empiles */
const HAUT_ACTIF = 170;

function placer() {
  const empiles = Math.max(0, Math.min(actif, 4));
  if (deck) deck.style.height = (HAUT_ACTIF + (empiles ? PEEK + (empiles - 1) * ECART : 0)) + 'px';

  panneaux.forEach((p, i) => {
    if (!p) return;
    if (i > actif) { p.classList.remove('on'); p.removeAttribute('data-rang'); return; }
    const rang = actif - i;
    if (rang === 0) { p.classList.add('on'); p.removeAttribute('data-rang'); return; }
    p.classList.remove('on');
    p.setAttribute('data-rang', Math.min(5, rang));
  });
}

function demarrer(i) {
  if (i <= actif || !panneaux[i]) return;
  /* Le bloc precedent est termine des que le suivant commence : il porte sa coche */
  for (let k = 0; k < i; k++) {
    const q = panneaux[k];
    if (q && !q.classList.contains('fait')) {
      q.classList.add('fait');
      const ic = q.querySelector('.ld-ic');
      if (ic) ic.innerHTML = CHECK;
    }
  }
  actif = i;
  placer();
  panneaux.forEach((p, k) => {
    if (p && k < i) { p.classList.add('pousse'); setTimeout(() => p.classList.remove('pousse'), 570); }
  });
}

function terminer(i, detail) {
  const p = panneaux[i];
  if (!p) return;
  if (i > actif) demarrer(i);
  if (!p.classList.contains('fait')) {
    p.classList.add('fait');
    const ic = p.querySelector('.ld-ic');
    if (ic) ic.innerHTML = CHECK;
  }
  if (detail) {
    const d = p.querySelector('.ld-detail');
    if (d) { d.textContent = detail; d.classList.add('vu'); }
  }
  if (i + 1 < panneaux.length) setTimeout(() => demarrer(i + 1), 300);
}

const RYTHME = [300, 2600, 5200, 7600, 10200];
const minuteurs = RYTHME.map((d, i) => setTimeout(() => demarrer(i), d));
function couperRythme(aPartirDe) {
  for (let i = aPartirDe; i < minuteurs.length; i++) clearTimeout(minuteurs[i]);
}

function log(msg) { /* debug supprimé */ }

/* Fonction principale d'audit */
async function runAudit() {
  try {
    const cleanUrl = ABYS.cleanUrl(url);

    log('Création du profil…');
    const lead = await ABYS.api('leads.php', { action: 'create', url: cleanUrl, source: 'url' });
    ABYS.store('lead_id',   lead.lead_id);
    ABYS.store('audit_url', cleanUrl);
    window.ABYS && ABYS.track && ABYS.track('audit_lance');

    log('Lecture de votre site…');
    let scrapeData = null;
    try {
      const scrape = await ABYS.api('scrape.php', { url: cleanUrl });
      if (scrape && scrape.success) scrapeData = scrape;
    } catch (_) { /* ignoré : scrape optionnel */ }

    if (scrapeData) {
      const titre = (scrapeData.title || scrapeData.h1 || '').trim();
      terminer(0, titre ? 'site lu' : 'page lue');
      if (titre) {
        const d = document.getElementById('display-url');
        if (d) d.textContent = titre.length > 42 ? titre.slice(0, 42) + '…' : titre;
      }
    }

    if (!scrapeData) {
      log('Site non lisible → questionnaire');
      setTimeout(() => { window.location.href = '/audit-questionnaire.php'; }, 600);
      return;
    }

    log('Analyse IA en cours…');
    const analysis = await ABYS.api('analyze.php', {
      domain:     cleanUrl,
      scrape_data: scrapeData,
      lead_id:    lead.lead_id,
    });

    if (analysis && analysis.audit) {
      const a = analysis.audit || {};
      couperRythme(1);
      terminer(1, a.sector_label || null);
      const nbOutils = Array.isArray(a.opportunities) ? a.opportunities.length : 0;
      setTimeout(() => terminer(2, nbOutils ? nbOutils + ' outils' : null), 320);
      setTimeout(() => terminer(3, a.total_time_saved_h_week ? a.total_time_saved_h_week + ' h/sem' : null), 700);
      setTimeout(() => terminer(4, 'prêt'), 1080);
      log('Redirection vers les résultats.');
      ABYS.store('audit_result', analysis.audit);
      ABYS.store('audit_id',    analysis.audit_id || 0);
      if (scrapeData) {
        ABYS.store('scrape_title', scrapeData.title || '');
        ABYS.store('scrape_h1',    scrapeData.h1    || '');
      }
      clearInterval(msgInterval);
      setTimeout(() => { window.location.href = '/audit-resultats.php'; }, 1500);
    } else {
      throw new Error(analysis.error || 'Réponse invalide du serveur');
    }

  } catch (err) {
    clearInterval(msgInterval);
    log(err.message);
    const loading = document.getElementById('phase-loading');
    const error   = document.getElementById('phase-error');
    if (loading) loading.style.display = 'none';
    if (error)   error.style.display   = 'block';
  }
}

if (url) {
  runAudit();
} else {
  window.location.href = '/';
}
</script>

<?php echo function_exists('milo_fiche') ? milo_fiche() : ''; ?>
