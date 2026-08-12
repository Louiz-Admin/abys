<?php
$page_title = 'Tarifs · ABYS AI';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<style>
/* ── Tarifs ── */
.tarifs-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  max-width: 1120px;
  margin: 0 auto 56px;
}
.tarif-card {
  border-radius: var(--r-xl);
  padding: 28px 24px 24px;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  text-align: left;               /* fini le centrage hérité des textes multi-lignes */
}
.tarif-card.light {
  background: #F4FFFC;
  border: 1px solid var(--border);
}
.tarif-card.dark {
  background: linear-gradient(150deg, #0A1F1A 0%, #064E3B 100%);
  border: 2px solid var(--green);
  color: #fff;
}
.tarif-card.accent {
  background: linear-gradient(150deg, #0D0D1F 0%, #1E1B4B 100%);
  border: 2px solid #7C3AED;
  color: #fff;
}
.tarif-badge-top {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
}
.tarif-plan {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  margin-bottom: 14px;
}
.tarif-card.light .tarif-plan { color: var(--ink-4); }
.tarif-card.dark  .tarif-plan { color: rgba(255,255,255,0.5); }
.tarif-card.accent .tarif-plan { color: rgba(255,255,255,0.5); }

.tarif-name {
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 4px;
}
.tarif-card.light .tarif-name { color: var(--ink-3); }
.tarif-card.dark  .tarif-name { color: rgba(255,255,255,0.7); }
.tarif-card.accent .tarif-name { color: rgba(255,255,255,0.7); }

.tarif-name { min-height: 18px; }
.tarif-price {
  font-size: 38px;
  font-weight: 700;
  letter-spacing: -0.04em;
  line-height: 1;
  margin-bottom: 2px;
  min-height: 40px;               /* même hauteur avec ou sans prix barré */
  display: flex; align-items: baseline;
}
.tarif-card.light  .tarif-price { color: var(--ink-2); }
.tarif-card.dark   .tarif-price { color: var(--green-2); }
.tarif-card.accent .tarif-price { color: #A78BFA; }

.tarif-period {
  font-size: 13px;
  margin-bottom: 16px;
  min-height: 36px;               /* 2 lignes max : les cartes restent calées */
}
.tarif-card.light  .tarif-period { color: var(--ink-4); }
.tarif-card.dark   .tarif-period { color: rgba(255,255,255,0.45); }
.tarif-card.accent .tarif-period { color: rgba(255,255,255,0.45); }

/* Aide inline */
.tarif-aide {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 10px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 18px;
  min-height: 46px;               /* badge 1 ou 2 lignes : même encombrement partout */
  line-height: 1.35;
}
.tarif-card.light .tarif-aide {
  background: rgba(16,185,129,0.1);
  color: var(--green-deep);
  border: 1px solid rgba(16,185,129,0.2);
}
.tarif-card.dark .tarif-aide {
  background: rgba(16,185,129,0.15);
  color: #6EE7B7;
  border: 1px solid rgba(16,185,129,0.3);
}
.tarif-card.accent .tarif-aide {
  background: rgba(167,139,250,0.15);
  color: #C4B5FD;
  border: 1px solid rgba(167,139,250,0.3);
}

.tarif-features {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 24px;
  flex: 1;
}
.tarif-feature {
  font-size: 13px;
  display: flex;
  align-items: flex-start;
  gap: 7px;
  line-height: 1.4;
}
.tarif-card.light  .tarif-feature { color: var(--ink-3); }
.tarif-card.dark   .tarif-feature { color: rgba(255,255,255,0.8); }
.tarif-card.accent .tarif-feature { color: rgba(255,255,255,0.8); }

.tarif-check {
  flex-shrink: 0;
  margin-top: 3px;
}
.tarif-card .btn { margin-top: auto; }   /* CTA calés en bas, à la même hauteur */

/* Prix net après aide */
.tarif-net {
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 8px;
  margin-bottom: 16px;
  text-align: center;
}
.tarif-card.dark .tarif-net {
  background: rgba(255,255,255,0.06);
  color: rgba(255,255,255,0.55);
}
.tarif-card.accent .tarif-net {
  background: rgba(255,255,255,0.06);
  color: rgba(255,255,255,0.55);
}

@media (max-width: 900px) {
  .tarifs-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 540px) {
  .tarifs-grid { grid-template-columns: 1fr; }
}
</style>

<div class="container" style="padding-top:60px;padding-bottom:80px;text-align:center">
  <div class="badge" style="margin:0 auto 16px">Transparent</div>
  <h1 style="font-size:44px;font-weight:300;letter-spacing:-0.04em;margin-bottom:14px">
    Commencez gratuitement.<br><strong style="font-weight:700">Grandissez à votre rythme.</strong>
  </h1>
  <p style="font-size:17px;color:var(--ink-3);margin-bottom:48px">
    Aucun abonnement caché. Aucune obligation.<br>Et des aides de l'État pour financer jusqu'à 100% de votre investissement.
  </p>

  <?php if (isset($_GET['cancelled'])): ?>
  <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:var(--r-md);padding:14px;margin-bottom:32px;font-size:14px;color:#B45309;max-width:480px;margin-left:auto;margin-right:auto">
    Paiement annulé. Vos résultats d'audit sont toujours disponibles.
  </div>
  <?php endif; ?>

  <div class="tarifs-grid">

    <!-- Gratuit -->
    <div class="tarif-card light">
      <div class="tarif-plan">Découverte</div>
      <div class="tarif-name">Audit gratuit</div>
      <div class="tarif-price">0€</div>
      <div class="tarif-period">gratuit</div>
      <div class="tarif-aide">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Sans carte bancaire
      </div>
      <div class="tarif-features">
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Score IA de votre entreprise</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 3 opportunités identifiées</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Simulation rapide des gains</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Logo et profil entreprise</div>
      </div>
      <a href="/audit.php" class="btn btn-secondary" style="display:flex;justify-content:center">Démarrer gratuitement →</a>
    </div>

    <!-- Rapport Premium -->
    <div class="tarif-card dark">
      <div class="tarif-plan">Passage à l'action</div>
      <div class="tarif-name">Rapport Premium</div>
      <div class="tarif-price"><span style="font-size:17px;color:var(--ink-4);text-decoration:line-through;font-weight:500;margin-right:8px">249€</span>99€</div>
      <div class="tarif-period">paiement unique · offre de lancement</div>
      <div class="tarif-aide">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
        Satisfait ou remboursé 14 jours
      </div>
      <div class="tarif-features">
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 7+ opportunités complètes</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Tutoriels personnalisés par outil</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Plan d'action sur 12 mois</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Simulation ROI interactive</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Analyse concurrentielle IA</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Accès à vie au rapport</div>
      </div>
      <a href="/facturation.php?plan=report" class="btn btn-primary" style="display:flex;justify-content:center">Obtenir mon rapport →</a>
    </div>

    <!-- Pack IA Accompagné -->
    <div class="tarif-card accent" style="position:relative">
      <div style="position:absolute;top:16px;right:16px;background:#7C3AED;color:#fff;font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;padding:3px 8px;border-radius:20px">Nouveau</div>
      <div class="tarif-plan">Déploiement complet</div>
      <div class="tarif-name">Pack IA Accompagné</div>
      <div class="tarif-price">499€</div>
      <div class="tarif-period">paiement unique</div>
      <div class="tarif-aide">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        Finançable OPCO → net 0€ possible
      </div>
      <div class="tarif-features">
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Tout le Rapport Premium inclus</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 3 sessions de mise en place (2h)</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Vos outils IA configurés pour vous</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Identification de vos aides éligibles</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Dossier OPCO / BPI préparé</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> 30 jours de suivi par notre assistant IA</div>
      </div>
      <div class="tarif-net">Avec OPCO : peut être financé à 100% · coût net potentiellement 0€</div>
      <a href="/contact.php?sujet=pack-ia" class="btn" style="display:flex;justify-content:center;background:#7C3AED;color:#fff;border-color:#7C3AED">Réserver mon accompagnement →</a>
    </div>

    <!-- Assistant IA -->
    <div class="tarif-card light">
      <div class="tarif-plan">Accompagnement</div>
      <div class="tarif-name">Assistant IA</div>
      <div class="tarif-price">29€</div>
      <div class="tarif-period">/mois · sans engagement</div>
      <div class="tarif-aide">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        Finançable plan de formation OPCO
      </div>
      <div class="tarif-features">
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Questions illimitées via votre espace personnel</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Réponses sous 24h par notre IA</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Adapté à votre secteur</div>
        <div class="tarif-feature"><svg class="tarif-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Résiliable à tout moment</div>
      </div>
      <a href="/facturation.php?plan=assistant" class="btn btn-secondary" style="display:flex;justify-content:center">S'abonner →</a>
    </div>

  </div>

  <!-- Aides de l'État · bande synthétique -->
  <div style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.15);border-radius:var(--r-xl);padding:32px 40px;max-width:960px;margin:0 auto 56px">
    <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--green-deep);margin-bottom:20px;display:flex;align-items:center;justify-content:center;gap:8px">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Aides de l'État disponibles pour financer votre transition IA
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:left">
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--ink-2);margin-bottom:4px">Diagnostic FranceNum</div>
        <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:4px">Gratuit</div>
        <div style="font-size:12px;color:var(--ink-4)">Bilan numérique + IA offert par l'État, via votre CCI</div>
      </div>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--ink-2);margin-bottom:4px">Formations via OPCO</div>
        <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:4px">Jusqu'à 100%</div>
        <div style="font-size:12px;color:var(--ink-4)">Vos formations IA prises en charge par votre branche</div>
      </div>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--ink-2);margin-bottom:4px">Crédit Impôt Recherche</div>
        <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:4px">30% remboursé</div>
        <div style="font-size:12px;color:var(--ink-4)">Sur vos dépenses IA, via votre déclaration fiscale</div>
      </div>
      <div>
        <div style="font-size:13px;font-weight:700;color:var(--ink-2);margin-bottom:4px">IA Booster · BPI France</div>
        <div style="font-size:20px;font-weight:800;color:var(--green);letter-spacing:-0.04em;margin-bottom:4px">50 à 80%</div>
        <div style="font-size:12px;color:var(--ink-4)">Accompagnement IA subventionné, programme France 2030</div>
      </div>
    </div>
    <div style="border-top:1px solid rgba(16,185,129,0.15);margin-top:20px;padding-top:16px;font-size:13px;color:var(--ink-3);text-align:center">
      Notre <strong>Pack IA Accompagné (499€)</strong> inclut l'identification et la préparation de vos dossiers d'aides · le rendant souvent <strong>finançable à 100%</strong>.
      <a href="/contact.php?sujet=aides" style="color:var(--green);font-weight:600;margin-left:8px">Me faire accompagner →</a>
    </div>
  </div>

  <h2 style="font-size:28px;font-weight:300;letter-spacing:-0.04em;margin-bottom:24px">Et aussi…</h2>
  <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;max-width:720px;margin:0 auto">
    <div class="card" style="text-align:left">
      <div style="margin-bottom:12px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
      <div style="font-size:16px;font-weight:600;margin-bottom:4px">Formations IA</div>
      <div style="color:var(--ink-3);font-size:13px;margin-bottom:12px">Modules pratiques générés par IA, adaptés à votre secteur.</div>
      <div style="font-size:22px;font-weight:700;color:var(--ink-2);margin-bottom:12px">97€ – 3 000€</div>
      <span style="color:var(--ink-4);font-size:13px">Bientôt disponible</span>
    </div>
    <div class="card" style="text-align:left">
      <div style="margin-bottom:12px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
      <div style="font-size:16px;font-weight:600;margin-bottom:4px">SEO Web & Visibilité IA</div>
      <div style="color:var(--ink-3);font-size:13px;margin-bottom:12px">Apparaissez dans Google, ChatGPT, Perplexity. Suivi mensuel.</div>
      <div style="font-size:22px;font-weight:700;color:var(--ink-2);margin-bottom:12px">49€/mois</div>
      <a href="/visibilite-ia.php" style="color:var(--blue);font-size:13px">En savoir plus →</a>
    </div>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
