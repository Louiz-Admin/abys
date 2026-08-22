<?php
// Fichier: abys-ai/metier.php
// PAGES MÉTIER · une page par activité, pensée pour être trouvée.
// Deux publics : les moteurs de recherche, et les IA qui recommandent des prestataires.
// URL : /metier.php?m=artisan-btp  (réécrite en /ia-pour/artisan-btp si le serveur le permet)

$METIERS = [
  'artisan-btp' => [
    'nom'     => 'les artisans et le bâtiment',
    'court'   => 'Artisanat et BTP',
    'titre'   => "L'IA pour les artisans et le bâtiment",
    'chapo'   => "Vous passez vos soirées sur les devis, les relances et la paperasse pendant que le chantier avance sans vous. Voici ce que l'IA prend en charge, tâche par tâche, avec les heures que ça vous rend.",
    'photo'   => 'https://images.pexels.com/photos/1216589/pexels-photo-1216589.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches'  => [
      ['Les devis', "Vous les rédigez le soir, un par un, en reprenant l'ancien et en changeant les lignes.", "Le devis se génère depuis votre catalogue et vos prix, vous relisez et vous envoyez.", '5 h', 'Axonaut'],
      ['Les relances d\'impayés', "Vous y pensez, vous repoussez, et vous finissez par appeler trois semaines trop tard.", "Les relances partent seules à J+7, J+14 et J+30, avec le bon ton et le bon montant.", '2 h', 'Axonaut'],
      ['Les demandes entrantes', "Le téléphone sonne pendant que vous êtes sur le toit, vous rappelez le soir, parfois trop tard.", "Les demandes reçues par le site reçoivent une première réponse immédiate avec vos disponibilités.", '3 h', 'Brevo'],
      ['Les comptes rendus de chantier', "Vous prenez des photos et vous rédigez le rapport le week-end.", "Vous dictez sur place, le compte rendu est mis en forme et envoyé au client.", '3 h', 'Otter'],
    ],
    'visibilite' => "Quand un particulier demande à ChatGPT un couvreur ou un plombier dans votre secteur, une liste sort. Vous n'y êtes probablement pas, et ce n'est pas une question de qualité de travail : c'est une question de lisibilité pour la machine.",
    'faq' => [
      ["Je ne suis pas à l'aise avec l'informatique, est-ce que c'est pour moi ?",
       "Oui, et c'est même le cas le plus fréquent. Les outils sont installés et paramétrés pour vous, vous récupérez quelque chose qui fonctionne, pas un logiciel à apprendre."],
      ["Combien de temps avant de voir un résultat ?",
       "Le premier outil est opérationnel en quelques jours. Les heures gagnées se voient dès la première semaine d'usage, sur les devis en général."],
      ["Est-ce que ça remplace mon logiciel de facturation ?",
       "Pas forcément. Dans la plupart des cas on complète ce que vous avez déjà, parce que changer d'outil coûte plus cher que l'automatiser."],
    ],
  ],

  'restauration' => [
    'nom'   => 'la restauration',
    'court' => 'Restauration',
    'titre' => "L'IA pour la restauration et les cafés",
    'chapo' => "Entre les commandes fournisseurs, les avis clients et la communication, il reste peu de temps pour la cuisine. Voici ce qu'une machine fait mieux que vous, et ce qu'elle vous rend.",
    'photo' => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les stocks et les commandes', "Vous comptez à la main avant chaque commande, et il manque toujours quelque chose.", "Le stock se met à jour seul et la commande fournisseur est proposée au bon moment.", '4 h', 'MarketMan'],
      ['Les avis clients', "Vous répondez quand vous y pensez, souvent des jours après.", "Chaque avis reçoit une réponse personnalisée dans l'heure, que vous validez d'un clic.", '2 h', 'Brevo'],
      ['La communication', "Publier chaque semaine demande une soirée que vous n'avez pas.", "Les publications sont préparées à l'avance à partir de vos plats et programmées.", '3 h', 'Buffer'],
      ['Les réservations de groupe', "Chaque demande de groupe redemande le même échange de mails.", "Les questions habituelles reçoivent une réponse immédiate avec vos formules.", '2 h', 'Brevo'],
    ],
    'visibilite' => "« Où bien manger ce soir dans le secteur ? » Cette question, des milliers de gens la posent maintenant à une IA plutôt qu'à un moteur de recherche. La réponse cite trois établissements. Le vôtre en fait rarement partie.",
    'faq' => [
      ["Je travaille déjà avec une agence pour les réseaux, ça fait doublon ?",
       "Non. L'agence produit, l'outil régularise. Beaucoup de restaurateurs gardent leur agence pour les temps forts et automatisent le quotidien."],
      ["Est-ce que mes clients vont voir que c'est automatisé ?",
       "Pas si c'est bien fait. Les réponses partent de vos mots et de votre carte, vous gardez la main sur ce qui est publié."],
      ["Combien ça coûte au total ?",
       "L'audit est gratuit. La mise en place d'un premier outil est à 79 euros, et la plupart des outils cités ont une version gratuite ou à moins de 30 euros par mois."],
    ],
  ],

  'commerce' => [
    'nom'   => 'le commerce',
    'court' => 'Commerce et e-commerce',
    'titre' => "L'IA pour le commerce et la boutique en ligne",
    'chapo' => "Fiches produits, service après-vente, relances de panier : autant de tâches qui se répètent et qui mangent vos journées. Voici lesquelles se délèguent.",
    'photo' => 'https://images.pexels.com/photos/1005638/pexels-photo-1005638.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les fiches produits', "Vous écrivez chaque description à la main, ou vous recopiez celle du fournisseur.", "Les descriptions sont rédigées depuis vos caractéristiques, dans votre ton, prêtes à relire.", '4 h', 'Gamma'],
      ['Les questions clients', "Les mêmes dix questions reviennent toute la semaine.", "Les réponses partent immédiatement, les cas particuliers seuls arrivent jusqu'à vous.", '5 h', 'Gorgias'],
      ['Les relances de panier', "Vous n'en faites pas, faute de temps.", "Le client qui a abandonné reçoit un message adapté, sans que vous fassiez quoi que ce soit.", '2 h', 'Brevo'],
      ['Les visuels', "Chaque visuel demande une heure sur un outil que vous maîtrisez à moitié.", "Les déclinaisons sont générées à partir d'un modèle, vous choisissez.", '3 h', 'Canva'],
    ],
    'visibilite' => "Un acheteur qui demande à une IA où trouver un produit reçoit deux ou trois noms. Ce sont ces noms qui font la vente, pas la première page de résultats classiques.",
    'faq' => [
      ["J'ai déjà une boutique Shopify, est-ce compatible ?",
       "Oui. La plupart des outils recommandés se branchent sur Shopify, WooCommerce ou PrestaShop sans développement."],
      ["Est-ce que l'IA va écrire n'importe quoi sur mes produits ?",
       "Elle écrit à partir de vos caractéristiques réelles, et rien n'est publié sans votre relecture tant que vous ne l'avez pas décidé."],
      ["Et si je vends en magasin uniquement ?",
       "Les gains sont ailleurs : le service client, les visuels, la présence en ligne. L'audit vous dira lesquels comptent chez vous."],
    ],
  ],

  'services-conseil' => [
    'nom'   => 'les services aux entreprises',
    'court' => 'Services et conseil',
    'titre' => "L'IA pour les cabinets et les prestataires de services",
    'chapo' => "Votre temps facturable part dans la production de documents, la prise de notes et le suivi client. Voici ce qui se récupère.",
    'photo' => 'https://images.pexels.com/photos/3184360/pexels-photo-3184360.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les comptes rendus', "Vous prenez des notes en réunion, vous les remettez au propre le soir.", "La réunion est transcrite et résumée, le compte rendu part au client dans l'heure.", '6 h', 'Otter'],
      ['Les propositions commerciales', "Chaque proposition repart d'un ancien document que vous adaptez.", "La trame est générée depuis le besoin exprimé, vous ajustez le fond.", '4 h', 'Gamma'],
      ['La saisie comptable', "Vous rassemblez les justificatifs en fin de mois, dans l'urgence.", "Les pièces sont classées et rapprochées au fil de l'eau.", '5 h', 'Pennylane'],
      ['Le suivi client', "Les relances dépendent de votre mémoire et de votre agenda.", "Chaque client reçoit le bon message au bon moment, sans intervention.", '3 h', 'Brevo'],
    ],
    'visibilite' => "Une petite entreprise qui cherche un cabinet demande de plus en plus souvent à une IA plutôt qu'à son réseau. Être cité dans cette réponse vaut plus qu'une page de publicité.",
    'faq' => [
      ["Le secret professionnel est-il compatible avec ces outils ?",
       "C'est le premier point que nous vérifions. Les outils recommandés hébergent en Europe et n'utilisent pas vos données pour entraîner leurs modèles, sinon ils ne sont pas retenus."],
      ["Est-ce que mes clients vont trouver ça impersonnel ?",
       "L'inverse en général. Un compte rendu reçu dans l'heure fait meilleure impression qu'un compte rendu soigné reçu trois jours après."],
      ["Combien d'heures peut-on réellement récupérer ?",
       "Sur les cabinets audités, la fourchette la plus fréquente se situe entre dix et vingt heures par semaine, réparties sur toute l'équipe."],
    ],
  ],

  'sante-bien-etre' => [
    'nom'   => 'la santé et le bien-être',
    'court' => 'Santé et bien-être',
    'titre' => "L'IA pour les praticiens et les métiers du soin",
    'chapo' => "Entre le planning, les rappels et l'administratif, une part de votre semaine ne soigne personne. Voici ce qui peut être repris par un outil.",
    'photo' => 'https://images.pexels.com/photos/40568/medical-appointment-doctor-healthcare-40568.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les rappels de séance', "Les oublis créent des trous dans le planning que vous ne remplissez plus.", "Le rappel part automatiquement, le créneau libéré est reproposé.", '3 h', 'Brevo'],
      ['La gestion du planning', "Chaque changement demande deux ou trois échanges.", "Le patient décale lui-même dans les créneaux que vous avez ouverts.", '4 h', 'Calendly'],
      ['Les contenus et la pédagogie', "Vous voudriez expliquer votre pratique mais vous n'écrivez jamais.", "Vos explications habituelles deviennent des publications régulières, dans vos mots.", '3 h', 'Buffer'],
      ['La comptabilité', "Les recettes sont saisies à la main, souvent en retard.", "Les encaissements sont classés et rapprochés automatiquement.", '2 h', 'Pennylane'],
    ],
    'visibilite' => "Quand quelqu'un cherche un praticien près de chez lui via une IA, deux ou trois noms sortent. La réputation locale ne suffit plus à en faire partie.",
    'faq' => [
      ["Les données de mes patients sont-elles concernées ?",
       "Aucun outil traitant des données de santé n'est recommandé sans hébergement conforme. Sur la plupart des gains identifiés, aucune donnée patient n'est en jeu."],
      ["Je suis seul, est-ce que ça vaut le coup ?",
       "C'est souvent là que le gain est le plus visible, parce que personne d'autre ne fait l'administratif à votre place."],
      ["Faut-il changer mon logiciel métier ?",
       "Non. On complète, on ne remplace pas ce qui fonctionne déjà."],
    ],
  ],

  'hotellerie-tourisme' => [
    'nom'   => "l'hôtellerie et le tourisme",
    'court' => 'Hôtellerie et tourisme',
    'titre' => "L'IA pour l'hôtellerie, les gîtes et le tourisme",
    'chapo' => "Demandes de groupe, avis, coordination des équipes : le métier se joue autant dans la boîte mail que dans l'établissement. Voici ce qui s'automatise.",
    'photo' => 'https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les demandes de séminaire', "Chaque demande relance le même échange de mails et la même proposition à refaire.", "La proposition personnalisée part dans l'heure, avec vos formules et vos tarifs.", '6 h', 'Gamma'],
      ['Les comptes rendus d\'équipe', "Le brief du matin se perd, chacun retient autre chose.", "Le brief est transcrit et diffusé, tout le monde a la même version.", '3 h', 'Otter'],
      ['Les avis voyageurs', "Répondre à tout prend un temps que personne n'a.", "Chaque avis reçoit une réponse adaptée, validée d'un clic.", '3 h', 'Brevo'],
      ['Les stocks de restauration', "Les commandes se font au jugé, les pertes suivent.", "Les besoins sont anticipés à partir du taux d'occupation.", '4 h', 'MarketMan'],
    ],
    'visibilite' => "Un organisateur qui cherche un lieu de séminaire dans votre région interroge maintenant une IA. Elle cite trois établissements et personne ne descend plus bas dans la liste.",
    'faq' => [
      ["Nous travaillons déjà avec des plateformes de réservation, est-ce redondant ?",
       "Non. Les plateformes captent la demande, ces outils traitent tout ce qui se passe avant et après, là où votre temps part réellement."],
      ["Est-ce adapté à un petit établissement ?",
       "Oui. Les gains les plus nets se voient souvent sur les structures de moins de dix personnes, faute de service administratif."],
      ["Peut-on garder la main sur les réponses envoyées ?",
       "Toujours. Vous choisissez ce qui part seul et ce qui passe par vous."],
    ],
  ],

  'transport-logistique' => [
    'nom'   => 'le transport et la logistique',
    'court' => 'Transport et logistique',
    'titre' => "L'IA pour le transport et la logistique",
    'chapo' => "Le planning, les preuves de livraison et le suivi client absorbent des heures qui ne roulent pas. Voici ce qui se délègue.",
    'photo' => 'https://images.pexels.com/photos/1427541/pexels-photo-1427541.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Le suivi client', "Le client appelle pour savoir où en est sa livraison.", "Il reçoit l'information sans avoir à la demander.", '4 h', 'Brevo'],
      ['Les documents de transport', "Les lettres de voiture et les preuves se saisissent à la main.", "Les documents sont lus et classés automatiquement.", '4 h', 'Pennylane'],
      ['Les devis', "Chaque demande de prix repart d'un tableur.", "Le prix est calculé depuis vos grilles et envoyé dans la foulée.", '3 h', 'Axonaut'],
      ['Le recrutement de conducteurs', "Les annonces et le tri des candidatures traînent des semaines.", "Le tri est fait, vous ne voyez que les profils qui correspondent.", '3 h', 'Manatal'],
    ],
    'visibilite' => "Les donneurs d'ordre cherchent des transporteurs autrement qu'avant. Une IA qui ne connaît pas votre entreprise ne peut pas vous proposer.",
    'faq' => [
      ["Nos tournées sont déjà gérées par un logiciel, quel intérêt ?",
       "Le logiciel gère les tournées, pas les échanges autour. C'est là que se trouvent les heures récupérables."],
      ["Nous sommes une petite structure, est-ce accessible ?",
       "Les outils recommandés commencent à quelques dizaines d'euros par mois, et l'audit est gratuit."],
      ["Combien de temps pour installer ?",
       "Le premier outil est en service en quelques jours, sans interruption de votre activité."],
    ],
  ],

  'immobilier' => [
    'nom'   => "l'immobilier",
    'court' => 'Immobilier',
    'titre' => "L'IA pour les agences et les professionnels de l'immobilier",
    'chapo' => "Annonces, qualification des demandes, suivi des dossiers : le métier consomme du temps d'écriture. Voici ce qui se récupère.",
    'photo' => 'https://images.pexels.com/photos/323780/pexels-photo-323780.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les annonces', "Chaque bien demande une rédaction, souvent bâclée faute de temps.", "L'annonce est rédigée depuis vos caractéristiques et vos photos, prête à relire.", '4 h', 'Gamma'],
      ['La qualification des demandes', "Vous rappelez tout le monde, y compris les curieux.", "Les questions de cadrage sont posées automatiquement, vous appelez les bons dossiers.", '5 h', 'Brevo'],
      ['Les comptes rendus de visite', "Ils s'écrivent le soir, quand ils s'écrivent.", "Vous dictez en sortant, le compte rendu part au propriétaire.", '3 h', 'Otter'],
      ['Le suivi des dossiers', "Les relances dépendent de votre mémoire.", "Chaque étape déclenche le bon message au bon moment.", '3 h', 'Axonaut'],
    ],
    'visibilite' => "Un vendeur qui cherche une agence dans sa ville pose la question à une IA. Trois noms sortent, et ce ne sont pas forcément les plus grosses vitrines.",
    'faq' => [
      ["Nous avons déjà un logiciel de transaction, faut-il en changer ?",
       "Non. Les outils viennent se greffer sur ce que vous utilisez déjà."],
      ["Les annonces générées sont-elles conformes ?",
       "Elles reprennent vos données réelles et restent sous votre relecture. Rien ne se publie sans validation tant que vous ne l'avez pas choisi."],
      ["Est-ce que ça marche pour un agent indépendant ?",
       "C'est même le profil qui gagne le plus, faute d'assistant pour absorber l'administratif."],
    ],
  ],

  'agriculture' => [
    'nom'   => "l'agriculture",
    'court' => 'Agriculture et viticulture',
    'titre' => "L'IA pour les exploitations agricoles et viticoles",
    'chapo' => "La paperasse réglementaire et la vente directe prennent des heures qui manquent ailleurs. Voici ce qui peut être pris en charge.",
    'photo' => 'https://images.pexels.com/photos/974314/pexels-photo-974314.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les déclarations et le suivi', "Les registres se remplissent le soir, de mémoire.", "Les saisies sont dictées sur place et classées automatiquement.", '4 h', 'Otter'],
      ['La vente directe', "Prévenir les clients d'un arrivage demande du temps que vous n'avez pas.", "Les clients reçoivent le message au bon moment, sans que vous y pensiez.", '3 h', 'Brevo'],
      ['La comptabilité', "Les factures s'accumulent jusqu'à la visite du comptable.", "Les pièces sont classées et rapprochées au fil de l'eau.", '3 h', 'Pennylane'],
      ['La communication', "Raconter votre travail demande une régularité impossible à tenir.", "Vos photos et vos mots deviennent des publications régulières.", '2 h', 'Buffer'],
    ],
    'visibilite' => "Un consommateur qui cherche un producteur près de chez lui demande à une IA. Les exploitations lisibles en ligne sortent, les autres n'existent pas dans la réponse.",
    'faq' => [
      ["Je n'ai pas de site internet, est-ce bloquant ?",
       "Non. L'audit fonctionne sans site, et une partie des gains identifiés ne dépend pas d'une présence en ligne."],
      ["La connexion est mauvaise sur l'exploitation, est-ce un problème ?",
       "Les outils recommandés fonctionnent sur téléphone et se synchronisent quand le réseau revient."],
      ["Est-ce que c'est réservé aux grandes exploitations ?",
       "Non, et c'est souvent l'inverse : moins il y a de bras, plus le temps administratif pèse."],
    ],
  ],

  'beaute-coiffure' => [
    'nom'   => 'la beauté et la coiffure',
    'court' => 'Beauté et coiffure',
    'titre' => "L'IA pour les salons de coiffure et les instituts",
    'chapo' => "Les rendez-vous manqués, la communication et la fidélisation coûtent plus cher qu'on ne le croit. Voici ce qui s'automatise.",
    'photo' => 'https://images.pexels.com/photos/3993449/pexels-photo-3993449.jpeg?auto=compress&cs=tinysrgb&w=1200&h=700',
    'taches' => [
      ['Les rendez-vous manqués', "Un client qui oublie, c'est un créneau perdu et personne pour le reprendre.", "Le rappel part seul, le créneau libéré est reproposé aux clients en attente.", '3 h', 'Calendly'],
      ['La prise de rendez-vous', "Le téléphone sonne pendant une coupe, vous rappelez plus tard.", "Le client réserve lui-même dans vos créneaux réels.", '4 h', 'Calendly'],
      ['La communication', "Publier régulièrement demande une énergie que la journée ne laisse pas.", "Vos photos deviennent des publications programmées, dans votre style.", '3 h', 'Buffer'],
      ['La fidélisation', "Les clients qui ne reviennent pas partent sans que vous le remarquiez.", "L'absence est détectée et un message part au bon moment.", '2 h', 'Brevo'],
    ],
    'visibilite' => "« Un bon salon près d'ici ? » Cette question part aujourd'hui dans une IA aussi souvent que dans un moteur de recherche. La réponse cite trois salons.",
    'faq' => [
      ["J'ai déjà un logiciel de réservation, est-ce utile ?",
       "Oui, sur ce qu'il ne fait pas : la relance des clients qui s'éloignent et la régularité de votre présence en ligne."],
      ["Est-ce que mes clients vont se sentir traités par une machine ?",
       "Les messages partent de vos mots. Ce qui marque le client, c'est d'être reconnu, pas de savoir qui a appuyé sur le bouton."],
      ["Combien ça coûte pour commencer ?",
       "L'audit est gratuit, et l'installation d'un premier outil est à 79 euros."],
    ],
  ],
];

