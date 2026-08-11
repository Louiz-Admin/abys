<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/db.php';
$db = get_db();

// Stats globales
$stats = [
    'leads'         => $db->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
    'audits'        => $db->query("SELECT COUNT(*) FROM audits")->fetchColumn(),
    'reports_paid'  => $db->query("SELECT COUNT(*) FROM reports WHERE paid_at IS NOT NULL")->fetchColumn(),
    'revenue'       => $db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='succeeded'")->fetchColumn(),
    'subscriptions' => $db->query("SELECT COUNT(*) FROM subscriptions WHERE status='active'")->fetchColumn(),
    'clients'       => $db->query("SELECT COUNT(*) FROM client_accounts")->fetchColumn(),
];

// Derniers leads
$leads = $db->query("
    SELECT l.*,
           MAX(a.score) as score,
           MAX(a.created_at) as audit_at,
           s.plan, s.status as sub_status,
           r.paid_at as report_paid,
           ca.email as account_email
    FROM leads l
    LEFT JOIN audits a ON a.lead_id = l.id
    LEFT JOIN subscriptions s ON s.lead_id = l.id
    LEFT JOIN reports r ON r.lead_id = l.id AND r.paid_at IS NOT NULL
    LEFT JOIN client_accounts ca ON ca.lead_id = l.id
    GROUP BY l.id
    ORDER BY l.created_at DESC
    LIMIT 30
")->fetchAll();

// Derniers paiements
$payments = $db->query("
    SELECT p.*, l.url, l.email
    FROM payments p
    LEFT JOIN leads l ON l.id = p.lead_id
    WHERE p.status = 'succeeded'
    ORDER BY p.created_at DESC
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ABYS Admin</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#F3F4F6;color:#111827;min-height:100vh}
.topbar{background:#0A1F1A;color:#fff;padding:0 32px;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.topbar-logo{font-size:16px;font-weight:200;letter-spacing:.18em;text-transform:uppercase}
.topbar-logo sup{font-size:8px;opacity:.5;font-weight:400}
.topbar-nav{display:flex;gap:24px;align-items:center}
.topbar-nav a{color:rgba(255,255,255,.7);text-decoration:none;font-size:13px;transition:color 150ms}
.topbar-nav a:hover,.topbar-nav a.active{color:#fff}
.topbar-nav .logout{color:rgba(255,255,255,.4);font-size:12px}
.main{max-width:1280px;margin:0 auto;padding:32px 24px}
h1{font-size:24px;font-weight:700;color:#111827;margin-bottom:8px}
.sub{font-size:14px;color:#6B7280;margin-bottom:32px}
.stats-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:16px;margin-bottom:40px}
@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
.stat-card{background:#fff;border:1px solid #E5E7EB;border-radius:12px;padding:20px;text-align:center}
.stat-val{font-size:32px;font-weight:700;color:#111827;letter-spacing:-0.04em}
.stat-val.green{color:#059669}
.stat-val.blue{color:#2563EB}
.stat-label{font-size:12px;color:#9CA3AF;margin-top:4px;text-transform:uppercase;letter-spacing:.06em}
.section{background:#fff;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;margin-bottom:32px}
.section-head{padding:16px 20px;border-bottom:1px solid #F3F4F6;display:flex;justify-content:space-between;align-items:center}
.section-head h2{font-size:15px;font-weight:600;color:#111827}
.section-head span{font-size:12px;color:#9CA3AF}
table{width:100%;border-collapse:collapse}
th{text-align:left;padding:10px 20px;font-size:11px;font-weight:600;color:#9CA3AF;text-transform:uppercase;letter-spacing:.06em;background:#F9FAFB;border-bottom:1px solid #F3F4F6}
td{padding:12px 20px;font-size:13px;color:#374151;border-bottom:1px solid #F9FAFB}
tr:last-child td{border-bottom:none}
tr:hover td{background:#F9FAFB}
.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600}
.badge.green{background:#D1FAE5;color:#065F46}
.badge.blue{background:#DBEAFE;color:#1E40AF}
.badge.orange{background:#FEF3C7;color:#92400E}
.badge.gray{background:#F3F4F6;color:#6B7280}
.score{display:inline-flex;align-items:center;gap:4px;font-weight:700}
.score.high{color:#059669}
.score.mid{color:#D97706}
.score.low{color:#DC2626}
.empty{padding:32px;text-align:center;color:#9CA3AF;font-size:14px}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-logo">ABYS<sup>AI</sup> Admin</div>
  <div class="topbar-nav">
    <a href="/admin/index.php" class="active">Dashboard</a>
    <a href="/admin/clients.php">Clients</a>
    <a href="https://abys.ai" target="_blank">Voir le site →</a>
    <a href="/admin/logout.php" class="logout">Déconnexion</a>
  </div>
</div>

<div class="main">
  <h1>Dashboard</h1>
  <p class="sub">Activité ABYS AI en temps réel</p>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-val"><?= number_format($stats['leads']) ?></div>
      <div class="stat-label">Leads</div>
    </div>
    <div class="stat-card">
      <div class="stat-val"><?= number_format($stats['audits']) ?></div>
      <div class="stat-label">Audits</div>
    </div>
    <div class="stat-card">
      <div class="stat-val blue"><?= number_format($stats['clients']) ?></div>
      <div class="stat-label">Comptes clients</div>
    </div>
    <div class="stat-card">
      <div class="stat-val"><?= number_format($stats['reports_paid']) ?></div>
      <div class="stat-label">Rapports vendus</div>
    </div>
    <div class="stat-card">
      <div class="stat-val blue"><?= number_format($stats['subscriptions']) ?></div>
      <div class="stat-label">Abonnements actifs</div>
    </div>
    <div class="stat-card">
      <div class="stat-val green"><?= number_format($stats['revenue'], 0, ',', ' ') ?>€</div>
      <div class="stat-label">Revenus totaux</div>
    </div>
  </div>

  <!-- Derniers paiements -->
  <div class="section">
    <div class="section-head">
      <h2>Derniers paiements</h2>
      <span><?= count($payments) ?> affichés</span>
    </div>
    <?php if ($payments): ?>
    <table>
      <tr>
        <th>Date</th><th>Site</th><th>Email</th><th>Type</th><th>Montant</th>
      </tr>
      <?php foreach($payments as $p): ?>
      <tr>
        <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
        <td><?= htmlspecialchars($p['url'] ?? '—') ?></td>
        <td><?= htmlspecialchars($p['email'] ?? '—') ?></td>
        <td>
          <?php if($p['type']==='report'): ?>
            <span class="badge blue">Rapport 249€</span>
          <?php elseif($p['type']==='subscription'): ?>
            <span class="badge green">Abonnement</span>
          <?php else: ?>
            <span class="badge gray"><?= htmlspecialchars($p['type']) ?></span>
          <?php endif; ?>
        </td>
        <td><strong><?= number_format($p['amount'], 0, ',', ' ') ?>€</strong></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php else: ?>
      <div class="empty">Aucun paiement encore</div>
    <?php endif; ?>
  </div>

  <!-- Derniers leads -->
  <div class="section">
    <div class="section-head">
      <h2>Derniers leads</h2>
      <span><?= count($leads) ?> affichés</span>
    </div>
    <?php if ($leads): ?>
    <table>
      <tr>
        <th>Date</th><th>Site</th><th>Email</th><th>Secteur</th><th>Score IA</th><th>Rapport</th><th>Abo.</th><th>Compte</th>
      </tr>
      <?php foreach($leads as $l):
        $score = (int)($l['score'] ?? 0);
        $scoreClass = $score >= 70 ? 'high' : ($score >= 40 ? 'mid' : 'low');
      ?>
      <tr>
        <td><?= date('d/m', strtotime($l['created_at'])) ?></td>
        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($l['url'] ?? '—') ?></td>
        <td><?= htmlspecialchars($l['email'] ?? '—') ?></td>
        <td><?= htmlspecialchars($l['secteur'] ?? '—') ?></td>
        <td>
          <?php if($score): ?>
            <span class="score <?= $scoreClass ?>"><?= $score ?>/100</span>
          <?php else: ?> — <?php endif; ?>
        </td>
        <td>
          <?php if($l['report_paid']): ?>
            <span class="badge green">Payé</span>
          <?php else: ?> — <?php endif; ?>
        </td>
        <td>
          <?php if($l['sub_status'] === 'active'): ?>
            <span class="badge blue"><?= htmlspecialchars($l['plan'] ?? '') ?></span>
          <?php else: ?> — <?php endif; ?>
        </td>
        <td>
          <?php if($l['account_email']): ?>
            <span class="badge green">✓</span>
          <?php else: ?> — <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php else: ?>
      <div class="empty">Aucun lead encore — partagez le site !</div>
    <?php endif; ?>
  </div>

</div>
</body>
</html>
