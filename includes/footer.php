<?php
// Fichier: abys-ai/includes/footer.php
?>
<footer style="background:#0A1F1A;color:rgba(255,255,255,0.7);padding:56px 0 32px;margin-top:80px">
  <div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:48px">

      <!-- Colonne brand -->
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
          <svg width="30" height="30" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter:drop-shadow(0 3px 8px rgba(16,185,129,0.35))">
            <rect width="32" height="32" rx="9" fill="#052E16"/>
            <path d="M16 7L24.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/>
            <path d="M16 7L7.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/>
            <line x1="10.5" y1="19" x2="21.5" y2="19" stroke="#10B981" stroke-width="2" stroke-linecap="round"/>
            <circle cx="16" cy="7" r="2" fill="#34D399"/>
          </svg>
          <span style="font-family:'Plus Jakarta Sans','Rubik',sans-serif;font-size:18px;font-weight:800;color:#fff;letter-spacing:-0.05em">ABYS<em style="font-style:normal;font-weight:700;color:#10B981"> AI</em></span>
        </div>
        <p style="font-size:13px;line-height:1.7;max-width:240px;margin-bottom:20px">
          L'IA accessible à toutes les PME et TPE françaises. Audit gratuit, conseils concrets, résultats mesurables.
        </p>
        <div style="font-size:11px;color:rgba(255,255,255,0.3)">🇫🇷 Fabriqué en France</div>
      </div>

      <!-- Colonne Produit -->
      <div>
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.3);margin-bottom:16px">Produit</div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <a href="/" style="font-size:13px;color:rgba(255,255,255,0.6);transition:color 150ms" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Audit IA gratuit</a>
          <a href="/tarifs.php" style="font-size:13px;color:rgba(255,255,255,0.6);transition:color 150ms" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Tarifs</a>
          <a href="/formation.php" style="font-size:13px;color:rgba(255,255,255,0.6);transition:color 150ms" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Formations</a>
          <a href="/outils-ia.php" style="font-size:13px;color:rgba(255,255,255,0.6);transition:color 150ms" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Outils IA</a>
          <a href="/visibilite-ia.php" style="font-size:13px;color:rgba(255,255,255,0.6);transition:color 150ms" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Visibilité IA</a>
        </div>
      </div>

      <!-- Colonne Ressources -->
      <div>
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.3);margin-bottom:16px">Ressources</div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <a href="/comment-ca-marche.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Comment ça marche</a>
          <a href="/qui-sommes-nous.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Qui sommes-nous</a>
          <a href="/contact.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Contact</a>
          <a href="/assistant.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Assistant IA</a>
        </div>
      </div>

      <!-- Colonne Légal -->
      <div>
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.3);margin-bottom:16px">Légal</div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <a href="/mentions-legales.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Mentions légales</a>
          <a href="/cgv.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">CGV</a>
          <a href="/confidentialite.php" style="font-size:13px;color:rgba(255,255,255,0.6)" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">Confidentialité</a>
        </div>
      </div>

    </div>

    <!-- Bas de footer -->
    <div style="border-top:1px solid rgba(255,255,255,0.07);padding-top:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div style="font-size:12px;color:rgba(255,255,255,0.3)">
        © <?= date('Y') ?> ABYS AI · Tous droits réservés
      </div>
      <div style="font-size:12px;color:rgba(255,255,255,0.3)">
        Paiements sécurisés par <span style="color:rgba(255,255,255,0.5)">Stripe</span>
      </div>
    </div>
  </div>
</footer>

<script src="<?= function_exists('abys_asset') ? abys_asset('/assets/js/app.js') : '/assets/js/app.js' ?>"></script>
<?php if (!empty($extra_js)): ?>
  <?php foreach ($extra_js as $js): ?>
  <script src="<?= htmlspecialchars(function_exists('abys_asset') ? abys_asset($js) : $js) ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>

<?php /* La fiche de Milo, une seule fois par page */ echo function_exists('milo_fiche') ? milo_fiche() : ''; ?>