$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['m'] ?? ''));
if (!isset($METIERS[$slug])) {
    header('Location: /', true, 302);
    exit;
}
$M = $METIERS[$slug];

$total_h = 0;
foreach ($M['taches'] as $t) $total_h += (int) $t[3];

$page_title       = $M['titre'] . ' · ABYS AI';
$page_description = mb_substr(strip_tags($M['chapo']), 0, 155);
$page_canonical   = 'https://abys.ai/metier.php?m=' . $slug;

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';
?>

<script type="application/ld+json"><?= json_encode([
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'FAQPage',
      'mainEntity' => array_map(fn($q) => [
        '@type' => 'Question',
        'name'  => $q[0],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q[1]],
      ], $M['faq']),
    ],
    [
      '@type' => 'Service',
      'name' => $M['titre'],
      'serviceType' => "Audit et intégration d'outils d'intelligence artificielle",
      'areaServed' => ['@type' => 'Country', 'name' => 'France'],
      'provider' => ['@type' => 'Organization', 'name' => 'ABYS AI', 'url' => 'https://abys.ai'],
      'description' => strip_tags($M['chapo']),
    ],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<style>
  .mt-hero { position:relative; overflow:hidden; background:#041712; color:#EAF6F1; }
  .mt-hero img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.30; }
  .mt-hero::after { content:''; position:absolute; inset:0;
    background:linear-gradient(180deg, rgba(4,23,18,.72), rgba(4,23,18,.95)); }
  .mt-hero-in { position:relative; z-index:2; max-width:1120px; margin:0 auto; padding:78px 24px 66px; }
  .mt-eyebrow { font-size:11.5px; font-weight:700; letter-spacing:.15em; text-transform:uppercase; color:#6EE7B7; }
  .mt-hero h1 { font-size:clamp(30px,4.4vw,50px); font-weight:700; letter-spacing:-.04em; line-height:1.12;
    margin:12px 0 16px; max-width:820px; color:#F3FBF8; }
  .mt-hero p { font-size:16px; line-height:1.7; color:rgba(255,255,255,.78); max-width:660px; margin:0 0 26px; }
  .mt-cta { display:inline-flex; align-items:center; gap:9px; text-decoration:none; font-size:15.5px; font-weight:700;
    color:#03251B; border-radius:13px; padding:15px 28px;
    background:linear-gradient(90deg,#34D399,#5EEAD4 55%,#7DD3FC);
    box-shadow:0 18px 42px -18px rgba(52,211,153,.9); transition:transform .14s, filter .16s; }
  .mt-cta:hover { transform:translateY(-2px); filter:brightness(1.06); }
  .mt-sous { font-size:12.5px; color:#6E8C84; margin-top:12px; }

  .mt-wrap { max-width:1120px; margin:0 auto; padding:0 24px; }
  .mt-sect { padding:62px 0; }
  .mt-sect h2 { font-size:clamp(23px,3vw,32px); font-weight:300; letter-spacing:-.03em; margin:0 0 8px; color:var(--ink,#0A1F1A); }
  .mt-sect h2 strong { font-weight:800; }
  .mt-sect .sous { font-size:15px; color:var(--ink-3,#4B5563); margin:0 0 30px; max-width:640px; line-height:1.65; }

  .mt-taches { display:flex; flex-direction:column; gap:12px; }
  .mt-tache { display:grid; grid-template-columns:1fr 44px 1fr; align-items:stretch; }
  .mt-cell { border-radius:16px; padding:18px 20px; }
  .mt-av { background:#fff; border:2px solid var(--border,#E5E7EB); }
  .mt-ap { background:linear-gradient(155deg,#0A1F1A,#064E3B); border:2px solid #10B981; }
  .mt-fleche { display:flex; align-items:center; justify-content:center; color:#10B981; }
  .mt-nom { display:flex; align-items:center; gap:9px; font-size:12px; font-weight:800; letter-spacing:.04em;
    text-transform:uppercase; margin-bottom:9px; }
  .mt-av .mt-nom { color:var(--ink-4,#9CA3AF); }
  .mt-ap .mt-nom { color:#6EE7B7; }
  .mt-gain { margin-left:auto; font-size:11px; font-weight:600; text-transform:none; letter-spacing:0;
    background:rgba(16,185,129,.14); color:#059669; border-radius:20px; padding:3px 9px; }
  .mt-ap .mt-gain { background:rgba(16,185,129,.22); color:#6EE7B7; }
  .mt-txt { font-size:14.5px; line-height:1.65; }
  .mt-av .mt-txt { color:var(--ink-3,#4B5563); }
  .mt-ap .mt-txt { color:rgba(255,255,255,.86); }
  .mt-outil { display:inline-block; margin-top:9px; font-size:12px; font-weight:650; color:#6EE7B7;
    border:1px solid rgba(52,211,153,.35); border-radius:20px; padding:2px 10px; }
  @media(max-width:820px){ .mt-tache{ grid-template-columns:1fr; } .mt-fleche{ transform:rotate(90deg); margin:2px auto; } }

  .mt-total { margin-top:22px; border-radius:18px; padding:20px 24px; display:flex; align-items:center; gap:18px;
    flex-wrap:wrap; background:linear-gradient(135deg,#064E3B,#065F46 55%,#0A2315); color:#fff; }
  .mt-total b { font-size:30px; font-weight:800; letter-spacing:-.03em; }
  .mt-total span { font-size:14.5px; color:rgba(255,255,255,.82); line-height:1.55; }

  .mt-vis { background:#041712; color:#EAF6F1; border-radius:22px; padding:34px 36px; }
  .mt-vis h3 { font-size:clamp(20px,2.5vw,26px); font-weight:700; letter-spacing:-.03em; margin:0 0 12px; color:#F3FBF8; }
  .mt-vis p { font-size:15px; line-height:1.7; color:rgba(255,255,255,.78); margin:0 0 18px; max-width:700px; }
  .mt-vis a { color:#6EE7B7; font-weight:650; text-decoration:none; border-bottom:1px solid rgba(110,231,183,.4); }

  .mt-faq details { border:1px solid var(--border,#E5E7EB); border-radius:14px; padding:16px 18px; margin-bottom:10px;
    background:#fff; }
  .mt-faq summary { cursor:pointer; font-size:15.5px; font-weight:650; color:var(--ink,#0A1F1A); list-style:none; }
  .mt-faq summary::-webkit-details-marker { display:none; }
  .mt-faq summary::after { content:'+'; float:right; color:#10B981; font-weight:700; }
  .mt-faq details[open] summary::after { content:'–'; }
  .mt-faq p { font-size:14.5px; line-height:1.7; color:var(--ink-3,#4B5563); margin:12px 0 0; }

  .mt-autres { display:flex; flex-wrap:wrap; gap:9px; }
  .mt-autres a { font-size:13.5px; text-decoration:none; color:var(--ink-3,#4B5563);
    border:1px solid var(--border,#E5E7EB); border-radius:20px; padding:7px 14px; background:#fff;
    transition:border-color .16s, color .16s; }
  .mt-autres a:hover { border-color:#10B981; color:#059669; }
</style>

<header class="mt-hero">
  <img src="<?= htmlspecialchars($M['photo']) ?>" alt="<?= htmlspecialchars($M['court']) ?>" loading="eager">
  <div class="mt-hero-in">
    <div class="mt-eyebrow"><?= htmlspecialchars($M['court']) ?></div>
    <h1><?= htmlspecialchars($M['titre']) ?></h1>
    <p><?= htmlspecialchars($M['chapo']) ?></p>
    <a class="mt-cta" href="/audit-questionnaire.php">
      Faire l'audit gratuit
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h13"/><path d="m12 5 7 7-7 7"/></svg>
    </a>
    <div class="mt-sous">Deux minutes, sans carte bancaire. Résultat immédiat à l'écran.</div>
  </div>
</header>

<section class="mt-sect">
  <div class="mt-wrap">
    <h2>Concrètement, <strong>ce qui change</strong></h2>
    <p class="sous">Tâche par tâche, ce que ces outils prennent en charge dans <?= htmlspecialchars($M['nom']) ?>, et le temps que ça rend chaque semaine.</p>

    <div class="mt-taches">
      <?php foreach ($M['taches'] as $t): ?>
      <div class="mt-tache">
        <div class="mt-cell mt-av">
          <div class="mt-nom">Aujourd'hui : <?= htmlspecialchars($t[0]) ?></div>
          <div class="mt-txt"><?= htmlspecialchars($t[1]) ?></div>
        </div>
        <div class="mt-fleche">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h13"/><path d="m12 5 7 7-7 7"/></svg>
        </div>
        <div class="mt-cell mt-ap">
          <div class="mt-nom">Avec l'IA <span class="mt-gain"><?= htmlspecialchars($t[3]) ?>/semaine</span></div>
          <div class="mt-txt"><?= htmlspecialchars($t[2]) ?></div>
          <span class="mt-outil"><?= htmlspecialchars($t[4]) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="mt-total">
      <b><?= $total_h ?> h</b>
      <span>par semaine sur ces quatre tâches seulement. Votre chiffre dépend de votre organisation réelle, c'est précisément ce que calcule l'audit gratuit.</span>
    </div>
  </div>
</section>

<section class="mt-sect" style="padding-top:0">
  <div class="mt-wrap">
    <div class="mt-vis">
      <h3>Le levier que vos concurrents ignorent encore</h3>
      <p><?= htmlspecialchars($M['visibilite']) ?></p>
      <p style="font-size:14px;color:rgba(255,255,255,.62)">Être cité par ChatGPT, Gemini ou Perplexity quand un client cherche votre métier ne relève pas du hasard. C'est un travail de structure, et il se mesure.</p>
      <a href="/visibilite-ia.php">Comprendre la visibilité IA</a>
    </div>
  </div>
</section>

<section class="mt-sect" style="padding-top:0">
  <div class="mt-wrap mt-faq">
    <h2>Les questions <strong>qu'on nous pose</strong></h2>
    <p class="sous">Les réponses viennent des audits déjà réalisés dans <?= htmlspecialchars($M['nom']) ?>.</p>
    <?php foreach ($M['faq'] as $q): ?>
    <details>
      <summary><?= htmlspecialchars($q[0]) ?></summary>
      <p><?= htmlspecialchars($q[1]) ?></p>
    </details>
    <?php endforeach; ?>
  </div>
</section>

<section class="mt-sect" style="padding-top:0">
  <div class="mt-wrap" style="text-align:center">
    <h2 style="margin-bottom:14px">Votre situation, <strong>vos chiffres</strong></h2>
    <p class="sous" style="margin:0 auto 24px">Cette page décrit une moyenne. L'audit, lui, part de votre activité réelle et vous rend un plan chiffré en deux minutes.</p>
    <a class="mt-cta" href="/audit-questionnaire.php">
      Faire l'audit gratuit
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h13"/><path d="m12 5 7 7-7 7"/></svg>
    </a>
  </div>
</section>

<section class="mt-sect" style="padding-top:0;padding-bottom:70px">
  <div class="mt-wrap">
    <h2 style="font-size:19px;margin-bottom:16px">Les autres métiers</h2>
    <div class="mt-autres">
      <?php foreach ($METIERS as $s => $m): if ($s === $slug) continue; ?>
        <a href="/metier.php?m=<?= $s ?>"><?= htmlspecialchars($m['court']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
