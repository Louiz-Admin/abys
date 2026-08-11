<?php
$page_title = '200+ outils IA pour PME — Annuaire complet ABYS AI';
$page_description = 'L\'annuaire de référence des outils IA pour les PME et TPE françaises. Plus de 200 solutions classées par catégorie.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/nav.php';

$categories = [
  'Communication' => [
    ['ChatGPT', 'chatgpt.com', 'Assistant IA conversationnel de référence'],
    ['Claude', 'claude.ai', 'IA Anthropic, excellente pour l\'analyse et la rédaction'],
    ['Gemini', 'gemini.google.com', 'IA de Google, intégrée à l\'écosystème Google'],
    ['Mistral', 'mistral.ai', 'IA française, open source et performante'],
    ['Perplexity', 'perplexity.ai', 'Moteur de recherche IA avec sources citées'],
    ['Jasper', 'jasper.ai', 'Rédaction marketing IA pour équipes'],
    ['Copy.ai', 'copy.ai', 'Génération de contenu marketing automatisé'],
    ['Writesonic', 'writesonic.com', 'Textes SEO et marketing par IA'],
    ['Rytr', 'rytr.me', 'Assistant rédaction IA abordable'],
    ['Anyword', 'anyword.com', 'Optimisation copywriting par données'],
    ['Wordtune', 'wordtune.com', 'Reformulation et amélioration de textes'],
    ['Hypotenuse AI', 'hypotenuse.ai', 'Génération de descriptions produits'],
    ['Narrato', 'narrato.io', 'Plateforme de contenu IA collaborative'],
    ['Lex', 'lex.page', 'Éditeur de texte avec IA intégrée'],
    ['Sudowrite', 'sudowrite.com', 'IA de rédaction créative et narrative'],
  ],
  'Email & CRM' => [
    ['HubSpot', 'hubspot.com', 'CRM complet avec IA pour PME'],
    ['Pipedrive', 'pipedrive.com', 'CRM centré ventes avec automatisation'],
    ['Brevo', 'brevo.com', 'Email marketing et CRM made in France'],
    ['Mailchimp', 'mailchimp.com', 'Email marketing avec suggestions IA'],
    ['ActiveCampaign', 'activecampaign.com', 'Automatisation email avancée'],
    ['Lemlist', 'lemlist.com', 'Cold email personnalisé par IA'],
    ['Apollo', 'apollo.io', 'Prospection B2B et enrichissement contacts'],
    ['Reply.io', 'reply.io', 'Séquences email automatisées IA'],
    ['Instantly', 'instantly.ai', 'Outreach email IA à grande échelle'],
    ['Hunter.io', 'hunter.io', 'Recherche d\'adresses email professionnelles'],
    ['Snov.io', 'snov.io', 'Génération de leads et email outreach'],
    ['Woodpecker', 'woodpecker.co', 'Email outreach B2B automatisé'],
    ['Zoho CRM', 'zoho.com', 'Suite CRM complète avec IA Zia'],
    ['Freshsales', 'freshworks.com', 'CRM IA pour petites équipes commerciales'],
    ['Close', 'close.com', 'CRM optimisé pour la prospection téléphonique'],
  ],
  'Design & Visuels' => [
    ['Canva', 'canva.com', 'Design graphique IA pour non-designers'],
    ['Adobe Firefly', 'firefly.adobe.com', 'Génération d\'images IA par Adobe'],
    ['Midjourney', 'midjourney.com', 'Création d\'images artistiques par IA'],
    ['DALL-E', 'openai.com', 'Génération d\'images par OpenAI'],
    ['Leonardo.ai', 'leonardo.ai', 'Images IA haute qualité pour créatifs'],
    ['Ideogram', 'ideogram.ai', 'IA d\'images avec texte intégré'],
    ['Looka', 'looka.com', 'Création de logo par IA'],
    ['Adobe Express', 'express.adobe.com', 'Création de contenu visuel rapide'],
    ['Figma AI', 'figma.com', 'Design collaboratif avec fonctions IA'],
    ['Picsart AI', 'picsart.com', 'Édition photo et vidéo par IA'],
    ['Designs.ai', 'designs.ai', 'Suite créative IA complète'],
    ['Brandmark', 'brandmark.io', 'Création d\'identité visuelle par IA'],
    ['Wepik', 'wepik.com', 'Templates design personnalisables par IA'],
    ['Visme', 'visme.co', 'Infographies et présentations IA'],
    ['Kittl', 'kittl.com', 'Design graphique et typographie IA'],
  ],
  'Automatisation' => [
    ['Zapier', 'zapier.com', 'Automatisation entre 6000+ applications'],
    ['Make', 'make.com', 'Automatisation visuelle de workflows'],
    ['n8n', 'n8n.io', 'Automatisation open source et hébergeable'],
    ['Activepieces', 'activepieces.com', 'Alternative open source à Zapier'],
    ['Bardeen', 'bardeen.ai', 'Automatisation navigateur par IA'],
    ['Power Automate', 'powerautomate.microsoft.com', 'Automatisation Microsoft 365'],
    ['Pipedream', 'pipedream.com', 'Automatisation pour développeurs'],
    ['Workato', 'workato.com', 'Intégration et automatisation entreprise'],
    ['Latenode', 'latenode.com', 'No-code automation avec IA'],
    ['Parabola', 'parabola.io', 'Automatisation de données sans code'],
    ['Pabbly Connect', 'pabbly.com', 'Automatisation multi-apps abordable'],
    ['Relay.app', 'relay.app', 'Automatisation collaborative d\'équipe'],
    ['IFTTT', 'ifttt.com', 'Automatisation simple entre applications'],
    ['Albato', 'albato.com', 'Intégrations et automatisations cloud'],
    ['Tray.io', 'tray.io', 'Plateforme d\'intégration enterprise'],
  ],
  'Productivité' => [
    ['Notion AI', 'notion.so', 'Workspace IA tout-en-un pour équipes'],
    ['ClickUp AI', 'clickup.com', 'Gestion de projet avec IA intégrée'],
    ['Monday AI', 'monday.com', 'Plateforme de travail IA collaborative'],
    ['Asana AI', 'asana.com', 'Gestion de tâches et projets IA'],
    ['Linear', 'linear.app', 'Gestion de projets tech rapide et IA'],
    ['Taskade AI', 'taskade.com', 'To-do lists et projets collaboratifs IA'],
    ['Mem.ai', 'mem.ai', 'Notes IA qui s\'organisent seules'],
    ['Coda AI', 'coda.io', 'Documents et bases de données IA'],
    ['Airtable', 'airtable.com', 'Base de données flexible avec IA'],
    ['Height', 'height.app', 'Gestion de projet IA autonome'],
    ['Hive', 'hive.com', 'Gestion de projet avec IA prédictive'],
    ['Wrike AI', 'wrike.com', 'Gestion de projets complexes IA'],
    ['Smartsheet AI', 'smartsheet.com', 'Sheets et projets avec IA'],
    ['Todoist', 'todoist.com', 'Gestion des tâches intelligente'],
    ['Obsidian', 'obsidian.md', 'Prise de notes et gestion de connaissances'],
  ],
  'Réunions & Transcription' => [
    ['Fireflies.ai', 'fireflies.ai', 'Transcription et résumés de réunions auto'],
    ['Otter.ai', 'otter.ai', 'Transcription en temps réel des réunions'],
    ['Fathom', 'fathom.video', 'Enregistrement et résumé de visioconférences'],
    ['tl;dv', 'tldv.io', 'Résumés vidéo de réunions Zoom/Meet'],
    ['Krisp', 'krisp.ai', 'Suppression de bruit et transcription IA'],
    ['Airgram', 'airgram.io', 'Notes de réunion IA automatiques'],
    ['Avoma', 'avoma.com', 'Intelligence conversation et coaching vente'],
    ['MeetGeek', 'meetgeek.ai', 'Résumés automatiques de réunions'],
    ['Notta', 'notta.ai', 'Transcription multilingue en temps réel'],
    ['Read AI', 'read.ai', 'Analyse et insights de réunions IA'],
    ['Tactiq', 'tactiq.io', 'Transcriptions Google Meet et Teams'],
    ['Grain', 'grain.com', 'Enregistrement et partage d\'insights réunion'],
    ['Gong', 'gong.io', 'Intelligence des conversations commerciales'],
    ['Modjo', 'modjo.ai', 'IA d\'analyse des appels commerciaux (FR)'],
    ['Vowel', 'vowel.com', 'Réunions collaboratives avec IA intégrée'],
  ],
  'Comptabilité & Finance' => [
    ['Pennylane', 'pennylane.com', 'Comptabilité IA pour PME françaises'],
    ['Axonaut', 'axonaut.com', 'CRM + comptabilité pour TPE françaises'],
    ['Dext', 'dext.com', 'Capture et traitement automatique de justificatifs'],
    ['QuickBooks', 'quickbooks.intuit.com', 'Comptabilité IA pour petites entreprises'],
    ['FreshBooks', 'freshbooks.com', 'Facturation et comptabilité automatisée'],
    ['Xero', 'xero.com', 'Comptabilité cloud avec réconciliation IA'],
    ['Wave', 'waveapps.com', 'Comptabilité gratuite pour freelances'],
    ['Agicap', 'agicap.com', 'Gestion de trésorerie et prévisions IA'],
    ['Spendesk', 'spendesk.com', 'Gestion des dépenses d\'entreprise'],
    ['Ramp', 'ramp.com', 'Finance IA et optimisation des dépenses'],
    ['Expensify', 'expensify.com', 'Gestion automatique des notes de frais'],
    ['Qonto', 'qonto.com', 'Compte pro IA pour startups et PME FR'],
    ['Tiime', 'tiime.fr', 'Comptabilité collaborative IA pour TPE FR'],
    ['Payfit', 'payfit.com', 'Paie et RH automatisées pour PME FR'],
    ['Silae', 'silae.fr', 'Logiciel de paie IA made in France'],
  ],
  'E-commerce' => [
    ['Shopify Magic', 'shopify.com', 'IA native Shopify pour e-commerçants'],
    ['Klaviyo', 'klaviyo.com', 'Email et SMS marketing pour e-commerce'],
    ['Yotpo', 'yotpo.com', 'Avis clients et fidélisation IA'],
    ['Gorgias', 'gorgias.com', 'Support client IA pour boutiques en ligne'],
    ['Omnisend', 'omnisend.com', 'Automatisation marketing e-commerce'],
    ['Attentive', 'attentive.com', 'Marketing SMS personnalisé IA'],
    ['Nosto', 'nosto.com', 'Personnalisation produits e-commerce IA'],
    ['Rebuy', 'rebuyengine.com', 'Recommandations produits IA pour Shopify'],
    ['Bloomreach', 'bloomreach.com', 'Personnalisation et découverte produits IA'],
    ['Okendo', 'okendo.io', 'Avis clients et UGC pour Shopify'],
    ['Postscript', 'postscript.io', 'SMS marketing automatisé e-commerce'],
    ['Privy', 'privy.com', 'Pop-ups et email capture pour boutiques'],
    ['LoyaltyLion', 'loyaltylion.com', 'Programme fidélité e-commerce IA'],
    ['Drip', 'drip.com', 'CRM e-commerce et email automation'],
    ['Dynamic Yield', 'dynamicyield.com', 'Personnalisation expérience client IA'],
  ],
  'SEO & Marketing' => [
    ['Semrush', 'semrush.com', 'Suite SEO et marketing digital complète'],
    ['Surfer SEO', 'surferseo.com', 'Optimisation de contenu SEO par IA'],
    ['Ahrefs', 'ahrefs.com', 'Analyse de backlinks et recherche de mots-clés'],
    ['Clearscope', 'clearscope.io', 'Optimisation de contenu sémantique IA'],
    ['Frase', 'frase.io', 'Recherche et rédaction SEO assistée par IA'],
    ['NeuronWriter', 'neuronwriter.com', 'Rédaction SEO optimisée par IA'],
    ['MarketMuse', 'marketmuse.com', 'Stratégie de contenu basée sur les données'],
    ['SE Ranking', 'seranking.com', 'Suite SEO all-in-one abordable'],
    ['Mangools', 'mangools.com', 'Outils SEO simples et accessibles'],
    ['RankIQ', 'rankiq.com', 'SEO IA pour blogueurs et créateurs'],
    ['Ubersuggest', 'ubersuggest.com', 'Recherche de mots-clés et analyse SEO'],
    ['AdCreative.ai', 'adcreative.ai', 'Création de visuels publicitaires IA'],
    ['Albert.ai', 'albert.ai', 'Gestion publicitaire autonome par IA'],
    ['Smartly.io', 'smartly.io', 'Automatisation créative pour réseaux sociaux'],
    ['Revealbot', 'revealbot.com', 'Automatisation et optimisation des ads'],
  ],
  'Réseaux Sociaux' => [
    ['Buffer', 'buffer.com', 'Planification et analyse réseaux sociaux'],
    ['Hootsuite', 'hootsuite.com', 'Gestion multi-plateformes avec IA'],
    ['Later', 'later.com', 'Planification visuelle Instagram et TikTok'],
    ['Sprout Social', 'sproutsocial.com', 'Management social media entreprise'],
    ['Agorapulse', 'agorapulse.com', 'Gestion réseaux sociaux made in France'],
    ['SocialBee', 'socialbee.io', 'Gestion de contenu social media IA'],
    ['ContentStudio', 'contentstudio.io', 'Curation et planification contenu IA'],
    ['Flick', 'flick.tech', 'Hashtags et stratégie Instagram IA'],
    ['Ocoya', 'ocoya.com', 'Création et planification contenu social IA'],
    ['FeedHive', 'feedhive.io', 'Planification sociale IA avec prédictions'],
    ['Typefully', 'typefully.com', 'Rédaction et planification Twitter/X IA'],
    ['Hypefury', 'hypefury.com', 'Croissance Twitter/X automatisée'],
    ['Publer', 'publer.io', 'Planification multi-réseaux et analytics'],
    ['Missinglettr', 'missinglettr.com', 'Campagnes sociales automatiques depuis blog'],
    ['Predis.ai', 'predis.ai', 'Génération contenu social media par IA'],
  ],
  'Vidéo & Audio' => [
    ['Descript', 'descript.com', 'Édition vidéo et podcast par texte IA'],
    ['Synthesia', 'synthesia.io', 'Vidéos avec avatars IA sans caméra'],
    ['HeyGen', 'heygen.com', 'Génération de vidéos IA avec avatars réalistes'],
    ['Runway', 'runwayml.com', 'Édition et génération vidéo par IA'],
    ['Captions.ai', 'captions.ai', 'Sous-titres automatiques et édition IA'],
    ['Pictory', 'pictory.ai', 'Transformation texte en vidéo IA'],
    ['Lumen5', 'lumen5.com', 'Vidéos marketing automatiques depuis articles'],
    ['InVideo AI', 'invideo.io', 'Création vidéo IA en quelques minutes'],
    ['Fliki', 'fliki.ai', 'Vidéos text-to-video avec voix IA'],
    ['Opus Clip', 'opus.pro', 'Découpe automatique de clips courts viraux'],
    ['Veed.io', 'veed.io', 'Édition vidéo en ligne avec IA'],
    ['ElevenLabs', 'elevenlabs.io', 'Synthèse vocale ultra-réaliste IA'],
    ['Murf AI', 'murf.ai', 'Voix off professionnelles générées par IA'],
    ['Podcastle', 'podcastle.fm', 'Production podcast assistée par IA'],
    ['Adobe Podcast', 'podcast.adobe.com', 'Amélioration audio podcast par IA'],
  ],
  'RH & Recrutement' => [
    ['BambooHR', 'bamboohr.com', 'RH complet pour PME avec IA'],
    ['Factorial', 'factorialhr.com', 'RH et paie pour PME européennes'],
    ['Personio', 'personio.com', 'SIRH pour moyennes entreprises'],
    ['Manatal', 'manatal.com', 'ATS IA pour recrutement rapide'],
    ['Lever', 'lever.co', 'Recrutement collaboratif avec IA'],
    ['Greenhouse', 'greenhouse.io', 'Plateforme de recrutement structurée'],
    ['Teamtailor', 'teamtailor.com', 'Marque employeur et ATS moderne'],
    ['Recruitee', 'recruitee.com', 'Recrutement collaboratif pour équipes'],
    ['Fetcher', 'fetcher.ai', 'Sourcing automatique de candidats IA'],
    ['Textio', 'textio.com', 'Optimisation des offres d\'emploi par IA'],
    ['Lattice', 'lattice.com', 'Performance et engagement collaborateurs'],
    ['15Five', '15five.com', 'Gestion performance continue IA'],
    ['Leapsome', 'leapsome.com', 'Plateforme RH IA tout-en-un'],
    ['Lucca', 'lucca.fr', 'Suite RH française complète'],
    ['Payfit', 'payfit.com', 'Paie et RH PME françaises'],
  ],
  'Service Client' => [
    ['Intercom', 'intercom.com', 'Plateforme client IA avec chatbot avancé'],
    ['Zendesk AI', 'zendesk.com', 'Support client omnicanal avec IA'],
    ['Tidio', 'tidio.com', 'Chat IA et chatbot pour PME'],
    ['Crisp', 'crisp.chat', 'Chat client IA made in France'],
    ['Freshdesk', 'freshdesk.com', 'Helpdesk IA pour équipes support'],
    ['Help Scout', 'helpscout.com', 'Support client email IA simplifié'],
    ['Drift', 'drift.com', 'Marketing conversationnel et chatbot B2B'],
    ['Chatfuel', 'chatfuel.com', 'Chatbots IA pour Messenger et Instagram'],
    ['ManyChat', 'manychat.com', 'Automatisation messages et chatbots'],
    ['Landbot', 'landbot.io', 'Chatbots conversationnels no-code'],
    ['Voiceflow', 'voiceflow.com', 'Design et déploiement d\'agents IA'],
    ['Kustomer', 'kustomer.com', 'CRM service client IA unifié'],
    ['Front', 'front.com', 'Boîte mail partagée IA pour équipes'],
    ['Dixa', 'dixa.com', 'Service client conversationnel IA'],
    ['Gorgias', 'gorgias.com', 'Support e-commerce automatisé par IA'],
  ],
  'Présentations' => [
    ['Gamma.app', 'gamma.app', 'Présentations et sites web générés par IA'],
    ['Beautiful.ai', 'beautiful.ai', 'Slides qui se designent toutes seules'],
    ['Tome', 'tome.app', 'Narration visuelle et présentations IA'],
    ['Pitch', 'pitch.com', 'Présentations collaboratives modernes IA'],
    ['Slides AI', 'slidesai.io', 'Génération de slides Google depuis texte'],
    ['Plus AI', 'plusdocs.com', 'Création de présentations IA pour Google Slides'],
    ['Decktopus', 'decktopus.com', 'Présentations intelligentes en quelques clics'],
    ['Storydoc', 'storydoc.com', 'Présentations interactives pour commerciaux'],
    ['Genially', 'genially.com', 'Contenus interactifs et animés IA'],
    ['Vev', 'vev.design', 'Design web interactif no-code'],
  ],
  'Code & Tech' => [
    ['GitHub Copilot', 'github.com', 'IA de complétion de code par GitHub/OpenAI'],
    ['Cursor', 'cursor.sh', 'Éditeur de code IA nouvelle génération'],
    ['Replit', 'replit.com', 'IDE IA en ligne pour tous niveaux'],
    ['Codeium', 'codeium.com', 'Autocomplétion de code IA gratuite'],
    ['Tabnine', 'tabnine.com', 'Assistant IA de code respectueux de la vie privée'],
    ['Warp', 'warp.dev', 'Terminal intelligent avec IA intégrée'],
    ['Pieces', 'pieces.app', 'Gestionnaire de snippets IA pour développeurs'],
    ['Swimm', 'swimm.io', 'Documentation de code automatique IA'],
    ['Phind', 'phind.com', 'Moteur de recherche IA pour développeurs'],
    ['Continue.dev', 'continue.dev', 'Extension IA pour VS Code et JetBrains'],
  ],
  'Planning & Agenda' => [
    ['Reclaim.ai', 'reclaim.ai', 'Planification automatique des tâches IA'],
    ['Clockwise', 'getclockwise.com', 'Optimisation d\'agenda pour équipes'],
    ['Motion', 'usemotion.com', 'Planning IA automatique de journée'],
    ['Calendly', 'calendly.com', 'Prise de rendez-vous automatisée'],
    ['Cal.com', 'cal.com', 'Calendrier open source avec IA'],
    ['Savvycal', 'savvycal.com', 'Planification de réunions personnalisée'],
    ['Chili Piper', 'chilipiper.com', 'Routing et booking de réunions B2B'],
    ['Doodle', 'doodle.com', 'Coordination d\'agendas simplifiée'],
    ['Simplybook.me', 'simplybook.me', 'Réservation en ligne pour services'],
    ['Acuity', 'acuityscheduling.com', 'Planification rendez-vous clients automatisée'],
  ],
  'Traduction' => [
    ['DeepL', 'deepl.com', 'Traduction IA de qualité professionnelle'],
    ['Lilt', 'lilt.com', 'Traduction IA pour entreprises'],
    ['Smartling', 'smartling.com', 'Localisation continue IA pour produits'],
    ['Phrase', 'phrase.com', 'Gestion des traductions et localisation IA'],
    ['Lokalise', 'lokalise.com', 'Localisation produit en équipe avec IA'],
    ['Weglot', 'weglot.com', 'Traduction de site web automatique'],
    ['Crowdin', 'crowdin.com', 'Localisation collaborative avec IA'],
    ['POEditor', 'poeditor.com', 'Plateforme de localisation simplifiée'],
  ],
  'Santé & Bien-être' => [
    ['Nabla', 'nabla.com', 'Assistant IA pour professionnels de santé (FR)'],
    ['Suki AI', 'suki.ai', 'Dictée médicale et notes cliniques IA'],
    ['Corti', 'corti.ai', 'IA d\'aide à la décision médicale'],
    ['Abridge', 'abridge.com', 'Résumés automatiques de consultations médicales'],
    ['Aidoc', 'aidoc.com', 'IA de détection médicale pour radiologie'],
  ],
  'Restauration' => [
    ['Sunday', 'sundayapp.io', 'Paiement et avis clients pour restaurants'],
    ['Lightspeed', 'lightspeedhq.com', 'Caisse IA pour restaurants et commerces'],
    ['MarketMan', 'marketman.com', 'Gestion des stocks restaurant par IA'],
    ['Toast', 'pos.toasttab.com', 'Système de caisse IA pour restaurateurs'],
    ['Popmenu', 'popmenu.com', 'Marketing digital IA pour restaurants'],
    ['SevenRooms', 'sevenrooms.com', 'CRM et réservation pour la restauration'],
  ],
  'BTP & Immobilier' => [
    ['Procore', 'procore.com', 'Gestion de projets de construction IA'],
    ['Buildertrend', 'buildertrend.com', 'Logiciel de gestion chantier PME'],
    ['Fieldwire', 'fieldwire.com', 'Gestion de chantier mobile IA'],
    ['Autodesk', 'autodesk.com', 'Suite conception et construction IA'],
    ['Propertybase', 'propertybase.com', 'CRM immobilier avec IA intégrée'],
    ['Structurely', 'structurely.com', 'Qualification de leads immobiliers par IA'],
  ],
];

