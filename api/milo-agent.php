<?php
// Fichier: abys-ai/api/milo-agent.php
// MILO CHEF D'ORCHESTRE.
// Milo n'attend pas qu'on lui écrive : il ouvre les dossiers, juge chaque situation,
// décide quoi faire, agit, et rend compte à Thomas.
//
// Cycle (déclenché par le trafic du site, au plus une fois par heure) :
//   1. Il constitue son tableau de bord (audits récents, relances déjà faites)
//   2. Il DÉCIDE dossier par dossier (relancer, attendre, abandonner)
//   3. Il exécute ses décisions (emails écrits par lui, personnalisés)
//   4. Il journalise tout et rend compte à Thomas
//
// URL : https://abys.ai/api/milo-agent.php?key=<imap_cron_key>[&dry=1]

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/email.php';
require_once __DIR__ . '/ai-helpers.php';

header('Content-Type: application/json; charset=utf-8');
ignore_user_abort(true);
@set_time_limit(240);

$db       = get_db();
$settings = get_settings($db);

$cron_key = $settings['imap_cron_key'] ?? 'abys_cron_2026_x7k9p';
if (($_GET['key'] ?? '') !== $cron_key) { http_response_code(403); exit('Forbidden'); }

$dry = isset($_GET['dry']);   // mode observation : Milo décide mais n'envoie rien

// Trace du dernier cycle : lisible a tout moment via ?peek=1 (le cycle dure ~90 s,
// bien plus que le timeout d'un navigateur ou d'un curl).
function milo_trace(PDO $db, array $payload): void {
    try {
        $db->prepare("INSERT INTO settings (`key`, value) VALUES ('milo_agent_last_result', ?) ON DUPLICATE KEY UPDATE value = VALUES(value)")
           ->execute([json_encode($payload, JSON_UNESCAPED_UNICODE)]);
    } catch (Throwable $e) { /* jamais bloquant */ }
}

if (isset($_GET['ping'])) {
    exit(json_encode(['version' => 'v10', 'ts' => date('c')]));
}

if (isset($_GET['diag'])) {
    $out = ['version' => 'v10', 'ts' => date('c')];
    try { $out['verrou_milo_agent'] = $db->query("SELECT IS_USED_LOCK('milo_agent')")->fetchColumn(); } catch (Throwable $e) { $out['verrou_err'] = $e->getMessage(); }
    try {
        $pl = $db->query("SELECT ID, TIME, STATE, LEFT(INFO, 120) AS REQUETE FROM information_schema.PROCESSLIST WHERE COMMAND <> 'Sleep' ORDER BY TIME DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        $out['requetes_en_cours'] = $pl;
    } catch (Throwable $e) { $out['processlist_err'] = $e->getMessage(); }
    $t0 = microtime(true);
    try { $out['leads_total'] = (int) $db->query("SELECT COUNT(*) FROM leads")->fetchColumn(); }
    catch (Throwable $e) { $out['leads_err'] = $e->getMessage(); }
    $out['leads_ms'] = round((microtime(true) - $t0) * 1000);

    // La table settings sait-elle vraiment encaisser une trace ?
    try { $out['colonnes_settings'] = $db->query("SHOW COLUMNS FROM settings")->fetchAll(PDO::FETCH_ASSOC); }
    catch (Throwable $e) { $out['colonnes_err'] = $e->getMessage(); }
    try { $out['lignes_last_result'] = (int) $db->query("SELECT COUNT(*) FROM settings WHERE `key` = 'milo_agent_last_result'")->fetchColumn(); }
    catch (Throwable $e) { $out['lignes_err'] = $e->getMessage(); }
    try {
        $db->prepare("INSERT INTO settings (`key`, value) VALUES ('milo_agent_test', ?) ON DUPLICATE KEY UPDATE value = VALUES(value)")
           ->execute([json_encode(['essai' => date('c')])]);
        $out['ecriture_test'] = $db->query("SELECT value FROM settings WHERE `key` = 'milo_agent_test' ORDER BY 1 DESC LIMIT 1")->fetchColumn();
    } catch (Throwable $e) { $out['ecriture_err'] = $e->getMessage(); }

    exit(json_encode($out, JSON_UNESCAPED_UNICODE));
}

if (isset($_GET['peek'])) {
    exit($settings['milo_agent_last_result'] ?? json_encode(['info' => 'aucun cycle enregistre']));
}

// ── Interrupteur général ────────────────────────────────────────────────────
if (($settings['milo_agent_enabled'] ?? '1') !== '1') {
    milo_trace($db, ['skipped' => 'agent desactive', 'ts' => date('c')]);
    exit(json_encode(['skipped' => 'agent desactive (settings.milo_agent_enabled)']));
}

$api_key = decrypt_value($settings['claude_key'] ?? '') ?: '';
if (!$api_key) { milo_trace($db, ['skipped' => 'cle IA manquante', 'ts' => date('c')]); exit(json_encode(['skipped' => 'cle IA manquante'])); }

// ── Verrou : un seul cycle à la fois ────────────────────────────────────────
$lock = 'milo_agent';
if (!(int) $db->query("SELECT GET_LOCK(" . $db->quote($lock) . ", 0)")->fetchColumn()) {
    milo_trace($db, ['skipped' => 'cycle deja en cours', 'ts' => date('c')]);
    exit(json_encode(['skipped' => 'cycle deja en cours']));
}

// ── Cadence : un cycle de décision par heure au maximum ─────────────────────
$last    = $settings['milo_agent_last_run'] ?? '';
$min_gap = (int) ($settings['milo_agent_min_gap_min'] ?? 60);
if ($last && (time() - strtotime($last)) < $min_gap * 60 && !$dry) {
    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lock) . ")");
    milo_trace($db, ['skipped' => 'cycle recent', 'dernier' => $last, 'ts' => date('c')]);
    exit(json_encode(['skipped' => 'cycle recent', 'dernier' => $last]));
}

