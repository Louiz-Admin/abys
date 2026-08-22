<?php
// Fichier: abys-ai/includes/milo.php
// MILO INCARNÉ · un seul endroit pour son portrait, son animation et sa fiche.
//
//   milo_avatar(64)                      portrait cliquable, taille libre
//   milo_avatar(96, 'ma-classe')         avec une classe en plus
//   milo_avatar(64, '', 'pose-analyse')  variante de pose si le fichier existe
//   milo_fiche()                         la fenêtre « qui je suis », une fois par page
//
// La fiche s'ouvre au clic sur n'importe quel portrait, sur toutes les pages.

if (!function_exists('milo_avatar')) {

    /** Adresse d'un script ou d'une feuille, avec la date du fichier : jamais de cache perime. */
    function abys_asset(string $rel): string {
        $abs = __DIR__ . '/..' . $rel;
        $v   = is_file($abs) ? filemtime($abs) : time();
        return $rel . '?v=' . $v;
    }

    /**
     * Portrait de Milo, vivant et cliquable.
     * Les variantes sont des fichiers assets/img/milo-<variante>.jpg. Si le
     * fichier n'existe pas, on retombe sur le portrait principal sans rien casser.
     */
    function milo_avatar(int $taille = 64, string $classe = '', string $variante = ''): string {
        $src = '/assets/img/milo-avatar.jpg';
        if ($variante !== '') {
            $rel = '/assets/img/milo-' . preg_replace('/[^a-z0-9\-]/', '', $variante) . '.jpg';
            if (is_file(__DIR__ . '/..' . $rel)) $src = $rel;
        }
        $t = max(28, min(240, $taille));
        return '<button type="button" class="milo-av ' . htmlspecialchars($classe) . '"'
             . ' style="--mv:' . $t . 'px" data-milo aria-label="Qui est Milo ?" title="Qui est Milo ?">'
             . '<img src="' . $src . '" alt="Milo, le copilote IA d\'ABYS" width="' . $t . '" height="' . $t . '">'
             . '<span class="milo-av-halo" aria-hidden="true"></span>'
             . '<span class="milo-av-sweep" aria-hidden="true"></span>'
             . '<span class="milo-av-tag" aria-hidden="true">?</span>'
             . '</button>';
    }

    /** La fiche de Milo. À appeler une seule fois par page, avant la fermeture du body. */
    function milo_fiche(): string {
        ob_start(); ?>
<style>
  /* ══════════ Portrait vivant · s'applique a tous les Milo de la page ══════════ */
  img[data-milo] {
    cursor:pointer;
    animation:milo-glow 4.6s ease-in-out infinite;
    transition:transform .26s cubic-bezier(.22,1,.36,1), filter .26s ease;
  }
  img[data-milo]:hover { transform:scale(1.05); filter:drop-shadow(0 0 14px rgba(52,211,153,.75)); }
  img[data-milo]:focus-visible { outline:2px solid #34D399; outline-offset:4px; }
  @keyframes milo-glow {
    0%,100% { filter:drop-shadow(0 0 4px rgba(16,185,129,.30)); }
    50%     { filter:drop-shadow(0 0 12px rgba(52,211,153,.55)); }
  }

  /* Portrait pose par milo_avatar() : meme chose, avec le balayage de lumiere */
  .milo-av { position:relative; width:var(--mv,64px); height:var(--mv,64px); flex-shrink:0;
    padding:0; border:none; background:none; cursor:pointer; border-radius:50%; display:block;
    animation:milo-float 7s ease-in-out infinite; }
  .milo-av img { width:100%; height:100%; border-radius:50%; object-fit:cover; display:block;
    border:2px solid rgba(52,211,153,.7); position:relative; z-index:2; }
  .milo-av-halo { position:absolute; inset:-6px; border-radius:50%; z-index:1;
    box-shadow:0 0 0 5px rgba(16,185,129,.12); animation:milo-breathe 4.5s ease-in-out infinite; }
  .milo-av-sweep { position:absolute; inset:0; border-radius:50%; overflow:hidden; z-index:3; pointer-events:none; }
  .milo-av-sweep::before { content:''; position:absolute; top:-60%; left:-120%; width:60%; height:220%;
    background:linear-gradient(100deg, transparent, rgba(255,255,255,.30), transparent);
    transform:rotate(14deg); animation:milo-sweep 9s ease-in-out infinite; }
  .milo-av-tag { position:absolute; right:-2px; bottom:-2px; z-index:4;
    width:calc(var(--mv,64px) * .34); height:calc(var(--mv,64px) * .34);
    min-width:18px; min-height:18px; border-radius:50%;
    display:grid; place-items:center; font-size:11px; font-weight:800; font-family:inherit;
    background:#0A3A2C; color:#6EE7B7; border:1.5px solid rgba(52,211,153,.6);
    opacity:0; transform:scale(.6); transition:opacity .2s ease, transform .22s cubic-bezier(.22,1,.36,1); }
  .milo-av:hover .milo-av-halo { box-shadow:0 0 0 8px rgba(16,185,129,.18); }
  .milo-av:hover .milo-av-tag, .milo-av:focus-visible .milo-av-tag { opacity:1; transform:scale(1); }
  .milo-av:focus-visible { outline:2px solid #34D399; outline-offset:4px; }

  @keyframes milo-float   { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-3px); } }
  @keyframes milo-breathe { 0%,100% { box-shadow:0 0 0 5px rgba(16,185,129,.10); }
                            50%     { box-shadow:0 0 0 9px rgba(16,185,129,.20); } }
  @keyframes milo-sweep   { 0%,72% { left:-120%; } 88%,100% { left:150%; } }
  @media (prefers-reduced-motion: reduce) {
    img[data-milo], .milo-av, .milo-av-halo, .milo-av-sweep::before { animation:none; }
  }

  /* ══════════ La fiche ══════════ */
  .milo-fiche { position:fixed; inset:0; z-index:9000; display:none; padding:18px;
    background:rgba(2,14,11,.72); backdrop-filter:blur(7px); -webkit-backdrop-filter:blur(7px);
    align-items:center; justify-content:center; }
  .milo-fiche.on { display:flex; animation:milo-fade .3s ease both; }
  @keyframes milo-fade { from { opacity:0; } to { opacity:1; } }

  .milo-panel { position:relative; width:100%; max-width:1040px; max-height:94vh; overflow:hidden;
    border-radius:26px; border:1px solid rgba(52,211,153,.26); background:#041712; color:#EAF6F1;
    box-shadow:0 50px 120px -40px rgba(0,0,0,.9); display:grid; grid-template-columns:340px 1fr;
    animation:milo-pop .42s cubic-bezier(.22,1,.36,1) both; }
  @keyframes milo-pop { from { opacity:0; transform:translateY(22px) scale(.98); } to { opacity:1; transform:none; } }

  .milo-portrait { position:relative; overflow:hidden; background:#03130F; }
  .milo-portrait img { width:100%; height:100%; object-fit:cover; display:block; }
  .milo-portrait::after { content:''; position:absolute; inset:0;
    background:linear-gradient(to right, transparent 55%, #041712),
               linear-gradient(to top, rgba(4,23,18,.85), transparent 45%); }
  .milo-sign { position:absolute; left:24px; bottom:22px; z-index:3; }
  .milo-sign b { display:block; font-size:21px; font-weight:700; letter-spacing:-.02em; }
  .milo-sign span { display:block; font-size:12px; color:#8CA79E; margin-top:3px; }

  .milo-body { padding:24px 30px 20px; }
  .milo-eyebrow { font-size:11px; font-weight:700; letter-spacing:.15em; text-transform:uppercase; color:#6EE7B7; }
  .milo-body h2 { font-size:clamp(20px,2.3vw,25px); font-weight:700; letter-spacing:-.03em; margin:7px 0 11px; line-height:1.22; color:#F3FBF8; }
  .milo-body > p { font-size:13.8px; line-height:1.6; color:rgba(255,255,255,.76); margin:0 0 16px; max-width:620px; }

  .milo-chiffres { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:11px; margin:0 0 10px; }
  .milo-chiffres > div { border:1px solid rgba(52,211,153,.24); border-radius:14px; padding:11px 13px;
    background:linear-gradient(155deg, rgba(16,185,129,.13), rgba(56,189,248,.04)); }
  .milo-chiffres b { display:block; font-size:18px; font-weight:750; letter-spacing:-.03em; line-height:1.15;
    background:linear-gradient(90deg,#34D399,#7DD3FC); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .milo-chiffres span { display:block; font-size:11.5px; line-height:1.45; color:#9FC4B9; margin-top:5px; }
  .milo-note { font-size:11.5px; line-height:1.5; color:#6E8C84; margin:8px 0 14px; max-width:620px; }

  .milo-role { border-left:2px solid #34D399; padding:2px 0 2px 15px; margin:0 0 16px; }
  .milo-role b { display:block; font-size:11px; font-weight:700; letter-spacing:.14em; text-transform:uppercase;
    color:#6EE7B7; margin-bottom:7px; }
  .milo-role p { font-size:13.8px; line-height:1.65; color:rgba(255,255,255,.82); margin:0; max-width:620px; }

  .milo-forge { border:1px solid rgba(52,211,153,.24); border-radius:15px; padding:13px 15px; margin:0 0 16px;
    background:linear-gradient(150deg, rgba(16,185,129,.12), rgba(56,189,248,.04)); }
  .milo-forge b { display:block; font-size:11px; font-weight:700; letter-spacing:.14em; text-transform:uppercase;
    color:#6EE7B7; margin-bottom:7px; }
  .milo-forge p { font-size:13px; line-height:1.6; color:rgba(255,255,255,.78); margin:0; }

  .milo-steps { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin:0 0 14px; }
  .milo-step { display:block; border:1px solid rgba(255,255,255,.10); border-radius:14px; padding:13px 14px;
    background:rgba(255,255,255,.04); }
  .milo-step .ic { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; margin-bottom:9px;
    background:rgba(52,211,153,.10); border:1px solid rgba(52,211,153,.22); color:#34D399; }
  .milo-step .ic svg { width:16px; height:16px; display:block; }
  .milo-step b { display:block; font-size:13.5px; font-weight:650; color:#EAF6F1; margin-bottom:4px; letter-spacing:-.01em; }
  .milo-step > div > span { display:block; font-size:12.5px; line-height:1.5; color:rgba(255,255,255,.62); }

  .milo-franchise { border-left:2px solid rgba(52,211,153,.5); padding:2px 0 2px 15px; margin:0 0 22px; }
  .milo-franchise b { display:block; font-size:13px; font-weight:700; color:#6EE7B7; margin-bottom:6px;
    letter-spacing:.04em; text-transform:uppercase; }
  .milo-franchise p { font-size:13.5px; line-height:1.65; color:rgba(255,255,255,.66); margin:0; }

  .milo-foot { display:flex; align-items:center; gap:14px; flex-wrap:wrap;
    border-top:1px solid rgba(255,255,255,.10); padding-top:14px; }
  .milo-foot a { display:inline-flex; align-items:center; gap:8px; text-decoration:none;
    font-size:13.5px; font-weight:650; color:#03251B; border-radius:11px; padding:11px 18px;
    background:linear-gradient(90deg,#34D399,#5EEAD4 55%,#7DD3FC);
    box-shadow:0 16px 36px -18px rgba(52,211,153,.9); transition:transform .14s, filter .16s; }
  .milo-foot a:hover { transform:translateY(-2px); filter:brightness(1.06); }
  .milo-foot a svg { width:16px; height:16px; }
  .milo-foot em { font-style:normal; font-size:11.5px; line-height:1.5; color:#6E8C84; flex:1; min-width:220px; }

  .milo-close { position:absolute; top:14px; right:16px; z-index:5; width:36px; height:36px;
    border-radius:50%; border:1px solid rgba(255,255,255,.16); background:rgba(4,23,18,.7);
    color:#CFE9E0; cursor:pointer; display:grid; place-items:center; transition:background .16s, border-color .16s; }
  .milo-close:hover { background:rgba(255,255,255,.10); border-color:rgba(255,255,255,.3); }
  .milo-close svg { width:16px; height:16px; }

  @media (max-width:720px) { .milo-chiffres { grid-template-columns:1fr; } }

  /* Ecrans peu hauts : on resserre plutot que de faire defiler */
  @media (min-width:861px) and (max-height:760px) {
    .milo-body { padding:18px 26px 16px; }
    .milo-body h2 { font-size:21px; margin:5px 0 8px; }
    .milo-body > p { font-size:13px; margin:0 0 12px; }
    .milo-note { display:none; }
    .milo-chiffres { margin-bottom:12px; }
    .milo-chiffres b { font-size:16.5px; }
    .milo-forge { padding:11px 13px; margin-bottom:12px; }
    .milo-role { margin-bottom:12px; }
    .milo-role p { font-size:13px; }
    .milo-forge p { font-size:12.5px; }
    .milo-step { padding:11px 12px; }
  }
  @media (max-width:860px) {
    .milo-panel { grid-template-columns:1fr; max-height:92vh; overflow:auto; }
    .milo-steps, .milo-chiffres { grid-template-columns:1fr; }
    .milo-portrait { height:230px; }
    .milo-portrait::after { background:linear-gradient(to top, #041712, transparent 60%); }
    .milo-body { padding:24px 22px 26px; }
  }
</style>

<div class="milo-fiche" id="milo-fiche" role="dialog" aria-modal="true" aria-labelledby="milo-fiche-titre">
  <div class="milo-panel">
    <button class="milo-close" type="button" data-milo-close aria-label="Fermer">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
    </button>

    <div class="milo-portrait">
      <img src="/assets/img/milo.jpg" alt="Milo, le copilote IA d'ABYS">
      <div class="milo-sign"><b>Milo</b><span>Copilote IA, ABYS</span></div>
    </div>

    <div class="milo-body">
      <div class="milo-eyebrow">Qui vous parle</div>
      <h2 id="milo-fiche-titre">Bonjour. Je suis Milo.</h2>
      <p>Je suis l'agent alimenté par une intelligence artificielle qui fait fonctionner entièrement ABYS. C'est moi qui parcours votre site, qui pose les questions, qui calcule vos gains et qui écris votre plan. C'est moi aussi qui réponds quand vous écrivez à ABYS.</p>
      <p>Derrière moi, il y a Thomas Capiten, mon créateur, qui fixe le cap. Et moi, qui exécute. Ce portrait est une image de synthèse, autant vous le dire tout de suite.</p>

      <div class="milo-role">
        <b>Ce que je fais pour vous</b>
        <p>Je ne me contente pas d'analyser. Je vous guide, je reste avec vous, et je mets vos outils en place jusqu'à ce qu'ils tournent vraiment. Le but tient en trois mots : plus de temps, plus d'argent, et ne pas regarder passer le train de l'IA pendant que vos concurrents montent dedans.</p>
      </div>

      <div class="milo-forge">
        <b>Ce qui tourne derrière</b>
        <p>Architecture multi-modèles : un modèle rapide pour la lecture et la qualification sectorielle, un modèle de raisonnement pour l'arbitrage et la rédaction. Sortie contrainte par schéma strict avec réparation des réponses tronquées, corpus d'outils segmenté par secteur et par usage, pondération des gains selon la taille et le volume d'affaires, et une boucle de décision autonome qui ouvre les dossiers, juge, agit et rend compte.</p>
      </div>

      <div class="milo-foot">
        <a href="mailto:contact@abys.ai">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4.5" width="20" height="15" rx="2.5"/><path d="m3 7 8.2 5.6a1.5 1.5 0 0 0 1.6 0L21 7"/></svg>
          Écrivez-moi
        </a>
        <em>Ni agenda ni téléphone, et aucune promesse que je ne sais pas chiffrer. Tout ce que vous m'écrivez, c'est moi qui le traite.</em>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var fiche = document.getElementById('milo-fiche');
  if (!fiche) return;
  var dernier = null;

  function ouvrir(src) {
    dernier = src || null;
    fiche.classList.add('on');
    document.documentElement.style.overflow = 'hidden';
    var c = fiche.querySelector('[data-milo-close]');
    if (c) setTimeout(function () { c.focus(); }, 60);
  }
  function fermer() {
    fiche.classList.remove('on');
    document.documentElement.style.overflow = '';
    if (dernier && dernier.focus) dernier.focus();
  }

  /* Tout portrait de Milo devient vivant et cliquable, meme injecte plus tard */
  function marquer(racine) {
    var sel = 'img[src*="milo-avatar"], img[src*="milo.jpg"]';
    var lot = (racine.querySelectorAll ? racine.querySelectorAll(sel) : []);
    Array.prototype.forEach.call(lot, function (img) {
      if (img.hasAttribute('data-milo') || img.closest('#milo-fiche') || img.closest('.milo-av')) return;
      img.setAttribute('data-milo', '');
      img.setAttribute('tabindex', '0');
      img.setAttribute('role', 'button');
      img.setAttribute('title', 'Qui est Milo ?');
    });
  }
  marquer(document);
  if (window.MutationObserver) {
    new MutationObserver(function (muts) {
      muts.forEach(function (m) {
        Array.prototype.forEach.call(m.addedNodes, function (n) {
          if (n.nodeType === 1) marquer(n.matches && n.matches('img') ? n.parentNode || document : n);
        });
      });
    }).observe(document.documentElement, { childList: true, subtree: true });
  }

  document.addEventListener('keydown', function (e) {
    if ((e.key === 'Enter' || e.key === ' ') && e.target && e.target.hasAttribute && e.target.hasAttribute('data-milo')) {
      e.preventDefault(); ouvrir(e.target);
    }
  });

  document.addEventListener('click', function (e) {
    var av = e.target.closest ? e.target.closest('[data-milo]') : null;
    if (av) { e.preventDefault(); ouvrir(av); return; }
    if (e.target.closest && e.target.closest('[data-milo-close]')) { fermer(); return; }
    if (e.target === fiche) fermer();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && fiche.classList.contains('on')) fermer();
  });

  window.MiloFiche = { ouvrir: ouvrir, fermer: fermer };
})();
</script>
<?php
        return ob_get_clean();
    }
}
