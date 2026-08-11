<?php
// Fichier: abys-ai/includes/nav.php
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- Bandeau Made in France -->
<div class="mif-bar">
  <span>🇫🇷</span>
  <span>Fabriqué en France · IA éthique · Données protégées</span>
</div>

<!-- Navigation principale -->
<nav class="nav">
  <a href="/" class="nav-logo">
    <div class="nav-logo-mark">
      <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="32" height="32" rx="9" fill="#052E16"/>
        <path d="M16 7L24.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/>
        <path d="M16 7L7.5 24" stroke="#10B981" stroke-width="2.4" stroke-linecap="round"/>
        <line x1="10.5" y1="19" x2="21.5" y2="19" stroke="#10B981" stroke-width="2" stroke-linecap="round"/>
        <circle cx="16" cy="7" r="2" fill="#34D399"/>
      </svg>
    </div>
    <span class="nav-logo-name"><strong>ABYS</strong><em> AI</em></span>
  </a>

  <div class="nav-links">
    <a href="/comment-ca-marche.php" <?= $current === 'comment-ca-marche.php' ? 'style="color:var(--blue)"' : '' ?>>
      Comment ça marche
    </a>
    <a href="/outils-ia.php" <?= $current === 'outils-ia.php' ? 'style="color:var(--blue)"' : '' ?>>
      Outils IA
    </a>
    <a href="/formation.php" <?= $current === 'formation.php' ? 'style="color:var(--blue)"' : '' ?>>
      Formations
    </a>
    <a href="/visibilite-ia.php" <?= $current === 'visibilite-ia.php' ? 'style="color:var(--blue)"' : '' ?>>
      Visibilité IA
    </a>
    <a href="/tarifs.php" <?= $current === 'tarifs.php' ? 'style="color:var(--blue)"' : '' ?>>
      Tarifs
    </a>
    <a href="/contact.php" <?= $current === 'contact.php' ? 'style="color:var(--blue)"' : '' ?>>
      Contact
    </a>
  </div>

  <a href="/#audit" class="btn btn-sm">
    Audit gratuit →
  </a>
</nav>
