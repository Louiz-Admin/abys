<?php
$page_title = 'Qui sommes-nous — ABYS AI';
$page_description = 'L\'équipe ABYS AI — Thomas Capiten et une équipe passionnée qui aide les PME et TPE françaises à adopter l\'IA concrètement.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>

/* ── Reset & base ── */
.about-page { overflow-x: hidden; }

/* ── Hero split ── */
.about-hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 520px;
  align-items: stretch;
}
.about-hero-left {
  padding: 80px 64px 80px 40px;
  display: flex; flex-direction: column; justify-content: center;
  max-width: 600px;
  margin-left: auto;
}
.about-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 12px; font-weight: 700; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--green-deep);
  margin-bottom: 24px;
}
.about-eyebrow svg { stroke: var(--green); }
.about-hero h1 {
  font-size: clamp(36px, 4vw, 54px);
  font-weight: 300;
  letter-spacing: -0.04em;
  line-height: 1.1;
  margin: 0 0 20px;
}
.about-hero h1 strong { font-weight: 800; }
.about-hero p {
  font-size: 17px; color: var(--ink-3);
  line-height: 1.75; margin: 0 0 32px;
}
.about-hero-right {
  position: relative; overflow: hidden;
  min-height: 420px;
}
.about-hero-right img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center;
  display: block;
}
.about-hero-right::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(to right, rgba(255,255,255,0.08) 0%, transparent 40%);
}

/* ── Numbers bar ── */
.about-numbers {
  background: var(--ink-2);
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.about-number {
  padding: 36px 24px;
  text-align: center;
  border-right: 1px solid rgba(255,255,255,0.08);
}
.about-number:last-child { border-right: none; }
.about-number-val {
  font-size: 42px; font-weight: 800;
  color: #10B981; letter-spacing: -0.04em;
  line-height: 1;
}
.about-number-lbl {
  font-size: 12px; font-weight: 500;
  color: rgba(255,255,255,0.45); margin-top: 6px;
  text-transform: uppercase; letter-spacing: 0.06em;
}

/* ── Story ── */
.about-story {
  max-width: 860px; margin: 0 auto;
  padding: 88px 40px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: start;
}
.story-lead {
  font-size: 22px; font-weight: 700; color: var(--ink-2);
  line-height: 1.4; letter-spacing: -0.02em;
  margin-bottom: 24px;
}
.story-body p {
  font-size: 15.5px; color: var(--ink-3);
  line-height: 1.8; margin-bottom: 20px;
}
.story-body p:last-child { margin-bottom: 0; }
.story-quote {
  border-left: 2px solid var(--green);
  padding: 24px 28px;
  background: var(--bg);
  border-radius: 0 12px 12px 0;
}
.story-quote p {
  font-size: 17px; color: var(--ink-2);
  font-style: italic; line-height: 1.7;
  margin: 0 0 16px;
}
.story-quote-author {
  display: flex; align-items: center; gap: 12px;
}
.story-quote-photo {
  width: 44px; height: 44px; border-radius: 50%;
  object-fit: cover; border: 2px solid var(--border);
}
.story-quote-name {
  font-size: 13px; font-weight: 700; color: var(--ink-2);
}
.story-quote-role {
  font-size: 12px; color: var(--ink-4); margin-top: 1px;
}

/* ── Team ── */
.about-team {
  background: var(--bg);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 88px 40px;
}
.about-team-inner { max-width: 1040px; margin: 0 auto; }
.section-header { margin-bottom: 56px; }
.section-header h2 {
  font-size: 38px; font-weight: 300;
  letter-spacing: -0.04em; margin: 0 0 10px;
}
.section-header h2 strong { font-weight: 800; }
.section-header p {
  font-size: 16px; color: var(--ink-3); max-width: 480px;
}
.team-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 28px;
}
.team-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: transform 220ms cubic-bezier(.2,.8,.2,1), box-shadow 220ms;
}
.team-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 48px rgba(0,0,0,0.10);
}
.team-card-photo {
  width: 100%; aspect-ratio: 1;
  object-fit: cover; object-position: top center;
  display: block;
  filter: grayscale(15%);
  transition: filter 220ms;
}
.team-card:hover .team-card-photo { filter: grayscale(0); }
.team-card-body { padding: 20px; }
.team-card-name {
  font-size: 16px; font-weight: 700; color: var(--ink-2);
  margin-bottom: 3px;
}
.team-card-role {
  font-size: 12px; font-weight: 600;
  color: var(--green-deep); letter-spacing: 0.02em;
  margin-bottom: 10px;
}
.team-card-bio {
  font-size: 13px; color: var(--ink-3); line-height: 1.6;
}

