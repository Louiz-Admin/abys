// Fichier: abys-ai/assets/js/app.js
// Utilitaires globaux ABYS

const ABYS = {

  // Appel API PHP interne
  async api(endpoint, data = {}) {
    const res = await fetch(`/api/${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    if (!res.ok) throw new Error(`API error ${res.status}`);
    return res.json();
  },

  // Formater un montant en euros
  euros(n) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(n);
  },

  // Valider une URL/domaine
  isValidDomain(str) {
    const clean = str.replace(/^https?:\/\//, '').replace(/\/.*$/, '').trim();
    return /^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/.test(clean);
  },

  // Nettoyer une URL saisie
  cleanUrl(str) {
    return str.replace(/^https?:\/\//, '').replace(/\/.*$/, '').trim().toLowerCase();
  },

  // Animation counter (0 → n)
  animateCount(el, target, duration = 1200) {
    const start = performance.now();
    const step = (now) => {
      const p = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(target * eased).toLocaleString('fr-FR');
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  },

  // Stocker/récupérer depuis sessionStorage
  store(key, val) {
    try { sessionStorage.setItem('abys:' + key, JSON.stringify(val)); } catch {}
  },
  get(key) {
    try { const v = sessionStorage.getItem('abys:' + key); return v ? JSON.parse(v) : null; } catch { return null; }
  },

  // Mesure du parcours · aucune donnée personnelle, une étape comptée une fois
  trackKey() {
    let k = null;
    try { k = sessionStorage.getItem('abys:trk'); } catch {}
    if (!k) {
      k = (Date.now().toString(36) + Math.random().toString(36).slice(2, 10)).replace(/[^a-z0-9]/g, '');
      try { sessionStorage.setItem('abys:trk', k); } catch {}
    }
    return k;
  },

  track(etape, meta) {
    try {
      const body = JSON.stringify({
        etape: etape, cle: this.trackKey(),
        lead_id: this.get('lead_id') || 0,
        meta: meta || null,
      });
      if (navigator.sendBeacon) {
        navigator.sendBeacon('/api/track.php', new Blob([body], { type: 'application/json' }));
      } else {
        fetch('/api/track.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: body, keepalive: true });
      }
    } catch (e) { /* la mesure ne casse jamais la page */ }
  },

  // Afficher une notification toast
  toast(message, type = 'success') {
    const el = document.createElement('div');
    el.style.cssText = `
      position:fixed;bottom:24px;right:24px;z-index:9999;
      padding:14px 20px;border-radius:12px;font-family:'Rubik',sans-serif;
      font-size:14px;font-weight:500;color:#fff;max-width:340px;
      box-shadow:0 8px 32px rgba(0,0,0,0.12);
      background:${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : '#F59E0B'};
      animation:slideUp 300ms ease forwards;
    `;
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3500);
  },

  // Révéler les éléments .reveal au scroll
  initReveal() {
    const els = document.querySelectorAll('.reveal');
    const obs = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          e.target.style.animationDelay = (i * 80) + 'ms';
          e.target.classList.add('revealed');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    els.forEach(el => obs.observe(el));
  },
};

// Lancer au chargement
document.addEventListener('DOMContentLoaded', () => {
  ABYS.initReveal();
});