// Compute total tool count
$total_tools = 0;
foreach ($categories as $tools) {
  $total_tools += count($tools);
}
?>
<style>
/* ── Page hero ── */
.dir-hero {
  padding: 72px 0 56px;
  text-align: center;
  background: linear-gradient(180deg, rgba(16,185,129,0.04) 0%, transparent 100%);
  border-bottom: 1px solid var(--border);
}
.dir-hero h1 {
  font-size: clamp(36px, 5vw, 60px);
  font-weight: 300;
  letter-spacing: -0.04em;
  margin-bottom: 16px;
  line-height: 1.1;
}
.dir-hero h1 strong {
  font-weight: 800;
  background: var(--gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.dir-hero p {
  font-size: 17px;
  color: var(--ink-3);
  max-width: 520px;
  margin: 0 auto 36px;
  line-height: 1.65;
}

/* ── Search bar ── */
.search-wrap {
  max-width: 480px;
  margin: 0 auto;
  position: relative;
}
.search-wrap svg {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--ink-4);
  pointer-events: none;
}
#tool-search {
  width: 100%;
  padding: 14px 20px 14px 44px;
  border-radius: var(--r-pill);
  border: 1px solid var(--border);
  background: var(--white);
  font-size: 15px;
  color: var(--ink);
  box-shadow: var(--shadow-sm);
  outline: none;
  transition: border-color 150ms, box-shadow 150ms;
  box-sizing: border-box;
}
#tool-search:focus {
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
}
#tool-search::placeholder { color: var(--ink-4); }