if (!$dry) {
    $db->prepare("INSERT INTO settings (`key`, value) VALUES ('milo_agent_last_run', ?) ON DUPLICATE KEY UPDATE value = VALUES(value)")
       ->execute([date('Y-m-d H:i:s')]);
}

milo_trace($db, ['stage' => 'avant_migration', 'dry' => $dry, 'ts' => date('c')]);

// ── Migration : une seule fois dans la vie de l'agent ───────────────────────
if (($settings['milo_agent_schema'] ?? '') !== '2') {
    $db->exec("CREATE TABLE IF NOT EXISTS milo_actions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lead_id INT NOT NULL,
        action VARCHAR(30) NOT NULL,
        reason TEXT,
        subject VARCHAR(255),
        body TEXT,
        sent TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_lead (lead_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    try {
        $db->exec("ALTER TABLE leads
            ADD COLUMN IF NOT EXISTS company_name VARCHAR(190) NULL,
            ADD COLUMN IF NOT EXISTS secteur VARCHAR(120) NULL,
            ADD COLUMN IF NOT EXISTS prenom VARCHAR(100) NULL");
    } catch (Throwable $e) { /* deja en place */ }
    $db->prepare("INSERT INTO settings (`key`, value) VALUES ('milo_agent_schema', '2') ON DUPLICATE KEY UPDATE value = VALUES(value)")->execute();
}

milo_trace($db, ['stage' => 'demarrage', 'dry' => $dry, 'ts' => date('c')]);

$log = ['analyses' => 0, 'relances' => 0, 'attentes' => 0, 'abandons' => 0, 'erreurs' => 0];

try {
    // ════════════════════════════════════════════════════════════════
    // 1. LE TABLEAU DE BORD DE MILO
    // ════════════════════════════════════════════════════════════════
    $dossiers = $db->query("
        SELECT
            l.id, l.email, l.url, l.secteur, l.company_name, l.prenom, l.source,
            DATEDIFF(NOW(), a.created_at) AS jours_depuis_audit,
            a.score, a.recommendations,
            (SELECT COUNT(*) FROM milo_actions m WHERE m.lead_id = l.id AND m.action = 'relancer' AND m.sent = 1) AS relances_faites,
            (SELECT MAX(m.created_at) FROM milo_actions m WHERE m.lead_id = l.id AND m.sent = 1) AS derniere_relance,
            (SELECT COUNT(*) FROM milo_actions m WHERE m.lead_id = l.id AND m.action = 'abandonner') AS clos,
            (SELECT COUNT(*) FROM reports r WHERE r.lead_id = l.id AND r.paid_at IS NOT NULL) AS a_paye,
            (SELECT COUNT(*) FROM email_inbound_log e WHERE e.from_email = l.email) AS a_ecrit
        FROM leads l
        JOIN audits a ON a.lead_id = l.id
        WHERE l.email IS NOT NULL AND l.email <> ''
          AND a.created_at >= DATE_SUB(NOW(), INTERVAL 45 DAY)
          AND a.id = (SELECT MAX(a2.id) FROM audits a2 WHERE a2.lead_id = l.id)
        HAVING a_paye = 0 AND clos = 0 AND relances_faites < 3
        ORDER BY jours_depuis_audit ASC
        LIMIT 12
    ")->fetchAll(PDO::FETCH_ASSOC);

    milo_trace($db, ['stage' => 'dossiers', 'n' => count($dossiers), 'ts' => date('c')]);

    $panorama = $db->query("
        SELECT
            COUNT(*) AS audits_45j,
            SUM(CASE WHEN l.email IS NOT NULL AND l.email <> '' THEN 1 ELSE 0 END) AS avec_email,
            SUM(CASE WHEN l.email IS NULL OR l.email = '' THEN 1 ELSE 0 END) AS sans_email
        FROM audits a JOIN leads l ON l.id = a.lead_id
        WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 45 DAY)
    ")->fetch(PDO::FETCH_ASSOC) ?: [];
    $panorama['payes_45j'] = (int) $db->query("
        SELECT COUNT(*) FROM reports WHERE paid_at IS NOT NULL AND paid_at >= DATE_SUB(NOW(), INTERVAL 45 DAY)
    ")->fetchColumn();
    $log['panorama'] = $panorama;
    milo_trace($db, ['stage' => 'panorama', 'panorama' => $panorama, 'ts' => date('c')]);

    if (!$dossiers) {
        $db->query("SELECT RELEASE_LOCK(" . $db->quote($lock) . ")");
        $vide = array_merge($log, ['info' => 'aucun dossier a traiter', 'dry' => $dry, 'ts' => date('c')]);
        milo_trace($db, $vide);
        exit(json_encode($vide));
    }

    $cap         = (int) ($settings['milo_agent_daily_cap'] ?? 15);
    $envoyes_auj = (int) $db->query("SELECT COUNT(*) FROM milo_actions WHERE sent = 1 AND created_at >= CURDATE()")->fetchColumn();

    // ════════════════════════════════════════════════════════════════
    // 2. MILO DÉCIDE
    // ════════════════════════════════════════════════════════════════
    $fiches = [];
    foreach ($dossiers as $d) {
        $rec    = json_decode($d['recommendations'] ?? '', true) ?: [];
        $opps   = array_slice($rec['opportunities'] ?? [], 0, 3);
        $outils = array_map(fn($o) => ($o['tool'] ?? '') . ' (' . ($o['time_saved_h_week'] ?? 0) . ' h/sem)', $opps);
        $trs    = array_slice($rec['transformations'] ?? [], 0, 2);
        $taches = array_map(fn($t) => ($t['task'] ?? ''), $trs);

        $fiches[] = [
            'lead_id'              => (int) $d['id'],
            'prenom'               => $d['prenom'] ?: null,
            'site'                 => $d['url'] ?: ($d['company_name'] ?: 'sans site'),
            'secteur'              => $d['secteur'] ?: ($rec['sector_label'] ?? 'non precise'),
            'score_ia'             => (int) $d['score'],
            'jours_depuis_audit'   => (int) $d['jours_depuis_audit'],
            'relances_deja_faites' => (int) $d['relances_faites'],
            'derniere_relance'     => $d['derniere_relance'],
            'a_deja_ecrit'         => ((int) $d['a_ecrit']) > 0,
            'gain_h_semaine'       => $rec['total_time_saved_h_week'] ?? null,
            'gain_eur_mois'        => $rec['total_money_saved_eur_month'] ?? null,
            'top_outils'           => $outils,
            'taches_visees'        => $taches,
        ];
    }

    $fiches_json = json_encode($fiches, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $places      = max(0, $cap - $envoyes_auj);

    $prompt = <<<PROMPT
Tu es MILO, le copilote IA qui FAIT TOURNER l'entreprise ABYS (abys.ai). Tu n'es pas un standardiste qui attend les appels : tu es le chef d'orchestre. Chaque jour tu ouvres les dossiers, tu juges, tu décides et tu agis. Thomas, le fondateur, fixe la stratégie ; toi tu exécutes et tu lui rends compte.

ABYS propose un audit IA gratuit aux PME et artisans français. Après l'audit gratuit, le client peut acheter un Rapport Premium (99 euros, offre de lancement, au lieu de 249), une Mission de lancement (79 euros, un outil installé et actif), un Forfait Lancement (199 euros, 3 outils), un Assistant IA (29 euros par mois) ou la Visibilité IA (49 euros par mois, être recommandé par ChatGPT et les autres IA).

Photo de l'activite sur les 45 derniers jours : {$panorama['audits_45j']} audits realises, dont {$panorama['avec_email']} avec une adresse email exploitable et {$panorama['sans_email']} sans aucune adresse. {$panorama['payes_45j']} rapport(s) payant(s) sur la periode. Tiens-en compte dans ton bilan et ta recommandation a Thomas.

Voici les dossiers qui attendent une décision de ta part. Ce sont des gens qui ont fait l'audit gratuit et qui n'ont rien acheté.

{$fiches_json}

Pour CHAQUE dossier, choisis UNE action :
- "relancer" : tu écris un email personnalisé. Réserve ça aux dossiers où tu as vraiment quelque chose d'utile à dire.
- "attendre" : c'est trop tôt ou le moment est mal choisi. Explique pourquoi.
- "abandonner" : dossier sans suite (email douteux, doublon, 3 relances sans réaction, hors cible). Tu ne le rouvriras plus. Regle ferme : si le dossier a moins de 2 relances envoyees et un email valide, ne choisis pas "abandonner", choisis "attendre".

RÈGLE ABSOLUE : tu ne renvoies JAMAIS un dossier vers Thomas. Aucune situation ne remonte a un humain. Tu tranches, tu agis, tu assumes. Si tu hesites, tu choisis "attendre" et tu reprends le dossier au cycle suivant.

RÈGLES DE JUGEMENT :
- Tu ne peux envoyer que {$places} emails au maximum dans ce cycle. Choisis les dossiers qui le méritent le plus, mets les autres en "attendre".
- Rythme raisonnable : une relance vers J+1 ou J+2, une deuxième vers J+5 ou J+7, une troisième vers J+14 au plus. Jamais deux relances le même jour, jamais plus de 3 au total.
- Un dossier de moins de 1 jour : laisse respirer, sauf raison précise.
- Si la personne t'a déjà écrit, elle est chaude : traite-la en priorité et sois plus direct.

RÈGLES D'ÉCRITURE DES EMAILS (impératif) :
- OBLIGATOIRE : commence TOUJOURS par une salutation sur sa propre ligne. "Bonjour Prenom," si le prenom est connu, sinon "Bonjour," seul. Un email qui commence directement par une phrase est un email rate, sans exception.
- Deuxieme ligne obligatoire : rappelle le lien concret entre vous, en une phrase. Son audit, sa date, son site. C'est ce qui rend l'email legitime, pas ta presentation.
- Écris comme un humain sobre. AUCUN markdown : pas d'astérisques, pas de titres, pas de listes à puces.
- INTERDIT : le tiret long et le tiret demi-long. Utilise une virgule ou deux points.
- Court : 3 paragraphes maximum. Un seul point d'exclamation dans tout l'email.
- PERSONNALISÉ : parle de SON métier, de SES tâches, de SES chiffres d'audit. Jamais un email type interchangeable.
- Apporte quelque chose à chaque relance : un angle nouveau, un conseil utile, jamais « je reviens vers vous ».
- Si le champ prenom est renseigne, tu peux l'utiliser une fois en ouverture. S'il est vide, tu n'inventes JAMAIS de nom : tu ouvres directement sur son activite.
- N'ANNONCE JAMAIS que tu es une intelligence artificielle dans une relance. Personne n'aime etre relance par un robot. Tu ecris comme le ferait la personne qui a produit l'audit : sobrement, a la premiere personne, sans te presenter comme une machine. Si le destinataire pose la question, tu reponds honnetement, mais tu ne l'amenes jamais toi-meme.
- Tu n'as ni agenda ni téléphone : ne propose JAMAIS de créneau, de rendez-vous ou d'appel.
- JAMAIS de promesse d'argent, de remise, de remboursement ou d'engagement contractuel.
- Termine par "Milo" seul sur la dernière ligne.
- L'objet doit donner envie d'ouvrir sans être racoleur, et parler de son activité.

Réponds UNIQUEMENT avec ce JSON, sans texte autour :
{
  "bilan": "2 phrases pour Thomas : ce que tu constates sur l'ensemble des dossiers aujourd'hui.",
  "recommandation": "1 phrase : la chose la plus utile que Thomas devrait faire, selon toi. Sois franc, meme si c'est inconfortable.",
  "decisions": [
    {
      "lead_id": 0,
      "action": "relancer|attendre|abandonner",
      "raison": "Ta raison en une phrase, pour Thomas",
      "objet": "Objet de l'email, uniquement si action = relancer",
      "message": "Le corps de l'email, uniquement si action = relancer"
    }
  ]
}
PROMPT;

    // Sonnet : c'est du jugement, pas de la paraphrase. Volume faible, coût négligeable.
    $body = json_encode([
        'model'      => 'claude-sonnet-4-5',
        'max_tokens' => 6000,
        'messages'   => [['role' => 'user', 'content' => $prompt]],
    ]);
    milo_trace($db, ['stage' => 'appel_ia', 'prompt_len' => strlen($prompt), 'ts' => date('c')]);
    $raw = http_post_ai('https://api.anthropic.com/v1/messages', $body, [
        'x-api-key: ' . $api_key,
        'anthropic-version: 2023-06-01',
        'content-type: application/json',
    ], 180);

    milo_trace($db, ['stage' => 'reponse_ia', 'len' => strlen($raw), 'extrait' => mb_substr($raw, 0, 400), 'ts' => date('c')]);
    $data = json_decode($raw, true);
    if (isset($data['type']) && $data['type'] === 'error') {
        throw new Exception('API : ' . ($data['error']['message'] ?? 'inconnue'));
    }
    $text  = $data['content'][0]['text'] ?? '';
    $text  = trim(preg_replace('/^```json\s*|^```\s*$/m', '', $text));
    $start = strpos($text, '{');
    $end   = strrpos($text, '}');
    if ($start === false || $end === false) throw new Exception('Reponse de Milo illisible');
    $plan = json_decode(substr($text, $start, $end - $start + 1), true);
    if (!is_array($plan) || !isset($plan['decisions'])) throw new Exception('Plan de Milo invalide');

    // ════════════════════════════════════════════════════════════════
    // 3. EXÉCUTION
    // ════════════════════════════════════════════════════════════════
    $par_id = [];
    foreach ($dossiers as $d) $par_id[(int) $d['id']] = $d;

    $journal = [];
    foreach ($plan['decisions'] as $dec) {
        $lid    = (int) ($dec['lead_id'] ?? 0);
        $action = $dec['action'] ?? '';
        $raison = trim($dec['raison'] ?? '');
        if (!isset($par_id[$lid]) || !in_array($action, ['relancer','attendre','abandonner'], true)) continue;

        $lead = $par_id[$lid];
        $log['analyses']++;

        // « attendre » ne laisse pas de trace : le dossier revient au prochain cycle
        if ($action === 'attendre') {
            $log['attentes']++;
            $journal[] = ['lead' => $lead['url'] ?: $lead['email'], 'action' => 'attendre', 'raison' => $raison, 'objet' => '', 'envoye' => false];
            continue;
        }

        $objet   = trim($dec['objet'] ?? '');
        $message = milo_sanitize_agent(trim($dec['message'] ?? ''));
        $message = milo_saluer($message, $lead['prenom'] ?? '');
        $sent    = 0;

        // Un dossier ne se ferme jamais sur une impression : sans 2 relances sans reponse
        // et avec un email valide, la decision remonte a Thomas au lieu d'etre definitive.
        if ($action === 'abandonner'
            && (int) $lead['relances_faites'] < 2
            && filter_var($lead['email'], FILTER_VALIDATE_EMAIL)) {
            $log['attentes']++;
            $journal[] = ['lead' => $lead['url'] ?: $lead['email'], 'action' => 'attendre',
                          'raison' => 'Je voulais clore : ' . $raison . '. Sans 2 relances sans reponse, je garde le dossier ouvert.',
                          'objet' => '', 'message' => '', 'envoye' => false];
            continue;
        }

        // Jamais deux relances rapprochees, quoi qu'en dise le plan
        if ($action === 'relancer' && $lead['derniere_relance']
            && (time() - strtotime($lead['derniere_relance'])) < 48 * 3600) {
            $log['attentes']++;
            $journal[] = ['lead' => $lead['url'] ?: $lead['email'], 'action' => 'attendre',
                          'raison' => 'Relance trop rapprochee (moins de 48 h), je laisse respirer.',
                          'objet' => '', 'message' => '', 'envoye' => false];
            continue;
        }

        if ($action === 'relancer') {
            if ($envoyes_auj >= $cap)                      { $log['attentes']++; continue; }
            if ($objet === '' || mb_strlen($message) < 40)  { $log['erreurs']++;  continue; }

            if (!$dry) {
                $sent = send_email_perso($lead['email'], $objet, milo_agent_html($message)) ? 1 : 0;
                if ($sent) $envoyes_auj++;
            }
            $log['relances']++;
        } else {
            $log['abandons']++;
        }

        if (!$dry) {
            $db->prepare("INSERT INTO milo_actions (lead_id, action, reason, subject, body, sent) VALUES (?,?,?,?,?,?)")
               ->execute([$lid, $action, $raison, $objet ?: null, $message ?: null, $sent]);
        }

        $journal[] = [
            'lead'   => $lead['url'] ?: $lead['email'],
            'action' => $action,
            'raison' => $raison,
            'objet'  => $objet,
            'message' => $message,
            'envoye' => (bool) $sent,
        ];
    }

    // ════════════════════════════════════════════════════════════════
    // 4. MILO REND COMPTE À THOMAS
    // ════════════════════════════════════════════════════════════════
    if ($journal && !$dry) {
        $lignes = '';
        foreach ($journal as $j) {
            $badge = match($j['action']) {
                'relancer'   => $j['envoye'] ? 'Relance envoyee' : 'Relance echouee',
                'abandonner' => 'Dossier clos',
                default      => 'En attente',
            };
            $couleur = match($j['action']) {
                'relancer'   => '#059669',
                'abandonner' => '#9CA3AF',
                default      => '#6B7280',
            };
            $lignes .= '<tr>'
                . '<td style="padding:9px 10px;border-bottom:1px solid #E5E7EB;font-size:13px;color:#111827"><strong>'
                . htmlspecialchars($j['lead']) . '</strong></td>'
                . '<td style="padding:9px 10px;border-bottom:1px solid #E5E7EB;font-size:12px;font-weight:700;color:'
                . $couleur . '">' . $badge . '</td>'
                . '<td style="padding:9px 10px;border-bottom:1px solid #E5E7EB;font-size:12.5px;color:#4B5563">'
                . htmlspecialchars($j['raison']) . '</td></tr>';
        }

        $brief = '<div style="text-align:center;margin:0 0 16px">'
            . '<img src="https://abys.ai/assets/img/milo-avatar.jpg" alt="Milo" width="64" height="64" '
            . 'style="width:64px;height:64px;border-radius:50%;border:2px solid #10B981;object-fit:cover"></div>'
            . '<h2>Ce que j\'ai fait</h2>'
            . '<p>' . htmlspecialchars($plan['bilan'] ?? '') . '</p>'
            . '<div class="info-box"><strong>Ma recommandation :</strong><br>'
            . htmlspecialchars($plan['recommandation'] ?? '') . '</div>'
            . '<table style="width:100%;border-collapse:collapse;margin-top:8px">' . $lignes . '</table>'
            . '<p style="font-size:13px;color:#6B7280;margin-top:16px">'
            . $log['relances'] . ' relance(s), ' . $log['abandons'] . ' dossier(s) clos, '
            . $log['attentes'] . ' en attente.<br>Milo</p>';

        notify_admin('Milo · ' . $log['relances'] . ' relance(s), ' . $log['abandons'] . ' dossier(s) clos', $brief);
    }

    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lock) . ")");
    $sortie = array_merge($log, [
        'dry'            => $dry,
        'dossiers_ouverts' => count($dossiers),
        'bilan'          => $plan['bilan'] ?? '',
        'recommandation' => $plan['recommandation'] ?? '',
        'journal'        => $journal,
        'ts'             => date('c'),
    ]);
    milo_trace($db, $sortie);
    echo json_encode($sortie, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $db->query("SELECT RELEASE_LOCK(" . $db->quote($lock) . ")");
    error_log('[ABYS milo-agent] ' . $e->getMessage());
    milo_trace($db, ['error' => $e->getMessage(), 'dry' => $dry, 'ts' => date('c')]);
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// ── Nettoyage : mêmes règles de style que le canal email ────────────────────
function milo_sanitize_agent(string $t): string {
    $t = preg_replace('/\*{1,3}([^*]+)\*{1,3}/u', '$1', $t);
    $t = preg_replace('/^#{1,6}\s*/mu', '', $t);
    $t = preg_replace('/^\s*[-•]\s+/mu', '', $t);
    $t = str_replace([' — ', ' – ', '—', '–'], [' : ', ' : ', ', ', ', '], $t);
    $t = preg_replace('/[^.!?\n]*(cr[ée]neau|rendez-?vous|\brdv\b|t[ée]l[ée]phon|visio)[^.!?\n]*[.!?]?/iu', '', $t);
    // Aucune auto-presentation en machine : « je suis Milo, l'IA d'ABYS » degage.
    // Milo reste honnete si on lui pose la question, mais il ne l'amene jamais lui-meme.
    $t = preg_replace('/[^.!?\n]*\b(je suis|c\x27est moi|moi c\x27est)\b[^.!?\n]{0,60}\b(l\x27\s*IA|une\s+IA|intelligence artificielle|assistant virtuel|robot|agent (?:IA|conversationnel))\b[^.!?\n]*[.!?]?/iu', '', $t);
    $t = preg_replace('/!{2,}/', '!', $t);
    $t = preg_replace("/[ \t]+\n/", "\n", $t);
    $t = preg_replace('/^[ \t]+/mu', '', $t);
    return trim(preg_replace("/\n{3,}/", "\n\n", $t));
}

function milo_agent_html(string $text): string {
    $body = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px"><tr>'
        . '<td style="vertical-align:middle;padding-right:12px">'
        . '<img src="https://abys.ai/assets/img/milo-avatar.jpg" alt="Milo" width="48" height="48" '
        . 'style="display:block;width:48px;height:48px;border-radius:50%;border:2px solid #10B981"></td>'
        . '<td style="vertical-align:middle;font-size:13px;color:#6B7280;line-height:1.4">'
        . '<strong style="color:#111827">Milo</strong>, copilote IA d\'ABYS<br>abys.ai</td>'
        . '</tr></table><p style="margin:0">' . $body . '</p>';
}
