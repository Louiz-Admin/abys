// Fichier: abys-ai/assets/js/audit.js

const Audit = {

  async run(url) {
    const cleanUrl = ABYS.cleanUrl(url);

    const lead = await ABYS.api('leads.php', { action: 'create', url: cleanUrl, source: 'url' });
    ABYS.store('lead_id', lead.lead_id);
    ABYS.store('audit_url', cleanUrl);

    let scrapeData = null;
    try {
      const scrape = await ABYS.api('scrape.php', { url: cleanUrl });
      if (scrape.success) scrapeData = scrape;
    } catch (e) { /* Fallback questionnaire */ }

    if (!scrapeData) {
      window.location.href = '/audit-questionnaire.php';
      return;
    }

    const analysis = await ABYS.api('analyze.php', {
      domain: cleanUrl,
      scrape_data: scrapeData,
      lead_id: lead.lead_id,
    });

    if (analysis.audit) {
      ABYS.store('audit_result', analysis.audit);
      ABYS.store('audit_id', analysis.audit_id || 0);
      window.location.href = '/audit-resultats.php';
    } else {
      throw new Error(analysis.error || 'Analyse échouée');
    }
  },

  async runFromQuestionnaire(answers) {
    const leadId = ABYS.get('lead_id');
    const analysis = await ABYS.api('analyze.php', {
      domain: '',
      answers,
      lead_id: leadId || 0,
    });
    if (analysis.audit) {
      ABYS.store('audit_result', analysis.audit);
      ABYS.store('audit_id', analysis.audit_id || 0);
      window.location.href = '/audit-resultats.php';
    } else {
      throw new Error(analysis.error || 'Analyse échouée');
    }
  },

  drawGauge(canvasId, score) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height;
    const cx = W / 2, cy = H / 2 + 20;
    const r = Math.min(W, H) * 0.38;
    const start = Math.PI * 0.75, end = Math.PI * 2.25;
    const total = end - start;

    let current = 0;
    const target = (score / 100) * total;
    const step = () => {
      current = Math.min(current + target / 60, target);
      ctx.clearRect(0, 0, W, H);

      ctx.beginPath();
      ctx.arc(cx, cy, r, start, end);
      ctx.strokeStyle = 'rgba(14,165,233,0.1)';
      ctx.lineWidth = 14; ctx.lineCap = 'round'; ctx.stroke();

      const grad = ctx.createLinearGradient(0, 0, W, 0);
      grad.addColorStop(0, '#10B981');
      grad.addColorStop(1, '#0EA5E9');
      ctx.beginPath();
      ctx.arc(cx, cy, r, start, start + current);
      ctx.strokeStyle = grad;
      ctx.lineWidth = 14; ctx.lineCap = 'round'; ctx.stroke();

      const displayed = Math.round((current / total) * score);
      ctx.fillStyle = '#0A1F1A';
      ctx.font = `700 ${r * 0.7}px Rubik`;
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(displayed, cx, cy - 8);
      ctx.font = `400 ${r * 0.22}px Rubik`;
      ctx.fillStyle = '#6B9E8A';
      ctx.fillText('score IA', cx, cy + r * 0.38);

      if (current < target) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  },
};
