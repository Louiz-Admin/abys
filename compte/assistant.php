<?php
session_start();
if (empty($_SESSION['client_id'])) {
    header('Location: /compte/?redirect=assistant'); exit;
}
require_once __DIR__ . '/../api/db.php';
$db = get_db();

$client_id = (int)$_SESSION['client_id'];
$client = $db->prepare("SELECT ca.*, l.url, l.secteur, a.score, a.recommendations
    FROM client_accounts ca
    LEFT JOIN leads l ON l.id = ca.lead_id
    LEFT JOIN audits a ON a.lead_id = ca.lead_id
    WHERE ca.id = ?
    ORDER BY a.created_at DESC LIMIT 1");
$client->execute([$client_id]);
$client = $client->fetch();

$audit_url    = $client['url']    ?? '';
$audit_sector = $client['secteur'] ?? '';
$audit_score  = $client['score']   ?? 0;

// Top 3 opportunités pour les suggestions
$opps = [];
if ($client['recommendations']) {
    $rec  = json_decode($client['recommendations'], true);
    $opps = array_slice($rec['opportunities'] ?? [], 0, 3);
}

// Historique des messages
$history = $db->prepare("SELECT role, content, created_at FROM chat_messages WHERE client_id=? ORDER BY created_at ASC LIMIT 100");
$history->execute([$client_id]);
$history = $history->fetchAll();

$page_title = 'Assistant IA · ABYS AI';
include __DIR__ . '/../includes/head.php';
?>
<style>
*{box-sizing:border-box}
body{margin:0;height:100vh;display:flex;flex-direction:column;overflow:hidden}
/* Nav + corps */
.chat-layout{display:flex;flex:1;overflow:hidden;margin-top:0}

/* Sidebar */
.sidebar{width:260px;background:var(--ink-2);display:flex;flex-direction:column;flex-shrink:0;overflow-y:auto}
.sidebar-header{padding:20px 16px 12px;border-bottom:1px solid rgba(255,255,255,.08)}
.sidebar-logo{font-size:13px;font-weight:200;letter-spacing:.16em;text-transform:uppercase;color:#fff;display:flex;align-items:center;gap:8px;margin-bottom:16px}
.sidebar-logo sup{font-size:8px;opacity:.5}
.company-card{background:rgba(255,255,255,.06);border-radius:10px;padding:12px;margin-bottom:4px}
.company-domain{font-size:13px;font-weight:600;color:#fff;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.company-sector{font-size:11px;color:rgba(255,255,255,.4)}
.score-pill{display:inline-flex;align-items:center;gap:4px;background:rgba(16,185,129,.15);color:#6EE7B7;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;margin-top:6px}

.sidebar-section{padding:16px 16px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3)}
.sidebar-item{display:flex;align-items:center;gap:10px;padding:8px 16px;font-size:13px;color:rgba(255,255,255,.6);cursor:pointer;transition:all 150ms;border-radius:0;text-decoration:none}
.sidebar-item:hover{background:rgba(255,255,255,.06);color:#fff}
.sidebar-item svg{flex-shrink:0}

.sidebar-opp{padding:8px 16px;display:flex;align-items:flex-start;gap:8px;cursor:pointer;transition:background 150ms}
.sidebar-opp:hover{background:rgba(255,255,255,.04)}
.opp-dot{width:6px;height:6px;border-radius:50%;background:#10B981;flex-shrink:0;margin-top:5px}
.opp-name{font-size:12px;color:rgba(255,255,255,.6);line-height:1.4}

.sidebar-bottom{margin-top:auto;padding:16px;border-top:1px solid rgba(255,255,255,.08)}
.sidebar-bottom a{display:block;font-size:12px;color:rgba(255,255,255,.35);text-decoration:none;margin-bottom:6px;transition:color 150ms}
.sidebar-bottom a:hover{color:rgba(255,255,255,.7)}

/* Zone de chat */
.chat-area{flex:1;display:flex;flex-direction:column;background:var(--bg);overflow:hidden}
.chat-header{padding:16px 24px;border-bottom:1px solid var(--border);background:var(--white);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.chat-title{font-size:15px;font-weight:600;color:var(--ink-2)}
.chat-status{font-size:12px;color:var(--green);display:flex;align-items:center;gap:5px}
.chat-status::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

.messages{flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:16px}
.msg{display:flex;gap:12px;max-width:720px}
.msg.user{align-self:flex-end;flex-direction:row-reverse}
.msg-avatar{width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700}
.msg.assistant .msg-avatar{background:#052E16;border:2px solid #10B981;overflow:hidden}
.msg-avatar img{width:100%;height:100%;object-fit:cover;display:block;border-radius:50%}
.msg.user .msg-avatar{background:var(--border-2);color:var(--ink-3)}
.msg-bubble{padding:12px 16px;border-radius:16px;font-size:14px;line-height:1.65;max-width:560px}
.msg.assistant .msg-bubble{background:var(--white);border:1px solid var(--border);color:var(--ink-2);border-bottom-left-radius:4px}
.msg.user .msg-bubble{background:var(--ink-2);color:#fff;border-bottom-right-radius:4px}
.msg-bubble p{margin:0 0 8px}
.msg-bubble p:last-child{margin:0}
.msg-bubble ul,.msg-bubble ol{margin:8px 0 8px 20px}
.msg-bubble li{margin:2px 0}
.msg-bubble strong{font-weight:600}
.msg-bubble code{background:rgba(0,0,0,.06);padding:1px 5px;border-radius:4px;font-size:13px;font-family:monospace}
.msg.user .msg-bubble code{background:rgba(255,255,255,.15)}
.msg-time{font-size:10px;color:var(--ink-4);margin-top:4px;text-align:right}
.msg.assistant .msg-time{text-align:left}

/* Suggestions initiales */
.suggestions{display:flex;flex-wrap:wrap;gap:8px;padding:0 24px 16px}
.suggestion-chip{padding:8px 14px;background:var(--white);border:1px solid var(--border);border-radius:var(--r-pill);font-size:13px;color:var(--ink-3);cursor:pointer;transition:all 150ms}
.suggestion-chip:hover{border-color:var(--green);color:var(--green-deep);background:rgba(16,185,129,.05)}

/* Input */
.chat-input-wrap{padding:16px 24px;background:var(--white);border-top:1px solid var(--border);flex-shrink:0}
.chat-input-row{display:flex;gap:10px;align-items:flex-end;max-width:720px;margin:0 auto}
.chat-textarea{flex:1;padding:12px 16px;border:1px solid var(--border-2);border-radius:16px;font-family:var(--font);font-size:14px;color:var(--ink);background:var(--bg);resize:none;outline:none;transition:border-color 150ms;max-height:120px;min-height:44px;line-height:1.5;overflow-y:auto}
.chat-textarea:focus{border-color:var(--green)}
.chat-send{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#059669,#064E3B);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:opacity 150ms}
.chat-send:hover{opacity:.9}
.chat-send:disabled{opacity:.4;cursor:not-allowed}
.chat-hint{font-size:11px;color:var(--ink-4);text-align:center;margin-top:8px}

/* Typing indicator */
.typing{display:none;align-items:center;gap:8px}
.typing.active{display:flex}
.typing-dots{display:flex;gap:3px}
.typing-dot{width:6px;height:6px;border-radius:50%;background:var(--ink-4);animation:typing 1.2s ease infinite}
.typing-dot:nth-child(2){animation-delay:.2s}
.typing-dot:nth-child(3){animation-delay:.4s}
@keyframes typing{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-6px)}}

@media(max-width:768px){
  .sidebar{display:none}
  .chat-input-row{max-width:100%}
}
</style>

<?php include __DIR__ . '/../includes/nav.php'; ?>

<div class="chat-layout">
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <svg width="20" height="20" viewBox="0 0 34 34" fill="none"><circle cx="17" cy="17" r="16.5" stroke="rgba(255,255,255,.2)" stroke-width=".8"/><circle cx="17" cy="17" r="14.8" fill="rgba(255,255,255,.08)"/><path d="M17 6.5L25.5 25" stroke="white" stroke-width="2.2" stroke-linecap="round"/><path d="M17 6.5L8.5 25" stroke="white" stroke-width="2.2" stroke-linecap="round"/></svg>
        ABYS<sup>AI</sup>
      </div>
      <?php if ($audit_url): ?>
      <div class="company-card">
        <div class="company-domain"><?= htmlspecialchars($audit_url) ?></div>
        <div class="company-sector"><?= htmlspecialchars($audit_sector) ?></div>
        <?php if ($audit_score): ?>
        <div class="score-pill">Score IA : <?= $audit_score ?>/100</div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($opps): ?>
    <div class="sidebar-section">Vos opportunités IA</div>
    <?php foreach($opps as $o): ?>
    <div class="sidebar-opp" onclick="sendSuggestion('Comment mettre en place <?= htmlspecialchars(addslashes($o['tool'])) ?> pour mon activité ?')">
      <div class="opp-dot"></div>
      <div class="opp-name"><?= htmlspecialchars($o['tool']) ?></div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="sidebar-section">Navigation</div>
    <a href="/" class="sidebar-item">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      Accueil
    </a>
    <?php
    $report = $db->prepare("SELECT token FROM reports WHERE lead_id=? AND paid_at IS NOT NULL ORDER BY paid_at DESC LIMIT 1");
    $report->execute([$client['lead_id'] ?? 0]);
    $report_row = $report->fetch();
    if ($report_row):
    ?>
    <a href="/rapport.php?token=<?= htmlspecialchars($report_row['token']) ?>" class="sidebar-item">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Mon rapport premium
    </a>
    <?php endif; ?>
    <a href="/audit-questionnaire.php" class="sidebar-item">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Simulation gains
    </a>

    <div class="sidebar-bottom">
      <a href="/compte/logout.php">Déconnexion</a>
      <a href="mailto:contact@abys.ai">contact@abys.ai</a>
    </div>
  </div>

  <!-- Zone de chat -->
  <div class="chat-area">
    <div class="chat-header">
      <div class="chat-title">Milo · votre copilote IA</div>
      <div class="chat-status">En ligne 24h/24</div>
    </div>

    <div class="messages" id="messages">
      <!-- Message de bienvenue -->
      <div class="msg assistant">
        <div class="msg-avatar"><img src="/assets/img/milo-avatar.jpg" alt="Milo"></div>
        <div>
          <div class="msg-bubble">
            <p>Bonjour <?= htmlspecialchars($client['name'] ? explode(' ', $client['name'])[0] : '') ?>, je suis Milo.</p>
            <p>Je suis une IA, et c'est votre avantage : je suis là 24h/24, je connais votre activité <?php if($audit_url): ?>(<strong><?= htmlspecialchars($audit_url) ?></strong>)<?php endif; ?> et les outils les plus adaptés à votre secteur.</p>
            <p>Posez-moi n'importe quelle question sur le déploiement de l'IA dans votre entreprise, je vous guide étape par étape.</p>
          </div>
        </div>
      </div>

      <?php foreach($history as $msg): ?>
      <div class="msg <?= $msg['role'] ?>">
        <div class="msg-avatar"><?= $msg['role'] === 'assistant' ? '<img src="/assets/img/milo-avatar.jpg" alt="Milo">' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' ?></div>
        <div>
          <div class="msg-bubble" data-raw="<?= htmlspecialchars($msg['content']) ?>"><?= nl2br(htmlspecialchars($msg['content'])) ?></div>
          <div class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Typing indicator -->
      <div class="msg assistant typing" id="typing-indicator">
        <div class="msg-avatar"><img src="/assets/img/milo-avatar.jpg" alt="Milo"></div>
        <div class="msg-bubble">
          <div class="typing-dots">
            <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Suggestions si pas d'historique -->
    <?php if (empty($history)): ?>
    <div class="suggestions" id="suggestions">
      <?php if ($opps): foreach(array_slice($opps, 0, 2) as $o): ?>
      <div class="suggestion-chip" onclick="sendSuggestion('Comment configurer <?= htmlspecialchars(addslashes($o['tool'])) ?> pour mon activité ?')">
        Configurer <?= htmlspecialchars($o['tool']) ?> →
      </div>
      <?php endforeach; endif; ?>
      <div class="suggestion-chip" onclick="sendSuggestion('Par quoi commencer pour intégrer l\'IA dans mon activité ?')">Par où commencer ?</div>
      <div class="suggestion-chip" onclick="sendSuggestion('Combien de temps faut-il pour voir des résultats concrets avec l\'IA ?')">Délai avant résultats ?</div>
      <div class="suggestion-chip" onclick="sendSuggestion('Quelles sont les erreurs à éviter quand on lance l\'IA dans une PME ?')">Erreurs à éviter</div>
    </div>
    <?php endif; ?>

    <div class="chat-input-wrap">
      <div class="chat-input-row">
        <textarea class="chat-textarea" id="user-input" placeholder="Posez votre question…" rows="1" onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
        <button class="chat-send" id="send-btn" onclick="sendMessage()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
      <div class="chat-hint">Entrée pour envoyer · Shift+Entrée pour un saut de ligne</div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script>
const messagesEl = document.getElementById('messages');
const inputEl    = document.getElementById('user-input');
const sendBtn    = document.getElementById('send-btn');
const typingEl   = document.getElementById('typing-indicator');

function scrollToBottom() {
  messagesEl.scrollTop = messagesEl.scrollHeight;
}
scrollToBottom();

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function sendSuggestion(text) {
  document.getElementById('suggestions')?.remove();
  inputEl.value = text;
  sendMessage();
}

function appendMessage(role, html) {
  const div = document.createElement('div');
  div.className = `msg ${role}`;
  const avatar = role === 'assistant' ? '<img src="/assets/img/milo-avatar.jpg" alt="Milo">' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
  div.innerHTML = `
    <div class="msg-avatar">${avatar}</div>
    <div><div class="msg-bubble">${html}</div></div>
  `;
  messagesEl.insertBefore(div, typingEl);
  scrollToBottom();
  return div.querySelector('.msg-bubble');
}

// Markdown léger : gras, code inline, listes, sauts de ligne
function renderMarkdown(text) {
  return text
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
    .replace(/`([^`]+)`/g,'<code>$1</code>')
    .replace(/^[\*\-] (.+)$/gm,'<li>$1</li>')
    .replace(/(<li>.*<\/li>)/gs,'<ul>$1</ul>')
    .replace(/\n/g,'<br>');
}

async function sendMessage() {
  const text = inputEl.value.trim();
  if (!text || sendBtn.disabled) return;

  document.getElementById('suggestions')?.remove();
  inputEl.value = '';
  inputEl.style.height = 'auto';
  sendBtn.disabled = true;

  appendMessage('user', renderMarkdown(text));
  typingEl.classList.add('active');
  scrollToBottom();

  let bubble = null;
  let buffer = '';

  try {
    const res = await fetch('/api/chat.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({message: text})
    });

    if (!res.ok) throw new Error('Erreur serveur');

    const reader = res.body.getReader();
    const decoder = new TextDecoder();

    typingEl.classList.remove('active');
    bubble = appendMessage('assistant', '');

    while (true) {
      const {done, value} = await reader.read();
      if (done) break;
      const chunk = decoder.decode(value);
      const lines = chunk.split('\n');
      for (const line of lines) {
        if (!line.startsWith('data:')) continue;
        const data = line.slice(5).trim();
        if (data === '[DONE]') continue;
        try {
          const ev = JSON.parse(data);
          if (ev.text) {
            buffer += ev.text;
            bubble.innerHTML = renderMarkdown(buffer);
            scrollToBottom();
          }
        } catch {}
      }
    }
  } catch(e) {
    typingEl.classList.remove('active');
    if (!bubble) bubble = appendMessage('assistant', '');
    bubble.innerHTML = '<span style="color:var(--ink-4)">Erreur de connexion, réessayez.</span>';
  }

  sendBtn.disabled = false;
  inputEl.focus();
}
</script>
