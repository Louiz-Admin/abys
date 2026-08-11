// Fichier: abys-ai/assets/js/sphere.js
// Sphère neuronale 3D · canvas 2D, Fibonacci, nœuds + étiquettes métier

class NeuralSphere {
  constructor(canvasId, kwLayerId, options = {}) {
    this.canvas  = document.getElementById(canvasId);
    this.kwLayer = document.getElementById(kwLayerId);
    this.ctx     = this.canvas.getContext('2d');
    this.W       = this.canvas.width;
    this.H       = this.canvas.height;
    this.CX      = this.W / 2;
    this.CY      = this.H / 2;
    this.R       = options.radius || 170;
    this.t       = 0;
    this.nodes   = [];
    this.kwData  = [];

    this.KEYWORDS = options.keywords || [
      'Comptabilité', 'Emails', 'Devis', 'Réseaux sociaux',
      'Planning', 'Facturation', 'Support client', 'RH',
      'Marketing', 'Stocks', 'Agenda', 'Référencement',
      'Relances', 'Rapports', 'Juridique',
    ];

    this._buildNodes(options.nodeCount || 90);
    this._buildKeywords();
    this._loop();
  }

  _buildNodes(N) {
    const phi = Math.PI * (3 - Math.sqrt(5));
    for (let i = 0; i < N; i++) {
      const y = 1 - (i / (N - 1)) * 2;
      const r = Math.sqrt(1 - y * y);
      this.nodes.push({
        ox: Math.cos(phi * i) * r,
        oy: y,
        oz: Math.sin(phi * i) * r,
      });
    }
  }

  _buildKeywords() {
    const N = this.nodes.length;
    this.KEYWORDS.forEach((txt, i) => {
      const el = document.createElement('div');
      el.className = 'sphere-kw';
      el.textContent = txt;
      this.kwLayer.appendChild(el);

      const ni = Math.floor(i * (N / this.KEYWORDS.length) + N / this.KEYWORDS.length / 2);
      const src = this.nodes[ni];
      const s = 1.22; // légèrement au-delà de la surface
      this.kwData.push({ el, ox: src.ox * s, oy: src.oy * s, oz: src.oz * s });
    });
  }

  _project(x, y, z, rx, ry) {
    // Rotation Y
    const cy = Math.cos(ry), sy = Math.sin(ry);
    const x1 = x * cy - z * sy;
    const z1 = x * sy + z * cy;
    // Rotation X légère
    const cx = Math.cos(rx), sx = Math.sin(rx);
    const y1 = y * cx - z1 * sx;
    const z2 = y * sx + z1 * cx;
    const fov = 700;
    const sc = fov / (fov + z2 * this.R * 0.18);
    return { sx: x1 * this.R * sc + this.CX, sy: y1 * this.R * sc + this.CY, z: z2, sc };
  }

  _loop() {
    const ctx = this.ctx;
    const W = this.W, H = this.H;

    ctx.clearRect(0, 0, W, H);

    const ry = this.t * 0.004;
    const rx = Math.sin(this.t * 0.0013) * 0.2;
    const proj = this.nodes.map(n => this._project(n.ox, n.oy, n.oz, rx, ry));

    // Connexions
    const N = this.nodes.length;
    for (let i = 0; i < N; i++) {
      for (let j = i + 1; j < N; j++) {
        const dx = this.nodes[i].ox - this.nodes[j].ox;
        const dy = this.nodes[i].oy - this.nodes[j].oy;
        const dz = this.nodes[i].oz - this.nodes[j].oz;
        const d = Math.sqrt(dx*dx + dy*dy + dz*dz);
        if (d > 0.50) continue;
        const dep = (proj[i].z + proj[j].z) * 0.5;
        const a = (1 - d / 0.50) * 0.11 * Math.max(0, dep * 0.5 + 0.65);
        ctx.strokeStyle = `rgba(14,165,233,${a})`;
        ctx.lineWidth = 0.75;
        ctx.beginPath();
        ctx.moveTo(proj[i].sx, proj[i].sy);
        ctx.lineTo(proj[j].sx, proj[j].sy);
        ctx.stroke();
      }
    }

    // Nœuds
    proj.forEach(p => {
      const vis = (p.z + 1) * 0.5;
      const r = (1.4 + vis * 2.2) * p.sc;
      const alpha = 0.07 + vis * 0.48;
      const grd = ctx.createRadialGradient(p.sx, p.sy, 0, p.sx, p.sy, r * 4);
      grd.addColorStop(0, `rgba(14,165,233,${alpha * 0.25})`);
      grd.addColorStop(1, 'rgba(14,165,233,0)');
      ctx.fillStyle = grd;
      ctx.beginPath(); ctx.arc(p.sx, p.sy, r * 4, 0, Math.PI * 2); ctx.fill();
      ctx.fillStyle = vis > 0.55
        ? `rgba(52,211,153,${alpha})`
        : `rgba(14,165,233,${alpha})`;
      ctx.beginPath(); ctx.arc(p.sx, p.sy, r, 0, Math.PI * 2); ctx.fill();
    });

    // Étiquettes projetées en 3D sur la surface
    this.kwData.forEach(kw => {
      const p = this._project(kw.ox, kw.oy, kw.oz, rx, ry);
      const w = kw.el.offsetWidth, h = kw.el.offsetHeight;
      const lW = this.kwLayer.clientWidth, lH = this.kwLayer.clientHeight;
      const offX = (lW - W) / 2, offY = (lH - H) / 2;

      kw.el.style.left   = (p.sx + offX - w / 2) + 'px';
      kw.el.style.top    = (p.sy + offY - h / 2) + 'px';

      const depth = (p.z + 1) * 0.5;
      kw.el.style.opacity   = (depth < 0.25 ? depth * 0.5 : 0.25 + depth * 0.75).toFixed(2);
      kw.el.style.zIndex    = Math.round(depth * 10);
      const sc = 0.8 + depth * 0.24;
      kw.el.style.transform = `scale(${sc.toFixed(3)})`;
    });

    this.t++;
    requestAnimationFrame(() => this._loop());
  }
}
