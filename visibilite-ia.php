<?php
$page_title = 'Visibilité IA · Être recommandé par ChatGPT et les autres IA · ABYS AI';
$page_description = 'Vos clients demandent à ChatGPT, Gemini ou Perplexity de leur recommander un professionnel. ABYS fait en sorte que ce soit vous qui soyez cité.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';

// Modèles cités : logos servis par notre proxy (jamais de CDN externe bloqué)
$llms = [
  ['openai.com',        'ChatGPT',    'OpenAI'],
  ['gemini.google.com', 'Gemini',     'Google'],
  ['claude.ai',         'Claude',     'Anthropic'],
  ['perplexity.ai',     'Perplexity', 'Recherche IA'],
  ['mistral.ai',        'Le Chat',    'Mistral, France'],
  ['microsoft.com',     'Copilot',    'Microsoft'],
];
?>
<style>
/* ══════ Hero abysse ══════ */
.vi-hero { position:relative; overflow:hidden; background:#041712; color:#fff; padding:78px 24px 72px; text-align:center; }
.vi-beams { position:absolute; inset:0; overflow:hidden; pointer-events:none; }
.vi-beams span { position:absolute; top:-38%; left:var(--l); width:118px; height:186%; transform-origin:top center; transform:rotate(var(--a)); }
.vi-beams span::before { content:''; position:absolute; inset:0;
  background:linear-gradient(to bottom, rgba(155,247,208,.26), rgba(58,206,231,.09) 55%, transparent 80%);
  -webkit-mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
          mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
  filter:blur(7px); mix-blend-mode:screen; transform-origin:top center;
  animation:vi-ray var(--d) ease-in-out var(--delay,0s) infinite alternate; }
@keyframes vi-ray { from{ transform:rotate(calc(var(--s) * -1)); } to{ transform:rotate(var(--s)); } }
@media (prefers-reduced-motion: reduce){ .vi-beams span::before{ animation:none; } }
.vi-hero-in { position:relative; z-index:2; max-width:760px; margin:0 auto; }
.vi-kicker { display:inline-flex; align-items:center; gap:8px; font-size:11px; font-weight:700; letter-spacing:.13em; text-transform:uppercase;
  color:#6EE7B7; background:rgba(16,185,129,.13); border:1px solid rgba(16,185,129,.3); border-radius:30px; padding:7px 15px; margin-bottom:24px; }
.vi-hero h1 { font-size:clamp(30px,4.4vw,50px); font-weight:300; letter-spacing:-.04em; line-height:1.14; margin:0 0 16px; }
.vi-hero h1 strong { font-weight:800; }
.vi-hero p.lead { font-size:17px; line-height:1.7; color:rgba(255,255,255,.66); max-width:600px; margin:0 auto 30px; }

/* Conversation animée */
.vi-demo { max-width:600px; margin:0 auto; background:rgba(0,0,0,.34); border:1px solid rgba(255,255,255,.12);
  border-radius:16px; overflow:hidden; text-align:left; }
.vi-demo-bar { display:flex; align-items:center; gap:7px; padding:11px 16px; background:rgba(255,255,255,.05); border-bottom:1px solid rgba(255,255,255,.08); }
.vi-dot { width:9px; height:9px; border-radius:50%; background:rgba(255,255,255,.18); }
.vi-demo-title { font-size:11.5px; color:rgba(255,255,255,.45); margin-left:8px; }
.vi-demo-body { padding:18px; }
.vi-msg { font-size:14px; line-height:1.6; border-radius:14px; padding:11px 15px; margin-bottom:10px; max-width:90%; }
.vi-msg-user { background:rgba(16,185,129,.16); color:#D1FAE5; margin-left:auto; border-bottom-right-radius:4px; }
.vi-msg-ai { background:rgba(255,255,255,.07); color:rgba(255,255,255,.78); border-bottom-left-radius:4px; }
.vi-typing { display:inline-flex; gap:4px; vertical-align:middle; }
.vi-typing i { width:6px; height:6px; border-radius:50%; background:#6EE7B7; display:block; animation:vi-blink 1.2s infinite; }
.vi-typing i:nth-child(2){ animation-delay:.2s } .vi-typing i:nth-child(3){ animation-delay:.4s }
@keyframes vi-blink { 0%,60%,100%{ opacity:.25 } 30%{ opacity:1 } }
.vi-cite { display:flex; align-items:center; gap:10px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09);
  border-radius:10px; padding:9px 12px; margin-top:8px; font-size:13px; color:rgba(255,255,255,.62);
  opacity:0; transform:translateX(-8px); transition:opacity .45s ease, transform .45s cubic-bezier(.3,1,.4,1); }
.vi-cites.in .vi-cite { opacity:1; transform:none; }
.vi-cite.you { background:rgba(16,185,129,.16); border-color:rgba(16,185,129,.45); color:#fff; font-weight:600; }
.vi-rank { width:20px; height:20px; border-radius:6px; background:rgba(255,255,255,.1); color:rgba(255,255,255,.55);
  font-size:11px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.vi-cite.you .vi-rank { background:#10B981; color:#fff; }
.vi-swap { text-align:center; font-size:12.5px; color:#6EE7B7; margin-top:14px; opacity:0; transition:opacity .5s ease; }
.vi-swap.in { opacity:1; }

/* ══════ Corps ══════ */
.vi-wrap { max-width:1000px; margin:0 auto; padding:0 24px; }
.vi-h2 { font-size:clamp(22px,3vw,30px); font-weight:300; letter-spacing:-.03em; text-align:center; margin:70px 0 12px; color:var(--ink,#0A1F1A); }
.vi-h2 strong { font-weight:800; }
.vi-sub { font-size:15px; color:var(--ink-3,#4B5563); text-align:center; max-width:620px; margin:0 auto 36px; line-height:1.7; }

.vi-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:16px; }
@media(max-width:760px){ .vi-stats{ grid-template-columns:1fr; } }
.vi-stat { background:#fff; border:2px solid var(--border,#E5E7EB); border-radius:18px; padding:24px; text-align:center; }
.vi-stat b { display:block; font-size:38px; font-weight:800; letter-spacing:-.03em; line-height:1; margin-bottom:8px;
  background:linear-gradient(135deg,#059669,#0EA5E9); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
.vi-stat span { font-size:13.5px; color:var(--ink-3,#4B5563); line-height:1.55; display:block; }
.vi-source { font-size:11.5px; color:var(--ink-4,#9CA3AF); text-align:center; margin-bottom:8px; }
.vi-source a { color:var(--ink-4,#9CA3AF); text-decoration:underline; }

.vi-llms { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; margin-bottom:8px; }
@media(max-width:820px){ .vi-llms{ grid-template-columns:repeat(3,1fr); } }
.vi-llm { background:#fff; border:2px solid var(--border,#E5E7EB); border-radius:16px; padding:18px 10px; text-align:center; }
.vi-llm img { width:38px; height:38px; border-radius:10px; object-fit:contain; margin-bottom:10px; }
.vi-llm .n { font-size:13px; font-weight:700; color:var(--ink-2,#1F2937); }
.vi-llm .d { font-size:11px; color:var(--ink-4,#9CA3AF); margin-top:2px; }

.vi-steps { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
@media(max-width:760px){ .vi-steps{ grid-template-columns:1fr; } }
.vi-step { background:#fff; border:2px solid var(--border,#E5E7EB); border-radius:18px; padding:26px; }
.vi-step .ic { width:42px; height:42px; border-radius:12px; background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(14,165,233,.1));
  border:1px solid rgba(16,185,129,.22); display:flex; align-items:center; justify-content:center; margin-bottom:14px; }
.vi-step h4 { font-size:17px; font-weight:800; margin:0 0 8px; color:var(--ink,#0A1F1A); }
.vi-step p { font-size:13.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin:0; }
.vi-step ul { margin:8px 0 0; padding-left:18px; }
.vi-step li { font-size:13.5px; line-height:1.75; color:var(--ink-3,#4B5563); }

.vi-offer { position:relative; overflow:hidden; border-radius:24px; background:linear-gradient(160deg,#041712,#052E16 55%,#07231a);
  color:#fff; padding:46px 40px; text-align:center; margin:60px 0 90px; }
.vi-offer-in { position:relative; z-index:2; max-width:560px; margin:0 auto; }
.vi-offer h2 { font-size:30px; font-weight:300; letter-spacing:-.03em; margin:0 0 10px; }
.vi-offer h2 strong { font-weight:800; color:#34D399; }
.vi-offer p { font-size:14.5px; color:rgba(255,255,255,.7); line-height:1.7; margin:0 0 22px; }
.vi-price { font-size:52px; font-weight:800; letter-spacing:-.04em; color:#fff; line-height:1; }
.vi-period { font-size:14px; color:rgba(255,255,255,.5); margin:6px 0 26px; }
.vi-cta { display:inline-flex; align-items:center; justify-content:center; height:52px; padding:0 34px; border-radius:13px;
  background:linear-gradient(90deg,#059669,#0EA5E9 55%,#10B981); color:#fff; font-size:16px; font-weight:700; text-decoration:none;
  box-shadow:0 14px 34px -12px rgba(16,185,129,.85); transition:transform .15s; }
.vi-cta:hover { transform:translateY(-2px); }
.vi-note { font-size:12.5px; color:rgba(255,255,255,.45); margin-top:16px; }
</style>

<!-- ══════ HERO ══════ -->
<div class="vi-hero">
  <div class="vi-beams" aria-hidden="true">
    <span style="--a:-20deg;--l:55%;--d:9s;--s:8deg;--delay:-2s"></span>
    <span style="--a:-6deg;--l:61%;--d:7s;--s:10deg;--delay:-5s"></span>
    <span style="--a:10deg;--l:67%;--d:10.5s;--s:7deg;--delay:-1s"></span>
    <span style="--a:26deg;--l:73%;--d:8s;--s:9deg;--delay:-4s"></span>
  </div>
  <div class="vi-hero-in">
    <div class="vi-kicker">Visibilité IA</div>
    <h1>Vos clients ne cherchent plus.<br><strong>Ils demandent.</strong></h1>
    <p class="lead">
      « Tu peux me recommander un bon professionnel près de chez moi ? » Des millions de personnes
      posent cette question à une IA chaque jour. L'IA répond avec des noms précis.
      Notre travail : que ce soit le vôtre.
    </p>

    <div class="vi-demo" id="vi-demo">
      <div class="vi-demo-bar">
        <span class="vi-dot"></span><span class="vi-dot"></span><span class="vi-dot"></span>
        <span class="vi-demo-title">Conversation avec une IA</span>
      </div>
      <div class="vi-demo-body">
        <div class="vi-msg vi-msg-user" id="vi-q"></div>
        <div class="vi-msg vi-msg-ai" id="vi-a">
          <span class="vi-typing" id="vi-typing"><i></i><i></i><i></i></span>
          <span id="vi-answer"></span>
        </div>
        <div class="vi-swap" id="vi-swap">Avec ABYS, c'est votre entreprise qui prend cette place.</div>
      </div>
    </div>
  </div>
</div>

<div class="vi-wrap">

  <!-- ══════ POURQUOI MAINTENANT ══════ -->
  <h2 class="vi-h2">Le référencement a <strong>changé de camp</strong></h2>
  <p class="vi-sub">
    Être premier sur Google ne suffit plus quand le visiteur obtient sa réponse sans jamais cliquer.
    Ce qui compte désormais, c'est d'être la source que l'IA cite.
  </p>

  <div class="vi-stats">
    <div class="vi-stat">
      <b>68%</b>
      <span>des recherches Google se terminent sans aucun clic, début 2026</span>
    </div>
    <div class="vi-stat">
      <b>48%</b>
      <span>des recherches affichent déjà une réponse générée par l'IA</span>
    </div>
    <div class="vi-stat">
      <b>1 seule</b>
      <span>réponse est lue par votre futur client. Pas dix liens, une réponse</span>
    </div>
  </div>
  <p class="vi-source">
    Sources : <a href="https://searchengineland.com/google-zero-click-searches-2026-study-479717" target="_blank" rel="noopener">Search Engine Land, étude 2026</a>
    et <a href="https://thestacc.com/blog/google-ai-overview-statistics/" target="_blank" rel="noopener">statistiques AI Overviews 2026</a>.
  </p>

  <!-- ══════ LES MODÈLES ══════ -->
  <h2 class="vi-h2">Là où vos clients <strong>posent la question</strong></h2>
  <p class="vi-sub">Nous travaillons votre présence sur les IA réellement utilisées en France.</p>
  <div class="vi-llms">
    <?php foreach ($llms as $l): ?>
    <div class="vi-llm">
      <img src="/api/logo.php?d=<?= rawurlencode($l[0]) ?>" alt="<?= htmlspecialchars($l[1]) ?>" loading="lazy">
      <div class="n"><?= htmlspecialchars($l[1]) ?></div>
      <div class="d"><?= htmlspecialchars($l[2]) ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ══════ CE QU'ON FAIT ══════ -->
  <h2 class="vi-h2">Ce que nous faisons, <strong>concrètement</strong></h2>
  <p class="vi-sub">Pas de jargon, pas de promesse de première place. Un travail méthodique, mesuré chaque mois.</p>

  <div class="vi-steps">
    <div class="vi-step">
      <div class="ic"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
      <h4>1. On mesure où vous en êtes</h4>
      <p>Nous posons aux IA les questions que vos clients posent vraiment, sur votre métier et votre zone. Vous découvrez qui est cité aujourd'hui, et à quelle place vous apparaissez, si vous apparaissez.</p>
    </div>
    <div class="vi-step">
      <div class="ic"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
      <h4>2. On rend votre activité lisible par les IA</h4>
      <p>Une IA ne devine pas. Elle a besoin d'informations claires et structurées.</p>
      <ul>
        <li>Vos données structurées (métier, zone, horaires, spécialités)</li>
        <li>Des réponses directes aux vraies questions de vos clients</li>
        <li>Vos fiches et avis cohérents partout où les IA vont chercher</li>
      </ul>
    </div>
    <div class="vi-step">
      <div class="ic"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></div>
      <h4>3. On produit le contenu qui fait la différence</h4>
      <p>Chaque mois, du contenu utile rédigé pour votre métier, formulé de la façon dont les IA aiment citer leurs sources. Vous validez, nous publions.</p>
    </div>
    <div class="vi-step">
      <div class="ic"><svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
      <h4>4. On vous montre les résultats</h4>
      <p>Un rapport mensuel simple : sur quelles questions vous êtes cité maintenant, sur lesquelles vous ne l'êtes pas encore, et ce qu'on fait le mois suivant. Sans citation détectée, vous le voyez aussi : nous ne maquillons rien.</p>
    </div>
  </div>

  <!-- ══════ OFFRE ══════ -->
  <div class="vi-offer">
    <div class="vi-beams" aria-hidden="true">
      <span style="--a:-14deg;--l:60%;--d:9s;--s:8deg;--delay:-2s"></span>
      <span style="--a:4deg;--l:67%;--d:7.5s;--s:9deg;--delay:-5s"></span>
      <span style="--a:20deg;--l:73%;--d:10s;--s:7deg;--delay:-1s"></span>
    </div>
    <div class="vi-offer-in">
      <h2>Visibilité <strong>IA</strong></h2>
      <p>Mesure de votre présence, mise en forme de vos informations, contenu mensuel et rapport de citations.</p>
      <div class="vi-price">49€</div>
      <div class="vi-period">par mois, sans engagement</div>
      <a href="/facturation.php?plan=seo" class="vi-cta">Rendre mon entreprise visible</a>
      <p class="vi-note">Premier mois : la mesure complète de votre présence est offerte. Résiliation en un clic.</p>
    </div>
  </div>

</div>

<script>
/* Conversation animée : la question se tape, l'IA répond, puis votre place se révèle */
(function(){
  var demo = document.getElementById('vi-demo');
  if (!demo) return;
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var Q = 'Tu peux me recommander un bon professionnel près de chez moi ?';
  var qEl = document.getElementById('vi-q'), typing = document.getElementById('vi-typing'),
      ans = document.getElementById('vi-answer'), swap = document.getElementById('vi-swap');

  var CITES = [
    { n: 'Un concurrent que vous connaissez', you: false },
    { n: 'Une entreprise de la ville voisine', you: false },
    { n: 'Votre entreprise', you: true }
  ];
  function citesHtml(){
    return '<div class="vi-cites" id="vi-cites">' + CITES.map(function(c, i){
      return '<div class="vi-cite' + (c.you ? ' you' : '') + '" style="transition-delay:' + (i * 200) + 'ms">' +
             '<span class="vi-rank">' + (i + 1) + '</span>' + c.n + '</div>';
    }).join('') + '</div>';
  }

  function play(){
    qEl.textContent = ''; ans.innerHTML = ''; typing.style.display = 'inline-flex'; swap.classList.remove('in');
    if (reduce) {
      qEl.textContent = '« ' + Q + ' »';
      typing.style.display = 'none';
      ans.innerHTML = 'Voici les professionnels que je recommande :' + citesHtml();
      document.getElementById('vi-cites').classList.add('in');
      swap.classList.add('in');
      return;
    }
    var i = 0;
    qEl.textContent = '« ';
    var t = setInterval(function(){
      qEl.textContent = '« ' + Q.slice(0, ++i);
      if (i >= Q.length) {
        clearInterval(t);
        qEl.textContent = '« ' + Q + ' »';
        setTimeout(function(){
          typing.style.display = 'none';
          ans.innerHTML = 'Voici les professionnels que je recommande :' + citesHtml();
          setTimeout(function(){ document.getElementById('vi-cites').classList.add('in'); }, 50);
          setTimeout(function(){ swap.classList.add('in'); }, 1100);
        }, 1300);
      }
    }, 30);
  }

  var io = new IntersectionObserver(function(en){
    en.forEach(function(e){ if (e.isIntersecting) { play(); io.unobserve(e.target); } });
  }, { threshold: .3 });
  io.observe(demo);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
