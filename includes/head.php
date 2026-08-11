<?php
// Fichier: abys-ai/includes/head.php
if (!defined('SITE_URL')) {
    require_once __DIR__ . '/../api/config.php';
}
// get_db() is defined in db.php — load it if not already available
if (!function_exists('get_db')) {
    require_once __DIR__ . '/../api/db.php';
}
$page_title = $page_title ?? 'ABYS AI — Découvrez l\'IA pour votre entreprise';
$page_description = $page_description ?? 'Audit IA gratuit pour PME/TPE. Découvrez comment l\'intelligence artificielle peut vous faire gagner du temps et de l\'argent.';
$page_canonical = $page_canonical ?? (SITE_URL . $_SERVER['REQUEST_URI']);
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
  <meta property="og:type" content="website">
  <meta name="robots" content="index, follow">
  <title><?= htmlspecialchars($page_title) ?></title>
  <!-- Favicon SVG — logomark ABYS -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='9' fill='%23052E16'/%3E%3Cpath d='M16 7L24.5 24' stroke='%2310B981' stroke-width='2.4' stroke-linecap='round'/%3E%3Cpath d='M16 7L7.5 24' stroke='%2310B981' stroke-width='2.4' stroke-linecap='round'/%3E%3Cline x1='10.5' y1='19' x2='21.5' y2='19' stroke='%2310B981' stroke-width='2' stroke-linecap='round'/%3E%3Ccircle cx='16' cy='7' r='2' fill='%2334D399'/%3E%3C/svg%3E">
  <link rel="apple-touch-icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 180 180'%3E%3Crect width='180' height='180' rx='40' fill='%23052E16'/%3E%3Cpath d='M90 38L136 142' stroke='%2310B981' stroke-width='14' stroke-linecap='round'/%3E%3Cpath d='M90 38L44 142' stroke='%2310B981' stroke-width='14' stroke-linecap='round'/%3E%3Cline x1='59' y1='112' x2='121' y2='112' stroke='%2310B981' stroke-width='12' stroke-linecap='round'/%3E%3Ccircle cx='90' cy='38' r='11' fill='%2334D399'/%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    @keyframes slideUp {
      from { transform: translateY(20px); opacity: 0; }
      to   { transform: translateY(0);    opacity: 1; }
    }
  </style>

  <?php
  // ── Tracking IDs (stockés en DB, mis à jour sans redéploiement) ──────────
  $tracking = [];
  try {
      $tdb = get_db();
      foreach ($tdb->query("SELECT `key`,value FROM settings WHERE `key` IN ('ga4_id','gads_id','gads_conversion_label','meta_pixel_id')")->fetchAll() as $r) {
          $tracking[$r['key']] = $r['value'];
      }
  } catch (Exception $e) {}
  $ga4_id    = $tracking['ga4_id']    ?? '';
  $gads_id   = $tracking['gads_id']   ?? '';  // AW-XXXXXXXXX
  ?>

  <?php if ($ga4_id): ?>
  <!-- Google Analytics 4 + Google Ads -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga4_id) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= htmlspecialchars($ga4_id) ?>', { send_page_view: true });
    <?php if ($gads_id): ?>
    gtag('config', '<?= htmlspecialchars($gads_id) ?>');
    <?php endif; ?>
  </script>
  <?php endif; ?>
</head>
