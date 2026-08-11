<?php
$page_title = 'Analyse en cours · ABYS AI';
$extra_js   = ['/assets/js/audit.js'];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

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
  width: 300vmax; height: 300vmax;
  transform: translate(-50%, -50%);
  border-radius: 50%;              /* cercle : aucun bord droit possible */
  z-index: 0;
  pointer-events: none;
  transform-origin: center;
  will-change: transform;
  background: repeating-conic-gradient(
    from 0deg,
    transparent 0deg 24deg,
    rgba(52,211,153,0.00) 26deg,
    rgba(52,211,153,0.34) 30deg,
    rgba(14,165,233,0.38) 34deg,
    rgba(16,185,129,0.34) 38deg,
    rgba(52,211,153,0.00) 42deg,
    transparent 44deg 60deg
  );
  filter: blur(4px);
  animation: beam-spin-cosmos 20s linear infinite;
  /* les rayons partent près de la boîte et s'estompent en douceur vers l'extérieur, jamais de bord franc */
  -webkit-mask-image: radial-gradient(circle at center, transparent 120px, #000 360px, rgba(0,0,0,0.6) 60vmax, transparent 95vmax);
  mask-image: radial-gradient(circle at center, transparent 120px, #000 360px, rgba(0,0,0,0.6) 60vmax, transparent 95vmax);
}
@keyframes beam-spin-cosmos { to { transform: translate(-50%, -50%) rotate(360deg); } }
@media (prefers-reduced-motion: reduce) {
  .beam-cosmos { animation: none; }
}
</style>

<!-- Contenu principal centré -->
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;position:relative;z-index:1">

  <!-- Phase chargement -->
  <div class="beam-ring" id="beam-ring-wrap">
  <div class="beam-inner" id="phase-loading">

    <!-- Spinner circulaire vert 72px -->
    <div style="position:relative;width:72px;height:72px;margin:0 auto 28px">
      <svg width="72" height="72" viewBox="0 0 72 72" style="animation:spin 1s linear infinite;display:block">
        <circle cx="36" cy="36" r="30" fill="none" stroke="rgba(16,185,129,0.15)" stroke-width="5"/>
        <circle cx="36" cy="36" r="30" fill="none" stroke="#10B981" stroke-width="5"
                stroke-linecap="round" stroke-dasharray="60 130" stroke-dashoffset="0"/>
      </svg>
    </div>

    <!-- Titre URL -->
    <h2 style="font-size:28px;font-weight:300;letter-spacing:-0.03em;margin:0 0 10px;line-height:1.3">
      Analyse de <strong id="display-url" style="font-weight:700;color:#10B981">votre site</strong> en cours…
    </h2>

    <!-- Sous-titre rotatif -->
    <p id="audit-subtitle" style="font-size:15px;color:var(--ink-3,#6B7280);margin:0 0 32px;transition:opacity 300ms ease;min-height:22px">
      Notre IA examine votre activité et calcule vos opportunités.
    </p>

    <!-- Étapes -->
    <div style="display:flex;flex-direction:column;gap:12px;text-align:left">

      <div id="step-0" style="display:flex;align-items:center;gap:12px;opacity:0.3;transition:opacity 400ms,transform 400ms;transform:translateX(-4px)">
        <div id="step-icon-0" style="width:24px;height:24px;border-radius:50%;border:2px solid #D1D5DB;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;transition:border-color 300ms,color 300ms">○</div>
        <span style="font-size:14px;color:var(--ink-3,#6B7280)">Lecture de votre site web</span>
      </div>

      <div id="step-1" style="display:flex;align-items:center;gap:12px;opacity:0.3;transition:opacity 400ms,transform 400ms;transform:translateX(-4px)">
        <div id="step-icon-1" style="width:24px;height:24px;border-radius:50%;border:2px solid #D1D5DB;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;transition:border-color 300ms,color 300ms">○</div>
        <span style="font-size:14px;color:var(--ink-3,#6B7280)">Identification du secteur</span>
      </div>

      <div id="step-2" style="display:flex;align-items:center;gap:12px;opacity:0.3;transition:opacity 400ms,transform 400ms;transform:translateX(-4px)">
        <div id="step-icon-2" style="width:24px;height:24px;border-radius:50%;border:2px solid #D1D5DB;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;transition:border-color 300ms,color 300ms">○</div>
        <span style="font-size:14px;color:var(--ink-3,#6B7280)">Calcul des opportunités IA</span>
      </div>

      <div id="step-3" style="display:flex;align-items:center;gap:12px;opacity:0.3;transition:opacity 400ms,transform 400ms;transform:translateX(-4px)">
        <div id="step-icon-3" style="width:24px;height:24px;border-radius:50%;border:2px solid #D1D5DB;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;transition:border-color 300ms,color 300ms">○</div>
        <span style="font-size:14px;color:var(--ink-3,#6B7280)">Simulation des gains temps/argent</span>
      </div>

      <div id="step-4" style="display:flex;align-items:center;gap:12px;opacity:0.3;transition:opacity 400ms,transform 400ms;transform:translateX(-4px)">
        <div id="step-icon-4" style="width:24px;height:24px;border-radius:50%;border:2px solid #D1D5DB;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;transition:border-color 300ms,color 300ms">○</div>
        <span style="font-size:14px;color:var(--ink-3,#6B7280)">Génération du rapport</span>
      </div>

    </div>
  </div>
  </div><!-- /beam-inner -->
  </div><!-- /beam-ring -->

  <!-- Phase erreur (masquée initialement) -->
  <div id="phase-error" style="display:none;background:rgba(255,255,255,0.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-radius:24px;padding:48px;max-width:480px;width:100%;text-align:center;box-shadow:0 8px 40px rgba(0,0,0,0.10),0 1px 3px rgba(0,0,0,0.06)">
    <div style="font-size:48px;margin-bottom:20px;line-height:1">⚠️</div>
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

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Script inline APRÈS footer.php → app.js et audit.js sont déjà chargés -->
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

/* Étapes animées */
const delays = [400, 1800, 3600, 5800, 8000];
delays.forEach((d, i) => {
  setTimeout(() => {
    const el   = document.getElementById('step-' + i);
    const icon = document.getElementById('step-icon-' + i);
    if (el)   { el.style.opacity = '1'; el.style.transform = 'translateX(0)'; }
    if (icon) { icon.textContent = '●'; icon.style.borderColor = '#10B981'; icon.style.color = '#10B981'; }
  }, d);
});

function log(msg) { /* debug supprimé */ }

/* Fonction principale d'audit */
async function runAudit() {
  try {
    const cleanUrl = ABYS.cleanUrl(url);

    log('Création du profil…');
    const lead = await ABYS.api('leads.php', { action: 'create', url: cleanUrl, source: 'url' });
    ABYS.store('lead_id',   lead.lead_id);
    ABYS.store('audit_url', cleanUrl);

    log('Lecture de votre site…');
    let scrapeData = null;
    try {
      const scrape = await ABYS.api('scrape.php', { url: cleanUrl });
      if (scrape && scrape.success) scrapeData = scrape;
    } catch (_) { /* ignoré : scrape optionnel */ }

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
      log('✅ Redirection vers les résultats…');
      ABYS.store('audit_result', analysis.audit);
      ABYS.store('audit_id',    analysis.audit_id || 0);
      if (scrapeData) {
        ABYS.store('scrape_title', scrapeData.title || '');
        ABYS.store('scrape_h1',    scrapeData.h1    || '');
      }
      clearInterval(msgInterval);
      window.location.href = '/audit-resultats.php';
    } else {
      throw new Error(analysis.error || 'Réponse invalide du serveur');
    }

  } catch (err) {
    clearInterval(msgInterval);
    log('❌ ' + err.message);
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