/* ── Stats bar ── */
.dir-stats {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 0;
  border-bottom: 1px solid var(--border);
  margin-bottom: 0;
  flex-wrap: wrap;
  gap: 12px;
}
.dir-stats-count {
  font-size: 13px;
  color: var(--ink-3);
}
.dir-stats-count strong {
  color: var(--ink);
  font-weight: 700;
}

/* ── Category tabs ── */
.cat-tabs-wrap {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding: 20px 0;
  border-bottom: 1px solid var(--border);
  margin-bottom: 32px;
}
.cat-tabs-wrap::-webkit-scrollbar { display: none; }
.cat-tabs {
  display: flex;
  gap: 8px;
  width: max-content;
}
.cat-tab {
  padding: 8px 18px;
  border-radius: var(--r-pill);
  border: 1px solid var(--border);
  background: var(--white);
  font-size: 13px;
  font-weight: 500;
  color: var(--ink-3);
  cursor: pointer;
  white-space: nowrap;
  transition: all 150ms;
}
.cat-tab:hover {
  border-color: var(--green);
  color: var(--green-deep);
  background: rgba(16,185,129,0.04);
}
.cat-tab.active {
  border-color: var(--green);
  color: var(--green-deep);
  background: rgba(16,185,129,0.08);
  font-weight: 600;
}

