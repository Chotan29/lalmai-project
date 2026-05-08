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

/* Fullscreen scroll (preserve background) */
.container-fluid:fullscreen,
.container-fluid:-webkit-full-screen,
.container-fluid:-ms-fullscreen{ width:100vw; height:100vh; overflow-y:auto!important; background:inherit; }
.container-fluid.is-fs{ width:100vw; height:100vh; overflow-y:auto!important; background:inherit; }

/* Header (sticky only in FS) */
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding:0 8px}
.container-fluid.is-fs .header{
  position:sticky; top:0; z-index:120; background:inherit;
  box-shadow:0 2px 6px rgba(0,0,0,.06);
  margin-bottom:12px; padding:8px 8px;
}
.header h1{font-weight:700;font-size:22px;margin:0;color:var(--dark)}
.controls{display:flex;gap:10px;flex-wrap:wrap}
.select,.input{border:1px solid var(--border);border-radius:8px;padding:6px 10px}
.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:8px 12px;cursor:pointer;border:1px solid var(--border);background:#fff;font-size:14px}
.btn-primary{background:#346da5;color:#fff;border:none}
.btn-soft{background:var(--primary-light);color:var(--primary);border:1px dashed var(--primary)}
.btn:disabled{opacity:.6;cursor:not-allowed}

/* Sections */
.section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);padding:16px;margin-bottom:16px}
.section-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.section-icon{width:36px;height:36px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center}
.section-title{font-weight:700;color:var(--dark)}
#filters-wrap.hidden{display:none}

/* 2-column filter row */
.filters-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media (max-width: 900px){ .filters-grid{ grid-template-columns:1fr } }

