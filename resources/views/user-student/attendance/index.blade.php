@extends('user-student.layouts.master')

@php
  $stu = $student ?? null;
  $stuName = $stu ? trim(($stu->first_name ?? '').' '.($stu->middle_name ?? '').' '.($stu->last_name ?? '')) : (auth()->user()->name ?? 'Student');
  $stuReg  = $stu->reg_no ?? '';
@endphp

@section('content')
<style>
:root{
  --primary:#346da5; --primary-100:#eef2ff; --ink:#0f172a; --muted:#475569; --border:#e2e8f0; --bg:#ffffff;
}
*{box-sizing:border-box}
.att-page{padding:8px}
.card{border:1px solid var(--border);border-radius:14px;background:var(--bg);box-shadow:0 4px 6px -1px rgba(0,0,0,.06);margin-bottom:16px;overflow:hidden}
.card-head{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-bottom:1px solid #eef2f7;gap:12px;background:linear-gradient(180deg,#fafbff,transparent)}
.card-title{font-weight:800;color:var(--ink);font-size:18px;display:flex;align-items:center;gap:8px}
.card-controls{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.sel,.inp,.btn{border:1px solid var(--border);border-radius:10px;padding:4px 10px;font-size:14px;background:#fff}
.btn.primary{background:var(--primary);color:#fff;border:none}
.btn.soft{background:var(--primary-100);color:var(--primary);border:1px dashed var(--primary)}
.card-body{padding:12px 14px}
.range{margin-bottom:6px;color:var(--muted);font-weight:600}
.kpis{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 12px}
.kpi{border:1px solid var(--border);border-radius:10px;padding:8px 10px;font-weight:700;background:#fff}
.legend .badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:6px 12px;font-size:.9rem;margin:0 6px 6px;background:none}
.wrap{position:relative;border:1px solid var(--border);border-radius:12px;overflow:auto}
table.min{border-collapse:separate;border-spacing:0;width:100%}
table.min th, table.min td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:10px 12px;white-space:nowrap;background:#fff;font-size:1.02rem}
table.min th{position:sticky;top:0;background:#f8fafc;font-weight:800}
.tcenter{text-align:center}

/* notices + loading */
.notice{display:none; margin:8px 0 10px; padding:10px 12px; border-radius:10px; border:1px solid}
.notice.error{display:block;background:#fee2e2;border-color:#fecaca;color:#7f1d1d}
.notice.info{display:block;background:#eef2ff;border-color:#dbeafe;color:#1e3a8a}
.loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.6);align-items:center;justify-content:center;z-index:10}
.loading .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--primary);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* responsive */
@media (max-width:720px){
  .sel,.inp{padding:7px 9px}
  .btn{padding:8px 10px}
}

/* printing is handled via a separate popup so nothing to do here */
</style>

<div class="att-page">

  {{-- REGULAR --}}
  <div class="card" id="reg-card" data-endpoint="{{ $regularEndpoint ?? '' }}">
    <div class="card-head">
      <div class="card-title"><i class="fa fa-calendar-check"></i> Regular Attendance</div>
      <div class="card-controls no-print">
        <select id="ra-period" class="sel">
          <option value="monthly" selected>Monthly</option>
          <option value="yearly">Yearly</option>
          <option value="custom">Custom</option>
          <option value="lifetime">Lifetime</option>
        </select>

        <div id="ra-month-wrap" style="display:flex;gap:8px;align-items:center">
          @php $yy=(int)date('Y'); @endphp
          <select id="ra-year" class="sel">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}" {{ $i===0?'selected':'' }}>{{ $yy-$i }}</option>
            @endfor
          </select>
          <select id="ra-month" class="sel">
            @for($m=1;$m<=12;$m++)
              <option value="{{ $m }}" {{ $m==(int)date('n')?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,10)) }}</option>
            @endfor
          </select>
        </div>

        <div id="ra-year-wrap" style="display:none">
          <select id="ra-year-only" class="sel">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}" {{ $i===0?'selected':'' }}>{{ $yy-$i }}</option>
            @endfor
          </select>
        </div>

        <div id="ra-custom-wrap" style="display:none;gap:8px;align-items:center">
          <input id="ra-from" type="date" class="inp">
          <span>to</span>
          <input id="ra-to" type="date" class="inp">
        </div>

        <button id="ra-load" class="btn primary">Load</button>
        <button id="ra-print" class="btn soft"><i class="fa fa-print"></i> Print Regular</button>
      </div>
    </div>

    <div class="card-body">
      <div id="ra-notice" class="notice"></div>
      <div class="range" id="ra-range"></div>
      <div class="kpis" id="ra-kpis"></div>
      <div class="legend" id="ra-legend" style="margin-bottom:8px"></div>

      <div class="wrap">
        <div class="loading" id="ra-loading"><div class="spinner"></div></div>
        <table class="min">
          <thead>
            <tr>
              <th>Date</th><th class="tcenter">Status</th><th>Check-in</th><th>Check-out</th><th>Total</th>
            </tr>
          </thead>
          <tbody id="ra-body">
            <tr><td colspan="5" class="tcenter" style="color:#64748b">Load to view attendance…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- SUBJECT-WISE --}}
  <div class="card" id="sub-card" data-endpoint="{{ $subjectEndpoint ?? '' }}">
    <div class="card-head">
      <div class="card-title"><i class="fa fa-book-open"></i> Subject-wise Attendance</div>
      <div class="card-controls no-print">
        <select id="sa-period" class="sel">
          <option value="monthly" selected>Monthly</option>
          <option value="yearly">Yearly</option>
          <option value="custom">Custom</option>
          <option value="lifetime">Lifetime</option>
        </select>

        <div id="sa-month-wrap" style="display:flex;gap:8px;align-items:center">
          <select id="sa-year" class="sel">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}" {{ $i===0?'selected':'' }}>{{ $yy-$i }}</option>
            @endfor
          </select>
          <select id="sa-month" class="sel">
            @for($m=1;$m<=12;$m++)
              <option value="{{ $m }}" {{ $m==(int)date('n')?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,10)) }}</option>
            @endfor
          </select>
        </div>

        <div id="sa-year-wrap" style="display:none">
          <select id="sa-year-only" class="sel">
            @for($i=0;$i<6;$i++)
              <option value="{{ $yy-$i }}" {{ $i===0?'selected':'' }}>{{ $yy-$i }}</option>
            @endfor
          </select>
        </div>

        <div id="sa-custom-wrap" style="display:none;gap:8px;align-items:center">
          <input id="sa-from" type="date" class="inp">
          <span>to</span>
          <input id="sa-to" type="date" class="inp">
        </div>

        <select id="sa-subject" class="sel">
          <option value="">All Subjects</option>
          @isset($subjects)
            @foreach($subjects as $sid => $sname)
              <option value="{{ $sid }}">{{ $sname }}</option>
            @endforeach
          @endisset
        </select>

        <button id="sa-load" class="btn primary">Load</button>
        <button id="sa-print" class="btn soft"><i class="fa fa-print"></i> Print Subject</button>
      </div>
    </div>

    <div class="card-body">
      <div id="sa-notice" class="notice"></div>
      <div class="range" id="sa-range"></div>
      <div class="kpis" id="sa-kpis"></div>
      <div class="legend" id="sa-legend" style="margin-bottom:8px"></div>

      <div class="wrap">
        <div class="loading" id="sa-loading"><div class="spinner"></div></div>
        <table class="min">
          <thead>
          <tr>
            <th>Date</th><th>Subject</th><th class="tcenter">Status</th><th>In</th><th>Out</th><th>Total</th>
          </tr>
          </thead>
          <tbody id="sa-body">
            <tr><td colspan="6" class="tcenter" style="color:#64748b">Load to view attendance…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<script>
