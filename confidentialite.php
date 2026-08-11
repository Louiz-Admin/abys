<?php
$page_title = 'Politique de Confidentialité — ABYS AI';
$page_description = 'Comment ABYS AI collecte, utilise et protège vos données personnelles. RGPD.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>
<style>
.legal-wrap { max-width: 780px; margin: 0 auto; padding: 60px 24px 80px; }
.legal-wrap h1 { font-size: 38px; font-weight: 300; letter-spacing: -0.04em; margin-bottom: 8px; }
.legal-wrap h1 strong { font-weight: 700; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.legal-date { font-size: 13px; color: var(--ink-4); margin-bottom: 40px; }
.legal-wrap h2 { font-size: 20px; font-weight: 600; color: var(--ink-2); margin: 36px 0 12px; }
.legal-wrap p, .legal-wrap li { font-size: 15px; color: var(--ink-3); line-height: 1.75; }
.legal-wrap ul { padding-left: 20px; margin: 8px 0 16px; }
.legal-wrap li { margin-bottom: 6px; }
</style>

<div class="legal-wrap">
  <h1>Politique de<br><strong>Confidentialité</strong></h1>
  <p class="legal-date">Dernière mise à jour : avril 2026 — Conforme RGPD</p>

  <h2>1. Responsable du traitement</h2>
  <p>ABYS AI — <a href="mailto:contact@abys.ai">contact@abys.ai</a></p>

  <h2>2. Données collectées</h2>
  <ul>
    <li><strong>URL de votre site web</strong> — pour réaliser l'audit IA</li>
    <li><strong>Adresse email</strong> — pour vous envoyer votre rapport et les communications liées à votre compte</li>
    <li><strong>Données de paiement</strong> — traitées exclusivement par Stripe, jamais stockées sur nos serveurs</li>
    <li><strong>Données techniques</strong> — adresse IP (anonymisée), type de navigateur, pages visitées (analytics anonymes)</li>
  </ul>

  <h2>3. Finalités du traitement</h2>
  <ul>
    <li>Réalisation de l'audit IA et génération du rapport</li>
    <li>Gestion des abonnements et paiements</li>
    <li>Envoi de communications liées au service (pas de marketing sans consentement)</li>
    <li>Amélioration de nos algorithmes (données anonymisées uniquement)</li>
  </ul>

  <h2>4. Base légale</h2>
  <p>Le traitement repose sur l'exécution du contrat (audit, rapport, abonnement) et votre consentement pour les communications marketing.</p>

  <h2>5. Durée de conservation</h2>
  <ul>
    <li>Données de compte : durée de la relation commerciale + 3 ans</li>
    <li>Rapports générés : 90 jours après génération (accès au rapport premium)</li>
    <li>Données de paiement : 5 ans (obligation légale)</li>
  </ul>

  <h2>6. Partage des données</h2>
  <p>Vos données ne sont jamais vendues. Elles sont partagées uniquement avec :</p>
  <ul>
    <li><strong>Anthropic</strong> (Claude AI) — pour l'analyse de votre site, sans données personnelles identifiantes</li>
    <li><strong>Stripe</strong> — pour le traitement sécurisé des paiements</li>
    <li><strong>IONOS</strong> — hébergement des données en Europe</li>
  </ul>

  <h2>7. Vos droits (RGPD)</h2>
  <p>Vous disposez des droits d'accès, de rectification, d'effacement, de portabilité et d'opposition. Pour exercer ces droits : <a href="mailto:contact@abys.ai">contact@abys.ai</a></p>
  <p>En cas de réclamation non résolue, vous pouvez contacter la <strong>CNIL</strong> : <a href="https://www.cnil.fr" target="_blank" rel="noopener">cnil.fr</a></p>

  <h2>8. Cookies</h2>
  <p>Nous utilisons uniquement des cookies strictement nécessaires au fonctionnement du service (session, préférences). Aucun cookie publicitaire ou de tracking tiers.</p>

  <h2>9. Sécurité</h2>
  <p>Vos données sont chiffrées en transit (HTTPS/TLS) et au repos (AES-256). Les clés API et secrets sont chiffrés dans notre base de données.</p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
