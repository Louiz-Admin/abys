<?php
// Analyse des leads (lecture seule). Protege par jeton. A SUPPRIMER apres usage.
$T = 'abys-leads-Qw8n3z';
if (($_GET['k'] ?? '') !== $T) { http_response_code(404); die('{}'); }
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');

$db = get_db();
$out = [];

function q($db,$sql){ try { return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);} catch(Throwable $e){ return ['err'=>$e->getMessage()];} }
function one($db,$sql){ try { return $db->query($sql)->fetchColumn();} catch(Throwable $e){ return null;} }

// Schema de leads
$out['colonnes_leads'] = q($db,"SHOW COLUMNS FROM leads");

$out['total_leads']        = (int)one($db,"SELECT COUNT(*) FROM leads");
$out['avec_email']         = (int)one($db,"SELECT COUNT(*) FROM leads WHERE email IS NOT NULL AND email<>''");
$out['urls_distinctes']    = (int)one($db,"SELECT COUNT(DISTINCT url) FROM leads");
$out['par_source']         = q($db,"SELECT source, COUNT(*) n FROM leads GROUP BY source ORDER BY n DESC");
$out['par_jour']           = q($db,"SELECT DATE(created_at) j, COUNT(*) n FROM leads GROUP BY DATE(created_at) ORDER BY j");
$out['par_secteur']        = q($db,"SELECT COALESCE(NULLIF(secteur,''),'(vide)') secteur, COUNT(*) n FROM leads GROUP BY secteur ORDER BY n DESC LIMIT 20");

// URLs les plus frequentes (reperer les tests / doublons)
$out['urls_top']           = q($db,"SELECT url, COUNT(*) n FROM leads GROUP BY url ORDER BY n DESC LIMIT 15");

// Entonnoir
$out['audits_total']       = (int)one($db,"SELECT COUNT(*) FROM audits");
$out['leads_avec_audit']   = (int)one($db,"SELECT COUNT(DISTINCT lead_id) FROM audits");
$out['reports_total']      = (int)one($db,"SELECT COUNT(*) FROM reports");
$out['reports_payes']      = (int)one($db,"SELECT COUNT(*) FROM reports WHERE paid_at IS NOT NULL");

// Echantillon recent (le proprietaire peut voir ses donnees)
$out['echantillon_recent'] = q($db,"SELECT id, created_at, url, COALESCE(secteur,'') secteur, COALESCE(email,'') email, source FROM leads ORDER BY id DESC LIMIT 25");

// Emails valides (domaine) pour juger la qualite
$out['emails_domaines']    = q($db,"SELECT SUBSTRING_INDEX(email,'@',-1) domaine, COUNT(*) n FROM leads WHERE email LIKE '%@%' GROUP BY domaine ORDER BY n DESC LIMIT 20");

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
