<?php
$page_title = 'Votre audit IA en 2 minutes · ABYS AI';
include __DIR__ . '/includes/head.php';
// TUNNEL PLEIN ÉCRAN : pas de nav, pas de footer. Une question à la fois, posée par Milo.
?>
<style>
  html, body { margin:0; padding:0; background:#F0FDF8; }
  body { overflow-x:hidden; font-family:var(--font, -apple-system, sans-serif); }

  /* Faisceaux discrets en fond (cohérents avec le moment d'audit) */
  .qt-beams { position:fixed; inset:0; z-index:0; overflow:hidden; pointer-events:none; opacity:.5; }
  .qt-beams span { position:absolute; top:-35%; left:var(--l); width:120px; height:180%;
    transform-origin:top center; transform:rotate(var(--a)); }
  .qt-beams span::before { content:''; position:absolute; inset:0;
    background:linear-gradient(to bottom, rgba(52,211,153,.13), rgba(14,165,233,.05) 55%, transparent 80%);
    -webkit-mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
            mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
    filter:blur(8px); transform-origin:top center; will-change:transform;
    animation:qt-ray var(--d) ease-in-out var(--delay,0s) infinite alternate; }
  @keyframes qt-ray { from{ transform:rotate(calc(var(--s) * -1)); } to{ transform:rotate(var(--s)); } }
  @media (prefers-reduced-motion: reduce){ .qt-beams span::before{ animation:none; } }

  /* Barre de progression */
  .qt-top { position:fixed; top:0; left:0; right:0; z-index:10; }
  .qt-bar { height:4px; background:rgba(16,185,129,.12); }
  .qt-bar i { display:block; height:100%; width:0; background:linear-gradient(90deg,#10B981,#0EA5E9);
    transition:width .45s cubic-bezier(.3,1,.4,1); }
  .qt-meta { display:flex; justify-content:space-between; align-items:center; padding:14px 22px; }
  .qt-logo { display:flex; align-items:center; gap:8px; font-size:14px; letter-spacing:.14em; color:#0A1F1A; }
  .qt-logo b { font-weight:800; } .qt-logo i { font-style:normal; color:#10B981; font-weight:600; }
  .qt-count { font-size:12.5px; font-weight:600; color:#6B7280; letter-spacing:.04em; }

  /* Scène */
  .qt-stage { position:relative; z-index:2; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:96px 22px 60px; }
  .qt-step { width:100%; max-width:640px; opacity:0; transform:translateY(22px); pointer-events:none;
    position:absolute; transition:opacity .38s ease, transform .38s cubic-bezier(.3,1,.4,1); }
  .qt-step.on { opacity:1; transform:none; pointer-events:auto; position:relative; }
  .qt-step.out { opacity:0; transform:translateY(-22px); }

  /* Milo pose la question */
  .qt-milo { display:flex; gap:14px; align-items:flex-start; margin-bottom:26px; }
  .qt-milo img { width:52px; height:52px; border-radius:50%; border:2px solid #10B981; object-fit:cover; flex-shrink:0;
    box-shadow:0 0 0 5px rgba(16,185,129,.12); }
  .qt-q { background:#fff; border:1px solid #E5E7EB; border-radius:4px 18px 18px 18px; padding:16px 20px;
    box-shadow:0 8px 30px -14px rgba(2,30,20,.25); }
  .qt-q h2 { font-size:clamp(19px,2.6vw,23px); font-weight:700; letter-spacing:-.02em; color:#0A1F1A; margin:0; line-height:1.35; }
  .qt-q p { font-size:13.5px; color:#6B7280; margin:6px 0 0; }

  /* Options */
  .qt-opts { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:10px; }
  .qt-opts.wide { grid-template-columns:1fr; max-width:520px; }
  .qt-opt { display:flex; align-items:center; gap:12px; background:#fff; border:1.5px solid #E5E7EB; border-radius:14px;
    padding:14px 16px; cursor:pointer; font-size:14.5px; font-weight:500; color:#1F2937; text-align:left;
    transition:border-color .15s, transform .12s, box-shadow .15s; user-select:none; font-family:inherit; }
  .qt-opt:hover { border-color:rgba(16,185,129,.55); transform:translateY(-1px); box-shadow:0 8px 22px -12px rgba(16,185,129,.35); }
  .qt-opt.sel { border-color:#10B981; background:#ECFDF5; box-shadow:0 0 0 3px rgba(16,185,129,.12); }
  .qt-opt .ic { font-size:19px; flex-shrink:0; }
  .qt-opt .k { margin-left:auto; font-size:10.5px; font-weight:700; color:#9CA3AF; border:1px solid #E5E7EB;
    border-radius:6px; padding:2px 7px; flex-shrink:0; }
  .qt-opt.sel .k { color:#059669; border-color:rgba(16,185,129,.4); }

  /* Champs texte (étape finale) */
  .qt-fields { display:flex; flex-direction:column; gap:10px; max-width:440px; }
  .qt-fields input { padding:15px 16px; border-radius:13px; border:1.5px solid #E5E7EB; font-size:15.5px;
    font-family:inherit; outline:none; background:#fff; transition:border-color .15s, box-shadow .15s; }
  .qt-fields input:focus { border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,.12); }
  .qt-fields input.err { border-color:#EF4444; }

  /* Navigation bas */
  .qt-nav { display:flex; align-items:center; gap:14px; margin-top:26px; }
  .qt-back { background:none; border:none; color:#6B7280; font-size:13.5px; cursor:pointer; font-family:inherit;
    display:flex; align-items:center; gap:6px; padding:8px 4px; }
  .qt-back:hover { color:#0A1F1A; }
  .qt-next { margin-left:auto; background:linear-gradient(90deg,#059669,#0EA5E9 55%,#10B981); color:#fff; border:none;
    font-family:inherit; font-size:15px; font-weight:700; border-radius:13px; padding:14px 28px; cursor:pointer;
    box-shadow:0 10px 26px -10px rgba(16,185,129,.7); transition:transform .12s, opacity .15s; }
  .qt-next:hover { transform:translateY(-1px); }
  .qt-next:disabled { opacity:.4; cursor:not-allowed; transform:none; }
  .qt-hint { font-size:12px; color:#9CA3AF; margin-top:12px; }

  /* Écran de chargement final */
  .qt-loading { text-align:center; display:none; }
  .qt-loading img { width:76px; height:76px; border-radius:50%; border:3px solid #10B981; object-fit:cover;
    box-shadow:0 0 0 7px rgba(16,185,129,.12); margin-bottom:18px; }
  .qt-spin { width:44px; height:44px; margin:0 auto 16px; border-radius:50%;
    border:3px solid rgba(16,185,129,.15); border-top-color:#10B981; animation:qtspin .9s linear infinite; }
  @keyframes qtspin { to { transform:rotate(360deg); } }
  .qt-loading h2 { font-size:24px; font-weight:300; letter-spacing:-.03em; color:#0A1F1A; margin:0 0 8px; }
  .qt-loading h2 b { font-weight:700; }
  .qt-loading p { font-size:14px; color:#6B7280; min-height:22px; transition:opacity .3s; }
</style>

<!-- Faisceaux -->
<div class="qt-beams" aria-hidden="true">
  <span style="--a:-18deg;--l:56%;--d:9s;--s:8deg;--delay:-2s"></span>
  <span style="--a:-4deg;--l:62%;--d:7s;--s:10deg;--delay:-5s"></span>
  <span style="--a:12deg;--l:68%;--d:10.5s;--s:7deg;--delay:-1s"></span>
  <span style="--a:26deg;--l:73%;--d:8s;--s:9deg;--delay:-4s"></span>
</div>

<!-- Progression -->
<div class="qt-top">
  <div class="qt-bar"><i id="qt-bar"></i></div>
  <div class="qt-meta">
    <div class="qt-logo">
      <svg width="26" height="26" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="9" fill="#052E16"/><path d="M16 7L24.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/><path d="M16 7L7.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/><line x1="10.5" y1="19" x2="21.5" y2="19" stroke="#10B981" stroke-width="2" stroke-linecap="round"/><circle cx="16" cy="7" r="2" fill="#34D399"/></svg>
      <span><b>ABYS</b><i> AI</i></span>
    </div>
    <div class="qt-count" id="qt-count">1/10</div>
  </div>
</div>

<div class="qt-stage">
  <div id="qt-steps"></div>

  <!-- Chargement final -->
  <div class="qt-loading" id="qt-loading">
    <img src="/assets/img/milo-avatar.jpg" alt="Milo">
    <div class="qt-spin"></div>
    <h2>Merci. <b>J'analyse vos réponses.</b></h2>
    <p id="qt-loadmsg">Je croise votre profil avec plus de 300 outils IA…</p>
  </div>
</div>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/audit.js"></script>
<script>
/* ══════════════ Tunnel Milo : une question à la fois ══════════════ */

const STEPS = [
  {
    key: 'Secteur', type: 'single', required: true,
    q: "Dans quel univers travaillez-vous ?",
    sub: "C'est la question la plus importante : tout mon audit part de là.",
    opts: [
      ['🔧','Artisanat & BTP'], ['🍽️','Restauration & food'], ['🛍️','Commerce & e-commerce'],
      ['💼','Services & conseil'], ['🧑‍⚕️','Santé & bien-être'], ['🏨','Tourisme & hébergement'],
      ['🚚','Transport & logistique'], ['🌾','Agriculture'], ['🏠','Immobilier'], ['✨','Autre secteur'],
    ],
  },
  {
    key: 'Taille', type: 'single', required: true,
    q: "Vous êtes combien dans l'entreprise ?",
    opts: [ ['🧑','Je suis seul(e)'], ['👥','2 à 5'], ['👨‍👩‍👧','6 à 20'], ['🏢','21 à 50'], ['🏭','Plus de 50'] ],
  },
  {
    key: "Ancienneté entreprise", type: 'single',
    q: "Elle existe depuis combien de temps ?",
    opts: [ ['🌱','Moins de 2 ans'], ['🌿','2 à 5 ans'], ['🌳','5 à 15 ans'], ['🏛️','Plus de 15 ans'] ],
  },
  {
    key: "Chiffre d'affaires", type: 'single',
    q: "Votre chiffre d'affaires annuel, à la louche ?",
    sub: "Ça reste entre nous : ça me sert uniquement à calibrer les gains possibles.",
    opts: [ ['','Moins de 100 k€'], ['','100 à 300 k€'], ['','300 k€ à 1 M€'], ['','Plus de 1 M€'], ['🤫','Je préfère ne pas dire'] ],
  },
  {
    key: 'Tâches chronophages', type: 'multi', required: true,
    q: "Qu'est-ce qui vous vole le plus de temps ?",
    sub: "Choisissez tout ce qui vous parle : c'est là que je vais chercher vos gains.",
    opts: [
      ['📧','Emails et devis'], ['🧾','Factures et impayés'], ['📅','Planning et rendez-vous'],
      ['💬','Répondre aux clients'], ['📣','Communication et réseaux'], ['🧮','Comptabilité et paperasse'],
      ['🧑‍💼','Recrutement'], ['📦','Stocks et fournisseurs'],
    ],
  },
  {
    key: 'Temps admin/semaine', type: 'single',
    q: "Combien d'heures par semaine partent dans l'administratif ?",
    sub: "La moyenne des indépendants français est autour de 8 heures.",
    opts: [ ['','Moins de 5 h'], ['','5 à 10 h'], ['','10 à 20 h'], ['','Plus de 20 h'] ],
  },
  {
    key: 'Objectifs prioritaires', type: 'multi',
    q: "Qu'est-ce qui compte le plus pour vous en ce moment ?",
    opts: [
      ['⏱️','Gagner du temps'], ['📈','Trouver plus de clients'], ['👀','Être plus visible en ligne'],
      ['💶','Sécuriser ma trésorerie'], ['😌','Souffler un peu'], ['🚀','Développer une nouvelle offre'],
    ],
  },
  {
    key: 'Appétence numérique', type: 'single',
    q: "Avec les outils numériques, vous êtes plutôt…",
    opts: [
      ['🫣',"Ce n'est pas mon truc"], ['🙂','À l\'aise avec les bases'],
      ['😎','Plutôt à l\'aise'], ['🤓','Très à l\'aise, curieux(se) de tout'],
    ],
  },
  {
    key: 'Adoption équipe', type: 'single',
    skipIf: a => (a['Taille'] || '').includes('seul'),
    q: "Et votre équipe, face aux nouveaux outils ?",
    opts: [ ['🧗','Plutôt réticente'], ['🤝','Prête à essayer si c\'est simple'], ['⚡','Motivée et curieuse'] ],
  },
  {
    key: 'Outils déjà utilisés', type: 'multi',
    q: "Utilisez-vous déjà certains de ces outils ?",
    sub: "Aucun, c'est très bien aussi : je pars de votre réalité.",
    opts: [
      ['🤖','ChatGPT ou une IA de rédaction'], ['📊','Un logiciel de facturation'], ['📅','Un agenda en ligne'],
      ['📣','Canva ou un outil de visuels'], ['🧮','Un logiciel de comptabilité'], ['🚫','Aucun de ceux-là'],
    ],
  },
  {
    key: '_contact', type: 'contact', required: true,
    q: "C'est tout ! Où envoyons-nous votre audit ?",
    sub: "Votre plan personnalisé arrive à l'écran dans 30 secondes, et par email pour le garder.",
  },
];

const answers = {};
let cur = 0;
const $steps = document.getElementById('qt-steps');
const visible = () => STEPS.filter(s => !s.skipIf || !s.skipIf(answers));

/* ── Rendu d'une étape ── */
function render(idx, dir) {
  const seq = visible();
  cur = Math.max(0, Math.min(idx, seq.length - 1));
  const st = seq[cur];

  document.getElementById('qt-count').textContent = (cur + 1) + '/' + seq.length;
  document.getElementById('qt-bar').style.width = Math.round(((cur) / seq.length) * 100) + '%';

  const old = $steps.querySelector('.qt-step.on');
  if (old) { old.classList.add('out'); old.classList.remove('on'); setTimeout(() => old.remove(), 380); }

  const el = document.createElement('div');
  el.className = 'qt-step';
  let inner = `
    <div class="qt-milo">
      <img src="/assets/img/milo-avatar.jpg" alt="Milo">
      <div class="qt-q"><h2>${st.q}</h2>${st.sub ? `<p>${st.sub}</p>` : ''}</div>
    </div>`;

  if (st.type === 'contact') {
    inner += `
      <div class="qt-fields">
        <input type="text" id="qt-prenom" placeholder="Votre prénom (facultatif)" autocomplete="given-name">
        <input type="email" id="qt-email" placeholder="votre@email.fr" autocomplete="email" inputmode="email">
      </div>
      <div class="qt-nav">
        <button class="qt-back" onclick="go(-1)">&larr; Retour</button>
        <button class="qt-next" id="qt-submit" onclick="submitTunnel()">Voir mon audit</button>
      </div>
      <div class="qt-hint">Gratuit, sans carte bancaire. Vos réponses ne sont jamais revendues.</div>`;
  } else {
    const multi = st.type === 'multi';
    inner += `<div class="qt-opts${st.opts.some(o=>o[1].length>26)?' wide':''}">` + st.opts.map((o, i) => `
      <button class="qt-opt" data-i="${i}" onclick="pick(${i})">
        ${o[0] ? `<span class="ic">${o[0]}</span>` : ''}<span>${o[1]}</span>
        ${i < 9 ? `<span class="k">${i + 1}</span>` : ''}
      </button>`).join('') + `</div>
      <div class="qt-nav">
        ${cur > 0 ? '<button class="qt-back" onclick="go(-1)">&larr; Retour</button>' : '<span></span>'}
        ${multi ? '<button class="qt-next" id="qt-next" onclick="go(1)" disabled>Continuer</button>'
                : (st.required ? '' : '<button class="qt-next" style="background:#fff;color:#6B7280;box-shadow:none;border:1.5px solid #E5E7EB" onclick="go(1)">Passer</button>')}
      </div>
      ${multi ? '<div class="qt-hint">Plusieurs choix possibles. Touches 1-9 au clavier.</div>' : '<div class="qt-hint">Un clic suffit. Touches 1-9 au clavier.</div>'}`;
  }

  el.innerHTML = inner;
  $steps.appendChild(el);
  requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('on')));

  // restaurer la sélection déjà faite
  const prev = answers[st.key];
  if (prev && st.type !== 'contact') {
    st.opts.forEach((o, i) => {
      if ((st.type === 'multi' ? prev.split(', ') : [prev]).includes(o[1])) {
        el.querySelectorAll('.qt-opt')[i]?.classList.add('sel');
      }
    });
    if (st.type === 'multi') el.querySelector('#qt-next')?.removeAttribute('disabled');
  }
  if (st.type === 'contact') setTimeout(() => el.querySelector('#qt-email')?.focus(), 420);
}

/* ── Sélection ── */
function pick(i) {
  const seq = visible(); const st = seq[cur];
  const btns = $steps.querySelectorAll('.qt-step.on .qt-opt');
  if (st.type === 'multi') {
    btns[i].classList.toggle('sel');
    const sel = [...btns].filter(b => b.classList.contains('sel')).map(b => st.opts[b.dataset.i][1]);
    answers[st.key] = sel.join(', ');
    const nx = $steps.querySelector('.qt-step.on #qt-next');
    if (nx) nx.disabled = sel.length === 0;
  } else {
    btns.forEach(b => b.classList.remove('sel'));
    btns[i].classList.add('sel');
    answers[st.key] = st.opts[i][1];
    setTimeout(() => go(1), 260);   // auto-avance fluide
  }
}

function go(dir) {
  const seq = visible(); const st = seq[cur];
  if (dir > 0 && st.required && st.type !== 'contact' && !answers[st.key]) return;
  if (cur + dir >= seq.length) return;
  render(cur + dir, dir);
}

/* ── Clavier ── */
document.addEventListener('keydown', e => {
  const st = visible()[cur];
  if (!st || st.type === 'contact') { if (e.key === 'Enter') { document.getElementById('qt-submit')?.click(); } return; }
  const n = parseInt(e.key);
  if (n >= 1 && n <= (st.opts?.length || 0)) pick(n - 1);
  if (e.key === 'Enter' && st.type === 'multi' && answers[st.key]) go(1);
  if (e.key === 'Backspace' && cur > 0) { e.preventDefault(); go(-1); }
});

/* ── Soumission ── */
const LOAD_MSGS = [
  "Je croise votre profil avec plus de 300 outils IA…",
  "Je calcule vos gains de temps et d'argent…",
  "Je sélectionne les outils adaptés à votre secteur…",
  "Je rédige vos recommandations personnalisées…",
  "Presque prêt, encore quelques secondes…",
];
async function submitTunnel() {
  const email  = (document.getElementById('qt-email')?.value || '').trim();
  const prenom = (document.getElementById('qt-prenom')?.value || '').trim();
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    const f = document.getElementById('qt-email'); f.classList.add('err'); f.focus(); return;
  }

  document.getElementById('qt-steps').style.display = 'none';
  document.getElementById('qt-count').style.visibility = 'hidden';
  document.getElementById('qt-bar').style.width = '100%';
  document.getElementById('qt-loading').style.display = 'block';

  let mi = 0;
  const t = setInterval(() => {
    mi = (mi + 1) % LOAD_MSGS.length;
    const p = document.getElementById('qt-loadmsg');
    p.style.opacity = 0; setTimeout(() => { p.textContent = LOAD_MSGS[mi]; p.style.opacity = 1; }, 280);
  }, 3200);

  const payload = {};
  STEPS.forEach(s => { if (s.key !== '_contact' && answers[s.key]) payload[s.key] = answers[s.key]; });

  try {
    const lead = await ABYS.api('leads.php', {
      action: 'create', url: '', email,
      sector: answers['Secteur'] || '', source: 'questionnaire',
    });
    ABYS.store('lead_id', lead.lead_id);
    if (prenom) ABYS.store('prenom', prenom);
    await Audit.runFromQuestionnaire(payload);
  } catch (err) {
    clearInterval(t);
    document.getElementById('qt-loading').style.display = 'none';
    document.getElementById('qt-steps').style.display = '';
    document.getElementById('qt-count').style.visibility = '';
    alert('Erreur : ' + err.message);
  }
}

render(0, 1);
</script>