/* ── Tool grid ── */
.tools-directory {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  padding-bottom: 80px;
}
@media (max-width: 1100px) {
  .tools-directory { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
  .tools-directory { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}
@media (max-width: 480px) {
  .tools-directory { grid-template-columns: 1fr; }
}

/* ── Tool card ── */
.tool-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  box-shadow: var(--shadow-sm);
  transition: transform 150ms, box-shadow 150ms;
}
.tool-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}
.tool-card-top {
  display: flex;
  align-items: center;
  gap: 12px;
}
.tool-logo {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  object-fit: contain;
  background: #f8f9fa;
  padding: 4px;
  flex-shrink: 0;
}
.tool-logo-fallback {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: var(--gradient);
  display: none;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  flex-shrink: 0;
}
.tool-name {
  font-size: 15px;
  font-weight: 600;
  color: var(--ink-2);
  line-height: 1.3;
}
.tool-desc {
  font-size: 13px;
  color: var(--ink-3);
  line-height: 1.5;
  flex: 1;
}
.tool-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
}
.tool-cat {
  font-size: 11px;
  font-weight: 600;
  color: var(--green-deep);
  background: rgba(16,185,129,0.08);
  padding: 3px 10px;
  border-radius: 20px;
  display: inline-block;
  white-space: nowrap;
}
.tool-link {
  font-size: 12px;
  color: var(--blue);
  text-decoration: none;
  white-space: nowrap;
}
.tool-link:hover { text-decoration: underline; }

