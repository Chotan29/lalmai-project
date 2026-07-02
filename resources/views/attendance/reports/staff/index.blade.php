@extends('layouts.master')

@section('css')
<style>
:root{
  --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b; --dark:#0f172a;
  --light:#f8fafc; --border:#e2e8f0; --success:#10b981; --danger:#ef4444;
  --shadow:0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif}
.container-fluid{padding:0 15px}

/* Fullscreen scroll (keep original background) */
.container-fluid:fullscreen,
.container-fluid:-webkit-full-screen,
.container-fluid:-ms-fullscreen{
  width:100vw; height:100vh; overflow-y:auto !important; -webkit-overflow-scrolling:touch;
  background: inherit; background-color: inherit;
}
.container-fluid.is-fs{
  width:100vw; height:100vh; overflow-y:auto !important; -webkit-overflow-scrolling:touch;
  background: inherit; background-color: inherit;
}

/* Header (sticky only in fullscreen; don't change bg) */
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding:0 8px}
.container-fluid.is-fs .header{
  position:sticky; top:0; z-index:120; background:inherit;
  box-shadow:0 2px 6px rgba(0,0,0,.06);
  margin-bottom:12px; padding-top:8px; padding-bottom:8px;
}
.header h1{font-weight:700;font-size:22px;margin:0;color:var(--dark)}
.controls{display:flex;gap:10px;flex-wrap:wrap}
.select,.input{border:1px solid var(--border);border-radius:8px;padding:4px 10px;font-size:14px;background:#fff}
.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:8px 12px;cursor:pointer;border:1px solid var(--border);background:#fff;font-size:14px}
.btn-primary{background:#346da5;color:#fff;border:none}
.btn-soft{background:var(--primary-light);color:var(--primary);border:1px dashed var(--primary)}
.btn:disabled{opacity:.6;cursor:not-allowed}

/* Sections */
.section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);padding:16px;margin-bottom:16px}
.section-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.section-icon{width:36px;height:36px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center}
.section-title{font-weight:700;color:var(--dark)}

/* Filters container toggle */
#filters-wrap.hidden{display:none}

/* Two-column filters */
.filters-grid{display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:start}
@media (max-width: 900px){ .filters-grid{ grid-template-columns:1fr; } }
.controls-row{display:flex; gap:10px; flex-wrap:wrap}
.controls-row > *{min-width:160px}

/* Report */
#report-wrap{display:none}
.report-actions{display:flex;justify-content:flex-end;gap:8px;margin-bottom:8px}

