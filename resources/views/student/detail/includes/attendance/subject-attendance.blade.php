@php
  $subInit   = $data['subject_attendance_summary_initial'] ?? null;
/** Expecting $data['subjects'] as [id => title]. If not present, the filter will hide itself. */
  $subjects  = $data['subjects'] ?? [];
@endphp

<style>
/* ========== Subject Card Styles (scoped) ========== */
.att-sa{--border:#e2e8f0;--muted:#475569;--chip:#f8fafc;--brand:#346da5}
.att-sa .card{border:1px solid var(--border);border-radius:12px;background:#fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.06);margin-bottom:16px}
.att-sa .head{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-bottom:1px solid #eef2f7}
.att-sa .title{font-weight:800;color:#0f172a;display:flex;gap:8px;align-items:center}
.att-sa .controls{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.att-sa .select,.att-sa .input,.att-sa .btn{border:1px solid var(--border);border-radius:10px;padding:6px 10px;font-size:14px;background:#fff}
.att-sa .btn.primary{background:var(--brand);color:#fff;border:none}
.att-sa .body{padding:12px 14px}
.att-sa .range{margin-bottom:8px;color:var(--muted);font-weight:600}
.att-sa .kpis{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px}
.att-sa .kpi{background:var(--chip);border:1px solid var(--border);border-radius:12px;padding:8px 10px;font-weight:700}
.att-sa .legend-wrap{display:flex;align-items:center;gap:10px;margin:4px 0 10px}
.att-sa .legend-title{font-weight:700;color:#0f172a}
.att-sa .legend{flex:1}
.att-sa .badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.86rem;margin:0 6px 6px;background:#fff}
.att-sa .table-wrap{overflow:auto;border:1px solid var(--border);border-radius:12px;position:relative}
.att-sa table.min{border-collapse:separate;border-spacing:0;width:100%}
.att-sa table.min th, .att-sa table.min td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:8px 10px;white-space:nowrap;background:#fff;font-size:1rem}
.att-sa table.min th{position:sticky;top:0;background:#f8fafc;font-weight:800}
.att-sa .tcenter{text-align:center}
.att-sa .loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.7);align-items:center;justify-content:center;z-index:10}
.att-sa .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--brand);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
.att-sa .error{display:none;margin:8px 0;padding:10px 12px;border:1px solid #fecaca;background:#fff1f2;color:#991b1b;border-radius:10px;font-weight:600}
@keyframes spin{100%{transform:rotate(360deg)}}

/* Print only this card when asked */
@media print{
  body.print-sub-only #reg-att-card{display:none!important}
  .att-sa .head,.att-sa .controls{display:none!important}
  .att-sa table.min th, .att-sa table.min td{font-size:10px;padding:3px 5px}
}
</style>

<div class="att-sa">
  <div class="card" id="sub-att-card">
    <div class="head">
      <div class="title"><i class="fa fa-book"></i> Subject-wise Attendance</div>
      <div class="controls">
        <select id="sa-period" class="select">
          <option value="monthly" selected>Monthly</option>
          <option value="yearly">Yearly</option>
          <option value="custom">Custom</option>
          <option value="lifetime">Lifetime</option>
        </select>

        <div id="sa-month-wrap" style="display:flex;gap:8px;align-items:center">
          <select id="sa-year" class="select">
            @php $yy=(int)date('Y'); @endphp
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}">{{ $yy-$i }}</option>
            @endfor
          </select>
          <select id="sa-month" class="select">
            @for($m=1;$m<=12;$m++)
              <option value="{{ $m }}" {{ $m==(int)date('n')?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,10)) }}</option>
            @endfor
          </select>
        </div>

        <div id="sa-year-wrap" style="display:none">
          <select id="sa-year-only" class="select">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}">{{ $yy-$i }}</option>
            @endfor
          </select>
        </div>

        <div id="sa-custom-wrap" style="display:none;gap:8px;align-items:center">
          <input id="sa-from" type="date" class="input">
          <span>to</span>
          <input id="sa-to" type="date" class="input">
        </div>

        {{-- Subject filter (optional) --}}
        <div id="sa-subject-wrap" style="display:{{ $subjects ? 'block':'none' }}">
          <select id="sa-subject" class="select">
            <option value="">All Subjects</option>
            @foreach($subjects as $sid => $stitle)
              <option value="{{ $sid }}">{{ $stitle }}</option>
            @endforeach
          </select>
        </div>

        <button id="sa-load" class="btn primary"><i class="fa fa-play"></i> Load</button>
        <button id="sa-print" class="btn"><i class="fa fa-print"></i> Print</button>
      </div>
    </div>

    <div class="body">
      <div id="sa-error" class="error" role="alert"></div>

      <div id="sa-range" class="range"></div>

      <div class="kpis" id="sa-kpis"></div>

      <div class="legend-wrap">
        <div class="legend-title"><i class="fa fa-shapes"></i> Legend</div>
        <div class="legend" id="sa-legend"></div>
      </div>

      <div class="table-wrap">
        <div class="loading" id="sa-loading"><div class="spinner"></div></div>
        <table class="min" id="sa-table">
          <thead>
            <tr>
              <th>Date</th>
              <th class="tcenter">Status</th>
              <th>In</th>
              <th>Out</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody id="sa-body"></tbody>
          <tfoot id="sa-tfoot-legend"><!-- filled by JS for print --></tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const ENDPOINT = @json(route('student.attendance.subject_summary', $data['student']->id));
  const FALLBACK_LEGEND = [
    {code:'P',label:'Present',color:'#10B981'},
    {code:'A',label:'Absent', color:'#EF4444'},
    {code:'L',label:'Late',   color:'#F59E0B'},
    {code:'EL',label:'Excused',color:'#3B82F6'},
    {code:'LV',label:'Leave', color:'#7C3AED'},
    {code:'H',label:'Holiday',color:'#0EA5E9'},
  ];

  function showError(msg){
    const el = document.getElementById('sa-error');
    el.textContent = msg || 'Failed to load subject attendance.';
    el.style.display = 'block';
  }
  function hideError(){
    const el = document.getElementById('sa-error');
    el.textContent = '';
    el.style.display = 'none';
  }

  function syncWraps(){
    const p = document.getElementById('sa-period').value;
    document.getElementById('sa-month-wrap').style.display  = (p==='monthly') ? 'flex':'none';
    document.getElementById('sa-year-wrap').style.display   = (p==='yearly')  ? 'block':'none';
    document.getElementById('sa-custom-wrap').style.display = (p==='custom')  ? 'flex':'none';
  }
  document.getElementById('sa-period').addEventListener('change', syncWraps);
  syncWraps();

  const loading = (on)=> document.getElementById('sa-loading').style.display = on ? 'flex' : 'none';
  const badge   = (s)=> `<span class="badge" style="border-color:${s.color};color:${s.color}">${s.code} = ${s.label}</span>`;

  function renderLegend(legend){
    const list = (legend && legend.length) ? legend : FALLBACK_LEGEND;
    document.getElementById('sa-legend').innerHTML = list.map(badge).join(' ');
    const tf = document.getElementById('sa-tfoot-legend');
    tf.innerHTML = `<tr><td colspan="5" style="text-align:center;padding-top:6px;border-top:1px dashed #cbd5e1">${list.map(badge).join(' ')}</td></tr>`;
  }

  function fmtHM(mins){
    const m = Math.max(0, Math.round(+mins||0));
    const h = Math.floor(m/60), r = m%60;
    return `${h}h ${String(r).padStart(2,'0')}m`;
  }
  function parseHMtoMin(s){
    if(!s) return 0;
    const m1 = String(s).match(/^(\d+)\s*h\s*(\d{1,2})m$/i);
    if(m1) return (+m1[1])*60 + (+m1[2]);
    const m2 = String(s).match(/^(\d{1,2}):(\d{2})$/);
    if(m2) return (+m2[1])*60 + (+m2[2]);
    return 0;
  }
  function parseDT(s){
    if(!s) return null;
    if(/^\d{4}-\d{2}-\d{2} /.test(s)) return new Date(s.replace(' ','T'));
    if(/^\d{1,2}:\d{2}(:\d{2})?(\s?[APap][Mm])?$/.test(s)) return new Date('1970-01-01T'+s.replace(' ',''));
    const d = new Date(s);
    return isNaN(d) ? null : d;
  }
  function rowMinutes(r){
    if(r.minutes!=null) return +r.minutes;
    if(r.mins!=null) return +r.mins;
    if(r.total_mins!=null) return +r.total_mins;
    if(r.total_hm) return parseHMtoMin(r.total_hm);
    if(r.total) return parseHMtoMin(r.total);
    const tin = parseDT(r.in_raw||r.in), tout = parseDT(r.out_raw||r.out);
    if(tin && tout) return Math.max(0, Math.round((tout - tin)/60000));
    return 0;
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
    hideError();

    if(!resp || resp.ok === false){
      showError(resp?.msg || 'Failed to load subject attendance.');
      document.getElementById('sa-body').innerHTML =
        `<tr><td colspan="5" class="tcenter" style="padding:8px;color:#64748b">No data.</td></tr>`;
      return;
    }

    document.getElementById('sa-range').textContent =
      (resp.range?.start && resp.range?.end) ? `${resp.range.start} to ${resp.range.end}` : '';

    renderLegend(resp.legend);

    const colors = resp.colors || {};
    const body = document.getElementById('sa-body');
    body.innerHTML = '';
    const rows = resp.rows || [];

    if (!rows.length){
      body.innerHTML = `<tr><td colspan="5" class="tcenter" style="padding:8px;color:#64748b">No subject attendance in this period.</td></tr>`;
    } else {
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

    // KPIs (robust)
    const t = resp.totals || {};
    const pct = (resp.percent ?? 0);
    const totalMinutes = (resp.total_minutes!=null)
      ? +resp.total_minutes
      : rows.reduce((s,r)=> s + rowMinutes(r), 0);
    const totalHM = resp.total_hm || fmtHM(totalMinutes);

    document.getElementById('sa-kpis').innerHTML = `
      <div class="kpi">Present: <b>${t.P||0}</b></div>
      <div class="kpi">Absent: <b>${t.A||0}</b></div>
      <div class="kpi">Late: <b>${t.L||0}</b></div>
      <div class="kpi">Leave: <b>${t.LV||0}</b></div>
      <div class="kpi">Excused: <b>${t.EL||0}</b></div>
      <div class="kpi">Holiday: <b>${t.H||0}</b></div>
      <div class="kpi">Attendance %: <b>${pct}%</b></div>
      <div class="kpi">Total Hours: <b>${totalHM}</b></div>
    `;
  }

  function gather(){
    const p = document.getElementById('sa-period').value;
    const q = { period:p };
    if(p==='monthly'){ q.year=document.getElementById('sa-year').value; q.month=document.getElementById('sa-month').value; }
    else if(p==='yearly'){ q.year=document.getElementById('sa-year-only').value; }
    else if(p==='custom'){ q.date_from=document.getElementById('sa-from').value; q.date_to=document.getElementById('sa-to').value; }

    // Subject filter (optional). Backend should apply when present.
    const subSel = document.getElementById('sa-subject');
    if(subSel && subSel.value) q.subject_id = subSel.value;

    return q;
  }

  function load(){
    loading(true);
    const params = gather();
    const u = new URL(ENDPOINT, window.location.origin);
    Object.keys(params).forEach(k=>u.searchParams.set(k, params[k]));
    fetch(u, {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(resp=> render(resp))
      .catch(()=> { showError('Failed to load subject attendance.'); })
      .finally(()=> loading(false));
  }

  // print only this card
  document.getElementById('sa-print').addEventListener('click', ()=>{
    document.body.classList.add('print-sub-only');
    window.print();
    setTimeout(()=>document.body.classList.remove('print-sub-only'), 200);
  });

  document.getElementById('sa-load').addEventListener('click', load);

  @if($subInit && !empty($subInit['ok']))
    render(@json($subInit));
  @else
    load();
  @endif
})();
</script>
