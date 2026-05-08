@extends('layouts.master')

@section('css')
<style>
:root{
  --primary:#0ea5e9;--dark:#0f172a;--muted:#64748b;--light:#f8fafc;--border:#e2e8f0;
}
.container-fluid{background:#f1f5f9;min-height:100vh;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.header h1{font-size:22px;font-weight:800;color:var(--dark);margin:0}
.header p{color:var(--muted);margin:4px 0 0 0}
.card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:14px;margin-bottom:12px}
.grid-3{display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px}
.grid-2{display:grid;grid-template-columns:2fr 1fr;gap:12px}
.grid-1{display:grid;grid-template-columns:1fr;gap:12px}
@media(max-width:1200px){.grid-3,.grid-2{grid-template-columns:1fr}}
.kpis{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}
.kpi{background:#fff;border:1px solid var(--border);border-radius:12px;padding:12px}
.kpi .h{font-size:11px;color:var(--muted)}
.kpi .v{font-size:20px;font-weight:800;color:var(--dark)}
.row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.row .box{display:flex;flex-direction:column}
input[type="date"]{border:1px solid var(--border);border-radius:8px;padding:8px 10px}
.btn{background:var(--primary);color:#fff;border:none;border-radius:8px;padding:8px 12px;cursor:pointer}
.badge{display:inline-block;border:1px solid var(--border);padding:2px 8px;border-radius:9999px;font-size:12px;color:var(--muted)}
.tbl{width:100%;border-collapse:separate;border-spacing:0 6px}
.tbl thead th{font-size:12px;text-transform:uppercase;letter-spacing:.02em;color:#475569;text-align:left;padding:8px 10px}
.tbl tbody tr{background:#fff;border:1px solid var(--border)}
.tbl tbody td{padding:8px 10px;border-top:1px solid var(--border)}
.tbl tbody tr:first-child td{border-top:none}
.chart-box{position:relative;height:300px}
.chart-box.sm{height:240px}
.chart-box > canvas{width:100%!important;height:100%!important}
.muted{color:var(--muted)}
</style>
@endsection

@section('content')
<div class="container-fluid py-3" data-currency="{{ $currency }}">
  <div class="header">
    <div>
      <h1>Student Dashboard</h1>
      <p class="muted">Overview for {{ $student ? ($student->first_name ?? $student->name ?? 'Student') : '—' }}
        @if($student && $faculty_lbl)
          <span class="badge">Faculty: {{ $faculty_lbl }}</span>
        @endif
        @if($student && $semester_lbl)
          <span class="badge">Semester: {{ $semester_lbl }}</span>
        @endif
        @if($student && $batch_lbl)
          <span class="badge">Batch: {{ $batch_lbl }}</span>
        @endif
      </p>
    </div>
    <div class="row">
      <div class="box">
        <small class="muted">From</small>
        <input id="from_date" type="date" value="{{ $from_date }}">
      </div>
      <div class="box">
        <small class="muted">To</small>
        <input id="to_date" type="date" value="{{ $to_date }}">
      </div>
      <button class="btn" id="btn-apply">Apply</button>
    </div>
  </div>

  <div class="kpis">
    <div class="kpi"><div class="h">Fees Collected ({{ $currency }})</div><div class="v" id="k-fees">0</div></div>
    <div class="kpi"><div class="h">Fees Due ({{ $currency }})</div><div class="v" id="k-due">0</div></div>
    <div class="kpi"><div class="h">Subjects</div><div class="v" id="k-subjects">0</div></div>
    <div class="kpi"><div class="h">Degrees</div><div class="v" id="k-degrees">0</div></div>
    <div class="kpi"><div class="h">Scholarships</div><div class="v" id="k-scholarships">0</div></div>
    <div class="kpi"><div class="h">Placements</div><div class="v" id="k-placements">0</div></div>
  </div>

  <div class="grid-3">
    <div class="card">
      <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:6px">
        <strong>Fees by Day</strong>
        <span class="badge" id="student-badge">
          Reg: {{ $student ? ( ($student->reg_no ?? $student->registration_no ?? $student->roll_no ?? '—') ) : '—' }}
        </span>
      </div>
      <div class="chart-box"><canvas id="c-fees-byday"></canvas></div>
    </div>
    <div class="card">
      <strong>Fee Heads Share</strong>
      <div class="chart-box sm"><canvas id="c-headshare"></canvas></div>
    </div>
    <div class="card">
      <strong>Subjects by Semester</strong>
      <div class="chart-box sm"><canvas id="c-subjects-sem"></canvas></div>
    </div>
  </div>

  <div class="grid-2">
    <div class="card">
      <strong>Scholarships by Year</strong>
      <div class="chart-box"><canvas id="c-scholarships"></canvas></div>
    </div>
    <div class="card">
      <strong>Placements Timeline</strong>
      <div class="chart-box"><canvas id="c-placements"></canvas></div>
    </div>
  </div>

  <div class="grid-2">
    <div class="card">
      <strong>Recent Receipts</strong>
      <div class="table-responsive">
        <table class="tbl" id="t-receipts">
          <thead><tr>
            <th>Date</th><th>Method</th><th>Amount ({{ $currency }})</th><th>Discount</th><th>Fine</th><th>Note</th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <strong>Degrees</strong>
      <div class="table-responsive">
        <table class="tbl" id="t-degrees">
          <thead><tr>
            <th>Degree</th><th>Level</th><th>Board</th><th>Year</th><th>Grade</th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>
      <hr style="margin:10px 0;border:none;border-top:1px solid var(--border)"/>
      <strong>Annexures</strong>
      <div class="table-responsive">
        <table class="tbl" id="t-annexures">
          <thead><tr>
            <th>Title</th><th>Date</th><th>Note</th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
  // Config
  const SUMMARY_URL = @json($summary_url);
  const STUDENT_ID  = @json($student_id);
  const CURRENCY    = (document.querySelector('.container-fluid')?.dataset?.currency || '').toUpperCase();

  // Helpers
  const $ = (sel, ctx=document) => ctx.querySelector(sel);
  const $$= (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));
  const money = (n) => (Number(n||0)).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});

  function pickPalette(n, a=0.9){
    const out=[]; for(let i=0;i<n;i++){ const h=Math.round((360/n)*i)%360; out.push(`hsl(${h} 70% 55% / ${a})`); }
    return out;
  }
  function solidize(cols){ return cols.map(c=>c.replace(/\/\s*0\.?\d+\)/,'/ 1)')); }

  // Charts
  let chFeesByDay, chHeadShare, chSubjectsSem, chScholarships, chPlacements;

  function makeCharts(){
    const baseMoney = {
      responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{display:false}, tooltip:{callbacks:{label:(ctx)=> CURRENCY+' '+money(ctx.raw)}}},
      scales:{ y:{ beginAtZero:true, ticks:{ callback:(v)=> CURRENCY+' '+money(v) } } }
    };

    chFeesByDay = new Chart($('#c-fees-byday'), {
      type:'line',
      data:{ labels:[], datasets:[{ label:CURRENCY, data:[], tension:.25, fill:true }] },
      options: baseMoney
    });

    chHeadShare = new Chart($('#c-headshare'), {
      type:'doughnut',
      data:{ labels:[], datasets:[{ data:[] }] },
      options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:true, position:'bottom'} } }
    });

    chSubjectsSem = new Chart($('#c-subjects-sem'), {
      type:'bar',
      data:{ labels:[], datasets:[{ data:[] }] },
      options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } } }
    });

    chScholarships = new Chart($('#c-scholarships'), {
      type:'bar',
      data:{ labels:[], datasets:[{ data:[], label:'Scholarship' }] },
      options: baseMoney
    });

    chPlacements = new Chart($('#c-placements'), {
      type:'line',
      data:{ labels:[], datasets:[{ data:[], label:'Placements', tension:.25, fill:false }] },
      options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
    });
  }

  function paintDoughnut(chart){
    const n = chart.data.labels.length;
    const cols = pickPalette(n, .9);
    chart.data.datasets[0].backgroundColor = cols;
    chart.data.datasets[0].borderColor = solidize(cols);
    chart.update();
  }

  function paintBars(chart){
    const n = chart.data.labels.length;
    const cols = pickPalette(n, .85);
    chart.data.datasets[0].backgroundColor = cols;
    chart.data.datasets[0].borderColor = solidize(cols);
    chart.data.datasets[0].borderWidth = 1;
    chart.update();
  }

  function safeText(s){ const d=document.createElement('div'); d.textContent = String(s??''); return d.innerHTML; }

  function fillTable(tbodySel, rows, cols){
    const tb = $(tbodySel);
    tb.innerHTML = '';
    if(!rows || !rows.length){ tb.innerHTML = `<tr><td class="muted" colspan="${cols}">No data</td></tr>`; return; }
    rows.forEach(r => tb.insertAdjacentHTML('beforeend', r));
  }

  async function loadSummary(){
    if(!STUDENT_ID){ console.warn('No student_id; pass ?student_id=ID in URL or set mapping.'); return; }

    const from = $('#from_date').value;
    const to   = $('#to_date').value;
    const url  = new URL(SUMMARY_URL, location.origin);
    url.searchParams.set('student_id', STUDENT_ID);
    url.searchParams.set('from_date', from);
    url.searchParams.set('to_date', to);

    const resp = await fetch(url.toString(), { headers:{'X-Requested-With':'XMLHttpRequest'} });
    const data = await resp.json();

    // KPIs
    $('#k-fees').textContent         = money(data?.counters?.fees_collected || 0);
    $('#k-due').textContent          = money(data?.counters?.fees_due || 0);
    $('#k-subjects').textContent     = data?.counters?.subjects ?? 0;
    $('#k-degrees').textContent      = data?.counters?.degrees ?? 0;
    $('#k-scholarships').textContent = data?.counters?.scholarships ?? 0;
    $('#k-placements').textContent   = data?.counters?.placements ?? 0;

    // Fees by day
    chFeesByDay.data.labels = data?.feesByDay?.labels || [];
    chFeesByDay.data.datasets[0].data = data?.feesByDay?.data || [];
    chFeesByDay.update();

    // Head share
    chHeadShare.data.labels = data?.headShare?.labels || [];
    chHeadShare.data.datasets[0].data = data?.headShare?.data || [];
    paintDoughnut(chHeadShare);

    // Subjects by semester
    chSubjectsSem.data.labels = data?.subjectsBySem?.labels || [];
    chSubjectsSem.data.datasets[0].data = data?.subjectsBySem?.data || [];
    paintBars(chSubjectsSem);

    // Scholarships
    chScholarships.data.labels = data?.scholarships?.labels || [];
    chScholarships.data.datasets[0].data = data?.scholarships?.data || [];
    paintBars(chScholarships);

    // Placements
    chPlacements.data.labels = data?.placements?.labels || [];
    chPlacements.data.datasets[0].data = data?.placements?.data || [];
    chPlacements.update();

    // Receipts table
    const rrows = (data?.recentReceipts || []).map(x =>
      `<tr>
        <td>${safeText(x.date)}</td>
        <td>${safeText(x.payment_method)}</td>
        <td>${money(x.amount)}</td>
        <td>${money(x.discount)}</td>
        <td>${money(x.fine)}</td>
        <td>${safeText(x.note)}</td>
      </tr>`
    );
    fillTable('#t-receipts tbody', rrows, 6);

    // Degrees
    const drows = (data?.degrees || []).map(x =>
      `<tr>
        <td>${safeText(x.title)}</td>
        <td>${safeText(x.level)}</td>
        <td>${safeText(x.board)}</td>
        <td>${safeText(x.passed)}</td>
        <td>${safeText(x.grade)}</td>
      </tr>`
    );
    fillTable('#t-degrees tbody', drows, 5);

    // Annexures
    const arows = (data?.annexures || []).map(x =>
      `<tr>
        <td>${safeText(x.title)}</td>
        <td>${safeText(x.date)}</td>
        <td>${safeText(x.note)}</td>
      </tr>`
    );
    fillTable('#t-annexures tbody', arows, 3);
  }

  function applyDates(){
    let f = $('#from_date').value, t = $('#to_date').value;
    if(f && t && new Date(f) > new Date(t)){
      const tmp = f; f = t; t = tmp;
      $('#from_date').value = f; $('#to_date').value = t;
    }
    const params = new URLSearchParams(location.search);
    params.set('from_date', $('#from_date').value);
    params.set('to_date', $('#to_date').value);
    if (STUDENT_ID) params.set('student_id', STUDENT_ID);
    history.replaceState({}, '', location.pathname + '?' + params.toString());
    loadSummary();
  }

  // Boot
  makeCharts();
  $('#btn-apply').addEventListener('click', applyDates);
  loadSummary();
})();
</script>
@endsection
