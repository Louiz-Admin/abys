// Fichier: abys-ai/assets/js/simulation.js

const Simulation = {

  render(opportunities, containerId) {
    const container = document.getElementById(containerId);
    if (!container || !opportunities) return;

    let totalTime = 0, totalMoney = 0;

    opportunities.forEach((opp, i) => {
      const div = document.createElement('div');
      div.className = 'card';
      div.style.cssText = 'margin-bottom:16px;padding:24px';
      div.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px">
          <div>
            <div style="font-size:11px;font-weight:600;color:var(--ink-4);text-transform:uppercase;letter-spacing:0.1em">${opp.category}</div>
            <div style="font-size:16px;font-weight:600;color:var(--ink-2);margin-top:2px">${opp.tool}</div>
          </div>
          <div style="text-align:right">
            <div style="font-size:22px;font-weight:700;color:var(--green-deep)" id="money-${i}">${opp.money_saved_eur_month || 0}€/mois</div>
            <div style="font-size:13px;color:var(--ink-4)" id="time-${i}">${opp.time_saved_h_week || 0}h/sem</div>
          </div>
        </div>
        <div style="margin-bottom:12px">
          <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-4);margin-bottom:6px">
            <span>Heures consacrées actuellement / semaine</span>
            <span id="slider-val-${i}">${opp.time_saved_h_week || 2}h</span>
          </div>
          <input type="range" min="0" max="20" value="${opp.time_saved_h_week || 2}" step="0.5"
            style="width:100%;accent-color:var(--green)"
            data-idx="${i}" data-base-money="${opp.money_saved_eur_month || 0}"
            oninput="Simulation.updateSlider(this)"/>
        </div>
        <div style="display:flex;gap:12px">
          <div style="flex:1;padding:10px;background:rgba(16,185,129,0.06);border-radius:var(--r-md);text-align:center">
            <div style="font-size:11px;color:var(--ink-4);margin-bottom:2px">Gain 3 mois</div>
            <div style="font-weight:600;color:var(--green-deep)" id="roi3-${i}">${((opp.money_saved_eur_month || 0) * 3).toLocaleString('fr-FR')}€</div>
          </div>
          <div style="flex:1;padding:10px;background:rgba(14,165,233,0.06);border-radius:var(--r-md);text-align:center">
            <div style="font-size:11px;color:var(--ink-4);margin-bottom:2px">Gain 12 mois</div>
            <div style="font-weight:600;color:#0369A1" id="roi12-${i}">${((opp.money_saved_eur_month || 0) * 12).toLocaleString('fr-FR')}€</div>
          </div>
        </div>
      `;
      container.appendChild(div);
      totalTime  += opp.time_saved_h_week || 0;
      totalMoney += opp.money_saved_eur_month || 0;
    });

    Simulation.updateTotals(totalTime, totalMoney);
  },

  updateSlider(input) {
    const i   = input.dataset.idx;
    const val = parseFloat(input.value);
    const baseMoney = parseFloat(input.dataset.baseMoney);
    const ratio = val / (parseFloat(input.max) / 2);
    const money = Math.round(baseMoney * Math.min(ratio, 2));

    document.getElementById('slider-val-' + i).textContent = val + 'h';
    document.getElementById('time-' + i).textContent = val + 'h/sem';
    document.getElementById('money-' + i).textContent = money + '€/mois';
    document.getElementById('roi3-' + i).textContent = (money * 3).toLocaleString('fr-FR') + '€';
    document.getElementById('roi12-' + i).textContent = (money * 12).toLocaleString('fr-FR') + '€';

    let totalTime = 0, totalMoney = 0;
    document.querySelectorAll('input[type=range]').forEach(inp => {
      const v = parseFloat(inp.value);
      const bm = parseFloat(inp.dataset.baseMoney);
      totalTime  += v;
      totalMoney += Math.round(bm * Math.min(v / (parseFloat(inp.max) / 2), 2));
    });
    Simulation.updateTotals(totalTime, totalMoney);
  },

  updateTotals(time, money) {
    const tTime  = document.getElementById('total-time-sim');
    const tMoney = document.getElementById('total-money-sim');
    const tRoi   = document.getElementById('total-roi-sim');
    if (tTime)  tTime.textContent  = time.toFixed(1) + 'h/semaine';
    if (tMoney) tMoney.textContent = ABYS.euros(money) + '/mois';
    if (tRoi)   tRoi.textContent   = ABYS.euros(money * 12) + '/an';
  },
};
