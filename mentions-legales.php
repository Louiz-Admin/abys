<?php
$page_title = 'Mentions Légales · ABYS AI';
$page_description = 'Mentions légales de ABYS AI, service d\'audit IA pour PME et TPE françaises.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>
.legal-page { max-width: 860px; margin: 0 auto; padding: 72px 40px 96px; }

/* Header */
.legal-header { margin-bottom: 56px; }
.legal-header h1 {
  font-size: 42px; font-weight: 300;
  letter-spacing: -0.04em; margin: 0 0 8px;
}
.legal-header h1 strong { font-weight: 800; }
.legal-meta {
  font-size: 13px; color: var(--ink-4);
  display: flex; align-items: center; gap: 8px;
}
.legal-meta svg { stroke: var(--ink-4); }

/* Team section */
.legal-team-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 24px;
}
.legal-team-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}
.legal-team-card img {
  width: 100%; aspect-ratio: 1;
  object-fit: cover; object-position: top;
  display: block;
  filter: grayscale(20%);
}
.legal-team-card-body { padding: 12px 14px; }
.legal-team-card-name {
  font-size: 13px; font-weight: 700; color: var(--ink-2);
}
.legal-team-card-role {
  font-size: 11px; color: var(--ink-4); margin-top: 2px;
}

/* Sections */
.legal-section { margin-bottom: 40px; }
.legal-section h2 {
  font-size: 16px; font-weight: 700;
  color: var(--ink-2); margin: 0 0 12px;
  display: flex; align-items: center; gap: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--border);
}
.legal-section h2 svg { stroke: var(--green); flex-shrink: 0; }
.legal-section p {
  font-size: 15px; color: var(--ink-3); line-height: 1.8;
  margin: 0 0 8px;
}
.legal-section p:last-child { margin-bottom: 0; }
.legal-section a { color: var(--green-deep); }

@media (max-width: 700px) {
  .legal-team-grid { grid-template-columns: repeat(2, 1fr); }
  .legal-page { padding: 48px 24px 72px; }
}
@media (max-width: 400px) {
  .legal-team-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="legal-page">

  <div class="legal-header">
    <h1>Mentions<br><strong>Légales</strong></h1>
    <div class="legal-meta">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Dernière mise à jour : mai 2026
    </div>
  </div>

  <!-- Éditeur -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      Éditeur du site
    </h2>
    <p>
      <strong>ABYS AI</strong> · Entreprise individuelle, France<br>
      Responsable : <strong>Thomas Capiten</strong>, fondateur et dirigeant<br>
      Email : <a href="mailto:contact@abys.ai">contact@abys.ai</a><br>
      Site : <a href="https://abys.ai">https://abys.ai</a>
    </p>
  </div>

  <!-- Directeur de publication -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Directeur de publication
    </h2>
    <p><strong>Thomas Capiten</strong> · fondateur d'ABYS AI, responsable éditorial du site.</p>
  </div>

  <!-- Équipe -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 1-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      L'équipe ABYS AI
    </h2>
    <p>ABYS AI est une structure à taille humaine, portée par une équipe de quatre personnes passionnées par l'IA appliquée aux entreprises.</p>

    <div class="legal-team-grid">
      <div class="legal-team-card">
        <img src="/assets/img/thomas.jpg" alt="Thomas Capiten">
        <div class="legal-team-card-body">
          <div class="legal-team-card-name">Thomas Capiten</div>
          <div class="legal-team-card-role">Fondateur &amp; Stratégie IA</div>
        </div>
      </div>
      <div class="legal-team-card">
        <img src="/assets/img/lea.jpg" alt="Léa Fontaine">
        <div class="legal-team-card-body">
          <div class="legal-team-card-name">Léa Fontaine</div>
          <div class="legal-team-card-role">Conseil IA &amp; Partenariats</div>
        </div>
      </div>
      <div class="legal-team-card">
        <img src="/assets/img/romain.jpg" alt="Romain Delacroix">
        <div class="legal-team-card-body">
          <div class="legal-team-card-name">Romain Delacroix</div>
          <div class="legal-team-card-role">Développeur IA &amp; Intégrations</div>
        </div>
      </div>
      <div class="legal-team-card">
        <img src="/assets/img/sophie.jpg" alt="Sophie Mariani">
        <div class="legal-team-card-body">
          <div class="legal-team-card-name">Sophie Mariani</div>
          <div class="legal-team-card-role">Support &amp; Onboarding</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Hébergement -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      Hébergement
    </h2>
    <p>
      <strong>IONOS SE</strong><br>
      Elgendorfer Str. 57, 56410 Montabaur, Allemagne<br>
      <a href="https://www.ionos.fr" target="_blank" rel="noopener">www.ionos.fr</a>
    </p>
  </div>

  <!-- Propriété intellectuelle -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Propriété intellectuelle
    </h2>
    <p>L'ensemble des contenus présents sur le site abys.ai (textes, images, logos, code, design) sont protégés par le droit d'auteur. Toute reproduction, même partielle, est interdite sans autorisation préalable écrite d'ABYS AI.</p>
  </div>

  <!-- Responsabilité -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Responsabilité
    </h2>
    <p>ABYS AI s'efforce de fournir des informations exactes et à jour. Les recommandations générées par notre IA sont indicatives et ne constituent pas un conseil professionnel au sens juridique. ABYS AI ne peut être tenu responsable de décisions prises sur la base des informations ou simulations fournies.</p>
  </div>

  <!-- RGPD -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Données personnelles &amp; RGPD
    </h2>
    <p>Les données collectées (email, nom, informations sur l'entreprise) sont utilisées exclusivement pour la fourniture de nos services d'audit et d'accompagnement. Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression. Pour exercer ces droits : <a href="mailto:contact@abys.ai">contact@abys.ai</a>.</p>
  </div>

  <!-- Cookies -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01"/><path d="M16 15.5v.01"/><path d="M12 12v.01"/></svg>
      Cookies
    </h2>
    <p>Le site utilise uniquement des cookies techniques nécessaires au bon fonctionnement du service (sessionStorage pour votre audit). Aucun cookie publicitaire tiers n'est déposé sans votre consentement préalable.</p>
  </div>

  <!-- Médiation -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      Médiation des litiges
    </h2>
    <p>En cas de litige non résolu à l'amiable, vous pouvez recourir à la médiation de la consommation via la plateforme européenne : <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">ec.europa.eu/consumers/odr</a>.</p>
  </div>

  <!-- Droit applicable -->
  <div class="legal-section">
    <h2>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      Droit applicable
    </h2>
    <p>Le présent site et ses CGV sont soumis au droit français. En cas de litige, les tribunaux français sont compétents.</p>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
