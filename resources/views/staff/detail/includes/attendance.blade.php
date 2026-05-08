@php
  $attInit = $data['attendance_summary_initial'] ?? null;
@endphp

<style>
/* ========== Staff Regular Attendance Card (scoped) ========== */
.att-ra{--border:#e2e8f0;--muted:#475569;--chip:#f8fafc;--brand:#346da5}
.att-ra .card{border:1px solid var(--border);border-radius:12px;background:#fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.06);margin-bottom:16px}
.att-ra .head{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-bottom:1px solid #eef2f7}
.att-ra .title{font-weight:800;color:#0f172a;display:flex;gap:8px;align-items:center}
.att-ra .controls{display:flex;gap:8px;flex-wrap:wrap}
.att-ra .select,.att-ra .input,.att-ra .btn{border:1px solid var(--border);border-radius:10px;padding:6px 10px;font-size:14px;background:#fff}
.att-ra .btn.primary{background:var(--brand);color:#fff;border:none}
.att-ra .body{padding:12px 14px}
.att-ra .range{margin-bottom:8px;color:var(--muted);font-weight:600}
.att-ra .kpis{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px}
.att-ra .kpi{background:var(--chip);border:1px solid var(--border);border-radius:12px;padding:8px 10px;font-weight:700}
.att-ra .legend-wrap{display:flex;align-items:center;gap:10px;margin:4px 0 10px}
.att-ra .legend-title{font-weight:700;color:#0f172a}
.att-ra .legend{flex:1}
.att-ra .badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.86rem;margin:0 6px 6px;background:#fff}
.att-ra .table-wrap{overflow:auto;border:1px solid var(--border);border-radius:12px;position:relative}
.att-ra table.min{border-collapse:separate;border-spacing:0;width:100%}
.att-ra table.min th, .att-ra table.min td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:8px 10px;white-space:nowrap;background:#fff;font-size:1rem}
.att-ra table.min th{position:sticky;top:0;background:#f8fafc;font-weight:800}
.att-ra .tcenter{text-align:center}
.att-ra .loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.7);align-items:center;justify-content:center;z-index:10}
.att-ra .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--brand);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
@keyframes spin{100%{transform:rotate(360deg)}}

/* Print: only this card */
@media print{
  body.print-staff-att-only .hide-on-print{display:none!important}
  .att-ra .head,.att-ra .controls{display:none!important}
  .att-ra table.min th, .att-ra table.min td{font-size:10px;padding:3px 5px}
}
</style>

<div class="att-ra">
  <div class="card" id="staff-att-card">
    <div class="head">
      <div class="title"><i class="fa fa-user-check"></i> Staff Regular Attendance</div>
      <div class="controls">
        <select id="stf-period" class="select">
          <option value="monthly" selected>Monthly</option>
          <option value="yearly">Yearly</option>
          <option value="custom">Custom</option>
          <option value="lifetime">Lifetime</option>
        </select>

        <div id="stf-month-wrap" style="display:flex;gap:8px;align-items:center">
          @php $yy=(int)date('Y'); @endphp
          <select id="stf-year" class="select">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}">{{ $yy-$i }}</option>
            @endfor
          </select>
          <select id="stf-month" class="select">
            @for($m=1;$m<=12;$m++)
              <option value="{{ $m }}" {{ $m==(int)date('n')?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,10)) }}</option>
            @endfor
          </select>
        </div>

        <div id="stf-year-wrap" style="display:none">
          <select id="stf-year-only" class="select">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}">{{ $yy-$i }}</option>
            @endfor
          </select>
        </div>

        <div id="stf-custom-wrap" style="display:none;gap:8px;align-items:center">
          <input id="stf-from" type="date" class="input">
          <span>to</span>
          <input id="stf-to" type="date" class="input">
        </div>

        <button id="stf-load" class="btn primary"><i class="fa fa-play"></i> Load</button>
        <button id="stf-print" class="btn"><i class="fa fa-print"></i> Print</button>
      </div>
    </div>

    <div class="body">
      <div id="stf-range" class="range"></div>

      <div class="kpis" id="stf-kpis"></div>

      <div class="legend-wrap">
        <div class="legend-title"><i class="fa fa-shapes"></i> Legend</div>
        <div class="legend" id="stf-legend"></div>
      </div>

      <div class="table-wrap">
        <div class="loading" id="stf-loading"><div class="spinner"></div></div>
        <table class="min" id="stf-table">
          <thead>
            <tr>
              <th>Date</th>
              <th class="tcenter">Status</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody id="stf-body"></tbody>
          <tfoot id="stf-tfoot-legend"><!-- filled by JS for print --></tfoot>
        </table>
      </div>
      <div class="hide-on-print" style="margin-top:6px;color:#64748b;font-size:.9rem">
        Tip: use <b>Lifetime</b> to start from first recorded attendance, not 2000-01-01.
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const ENDPOINT = @json(route('staff.attendance.summary', $data['staff']->id));
  const FALLBACK_LEGEND = [
    {code:'P',label:'Present',color:'#10B981'},
    {code:'A',label:'Absent', color:'#EF4444'},
    {code:'L',label:'Late',   color:'#F59E0B'},
    {code:'EL',label:'Excused',color:'#3B82F6'},
    {code:'LV',label:'Leave', color:'#7C3AED'},
    {code:'H',label:'Holiday',color:'#0EA5E9'},
  ];

  function syncWraps(){
    const p = document.getElementById('stf-period').value;
    document.getElementById('stf-month-wrap').style.display  = (p==='monthly') ? 'flex':'none';
    document.getElementById('stf-year-wrap').style.display   = (p==='yearly')  ? 'block':'none';
    document.getElementById('stf-custom-wrap').style.display = (p==='custom')  ? 'flex':'none';
  }
  document.getElementById('stf-period').addEventListener('change', syncWraps);
  syncWraps();

  const loading = (on)=> document.getElementById('stf-loading').style.display = on ? 'flex' : 'none';
  const badge = (s)=> `<span class="badge" style="border-color:${s.color};color:${s.color}">${s.code} = ${s.label}</span>`;

  function renderLegend(legend){
    const list = (legend && legend.length) ? legend : FALLBACK_LEGEND;
    document.getElementById('stf-legend').innerHTML = list.map(badge).join(' ');
    const tf = document.getElementById('stf-tfoot-legend');
    tf.innerHTML = `<tr><td colspan="5" style="text-align:center;padding-top:6px;border-top:1px dashed #cbd5e1">${list.map(badge).join(' ')}</td></tr>`;
  }

  function fmtHM(mins){
    const m = Math.max(0, Math.round(+mins||0));
    const h = Math.floor(m/60), r = m%60;
    return `${h}h ${String(r).padStart(2,'0')}m`;
  }
  function parseDT(s){
    if(!s) return null;
    if(/^\d{4}-\d{2}-\d{2} /.test(s)) return new Date(s.replace(' ','T'));
    if(/^\d{1,2}:\d{2}(:\d{2})?(\s?[APap][Mm])?$/.test(s)) return new Date('1970-01-01T'+s.replace(' ',''));
    const d = new Date(s);
    return isNaN(d) ? null : d;
  }
  function totalFromRow(r){
    if(r.total && String(r.total).trim()) return r.total;
    if(r.total_hm && String(r.total_hm).trim()) return r.total_hm;
    if(r.mins!=null) return fmtHM(r.mins);
    if(r.total_mins!=null) return fmtHM(r.total_mins);
    const tin = parseDT(r.in_raw||r.in), tout = parseDT(r.out_raw||r.out);
    if(tin && tout) return fmtHM((tout - tin)/60000);
    return '0h 00m';
  }

  function render(resp){
    document.getElementById('stf-range').textContent =
      (resp.range?.start && resp.range?.end) ? `${resp.range.start} to ${resp.range.end}` : '';

    const t = resp.totals || {};
    const pct = (resp.percent ?? 0);
    const totalHM = resp.total_hm || '0h 00m';
    document.getElementById('stf-kpis').innerHTML = `
      <div class="kpi">Present: <b>${t.P||0}</b></div>
      <div class="kpi">Absent: <b>${t.A||0}</b></div>
      <div class="kpi">Late: <b>${t.L||0}</b></div>
      <div class="kpi">Leave: <b>${t.LV||0}</b></div>
      <div class="kpi">Excused: <b>${t.EL||0}</b></div>
      <div class="kpi">Holiday: <b>${t.H||0}</b></div>
      <div class="kpi">Attendance %: <b>${pct}%</b></div>
      <div class="kpi">Total Hours: <b>${totalHM}</b></div>
    `;

    renderLegend(resp.legend);

    const colors = resp.colors || {};
    const body = document.getElementById('stf-body');
    body.innerHTML = '';
    const rows = resp.rows || [];
    if (!rows.length){
      body.innerHTML = `<tr><td colspan="5" class="tcenter" style="padding:8px;color:#64748b">No attendance in this period.</td></tr>`;
      return;
    }
    rows.forEach(r=>{
      const col = colors[r.status]||'';
      const style = col ? ` style="color:${col};font-weight:700"`:'';
      const total = totalFromRow(r);
      body.insertAdjacentHTML('beforeend',
        `<tr>
          <td>${r.date||''}</td>
          <td class="tcenter"><span${style}>${r.status||''}</span></td>
          <td>${r.in||''}</td>
          <td>${r.out||''}</td>
          <td>${total}</td>
        </tr>`);
    });
  }

  function gather(){
    const p = document.getElementById('stf-period').value;
    const q = { period:p };
    if(p==='monthly'){ q.year=document.getElementById('stf-year').value; q.month=document.getElementById('stf-month').value; }
    else if(p==='yearly'){ q.year=document.getElementById('stf-year-only').value; }
    else if(p==='custom'){ q.date_from=document.getElementById('stf-from').value; q.date_to=document.getElementById('stf-to').value; }
    return q;
  }

  function load(){
    loading(true);
    const params = gather();
    const u = new URL(ENDPOINT, window.location.origin);
    Object.keys(params).forEach(k=>u.searchParams.set(k, params[k]));
    fetch(u, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      cache: 'no-store'
    })
    .then(async resp => {
      if (!resp.ok) {
        const text = await resp.text();
        console.error('Staff attendance fetch failed', resp.status, text);
        throw new Error('HTTP ' + resp.status);
      }
      return resp.json();
    })
    .then(resp=> render(resp))
    .catch(()=> alert('Failed to load staff attendance.'))
    .finally(()=> loading(false));
  }

  // print this card only
  document.getElementById('stf-print').addEventListener('click', ()=>{
    document.body.classList.add('print-staff-att-only');
    window.print();
    setTimeout(()=>document.body.classList.remove('print-staff-att-only'), 200);
  });

  document.getElementById('stf-load').addEventListener('click', load);

  @if($attInit && !empty($attInit['ok']))
    render(@json($attInit));
  @else
    // Auto-load so "Total Hours" is visible without clicking
    load();
  @endif
})();
</script>
