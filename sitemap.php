<?php
// Fichier: abys-ai/sitemap.php
// Plan du site, genere a la volee. Sert autant aux moteurs qu'aux IA qui explorent.
header('Content-Type: application/xml; charset=utf-8');

$METIERS = ['artisan-btp','restauration','commerce','services-conseil','sante-bien-etre',
            'hotellerie-tourisme','transport-logistique','immobilier','agriculture','beaute-coiffure'];

$pages = [
    ['/',                        '1.0', 'weekly'],
    ['/audit-questionnaire.php', '0.9', 'monthly'],
    ['/visibilite-ia.php',       '0.9', 'monthly'],
    ['/tarifs.php',              '0.8', 'monthly'],
    ['/outils-ia.php',           '0.8', 'weekly'],
    ['/comment-ca-marche.php',   '0.7', 'monthly'],
    ['/qui-sommes-nous.php',     '0.6', 'monthly'],
    ['/formation.php',           '0.6', 'monthly'],
    ['/contact.php',             '0.5', 'yearly'],
    ['/cgv.php',                 '0.2', 'yearly'],
    ['/mentions-legales.php',    '0.2', 'yearly'],
    ['/confidentialite.php',     '0.2', 'yearly'],
];
foreach ($METIERS as $m) $pages[] = ['/metier.php?m=' . $m, '0.8', 'monthly'];

$today = date('Y-m-d');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as $p) {
    echo "  <url>\n";
    echo "    <loc>https://abys.ai" . htmlspecialchars($p[0], ENT_XML1) . "</loc>\n";
    echo "    <lastmod>{$today}</lastmod>\n";
    echo "    <changefreq>{$p[2]}</changefreq>\n";
    echo "    <priority>{$p[1]}</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>';