/* mini header */
.mini-header{display:flex;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid #e5e7eb;padding:8px 4px;margin-bottom:8px;page-break-inside:avoid}
.mini-left{display:flex;align-items:center;gap:10px;min-width:0;flex:1}
.mini-text{min-width:0}
.mini-name{font-weight:800;font-size:18px;line-height:1.1;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mini-slogan{font-size:11px;color:#6b7280;line-height:1.2}
.mini-address{font-size:11px;color:#6b7280;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mini-right{font-size:12px;flex-shrink:0}
.mini-meta{border-collapse:collapse}
.mini-meta td{padding:2px 6px;white-space:nowrap;line-height:1.2}
.mini-meta td:first-child{color:#334155}

/* Title + sub */
.attn-title{text-align:center;font-weight:800;text-transform:uppercase;font-size:20px;margin:8px 0 4px;letter-spacing:.3px}
#report-sub{text-align:center;color:#334155;font-size:1.05rem;font-weight:600;margin-bottom:6px}

/* Scorecard */
.scorecard{display:flex;gap:10px;flex-wrap:wrap;margin:8px 0 6px}
.sc-pill{border:1px solid var(--border);border-radius:999px;padding:5px 10px;font-weight:700}
.sc-pill .kv{margin-left:6px}

/* Legend */
.legend{margin-top:6px;text-align:center}
.legend .badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.8rem;margin:0 6px 4px;background:none;}

/* Table */
.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:12px;position:relative}
table.report{border-collapse:separate;border-spacing:0;width:100%}
table.report th, table.report td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:6px 8px;font-size:.86rem;white-space:nowrap;background:#fff}
table.report th{position:sticky;top:0;background:#f8fafc;z-index:2}
.tcenter{text-align:center}

/* Notices + loading */
.notice{display:none;border-radius:10px;padding:10px 12px;margin:10px 0;border:1px solid}
.notice.info{background:#eef2ff;color:#1d4ed8;border-color:#bfdbfe}
.notice.error{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
.loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.7);align-items:center;justify-content:center;z-index:10}
.loading .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--primary);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
@keyframes spin{100%{transform:rotate(360deg)}}

/* Print */
tfoot.print-legend{display:none}
@media print{
  thead{display:table-header-group}
  tfoot.print-legend{display:table-footer-group}
  .no-print{display:none!important}
  #report-wrap{display:block}
  .table-wrap{overflow:visible}
  .mini-name{font-size:16pt}
  .mini-slogan,.mini-address,.mini-right{font-size:10pt}
  .attn-title{font-size:16pt;margin:4px 0}
  #report-sub{font-size:12pt}
  table.report th, table.report td{font-size:10px;padding:3px 5px}
  @page { size: A4 landscape; margin: 10mm; }
}
</style>
@endsection

@section('content')
@php
  $isStudent = ($kind === 'student');
@endphp

<div class="container-fluid py-3">

  <div class="header no-print">
    <div>
      <h1>{{ $isStudent ? 'Individual Student Attendance Card' : 'Individual Staff Attendance Card' }}</h1>
    </div>
    <div class="controls">
      <button id="btn-filters" class="btn btn-soft"><i class="fa fa-sliders-h"></i> Hide Filters</button>
      <button id="btn-fullscreen" class="btn btn-soft"><i class="fa fa-expand"></i> Fullscreen</button>
      @php
        $switchUrl = $isStudent
            ? route('attendance.reports.individual.staff')
            : route('attendance.reports.individual.students');
        $switchLabel = $isStudent ? 'Staff Card' : 'Student Card';
        $switchIcon  = $isStudent ? 'fa-user-tie' : 'fa-graduation-cap';
      @endphp
      <a id="btn-switch-kind" class="btn btn-soft" href="{{ $switchUrl }}" title="Switch to {{ $switchLabel }}">
        <i class="fa {{ $switchIcon }}"></i> {{ $switchLabel }}
      </a>
    </div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  {{-- FILTERS --}}
  <div id="filters-wrap" class="section no-print">
    <div class="section-head">
      <div class="section-icon"><i class="fa fa-filter"></i></div>
      <div class="section-title">Choose Person & Period</div>
    </div>

    <div class="filters-grid">
      {{-- Column 1: Person & Report Type --}}
      <div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:8px">
          <input id="q" class="input" type="text" placeholder="Search {{ $isStudent?'student':'staff' }} by name or reg no…" style="min-width:280px">
          <button id="btn-search" class="btn"><i class="fa fa-search"></i> Search</button>
          <select id="result" class="select" style="min-width:280px">
            <option value="">— Select from results —</option>
          </select>
          <input type="hidden" id="person_id" value="">
        </div>

        @if($isStudent)
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:6px">
            <label>Report Type</label>
            <select id="report_type" class="select">
              <option value="regular" selected>Regular</option>
              <option value="subject">Subject</option>
            </select>

            {{-- NEW: subject dropdown --}}
            <select id="subject_id" class="select" style="min-width:260px; display:none">
              <option value="">— Select subject —</option>
            </select>
          </div>
        @endif
      </div>

      {{-- Column 2: Period --}}
      <div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <label>Period</label>
          <select id="period" class="select">
            <option value="monthly" selected>Monthly</option>
            <option value="yearly">Yearly</option>
            <option value="custom">Custom Range</option>
            <option value="lifetime">Lifetime</option>
          </select>

          <div id="wrap-month" style="display:flex;gap:8px;align-items:center">
            <select id="year" class="select">
              @php $curY=(int)date('Y'); @endphp
              @foreach($years as $y)
                <option value="{{ $y }}" {{ $y===$curY ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
            <select id="month" class="select">
              @php $curM=(int)date('n'); @endphp
              @foreach($months as $mval=>$mname)
                <option value="{{ $mval }}" {{ (int)$mval===$curM ? 'selected' : '' }}>{{ $mname }}</option>
              @endforeach
            </select>
          </div>

          <div id="wrap-year" style="display:none;gap:8px;align-items:center">
            <select id="year_only" class="select">
              @foreach($years as $y)
                <option value="{{ $y }}" {{ $y===$curY ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>

          <div id="wrap-custom" style="display:none;gap:8px;align-items:center">
            <input id="date_from" type="date" class="input">
            <span>to</span>
            <input id="date_to" type="date" class="input">
          </div>

          <button id="btn-load" class="btn btn-primary"><i class="fa fa-play"></i> Load</button>
          <button id="btn-reset" class="btn"><i class="fa fa-undo"></i> Reset</button>
        </div>
      </div>
    </div>
  </div>

  {{-- REPORT --}}
  <div id="report-wrap" class="section" style="display:none">

    {{-- Actions --}}
    <div class="no-print" style="display:flex;justify-content:flex-end;margin-bottom:8px;gap:8px">
      <button id="btn-print-2" class="btn btn-primary" disabled><i class="fa fa-print"></i> Print</button>
    </div>

    {{-- Compact header --}}
    <div class="mini-header">
      <div class="mini-left">
        <div class="mini-text">
          <div class="mini-name">{{ $generalSetting->institute ?? 'Institution Name' }}</div>
          @if(!empty($generalSetting->salogan)) <div class="mini-slogan">{{ $generalSetting->salogan }}</div> @endif
          @if(!empty($generalSetting->address)) <div class="mini-address">{{ $generalSetting->address }}</div> @endif
        </div>
      </div>
      <div class="mini-right">
        <table class="mini-meta">
          <tr><td>{{ $isStudent?'Student':'Staff' }}</td><td>:</td><td id="hdr-name">—</td></tr>
          <tr><td>Reg No</td>       <td>:</td><td id="hdr-reg">—</td></tr>
          @unless($isStudent)
          <tr><td>Designation</td>  <td>:</td><td id="hdr-desig">—</td></tr>
          @endunless
          <tr><td>Print Date</td>   <td>:</td><td id="hdr-print">—</td></tr>
        </table>
      </div>
    </div>

    {{-- Title + sub --}}
    <div id="attn-title" class="attn-title">Attendance Card</div>
    <div id="report-sub"></div>

    {{-- Scorecard --}}
    <div id="scorecard" class="scorecard"></div>

    <div class="table-wrap">
      <div class="loading" id="loading"><div class="spinner"></div></div>
      <table class="report" id="report-table">
        <thead id="thead"></thead>
        <tbody id="tbody"></tbody>
        <tfoot id="tfoot-legend" class="print-legend"></tfoot>
      </table>
    </div>

    <div class="legend" id="legend"></div>
  </div>

</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<script>
(function($){
const KIND = @json($kind); // 'student' | 'staff'
const ENDPOINT_SEARCH   = "{{ route('attendance.reports.individual.search') }}";
const ENDPOINT_DATA     = "{{ route('attendance.reports.individual.data') }}";
const ENDPOINT_SUBJECTS = "{{ route('attendance.reports.individual.subjects') }}";

/* utils */
function nowStr(){
  const d=new Date();
  const y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0'), day=String(d.getDate()).padStart(2,'0');
  return `${y}-${m}-${day}`;
}
function setPrintDate(){ $('#hdr-print').text(nowStr()); }
function setFiltersVisible(show){
  const lbl = show ? 'Hide Filters' : 'Show Filters';
  $('#filters-wrap').toggleClass('hidden', !show);
  $('#btn-filters').html(`<i class="fa fa-sliders-h"></i> ${lbl}`);
}
$('#btn-filters').on('click', ()=> setFiltersVisible($('#filters-wrap').hasClass('hidden')));
setFiltersVisible(true);

/* period switch */
function syncPeriodVis(){
  const p = $('#period').val();
  $('#wrap-month').toggle(p==='monthly');
  $('#wrap-year').toggle(p==='yearly');
  $('#wrap-custom').toggle(p==='custom');
}
$('#period').on('change', syncPeriodVis); syncPeriodVis();

/* students: show subject select only when report_type=subject */
function syncReportType(){
  const rt = $('#report_type').val();
  $('#subject_id').css('display', rt==='subject' ? '' : 'none');
}
if (KIND==='student'){ $('#report_type').on('change', syncReportType); syncReportType(); }

/* Load subjects into dropdown (students only) */
function loadSubjects(){
  if (KIND!=='student') return;
  const $sel = $('#subject_id');
  $sel.empty().append('<option value="">— Select subject —</option>');
  $.get(ENDPOINT_SUBJECTS).done(resp=>{
    if(!resp || resp.ok===false) return;
    (resp.rows||[]).forEach(r=>{
      $sel.append(`<option value="${r.id}">${r.name}</option>`);
    });
  });
}
loadSubjects();

/* search */
$('#btn-search').on('click', function(){
  const q = $('#q').val().trim();
  $('#result').empty().append('<option value="">— Select from results —</option>');
  if(!q){ return; }
  $.get(ENDPOINT_SEARCH, {kind:KIND, q:q}).done(resp=>{
    if(!resp || resp.ok===false) return;
    (resp.rows||[]).forEach(r=>{
      const hint = r.hint ? ` — ${r.hint}` : '';
      $('#result').append(`<option value="${r.id}" data-reg="${r.reg_no||''}">${r.label}${hint}</option>`);
    });
  });
});
$('#result').on('change', function(){
  const id = $(this).val();
  $('#person_id').val(id || '');
});

/* load */
function gatherParams(){
  const period = $('#period').val();
  const p = {
    kind: KIND,
    person_id: $('#person_id').val(),
    period: period
  };
  if(period==='monthly'){
    p.year = $('#year').val();
    p.month= $('#month').val();
  }else if(period==='yearly'){
    p.year = $('#year_only').val();
  }else if(period==='custom'){
    p.date_from = $('#date_from').val();
    p.date_to   = $('#date_to').val();
  }
  if(KIND==='student'){
    p.report_type = $('#report_type').val();
    p.subject_id  = $('#subject_id').val() || '';
  }
  return p;
}

function showNotice(type,text){
  let n = $('#notice');
  if(!n.length){
    $('.table-wrap').before('<div id="notice" class="notice" role="alert" aria-live="polite"></div>');
    n = $('#notice');
  }
  n.removeClass('info error').addClass('notice '+type).text(text).show();
}
function hideNotice(){ $('#notice').hide(); }

function loadData(){
  const params = gatherParams();
  if(!params.person_id){ showNotice('error','Pick a person first.'); return; }
  if(KIND==='student' && params.report_type==='subject' && !params.subject_id){
    showNotice('error','Select a subject.'); return;
  }
  hideNotice();
  $('#report-wrap').show(); $('#loading').css('display','flex');
  $('#btn-print, #btn-print-2').prop('disabled', true);

  $.get(ENDPOINT_DATA, params)
    .done(resp=>{
      if(!resp || resp.ok===false){
        showNotice('error', resp && resp.msg ? resp.msg : 'Failed to load.');
        return;
      }
      hideNotice();
      renderReport(resp);
      $('#btn-print, #btn-print-2').prop('disabled', false);
    })
    .fail((xhr)=>{
      const txt = (xhr && xhr.status) ? `Server error ${xhr.status} ${xhr.statusText}` : 'Network/server error.';
      let extra = '';
      if (xhr && xhr.responseText) {
        try {
          const j = JSON.parse(xhr.responseText);
          if (j && j.message) extra = ' — ' + j.message;
        } catch(e) {/* ignore */}
      }
      showNotice('error', txt + extra);
    })
    .always(()=> $('#loading').hide());
}
$('#btn-load').on('click', loadData);

/* reset */
$('#btn-reset').on('click', function(){
  $('#q').val(''); $('#result').val(''); $('#person_id').val('');
  $('#period').val('monthly'); syncPeriodVis();
  const d=new Date(); $('#year').val(String(d.getFullYear())); $('#month').val(String(d.getMonth()+1));
  $('#year_only').val(String(d.getFullYear())); $('#date_from').val(''); $('#date_to').val('');
  if(KIND==='student'){ $('#report_type').val('regular'); $('#subject_id').val(''); syncReportType(); }
  $('#report-wrap').hide(); $('#thead,#tbody,#tfoot-legend,#legend').empty(); $('#scorecard').empty();
  $('#btn-print, #btn-print-2').prop('disabled', true);
});

/* fullscreen */
function isFullscreen(){ return !!(document.fullscreenElement||document.webkitFullscreenElement||document.msFullscreenElement); }
function reqFs(el){ if(el.requestFullscreen) return el.requestFullscreen(); if(el.webkitRequestFullscreen) return el.webkitRequestFullscreen(); if(el.msRequestFullscreen) return el.msRequestFullscreen(); }
function exitFs(){ if(document.exitFullscreen) return document.exitFullscreen(); if(document.webkitExitFullscreen) return document.webkitExitFullscreen(); if(document.msExitFullscreen) return document.msExitFullscreen(); }
function applyFsBackground(on){
  const el=document.querySelector('.container-fluid'); if(!el) return;
  if(on){ const cs=window.getComputedStyle(document.body); el.style.background=cs.background||cs.backgroundColor||''; }
  else{ el.style.background=''; }
}
function updateFsBtn(){
  const on=isFullscreen();
  $('#btn-fullscreen').html(`<i class="fa ${on?'fa-compress':'fa-expand'}"></i> ${on?'Exit Fullscreen':'Fullscreen'}`);
  $('.container-fluid').toggleClass('is-fs', on); applyFsBackground(on);
}
$('#btn-fullscreen').on('click', function(e){ e.preventDefault(); const target=document.querySelector('.container-fluid')||document.documentElement; if(isFullscreen()) exitFs(); else reqFs(target); });
document.addEventListener('fullscreenchange', updateFsBtn);
document.addEventListener('webkitfullscreenchange', updateFsBtn);
document.addEventListener('msfullscreenchange', updateFsBtn);

/* print */
function doPrint(){ setPrintDate(); window.print(); }
$('#btn-print, #btn-print-2').on('click', doPrint);
window.addEventListener('beforeprint', setPrintDate);

/* render */
function renderReport(resp){
  // header
  $('#hdr-name').text(resp.person?.name || '—');
  $('#hdr-reg').text(resp.person?.reg_no || '—');
  if(KIND==='staff') $('#hdr-desig').text(resp.person?.designation || '—');
  setPrintDate();

  // title + sub
  const start = resp.range?.start || '', end = resp.range?.end || '';
  let title = resp.type || 'Attendance Card';
  if (KIND==='student' && resp.type==='Subject Attendance' && resp.subject?.name) {
    title = `Subject Attendance — ${resp.subject.name}`;
  }
  $('#attn-title').text(title);
  $('#report-sub').text(start && end ? `${start}  to  ${end}` : '');

  // scorecard
  const sc = $('#scorecard').empty();
  const totals = resp.totals || {};
  ['P','A','L','LV','EL','H'].forEach(k=>{
    sc.append(`<div class="sc-pill">${k}: <span class="kv">${totals[k]||0}</span></div>`);
  });
  if (resp.total_hm) {
    sc.append(`<div class="sc-pill">Total Hours: <span class="kv">${resp.total_hm}</span></div>`);
  }

  // legend
  const legend = resp.legend||[];
  const colors = resp.colors||{};
  const makeBadge = (s)=> `<span class="badge" style="border-color:${s.color};color:${s.color}">${s.code} = ${s.label}</span>`;
  $('#legend').html(legend.map(makeBadge).join(' '));

  // table
  const $th=$('#thead').empty(), $tb=$('#tbody').empty(), $tf=$('#tfoot-legend').empty();
  const rows = resp.rows || [];
  $th.append(`
    <tr>
      <th>Date</th>
      <th class="tcenter">Status</th>
      <th>Check-in</th>
      <th>Check-out</th>
      <th>Total</th>
      <th>Notes</th>
    </tr>`
  );

  if (rows.length) {
    rows.forEach(r=>{
      const code = r.status || '';
      const col  = colors[code] || '';
      const style= col ? ` style="color:${col};font-weight:700"` : '';
      $tb.append(`
        <tr>
          <td>${r.date||''}</td>
          <td class="tcenter"><span${style}>${code}</span></td>
          <td>${r.in||''}</td>
          <td>${r.out||''}</td>
          <td>${r.total||''}</td>
          <td></td>
        </tr>`
      );
    });
  } else {
    $tb.append('<tr><td colspan="6" class="tcenter" style="padding:10px;color:#64748b">No attendance in this period.</td></tr>');
  }

  $tf.append(`<tr><td colspan="6" style="text-align:center;padding-top:6px;border-top:1px dashed #cbd5e1">${legend.map(makeBadge).join(' ')}</td></tr>`);
}

/* init */
setPrintDate();

})(jQuery);
</script>
@endsection