(function(){
  // ---------------- helpers
  const qs = (sel, root=document)=> root.querySelector(sel);
  const qsa= (sel, root=document)=> Array.from(root.querySelectorAll(sel));
  function show(el, on){ if(!el) return; el.style.display = on ? '' : 'none'; }
  function setText(id, txt){ const el = document.getElementById(id); if(el) el.textContent = txt || ''; }
  function setHTML(id, html){ const el = document.getElementById(id); if(el) el.innerHTML = html || ''; }

  function syncWraps(prefix){
    const p = qs('#'+prefix+'-period').value;
    show(qs('#'+prefix+'-month-wrap'),  p==='monthly');
    show(qs('#'+prefix+'-year-wrap'),   p==='yearly');
    show(qs('#'+prefix+'-custom-wrap'), p==='custom');
  }

  function badge(code, label, color){
    const c = color || '#334155';
    return `<span class="badge" style="border-color:${c};color:${c}">${code} = ${label}</span>`;
  }

  function computeH(tIn, tOut){
    if(!tIn || !tOut) return '';
    const now = new Date();
    const toDate = (t)=>{
      if(/^\d{2}:\d{2}$/.test(t)){ const [h,m]=t.split(':').map(Number); const d=new Date(now); d.setHours(h,m,0,0); return d; }
      const d = new Date(t);
      return isNaN(d.getTime()) ? null : d;
    };
    const a = toDate(tIn), b = toDate(tOut);
    if(!a || !b) return '';
    let mins = Math.max(0, Math.round((b-a)/60000));
    const h = Math.floor(mins/60), m = mins%60;
    return `${h}h ${m}m`;
  }

  // nice inline notice
  function info(id, msg){ const n = document.getElementById(id); if(!n) return; n.className='notice info'; n.textContent=msg; n.style.display='block'; }
  function error(id, msg){ const n = document.getElementById(id); if(!n) return; n.className='notice error'; n.textContent=msg; n.style.display='block'; }
  function clearNotice(id){ const n = document.getElementById(id); if(!n) return; n.style.display='none'; n.textContent=''; }

  // endpoints
  function resolveEndpoint(cardId, type){
    const card = document.getElementById(cardId);
    let url = (card?.dataset?.endpoint || '').trim();
    if(url) return url; // explicit from controller
    // fallback = same page ?ajax=1&type=regular|subject
    const base = window.location.pathname;
    const search = new URLSearchParams();
    search.set('ajax','1');
    search.set('type', type);
    return `${base}?${search.toString()}`;
  }
  const ENDPOINT_REG = resolveEndpoint('reg-card','regular');
  const ENDPOINT_SUB = resolveEndpoint('sub-card','subject');

  // ---------------- REGULAR
  syncWraps('ra');
  qs('#ra-period').addEventListener('change', ()=> syncWraps('ra'));

  function gatherRA(){
    const p = qs('#ra-period').value;
    const q = { period:p };
    if(p==='monthly'){ q.year=qs('#ra-year').value; q.month=qs('#ra-month').value; }
    else if(p==='yearly'){ q.year=qs('#ra-year-only').value; }
    else if(p==='custom'){ q.date_from=qs('#ra-from').value; q.date_to=qs('#ra-to').value; }
    return q;
  }

  function renderRA(resp){
    clearNotice('ra-notice');
    setText('ra-range', (resp?.range?.start && resp?.range?.end) ? `${resp.range.start} to ${resp.range.end}` : '');

    const t = resp?.totals || {};
    const pct = resp?.percent ?? 0;
    const hrs = resp?.hours_total_hm || resp?.hoursTotal || '';
    setHTML('ra-kpis', `
      <div class="kpi">Present: <b>${t.P||0}</b></div>
      <div class="kpi">Absent: <b>${t.A||0}</b></div>
      <div class="kpi">Late: <b>${t.L||0}</b></div>
      <div class="kpi">Leave: <b>${t.LV||0}</b></div>
      <div class="kpi">Excused: <b>${t.EL||0}</b></div>
      <div class="kpi">Holiday: <b>${t.H||0}</b></div>
      <div class="kpi">Attendance %: <b>${pct}%</b></div>
      ${hrs ? `<div class="kpi">Total Hours: <b>${hrs}</b></div>`:''}
    `);

    const leg = resp?.legend || [];
    setHTML('ra-legend', leg.map(s=>badge(s.code, s.label, s.color)).join(' '));

    const colors = resp?.colors || {};
    const dataRows = resp?.rows || resp?.grid || resp?.series || [];
    const body = qs('#ra-body'); body.innerHTML = '';
    if(!dataRows.length){
      body.innerHTML = `<tr><td colspan="5" class="tcenter" style="color:#64748b">No attendance in this period.</td></tr>`;
      return;
    }
    dataRows.forEach(r=>{
      const col = colors[r.status]||'';
      const style = col?` style="color:${col};font-weight:700"`:'';
      const total = r.total || computeH(r.in, r.out);
      body.insertAdjacentHTML('beforeend', `
        <tr>
          <td>${r.date||''}</td>
          <td class="tcenter"><span${style}>${r.status||''}</span></td>
          <td>${r.in||''}</td>
          <td>${r.out||''}</td>
          <td>${total||''}</td>
        </tr>
      `);
    });
  }

  qs('#ra-load').addEventListener('click', ()=>{
    clearNotice('ra-notice');
    const spinner = qs('#ra-loading'); show(spinner, true);
    const u = new URL(ENDPOINT_REG, window.location.origin);
    const p = gatherRA(); Object.keys(p).forEach(k=>u.searchParams.set(k,p[k]));
    fetch(u.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(async (r)=>{
        if(!r.ok) throw new Error(`HTTP ${r.status}`);
        const ct = r.headers.get('content-type') || '';
        if(!ct.includes('application/json')) throw new Error('Invalid response (not JSON)');
        return r.json();
      })
      .then(renderRA)
      .catch(err=> error('ra-notice', `Failed to load regular attendance. ${err.message||''}`))
      .finally(()=> show(spinner, false));
  });

  // ---------------- SUBJECT
  syncWraps('sa');
  qs('#sa-period').addEventListener('change', ()=> syncWraps('sa'));

  function gatherSA(){
    const p = qs('#sa-period').value;
    const q = { period:p };
    if(p==='monthly'){ q.year=qs('#sa-year').value; q.month=qs('#sa-month').value; }
    else if(p==='yearly'){ q.year=qs('#sa-year-only').value; }
    else if(p==='custom'){ q.date_from=qs('#sa-from').value; q.date_to=qs('#sa-to').value; }
    const sid = qs('#sa-subject')?.value || '';
    if (sid) q.subject_id = sid;
    return q;
  }

  function renderSA(resp){
    clearNotice('sa-notice');
    setText('sa-range', (resp?.range?.start && resp?.range?.end) ? `${resp.range.start} to ${resp.range.end}` : '');

    const t = resp?.totals || {};
    const pct = resp?.percent ?? 0;
    const hrs = resp?.hours_total_hm || resp?.hoursTotal || '';
    setHTML('sa-kpis', `
      <div class="kpi">Present: <b>${t.P||0}</b></div>
      <div class="kpi">Absent: <b>${t.A||0}</b></div>
      <div class="kpi">Late: <b>${t.L||0}</b></div>
      <div class="kpi">Leave: <b>${t.LV||0}</b></div>
      <div class="kpi">Excused: <b>${t.EL||0}</b></div>
      <div class="kpi">Holiday: <b>${t.H||0}</b></div>
      <div class="kpi">Attendance %: <b>${pct}%</b></div>
      ${hrs ? `<div class="kpi">Total Hours: <b>${hrs}</b></div>`:''}
    `);

    const leg = resp?.legend || [];
    setHTML('sa-legend', leg.map(s=>badge(s.code, s.label, s.color)).join(' '));

    const colors = resp?.colors || {};
    const rows = resp?.rows || resp?.grid || resp?.series || [];
    const body = qs('#sa-body'); body.innerHTML = '';
    if(!rows.length){
      body.innerHTML = `<tr><td colspan="6" class="tcenter" style="color:#64748b">No attendance in this period.</td></tr>`;
      return;
    }
    rows.forEach(r=>{
      const col = colors[r.status]||'';
      const style = col?` style="color:${col};font-weight:700"`:'';
      const total = r.total || computeH(r.in, r.out);
      body.insertAdjacentHTML('beforeend', `
        <tr>
          <td>${r.date||''}</td>
          <td>${r.subject||r.subject_name||''}</td>
          <td class="tcenter"><span${style}>${r.status||''}</span></td>
          <td>${r.in||''}</td>
          <td>${r.out||''}</td>
          <td>${total||''}</td>
        </tr>
      `);
    });
  }

  qs('#sa-load').addEventListener('click', ()=>{
    clearNotice('sa-notice');
    const spinner = qs('#sa-loading'); show(spinner, true);
    const u = new URL(ENDPOINT_SUB, window.location.origin);
    const p = gatherSA(); Object.keys(p).forEach(k=>u.searchParams.set(k,p[k]));
    fetch(u.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(async (r)=>{
        if(!r.ok) throw new Error(`HTTP ${r.status}`);
        const ct = r.headers.get('content-type') || '';
        if(!ct.includes('application/json')) throw new Error('Invalid response (not JSON)');
        return r.json();
      })
      .then(renderSA)
      .catch(err=> error('sa-notice', `Failed to load subject-wise attendance. ${err.message||''}`))
      .finally(()=> show(spinner, false));
  });

  // ---------------- PRINT (clean, card-only popup)
  const STUDENT_NAME = @json($stuName);
  const STUDENT_REG  = @json($stuReg);

  function printStyles(){
    return `
      <style>
        *{box-sizing:border-box}
        body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#0f172a;margin:0;padding:20px;background:#fff}
        h1{font-size:20px;margin:0 0 8px;font-weight:800}
        .muted{color:#475569}
        .topbar{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px}
        .kpis{display:flex;gap:8px;flex-wrap:wrap;margin:8px 0 12px}
        .kpi{border:1px solid #e2e8f0;border-radius:10px;padding:6px 10px;font-weight:700}
        .legend .badge{display:inline-block;border:1px solid #e2e8f0;border-radius:999px;padding:5px 10px;font-size:.95rem;margin:0 6px 6px}
        table{border-collapse:separate;border-spacing:0;width:100%}
        th,td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:8px 10px;white-space:nowrap;font-size:13px;background:#fff}
        th{background:#f8fafc;font-weight:800}
        .tcenter{text-align:center}
        @page{size:auto;margin:14mm}
      </style>
    `;
  }

  function buildPrintDoc({heading, subHeading, rangeText, kpisHTML, legendHTML, tableHTML}){
    return `
      <div class="topbar">
        <h1>${heading}</h1>
        <div class="muted">${new Date().toLocaleString()}</div>
      </div>
      <div class="muted" style="margin-bottom:6px">
        <div><b>Student:</b> ${STUDENT_NAME} &nbsp; <b>Reg:</b> ${STUDENT_REG}</div>
        ${subHeading ? `<div style="margin-top:2px">${subHeading}</div>` : ``}
        ${rangeText ? `<div style="margin-top:2px"><b>Range:</b> ${rangeText}</div>` : ``}
      </div>

      <div class="kpis">${kpisHTML||''}</div>
      <div class="legend">${legendHTML||''}</div>
      <div class="table-wrap">${tableHTML||''}</div>
    `;
  }

  qs('#ra-print').addEventListener('click', function(){
    const range  = qs('#ra-range')?.textContent || '';
    const kpis   = qs('#ra-kpis')?.innerHTML || '';
    const legend = qs('#ra-legend')?.innerHTML || '';
    const rows   = qs('#ra-body')?.innerHTML || '';
    const table  = `
      <table>
        <thead><tr>
          <th>Date</th><th class="tcenter">Status</th><th>Check-in</th><th>Check-out</th><th>Total</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table>`;
    const html = buildPrintDoc({
      heading:'Regular Attendance', subHeading:'', rangeText:range,
      kpisHTML:kpis, legendHTML:legend, tableHTML:table
    });
    const w = window.open('','_blank');
    w.document.write(`<html><head><title>Regular Attendance</title>${printStyles()}</head><body>${html}</body></html>`);
    w.document.close(); w.focus(); w.print(); w.close();
  });

  qs('#sa-print').addEventListener('click', function(){
    const range  = qs('#sa-range')?.textContent || '';
    const kpis   = qs('#sa-kpis')?.innerHTML || '';
    const legend = qs('#sa-legend')?.innerHTML || '';
    const rows   = qs('#sa-body')?.innerHTML || '';
    const subjSel= qs('#sa-subject');
    const subjTxt= (subjSel && subjSel.value) ? subjSel.options[subjSel.selectedIndex].text : 'All Subjects';
    const table  = `
      <table>
        <thead><tr>
          <th>Date</th><th>Subject</th><th class="tcenter">Status</th><th>In</th><th>Out</th><th>Total</th>
        </tr></thead>
        <tbody>${rows}</tbody>
      </table>`;
    const html = buildPrintDoc({
      heading:'Subject-wise Attendance', subHeading:`<b>Subject:</b> ${subjTxt}`, rangeText:range,
      kpisHTML:kpis, legendHTML:legend, tableHTML:table
    });
    const w = window.open('','_blank');
    w.document.write(`<html><head><title>Subject-wise Attendance</title>${printStyles()}</head><body>${html}</body></html>`);
    w.document.close(); w.focus(); w.print(); w.close();
  });

  // auto-load regular
  document.addEventListener('DOMContentLoaded', ()=> { qs('#ra-load').click(); });
})();
</script>
@endsection