/* ── Values ── */
.about-values {
  max-width: 1040px; margin: 0 auto;
  padding: 88px 40px;
}
.values-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  margin-top: 48px;
}
.value-card {
  display: grid;
  grid-template-columns: 48px 1fr;
  gap: 20px;
  align-items: start;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 28px;
  box-shadow: var(--shadow-sm);
}
.value-icon {
  width: 48px; height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(14,165,233,0.07));
  border: 1px solid rgba(16,185,129,0.2);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.value-icon svg { stroke: #10B981; }
.value-title {
  font-size: 15px; font-weight: 700; color: var(--ink-2);
  margin-bottom: 6px;
}
.value-desc {
  font-size: 14px; color: var(--ink-3); line-height: 1.65;
}

/* ── Beta ── */
.about-beta {
  background: linear-gradient(160deg, #0A1F1A 0%, #051510 100%);
  padding: 88px 40px;
}
.about-beta-inner {
  max-width: 920px; margin: 0 auto;
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 72px; align-items: center;
}
.beta-left h2 {
  font-size: 38px; font-weight: 300;
  letter-spacing: -0.04em; color: #fff;
  margin: 0 0 16px; line-height: 1.15;
}
.beta-left h2 strong { font-weight: 800; color: #10B981; }
.beta-left p {
  font-size: 16px; color: rgba(255,255,255,0.55);
  line-height: 1.75;
}
.beta-stats {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.beta-stat {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px;
  padding: 24px 20px;
  text-align: center;
}
.beta-stat-val {
  font-size: 40px; font-weight: 800;
  color: #10B981; letter-spacing: -0.04em;
  line-height: 1;
}
.beta-stat-lbl {
  font-size: 12px; color: rgba(255,255,255,0.4);
  margin-top: 6px; text-transform: uppercase; letter-spacing: 0.06em;
}

/* ── CTA ── */
.about-cta {
  text-align: center;
  padding: 88px 40px;
}
.about-cta h2 {
  font-size: 38px; font-weight: 300;
  letter-spacing: -0.04em; margin: 0 0 14px;
}
.about-cta h2 strong { font-weight: 800; }
.about-cta p { font-size: 16px; color: var(--ink-3); margin: 0 0 32px; }
.about-cta-links { margin-top: 20px; }
.about-cta-links a {
  font-size: 14px; color: var(--ink-4);
  text-decoration: none; transition: color 150ms;
}
.about-cta-links a:hover { color: var(--green-deep); }

/* ── Responsive ── */
@media (max-width: 1024px) {
  .team-grid { grid-template-columns: repeat(2, 1fr); }
  .about-beta-inner { grid-template-columns: 1fr; gap: 48px; }
}
@media (max-width: 800px) {
  .about-hero { grid-template-columns: 1fr; }
  .about-hero-right { min-height: 280px; order: -1; }
  .about-hero-left { padding: 48px 24px; max-width: 100%; margin: 0; }
  .about-numbers { grid-template-columns: repeat(2, 1fr); }
  .about-number:nth-child(2) { border-right: none; }
  .about-story { grid-template-columns: 1fr; gap: 48px; padding: 56px 24px; }
  .values-grid { grid-template-columns: 1fr; }
  .about-team, .about-values, .about-beta, .about-cta { padding: 56px 24px; }
}
@media (max-width: 540px) {
  .team-grid { grid-template-columns: 1fr 1fr; }
  .about-numbers { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="about-page">

  <!-- ── Hero split ── -->
  <div class="about-hero">
    <div class="about-hero-left">
      <div class="about-eyebrow">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Notre équipe
      </div>
      <h1>Derrière ABYS AI,<br><strong>des humains</strong><br>passionnés</h1>
      <p>Pas un grand cabinet, pas une usine à gaz. Une équipe de quatre personnes convaincues que l'IA peut transformer le quotidien des entrepreneurs — sans être réservée aux grands groupes.</p>
      <a href="/contact.php" class="btn btn-primary">Nous contacter</a>
    </div>
    <div class="about-hero-right">
      <img src="/assets/img/team-hero.jpg" alt="L'équipe ABYS AI au travail" loading="eager">
    </div>
  </div>

  <!-- ── Numbers ── -->
  <div class="about-numbers">
    <div class="about-number">
      <div class="about-number-val">47</div>
      <div class="about-number-lbl">Beta-testeurs</div>
    </div>
    <div class="about-number">
      <div class="about-number-val">12</div>
      <div class="about-number-lbl">Secteurs testés</div>
    </div>
    <div class="about-number">
      <div class="about-number-val">6h</div>
      <div class="about-number-lbl">Gagnées / semaine en moy.</div>
    </div>
    <div class="about-number">
      <div class="about-number-val">3</div>
      <div class="about-number-lbl">Mois de beta privée</div>
    </div>
  </div>

  <!-- ── Story ── -->
  <div class="about-story">

    <div class="story-quote">
      <p>"J'ai créé ABYS AI après avoir vu des dizaines de PME passer à côté de l'IA — non par manque d'intérêt, mais par manque d'accompagnement concret. Les grandes agences coûtent une fortune, les formations prennent des mois. Il fallait quelque chose de simple, d'honnête, d'efficace."</p>
      <div class="story-quote-author">
        <img src="/assets/img/thomas.jpg" alt="Thomas Capiten" class="story-quote-photo">
        <div>
          <div class="story-quote-name">Thomas Capiten</div>
          <div class="story-quote-role">Fondateur, ABYS AI</div>
        </div>
      </div>
    </div>

    <div class="story-body">
      <p class="story-lead">On a commencé avec 47 entreprises volontaires pendant 3 mois.</p>
      <p>Artisans, commerçants, consultants, agences — ils nous ont dit ce qui marchait, ce qui ne marchait pas, ce qui était trop compliqué. Ce produit, c'est autant le leur que le nôtre.</p>
      <p>Notre conviction : l'IA ne remplace pas tout le monde, mais elle peut libérer 5h à 10h par semaine sur des tâches répétitives. Ces heures retrouvées, c'est du temps pour faire ce que vous aimez vraiment dans votre métier.</p>
      <p>Aujourd'hui ABYS AI analyse des centaines de sites et accompagne des dirigeants dans toute la France. On reste une équipe à taille humaine — et on compte le rester.</p>
    </div>

  </div>

  <!-- ── Team ── -->
  <div class="about-team">
    <div class="about-team-inner">
      <div class="section-header">
        <h2>L'équipe au <strong>complet</strong></h2>
        <p>Quatre personnes, une mission : rendre l'IA accessible à chaque entrepreneur français.</p>
      </div>

      <div class="team-grid">

        <div class="team-card">
          <img src="/assets/img/thomas.jpg" alt="Thomas Capiten" class="team-card-photo">
          <div class="team-card-body">
            <div class="team-card-name">Thomas Capiten</div>
            <div class="team-card-role">Fondateur &amp; Stratégie IA</div>
            <div class="team-card-bio">Entrepreneur depuis 10 ans, passé par le conseil en transformation digitale. Convaincu que l'IA est l'outil le plus puissant de la décennie pour les TPE/PME.</div>
          </div>
        </div>

        <div class="team-card">
          <img src="/assets/img/lea.jpg" alt="Léa Fontaine" class="team-card-photo">
          <div class="team-card-body">
            <div class="team-card-name">Léa Fontaine</div>
            <div class="team-card-role">Conseil IA &amp; Partenariats</div>
            <div class="team-card-bio">Ancienne consultante en transformation digitale. Elle pilote nos audits terrain et nos partenariats avec les OPCO et organismes de financement.</div>
          </div>
        </div>

        <div class="team-card">
          <img src="/assets/img/romain.jpg" alt="Romain Delacroix" class="team-card-photo">
          <div class="team-card-body">
            <div class="team-card-name">Romain Delacroix</div>
            <div class="team-card-role">Développeur IA &amp; Intégrations</div>
            <div class="team-card-bio">Il construit les outils qui font tourner ABYS AI : l'analyse automatique, les algorithmes de recommandation et les intégrations avec les logiciels métier.</div>
          </div>
        </div>

        <div class="team-card">
          <img src="/assets/img/sophie.jpg" alt="Sophie Mariani" class="team-card-photo">
          <div class="team-card-body">
            <div class="team-card-name">Sophie Mariani</div>
            <div class="team-card-role">Support &amp; Onboarding</div>
            <div class="team-card-bio">Elle accompagne chaque client Premium de la première question jusqu'à la première automatisation opérationnelle. Réponse garantie sous 4h.</div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ── Values ── -->
  <div class="about-values">
    <div class="section-header">
      <h2>Ce qu'on <strong>croit vraiment</strong></h2>
      <p>Pas un manifeste marketing. Juste ce qui guide nos décisions au quotidien.</p>
    </div>

    <div class="values-grid">

      <div class="value-card">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <div>
          <div class="value-title">Le concret avant le jargon</div>
          <div class="value-desc">On ne vous parle pas de "machine learning". On vous dit : cette tâche peut être automatisée en 20 minutes, voilà exactement comment le faire.</div>
        </div>
      </div>

      <div class="value-card">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <div>
          <div class="value-title">L'honnêteté comme base</div>
          <div class="value-desc">Si l'IA n'est pas la bonne réponse pour vous, on vous le dit. On préfère vous perdre comme client que vous conseiller quelque chose d'inutile.</div>
        </div>
      </div>

      <div class="value-card">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
        <div>
          <div class="value-title">Le ROI d'abord</div>
          <div class="value-desc">Chaque outil qu'on recommande doit vous rapporter plus qu'il ne coûte — en temps, en argent ou en stress évité. On ne fait pas dans la gadgeterie.</div>
        </div>
      </div>

      <div class="value-card">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
        </div>
        <div>
          <div class="value-title">Vos données vous appartiennent</div>
          <div class="value-desc">On ne revend rien, on ne partage rien. Vos informations servent uniquement à vous fournir votre audit et votre rapport.</div>
        </div>
      </div>

    </div>
  </div>

  <!-- ── Beta ── -->
  <div class="about-beta">
    <div class="about-beta-inner">
      <div class="beta-left">
        <h2>Construit avec<br><strong>nos beta-testeurs</strong></h2>
        <p>Avant de lancer ABYS AI publiquement, on a travaillé pendant 3 mois avec 47 entrepreneurs volontaires. Ils nous ont dit ce qui marchait, ce qui ne marchait pas, ce qui était trop compliqué. Ce produit, c'est autant le leur que le nôtre.</p>
      </div>
      <div class="beta-stats">
        <div class="beta-stat">
          <div class="beta-stat-val">47</div>
          <div class="beta-stat-lbl">Beta-testeurs</div>
        </div>
        <div class="beta-stat">
          <div class="beta-stat-val">12</div>
          <div class="beta-stat-lbl">Secteurs</div>
        </div>
        <div class="beta-stat">
          <div class="beta-stat-val">6h</div>
          <div class="beta-stat-lbl">Gagnées / sem.</div>
        </div>
        <div class="beta-stat">
          <div class="beta-stat-val">3</div>
          <div class="beta-stat-lbl">Mois de test</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── CTA ── -->
  <div class="about-cta">
    <h2>Prêt à voir ce que l'IA<br><strong>peut faire pour vous ?</strong></h2>
    <p>L'audit est gratuit et prend 2 minutes. On s'occupe du reste.</p>
    <a href="/" class="btn btn-primary btn-lg">Lancer mon audit gratuit</a>
    <div class="about-cta-links">
      <a href="/contact.php">Ou contactez-nous directement</a>
    </div>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