/* ── Empty state ── */
.tools-empty {
  display: none;
  grid-column: 1 / -1;
  text-align: center;
  padding: 80px 20px;
  color: var(--ink-3);
}
.tools-empty strong { display: block; font-size: 17px; color: var(--ink-2); margin-bottom: 8px; }

/* ── CTA bottom ── */
.dir-cta {
  text-align: center;
  padding: 60px 20px 80px;
  border-top: 1px solid var(--border);
}
.dir-cta p {
  font-size: 18px;
  color: var(--ink-3);
  margin-bottom: 24px;
  max-width: 480px;
  margin-left: auto;
  margin-right: auto;
}
</style>

<!-- Hero -->
<section class="dir-hero">
  <div class="container">
    <div class="badge" style="margin: 0 auto 20px">Annuaire IA</div>
    <h1>
      <strong><?= $total_tools ?>+ outils IA</strong><br>
      référencés pour les PME
    </h1>
    <p>La référence française des solutions IA pour PME.</p>

    <div class="search-wrap">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input
        type="search"
        id="tool-search"
        placeholder="Rechercher un outil..."
        autocomplete="off"
        spellcheck="false"
        aria-label="Rechercher un outil IA"
      />
    </div>
  </div>
</section>

<div class="container">

  <!-- Stats bar -->
  <div class="dir-stats">
    <div class="dir-stats-count">
      <strong id="visible-count"><?= $total_tools ?></strong> outils affichés sur <strong><?= $total_tools ?></strong>
    </div>
    <div class="dir-stats-count"><?= count($categories) ?> catégories</div>
  </div>

  <!-- Category tabs -->
  <div class="cat-tabs-wrap" role="tablist" aria-label="Filtrer par catégorie">
    <div class="cat-tabs">
      <button class="cat-tab active" data-cat="all" role="tab" aria-selected="true">Tous</button>
      <?php foreach (array_keys($categories) as $cat): ?>
      <button class="cat-tab" data-cat="<?= htmlspecialchars($cat) ?>" role="tab" aria-selected="false">
        <?= htmlspecialchars($cat) ?>
      </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Tools grid -->
  <div class="tools-directory" id="tools-grid">

    <?php foreach ($categories as $cat => $tools): ?>
      <?php foreach ($tools as $tool):
        $name   = $tool[0];
        $domain = $tool[1];
        $desc   = $tool[2];
        $logo   = 'https://www.google.com/s2/favicons?domain=' . $domain . '&sz=128';
        $initials = mb_strtoupper(mb_substr($name, 0, 2));
      ?>
      <div
        class="tool-card reveal"
        data-name="<?= htmlspecialchars(strtolower($name)) ?>"
        data-cat="<?= htmlspecialchars($cat) ?>"
      >
        <div class="tool-card-top">
          <img
            class="tool-logo"
            src="<?= htmlspecialchars($logo) ?>"
            alt="<?= htmlspecialchars($name) ?> logo"
            loading="lazy"
            width="40"
            height="40"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
          />
          <div class="tool-logo-fallback" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
          <div class="tool-name"><?= htmlspecialchars($name) ?></div>
        </div>

        <p class="tool-desc"><?= htmlspecialchars($desc) ?></p>

        <div class="tool-footer">
          <span class="tool-cat"><?= htmlspecialchars($cat) ?></span>
          <a
            class="tool-link"
            href="https://<?= htmlspecialchars($domain) ?>"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Visiter <?= htmlspecialchars($name) ?>"
          ><?= htmlspecialchars($domain) ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- Et bien d'autres -->
    <div class="tool-card" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,rgba(16,185,129,0.06),rgba(14,165,233,0.06));border:1.5px dashed rgba(16,185,129,0.3);min-height:120px" id="tools-more">
      <div style="text-align:center">
        <div style="font-size:22px;font-weight:700;letter-spacing:-0.02em;color:var(--ink-2);margin-bottom:4px">et bien d'autres…</div>
        <div style="font-size:13px;color:var(--ink-4)">200+ outils référencés et mis à jour en continu</div>
      </div>
    </div>

    <!-- Empty state (shown by JS when no results) -->
    <div class="tools-empty" id="tools-empty" aria-live="polite">
      <strong>Aucun outil trouvé</strong>
      Essayez un autre terme ou sélectionnez une autre catégorie.
    </div>

  </div><!-- /.tools-directory -->

  <!-- Bottom CTA -->
  <div class="dir-cta">
    <p>Vous voulez savoir quels outils sont les plus adaptés à <strong>votre secteur</strong> ?</p>
    <a href="/" class="btn btn-primary" style="display:inline-flex">Faire mon audit IA gratuit</a>
  </div>

</div><!-- /.container -->

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
(function () {
  var totalCount = <?= $total_tools ?>;
  var searchInput = document.getElementById('tool-search');
  var cards = document.querySelectorAll('.tool-card');
  var emptyState = document.getElementById('tools-empty');
  var visibleCountEl = document.getElementById('visible-count');
  var tabs = document.querySelectorAll('.cat-tab');

  function filterTools() {
    var q = searchInput.value.toLowerCase().trim();
    var cat = (document.querySelector('.cat-tab.active') || {}).dataset.cat || 'all';
    var visible = 0;

    cards.forEach(function (card) {
      var name = card.dataset.name;
      var cardCat = card.dataset.cat;
      var matchCat = cat === 'all' || cardCat === cat;
      var matchQ = !q || name.indexOf(q) !== -1;
      var show = matchCat && matchQ;
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    visibleCountEl.textContent = visible;
    emptyState.style.display = visible === 0 ? 'block' : 'none';
  }

  searchInput.addEventListener('input', filterTools);

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      this.classList.add('active');
      this.setAttribute('aria-selected', 'true');
      filterTools();
    });
  });
})();
</script>
