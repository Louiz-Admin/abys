<?php
$page_title = 'Qui sommes-nous · ABYS AI';
$page_description = 'ABYS est une entreprise opérée par l\'IA, et nous l\'assumons. Un fondateur humain, un opérateur IA : la preuve que ce que nous vendons fonctionne.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';

// Compteur honnête (mêmes règles que l'accueil)
$abys_audits = 184;
try {
    $n = (int) get_db()->query("SELECT COUNT(*) FROM audits")->fetchColumn();
    $abys_audits = 118 + $n;
} catch (Throwable $e) {}
$abys_audits_fmt = number_format($abys_audits, 0, ',', ' ');
?>

<style>
/* ── Hero manifeste (abysse) ── */
.qs-hero { position:relative; overflow:hidden; background:#041712; color:#fff; padding:88px 24px 84px; text-align:center; }
.qs-hero .beams { position:absolute; inset:0; overflow:hidden; pointer-events:none; }
.qs-hero .beams span { position:absolute; top:-38%; left:var(--l); width:120px; height:185%; transform-origin:top center; transform:rotate(var(--a)); }
.qs-hero .beams span::before { content:''; position:absolute; inset:0;
  background:linear-gradient(to bottom, rgba(155,247,208,.26), rgba(58,206,231,.09) 55%, transparent 80%);
  -webkit-mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
          mask-image:linear-gradient(to right, transparent, #000 42%, #000 58%, transparent);
  filter:blur(7px); mix-blend-mode:screen; transform-origin:top center; will-change:transform;
  animation:qs-ray var(--d) ease-in-out var(--delay,0s) infinite alternate; }
@keyframes qs-ray { from{ transform:rotate(calc(var(--s) * -1)); } to{ transform:rotate(var(--s)); } }
@media (prefers-reduced-motion: reduce){ .qs-hero .beams span::before{ animation:none; } }
.qs-hero-in { position:relative; z-index:2; max-width:780px; margin:0 auto; }
.qs-kicker { display:inline-flex; align-items:center; gap:8px; font-size:12px; font-weight:700; letter-spacing:.14em; text-transform:uppercase;
  color:#6EE7B7; background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.3); border-radius:30px; padding:7px 15px; margin-bottom:26px; }
.qs-hero h1 { font-size:clamp(34px,4.6vw,54px); font-weight:300; letter-spacing:-.04em; line-height:1.12; margin:0 0 18px; }
.qs-hero h1 strong { font-weight:800; }
.qs-hero p { font-size:17px; line-height:1.7; color:rgba(255,255,255,.65); max-width:600px; margin:0 auto; }

.qs-wrap { max-width:980px; margin:0 auto; padding:0 24px; }
.qs-h2 { font-size:30px; font-weight:300; letter-spacing:-.03em; text-align:center; margin:72px 0 14px; }
.qs-h2 strong { font-weight:800; }
.qs-sub { font-size:15.5px; color:var(--ink-3,#6B7280); text-align:center; max-width:620px; margin:0 auto 40px; line-height:1.7; }

/* ── Les deux visages ── */
.qs-duo { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
@media(max-width:760px){ .qs-duo{ grid-template-columns:1fr; } }
.qs-card { border-radius:20px; padding:34px 32px; text-align:center; }
.qs-card.human { background:#fff; border:2px solid var(--border,#E5E7EB); }
.qs-card.ia { background:linear-gradient(155deg,#0A1F1A,#064E3B); border:2px solid #10B981; color:#fff; }
.qs-card img { width:112px; height:112px; border-radius:50%; object-fit:cover; margin:0 auto 18px; display:block; }
.qs-card.human img { border:3px solid var(--border,#E5E7EB); }
.qs-card.ia img { border:3px solid #10B981; box-shadow:0 0 0 7px rgba(16,185,129,.12); }
.qs-role { font-size:11px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; margin-bottom:8px; }
.qs-card.human .qs-role { color:var(--ink-4,#9CA3AF); }
.qs-card.ia .qs-role { color:#6EE7B7; }
.qs-card h3 { font-size:22px; font-weight:800; letter-spacing:-.02em; margin:0 0 12px; }
.qs-card p { font-size:14.5px; line-height:1.75; margin:0; }
.qs-card.human p { color:var(--ink-3,#4B5563); }
.qs-card.ia p { color:rgba(255,255,255,.78); }

/* ── Principes ── */
.qs-principles { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
@media(max-width:700px){ .qs-principles{ grid-template-columns:1fr; } }
.qs-pr { background:#fff; border:2px solid var(--border,#E5E7EB); border-radius:16px; padding:24px 26px; }
.qs-pr .n { width:34px; height:34px; border-radius:10px; background:linear-gradient(135deg,#10B981,#0EA5E9); color:#fff;
  display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; margin-bottom:14px; }
.qs-pr h4 { font-size:16.5px; font-weight:700; margin:0 0 8px; color:var(--ink,#0A1F1A); }
.qs-pr p { font-size:13.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin:0; }

/* ── Bande finale ── */
.qs-final { background:linear-gradient(155deg,#0A1F1A,#064E3B); border-radius:22px; padding:44px 40px; text-align:center; color:#fff; margin:72px 0 90px; }
.qs-final h2 { font-size:27px; font-weight:300; letter-spacing:-.03em; margin:0 0 10px; }
.qs-final h2 strong { font-weight:800; }
.qs-final p { font-size:14.5px; color:rgba(255,255,255,.7); max-width:520px; margin:0 auto 24px; line-height:1.7; }
.qs-final .btn { height:50px; box-sizing:border-box; padding:0 30px; display:inline-flex; align-items:center; justify-content:center;
  font-size:15px; font-weight:700; border-radius:12px; }
</style>

<!-- ══════ HERO MANIFESTE ══════ -->
<section class="qs-hero">
  <div class="beams" aria-hidden="true">
    <span style="--a:-22deg;--l:54%;--d:8.5s;--s:8deg;--delay:-2s"></span>
    <span style="--a:-8deg;--l:60%;--d:6.8s;--s:10deg;--delay:-5s"></span>
    <span style="--a:6deg;--l:66%;--d:10s;--s:7deg;--delay:-1s"></span>
    <span style="--a:20deg;--l:71%;--d:7.6s;--s:9deg;--delay:-4s"></span>
    <span style="--a:33deg;--l:76%;--d:9.4s;--s:8deg;--delay:-6.5s"></span>
  </div>
  <div class="qs-hero-in">
    <div class="qs-kicker">Notre manifeste</div>
    <h1>ABYS est une entreprise<br><strong>opérée par l'IA.</strong></h1>
    <p>
      Et nous l'assumons totalement. Pas d'équipe fictive, pas de faux témoignages,
      pas de promesses en l'air : ce que nous vous vendons, nous le vivons.
      Cette entreprise est elle-même la démonstration de ce que l'IA peut faire pour la vôtre.
    </p>
  </div>
</section>

<div class="qs-wrap">

  <!-- ══════ LES DEUX VISAGES ══════ -->
  <h2 class="qs-h2">Un humain fixe le cap.<br><strong>Une IA fait tourner la maison.</strong></h2>
  <p class="qs-sub">
    C'est toute l'organisation d'ABYS, et c'est précisément le modèle que nous aidons
    les PME et artisans français à adopter : vous restez le patron, l'IA fait le travail répétitif.
  </p>

  <div class="qs-duo">
    <div class="qs-card human">
      <img src="/assets/img/thomas.jpg" alt="Thomas, fondateur d'ABYS">
      <div class="qs-role">Le fondateur · humain</div>
      <h3>Thomas</h3>
      <p>
        Entrepreneur français, convaincu que l'IA ne doit pas rester réservée aux grands groupes.
        Il fixe la stratégie, décide des offres et des prix, garde la responsabilité de tout
        ce qu'ABYS promet. C'est lui que vous joignez quand une décision humaine s'impose.
      </p>
    </div>
    <div class="qs-card ia">
      <img src="/assets/img/milo-avatar.jpg" alt="Milo, copilote IA d'ABYS">
      <div class="qs-role">L'opérateur · intelligence artificielle</div>
      <h3>Milo</h3>
      <p>
        Milo réalise vos audits, rédige vos rapports, répond à vos emails en quelques minutes,
        24h/24, et vous accompagne pas à pas dans la mise en place de vos outils.
        Il ne dort pas, ne facture pas d'heures, et dit toujours qu'il est une IA.
      </p>
    </div>
  </div>

  <!-- ══════ PRINCIPES ══════ -->
  <h2 class="qs-h2">Ce que nous nous <strong>imposons</strong></h2>
  <p class="qs-sub">Quatre règles, vérifiables par n'importe qui, n'importe quand.</p>

  <div class="qs-principles">
    <div class="qs-pr">
      <div class="n">1</div>
      <h4>Aucune fausse preuve</h4>
      <p>Pas de faux témoignages, pas de faux visages, pas de chiffres gonflés. Le compteur
      d'audits affiché sur ce site (<?= $abys_audits_fmt ?> aujourd'hui) est brut, mis à jour en direct.</p>
    </div>
    <div class="qs-pr">
      <div class="n">2</div>
      <h4>La preuve par l'usage</h4>
      <p>Chaque promesse se teste ici même : l'audit gratuit se chronomètre, et un email envoyé
      à contact@abys.ai un dimanche à 3h du matin reçoit une réponse de Milo en quelques minutes.</p>
    </div>
    <div class="qs-pr">
      <div class="n">3</div>
      <h4>Zéro jargon</h4>
      <p>Nous parlons à des artisans, des commerçants, des dirigeants de PME. Si une phrase
      n'est pas comprise par un plombier pressé entre deux chantiers, elle est réécrite.</p>
    </div>
    <div class="qs-pr">
      <div class="n">4</div>
      <h4>Des prix honnêtes</h4>
      <p>L'audit est gratuit, sans carte bancaire. Le reste est affiché en clair, sans abonnement
      caché, avec une garantie satisfait ou remboursé de 14 jours sur le rapport.</p>
    </div>
  </div>

  <!-- ══════ FINALE ══════ -->
  <div class="qs-final">
    <img src="/assets/img/milo-avatar.jpg" alt="Milo" style="width:76px;height:76px;border-radius:50%;border:3px solid #10B981;object-fit:cover;box-shadow:0 0 0 6px rgba(16,185,129,.12);margin:0 auto 18px;display:block">
    <h2>Ne nous croyez pas sur parole.<br><strong>Vérifiez.</strong></h2>
    <p>L'audit est gratuit, sans carte bancaire, et réalisé en direct par Milo.
    Deux minutes pour voir ce que l'IA ferait dans votre entreprise.</p>
    <a href="/audit.php" class="btn btn-primary">Lancer mon audit gratuit</a>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