/* Compact header */
.mini-header{
  display:flex; align-items:center; justify-content:space-between; gap:16px;
  border-bottom:1px solid #e5e7eb; padding:8px 4px; margin-bottom:8px;
  page-break-inside:avoid;
}
.mini-left{display:flex; align-items:center; gap:10px; min-width:0; flex:1}
.mini-text{min-width:0}
.mini-name{font-weight:800; font-size:18px; line-height:1.1; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis}
.mini-slogan{font-size:11px; color:#6b7280; line-height:1.2}
.mini-address{font-size:11px; color:#6b7280; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis}

.mini-right{font-size:12px; flex-shrink:0}
.mini-meta{border-collapse:collapse}
.mini-meta td{padding:2px 6px; white-space:nowrap; line-height:1.2}
.mini-meta td:first-child{color:#334155}

/* Title + centered subtitle */
.attn-title{
  text-align:center; font-weight:800; text-transform:uppercase;
  font-size:20px; margin:8px 0 4px; letter-spacing:.3px;
}
#report-sub{
  text-align:center; color:#334155; font-size:1.05rem; font-weight:600; margin-bottom:6px;
}

/* Table + legend */
.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:12px;position:relative}
table.report{border-collapse:separate;border-spacing:0;width:100%}
table.report th, table.report td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:6px 8px;font-size:.86rem;white-space:nowrap;background:#fff}
table.report th{position:sticky;top:0;background:#f8fafc;z-index:2}
table.report th:first-child, table.report td:first-child{position:sticky;left:0;background:#fff;z-index:3}
table.report th:nth-child(2), table.report td:nth-child(2){position:sticky;left:140px;background:#fff;z-index:3}
table.report th:first-child{left:0;width:140px}
table.report th:nth-child(2){left:140px;width:260px}
.symbol{font-weight:700}
.th-strong{ font-weight:800 !important; }
@media print{ .th-strong{ font-weight:900 !important; } }
.td-strong{ font-weight:700; }
@media print{ .td-strong{ font-weight:900; } }

/* Notice + loading */
.notice{display:none;border-radius:10px;padding:10px 12px;margin:10px 0;border:1px solid}
.notice.info{background:#eef2ff;color:#1d4ed8;border-color:#bfdbfe}
.notice.error{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
.loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.7);align-items:center;justify-content:center;z-index:10}
.loading .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--primary);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
@keyframes spin{100%{transform:rotate(360deg)}}

/* Screen legend + print legend */
.report-meta-bottom{ margin-top:10px; display:flex; justify-content:center; border-top:1px dashed #cbd5e1; padding-top:8px; }
.legend .badge{ display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.8rem;margin:0 6px 4px;background:none }
tfoot.print-legend{display:none}

/* Print */
@media print{
  thead{display:table-header-group}
  tfoot.print-legend{display:table-footer-group}
  .no-print{display:none!important}
  #report-wrap{display:block}
  .table-wrap{overflow:visible}
  .mini-name{font-size:16pt}
  .mini-slogan,.mini-address,.mini-right{font-size:10pt}
  .attn-title{font-size:16pt; margin:4px 0}
  #report-sub{font-size:12pt}
  table.report th, table.report td{font-size:10px;padding:3px 5px}
  .report-meta-bottom{display:none!important}
  @page { size: A4 landscape; margin: 10mm; }
}
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

  <div class="header no-print">
    <div>
      <h1>Staff Monthly Attendance Report</h1>
      <p>Pick <strong>Year & Month</strong>. Optionally filter by <strong>Designation</strong> or <strong>Reg No / Name</strong>.</p>
    </div>
    <div class="controls">
      <button id="btn-filters" class="btn btn-soft"><i class="fa fa-sliders-h"></i> Hide Filters</button>
      <button id="btn-fullscreen" class="btn btn-soft"><i class="fa fa-expand"></i> Fullscreen</button>
      <button id="btn-reset" class="btn"><i class="fa fa-undo"></i> Reset</button>
      <a href="{{ route('attendance.reports.students.monthly') }}" class="btn btn-soft"><i class="fas fa-user-graduate"></i> Students</a>
    </div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  {{-- FILTERS (visible by default; single row = 2 columns) --}}
  <div id="filters-wrap">
    <div class="section no-print">
      <div class="section-head">
        <div class="section-icon"><i class="fa fa-filter"></i></div>
        <div class="section-title">Choose Year &amp; Month and Optional Filters</div>
      </div>

      <div class="filters-grid">
        {{-- Left: Year & Month --}}
        <div>
          <div class="controls-row">
            <select id="year" class="select" aria-label="Year">
              <option value="">Year…</option>
              @php $curY=(int)date('Y'); @endphp
              @foreach($years as $y)
                <option value="{{ $y }}" {{ $y===$curY ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>

            <select id="month" class="select" aria-label="Month">
              <option value="">Month…</option>
              @php $curM=(int)date('n'); @endphp
              @foreach($months as $mval=>$mname)
                <option value="{{ $mval }}" {{ (int)$mval===$curM ? 'selected' : '' }}>{{ $mname }}</option>
              @endforeach
            </select>

            <select id="designation_id" class="select" aria-label="Designation (optional)">
              <option value="">All Designations…</option>
              @foreach(($designations ?? []) as $d)
                <option value="{{ $d['id'] }}">{{ $d['title'] }}</option>
              @endforeach
            </select>

            <input id="q" type="text" class="input" placeholder="Reg No or Name (optional)" aria-label="Reg No or Name (optional)" />
          </div>
        </div>

        {{-- Right: Optional Filters --}}
        <div>
          <div class="controls-row">
            {{-- <select id="designation_id" class="select" aria-label="Designation (optional)">
              <option value="">All Designations…</option>
              @foreach(($designations ?? []) as $d)
                <option value="{{ $d['id'] }}">{{ $d['title'] }}</option>
              @endforeach
            </select>

            <input id="q" type="text" class="input" placeholder="Reg No or Name (optional)" aria-label="Reg No or Name (optional)" /> --}}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- REPORT --}}
  <div class="section" id="report-wrap">

    <div class="report-actions no-print">
      <button id="btn-print-report" class="btn btn-primary" disabled><i class="fa fa-print"></i> Print</button>
    </div>

    {{-- compact single-row header --}}
    <div class="mini-header">
      <div class="mini-left">
        <div class="mini-text">
          <div class="mini-name">{{ $generalSetting->institute ?? 'Institution Name' }}</div>
          @if(!empty($generalSetting->salogan))
            <div class="mini-slogan">{{ $generalSetting->salogan }}</div>
          @endif
          @if(!empty($generalSetting->address))
            <div class="mini-address">{{ $generalSetting->address }}</div>
          @endif
        </div>
      </div>
      <div class="mini-right">
        <table class="mini-meta">
          <tr><td>Designation</td><td>:</td><td id="hdr-desig">All</td></tr>
          <tr><td>Print Date</td><td>:</td><td id="hdr-print-date">—</td></tr>
        </table>
      </div>
    </div>

    {{-- Title + centered (Month Year) --}}
    <div id="attn-title" class="attn-title">Staff Attendance Sheet</div>
    <div id="report-sub"></div>

    <div id="notice" class="notice" role="alert" aria-live="polite"></div>

    <div class="table-wrap">
      <div class="loading" id="loading"><div class="spinner"></div></div>
      <table class="report" id="report-table">
        <thead id="thead"></thead>
        <tbody id="tbody"></tbody>
        <tfoot id="tfoot-legend" class="print-legend"></tfoot>
      </table>
    </div>

    {{-- Screen legend (hidden in print) --}}
    <div id="report-meta" class="report-meta-bottom">
      <div id="legend" class="legend"></div>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<script>
(function($){

/* endpoints */
const ENDPOINTS = {
  data: { url:"{{ route('attendance.reports.staff.monthly.data') }}" }
};

/* Notices */
function showNotice(type,text){ const $n=$('#notice'); $n.removeClass('info error').addClass('notice '+type).text(text).show(); }
function hideNotice(){ $('#notice').hide().text('').removeClass('info error'); }

/* Debounced autoload */
let debounceTimer=null, inflight=null, lastSig='';
function getParams(){
  return {
    year: $('#year').val() || '',
    month: $('#month').val() || '',
    designation_id: $('#designation_id').val() || '',
    q: $('#q').val() || ''
  };
}
function isReady(p){ return p.year && p.month; }

function attendanceAutoLoad(){
  const p=getParams();
  if(!isReady(p)){
    $('#report-wrap').hide();
    $('#btn-print-report').prop('disabled',true);
    hideNotice();
    return;
  }
  const sig=JSON.stringify(p);
  if(sig===lastSig) return;
  if(debounceTimer) clearTimeout(debounceTimer);
  debounceTimer=setTimeout(()=>loadData(p,sig), 250);
}

function loadData(params,sig){
  lastSig=sig;
  if(inflight && typeof inflight.abort==='function') inflight.abort();

  $('#report-wrap').show(); $('#loading').css('display','flex');
  $('#btn-print-report').prop('disabled',true); hideNotice();

  inflight=$.get(ENDPOINTS.data.url, params)
    .done(resp=>{
      if(!resp || resp.ok===false){
        const msg=(resp && resp.msg)?resp.msg:'Failed to load Staff Monthly Attendance Report.';
        $('#thead').empty(); $('#tbody').empty(); $('#tfoot-legend').empty();
        $('#attn-title').text('Staff Attendance Sheet'); $('#report-sub').text(''); $('#legend').empty();
        showNotice('error',msg); $('#btn-print-report').prop('disabled',true); return;
      }
      hideNotice(); buildTable(resp); $('#btn-print-report').prop('disabled',false);
    })
    .fail((xhr,status)=>{
      if(status!=='abort'){
        $('#thead').empty(); $('#tbody').empty(); $('#tfoot-legend').empty();
        showNotice('error','Network/server error while loading the report.');
        $('#btn-print-report').prop('disabled',true);
      }
    })
    .always(()=>{ $('#loading').hide(); inflight=null; });
}

/* Inputs → trigger */
$('#year,#month,#designation_id').on('change', attendanceAutoLoad);
let searchTimer=null;
$('#q').on('input', function(){
  if(searchTimer) clearTimeout(searchTimer);
  searchTimer = setTimeout(attendanceAutoLoad, 300);
});

/* Helpers */
function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[m]); }
function paletteFallback(){
  return {
    colors: { P:'#10B981', A:'#EF4444', L:'#F59E0B', E:'#3B82F6', HL:'#7C3AED', H:'#0EA5E9', LV:'#7C3AED', EL:'#7C3AED' },
    legend: [
      {code:'P',label:'Present',color:'#10B981'},
      {code:'A',label:'Absent',color:'#EF4444'},
      {code:'L',label:'Late',color:'#F59E0B'},
      {code:'E',label:'Excused',color:'#3B82F6'},
      {code:'HL',label:'Half-Leave',color:'#7C3AED'},
      {code:'H',label:'Holiday',color:'#0EA5E9'},
      {code:'LV',label:'Leave',color:'#7C3AED'},
      {code:'EL',label:'Leave',color:'#7C3AED'},
    ]
  };
}
function setPrintDate(){
  const d = new Date();
  const y = d.getFullYear();
  const m = String(d.getMonth()+1).padStart(2,'0');
  const day = String(d.getDate()).padStart(2,'0');
  $('#hdr-print-date').text(`${y}-${m}-${day}`);
}

/* Build table + legend */
function buildTable(resp){
  const days=resp.days||[], rows=resp.rows||[], meta=resp.meta||{};
  const fb=paletteFallback();
  const colors=meta.status_colors||fb.colors;
  const legend=meta.status_legend||fb.legend;

  // Title + Month/Year line
  const y=$('#year option:selected').text()||$('#year').val();
  const m=$('#month option:selected').text()||$('#month').val();
  $('#attn-title').text('Staff Attendance Sheet');
  $('#report-sub').text(`${m} ${y}`);

  // Header meta
  const desigText = meta.designation_title || ($('#designation_id option:selected').text() || 'All');
  $('#hdr-desig').text(desigText || 'All');
  setPrintDate();

  // Screen legend
  function makeLegendHtml(list){
    if(!list || !list.length) return '';
    return list.map(s=>{
      const txt = `${s.code} = ${s.label}`;
      return `<span class="badge" style="border-color:${s.color};color:${s.color}">${escapeHtml(txt)}</span>`;
    }).join('');
  }
  const legendHtml = makeLegendHtml(legend);
  $('#legend').html(legendHtml);

  // header
  const $th=$('#thead').empty();
  const $trH=$('<tr/>');
  $trH.append('<th class="th-strong">Reg No</th>');
  $trH.append('<th class="th-strong">Staff Name</th>');
  days.forEach(d=>$trH.append(`<th style="text-align:center">${d}</th>`));
  $trH.append('<th class="th-strong" style="text-align:center">P</th>');
  $trH.append('<th class="th-strong" style="text-align:center">A</th>');
  $trH.append('<th class="th-strong" style="text-align:center">L</th>');
  $trH.append('<th class="th-strong" style="text-align:center">LV</th>');
  $trH.append('<th class="th-strong" style="text-align:center">EL</th>');
  $trH.append('<th class="th-strong" style="text-align:center">H</th>');
  $th.append($trH);

  // print legend in tfoot
  const colSpan = 2 + days.length + 6;
  const $tf = $('#tfoot-legend').empty();
  $tf.append(
    `<tr>
       <td colspan="${colSpan}" style="text-align:center; padding-top:8px; border-top:1px dashed #cbd5e1;">
         <div class="legend" style="display:inline-block">${legendHtml}</div>
       </td>
     </tr>`
  );

  // body
  const $tb=$('#tbody').empty();
  if(!rows.length){
    showNotice('info','No staff attendance found for the selected month/filters.');
    $tb.append('<tr><td colspan="'+colSpan+'" class="text-muted" style="padding:10px">No rows to display</td></tr>');
    return;
  }

  rows.forEach(r=>{
    const $tr=$('<tr/>');
    $tr.append(`<td class="td-strong">${escapeHtml(r.reg_no||'—')}</td>`);
    $tr.append(`<td class="td-strong">${escapeHtml(r.name||'—')}</td>`);
    days.forEach(d=>{
      const code=(r.d && r.d[d]) ? r.d[d] : '';
      const col=colors[code]||'';
      const style= col ? ` style="color:${col}"` : '';
      $tr.append(`<td style="text-align:center"><span class="symbol"${style}>${code||''}</span></td>`);
    });
    const c=r.count||{};
    $tr.append(`<td class="td-strong" style="text-align:center">${c.P||0}</td>`);
    $tr.append(`<td class="td-strong" style="text-align:center">${c.A||0}</td>`);
    $tr.append(`<td class="td-strong" style="text-align:center">${c.L||0}</td>`);
    $tr.append(`<td class="td-strong" style="text-align:center">${c.LV||0}</td>`);
    $tr.append(`<td class="td-strong" style="text-align:center">${c.EL||0}</td>`);
    $tr.append(`<td class="td-strong" style="text-align:center">${c.H||0}</td>`);
    $tb.append($tr);
  });
}

/* Buttons */
$('#btn-print-report').on('click', ()=>{ setPrintDate(); window.print(); });
$('#btn-reset').on('click', function(){
  $('#designation_id').val('');
  $('#q').val('');
  const now=new Date();
  $('#year').val(String(now.getFullYear()));
  $('#month').val(String(now.getMonth()+1));
  lastSig=''; $('#report-wrap').hide(); $('#btn-print-report').prop('disabled',true); hideNotice();
});

/* Filters toggle (default SHOW) */
function setFiltersVisible(show){
  const lbl = show ? 'Hide Filters' : 'Show Filters';
  $('#filters-wrap').toggleClass('hidden', !show);
  $('#btn-filters').html(`<i class="fa fa-sliders-h"></i> ${lbl}`);
}
$('#btn-filters').on('click', function(){
  const hidden = $('#filters-wrap').hasClass('hidden');
  setFiltersVisible(hidden);
});
setFiltersVisible(true);

/* Fullscreen — preserve background exactly */
function isFullscreen(){
  return !!(document.fullscreenElement||document.webkitFullscreenElement||document.msFullscreenElement);
}
function reqFs(el){
  if(el.requestFullscreen) return el.requestFullscreen();
  if(el.webkitRequestFullscreen) return el.webkitRequestFullscreen();
  if(el.msRequestFullscreen) return el.msRequestFullscreen();
}
function exitFs(){
  if(document.exitFullscreen) return document.exitFullscreen();
  if(document.webkitExitFullscreen) return document.webkitExitFullscreen();
  if(document.msExitFullscreen) return document.msExitFullscreen();
}
function applyFsBackground(on){
  const el = document.querySelector('.container-fluid');
  if(!el) return;
  if(on){
    const cs = window.getComputedStyle(document.body);
    el.style.background = cs.background || cs.backgroundColor || '';
  }else{
    el.style.background = '';
  }
}
function updateFsBtn(){
  const on = isFullscreen();
  const html = `<i class="fa ${on?'fa-compress':'fa-expand'}"></i> ${on?'Exit Fullscreen':'Fullscreen'}`;
  $('#btn-fullscreen').html(html);
  $('.container-fluid').toggleClass('is-fs', on);
  applyFsBackground(on);
}
$('#btn-fullscreen').on('click', function(e){
  e.preventDefault();
  const target = document.querySelector('.container-fluid')||document.documentElement;
  if(isFullscreen()) exitFs(); else reqFs(target);
});
document.addEventListener('fullscreenchange', updateFsBtn);
document.addEventListener('webkitfullscreenchange', updateFsBtn);
document.addEventListener('msfullscreenchange', updateFsBtn);

/* Refresh print date right before printing */
window.addEventListener('beforeprint', setPrintDate);

/* Init */
attendanceAutoLoad();
setPrintDate();

})(jQuery);
</script>
@endsection
