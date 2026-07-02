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
.header p{color:var(--secondary);margin:4px 0 0}
.controls{display:flex;gap:10px;flex-wrap:wrap}
.select{border:1px solid var(--border);border-radius:8px;padding:4px 10px}
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

/* Hierarchy list (fixed layout) */
.hierarchy{
  display:flex; gap:12px; overflow-x:auto; padding:6px 0;
  -webkit-overflow-scrolling:touch; scroll-behavior:smooth; flex-wrap:nowrap;
}
.hlevel{
  flex:0 0 260px; min-width:260px;
  border:1px solid var(--border); border-radius:10px; box-shadow:var(--shadow);
  background:#fff; flex-shrink:0;
}
.hlevel .h-title{background:#f8fafc;padding:8px 10px;border-bottom:1px solid var(--border);font-weight:600}
.hlevel .h-items{max-height:360px;overflow:auto;padding:8px}
.hitem{
  padding:6px 10px;border-radius:8px;background:#f8fafc;margin-bottom:6px;cursor:pointer;border:1px solid transparent;
  white-space:normal;
}
.hitem:hover{background:#f1f5f9;border-color:var(--border)}
.hitem.active{background:var(--primary);color:#fff;border-color:var(--primary)}

#report-wrap{display:none}

/* Report action bar */
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

/* Teacher/Course/PrintDate line */
.ph-lines{display:flex; gap:16px; justify-content:space-between; margin:6px 0 6px}
.ph-line{flex:1; font-size:12px}
.dots{display:inline-block;border-bottom:1px dotted #111827;min-width:180px;line-height:1.2}
#hdr-teacher,#hdr-course,#hdr-print-date{ font-weight:700; }
@media print{ #hdr-teacher,#hdr-course,#hdr-print-date{ font-weight:900; } }

/* On-screen legend at bottom */
.report-meta-bottom{
  margin-top:10px; display:flex; justify-content:center;
  border-top:1px dashed #cbd5e1; padding-top:8px;
}
.legend .badge{
  display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.8rem;margin:0 6px 4px;background:none;
}
.badge.info{background:#eef2ff;color:#1d4ed8;border-color:#c7d2fe}

/* Table */
.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:12px;position:relative}
table.report{border-collapse:separate;border-spacing:0;width:100%}
table.report th, table.report td{border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;padding:6px 8px;font-size:.86rem;white-space:nowrap;background:#fff}
table.report th{position:sticky;top:0;background:#f8fafc;z-index:2}
table.report th:first-child, table.report td:first-child{position:sticky;left:0;background:#fff;z-index:3}
table.report th:nth-child(2), table.report td:nth-child(2){position:sticky;left:140px;background:#fff;z-index:3}
table.report th:first-child{left:0;width:140px}
table.report th:nth-child(2){left:140px;width:220px}
.symbol{font-weight:700}

/* Strong headers + cells */
.th-strong{ font-weight:800 !important; }
@media print{ .th-strong{ font-weight:900 !important; } }
.td-strong{ font-weight:700; }
@media print{ .td-strong{ font-weight:900; } }

/* Loading + notice (optional) */
.notice{display:none;border-radius:10px;padding:10px 12px;margin:10px 0;border:1px solid}
.notice.info{background:#eef2ff;color:#1d4ed8;border-color:#bfdbfe}
.notice.error{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
.loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.7);align-items:center;justify-content:center;z-index:10}
.loading .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--primary);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
@keyframes spin{100%{transform:rotate(360deg)}}

/* Print helpers — tfoot legend repeats */
tfoot.print-legend{display:none}
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
      <h1>Student Monthly Attendance Report</h1>
      <p>Pick <strong>Year & Month</strong> then choose the hierarchy (Semester & Batch required). Select a Subject to switch to Subject Attendance.</p>
    </div>
    <div class="controls">
      <button id="btn-filters" class="btn btn-soft"><i class="fa fa-sliders-h"></i> Hide Filters</button>
      <button id="btn-fullscreen" class="btn btn-soft"><i class="fa fa-expand"></i> Fullscreen</button>
      <button id="btn-reset" class="btn btn-soft"><i class="fa fa-undo"></i> Reset</button>
        <a href="{{ route('attendance.reports.staff.monthly') }}" class="btn btn-primary"><i class="fas fa-users"></i> Staff</a>
    </div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  {{-- FILTERS (visible by default) --}}
  <div id="filters-wrap">
    {{-- 1) Year & Month --}}
    <div class="section no-print">
      <div class="section-head">
        <div class="section-icon"><i class="fa fa-calendar-alt"></i></div>
        <div class="section-title">Choose Year & Month</div>
      </div>
      <div class="controls">
        <select id="year" class="select">
          <option value="">Year…</option>
          @php $curY=(int)date('Y'); @endphp
          @foreach($years as $y)
            <option value="{{ $y }}" {{ $y===$curY ? 'selected' : '' }}>{{ $y }}</option>
          @endforeach
        </select>
        <select id="month" class="select">
          <option value="">Month…</option>
          @php $curM=(int)date('n'); @endphp
          @foreach($months as $mval=>$mname)
            <option value="{{ $mval }}" {{ (int)$mval===$curM ? 'selected' : '' }}>{{ $mname }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- 2) Academic Hierarchy --}}
    <div class="section no-print">
      <div class="section-head">
        <div class="section-icon"><i class="fa fa-sitemap"></i></div>
        <div class="section-title">Academic Hierarchy</div>
      </div>

      <div class="hierarchy" id="hierarchy">
        <div class="hlevel">
          <div class="h-title"><i class="fa fa-user-tie"></i> Department Heads</div>
          <div class="h-items" id="heads">
            @forelse($department_heads as $id=>$name)
              <div class="hitem" data-level="department_head" data-id="{{ $id }}">{{ $name }}</div>
            @empty
              <div class="text-muted">No department heads</div>
            @endforelse
          </div>
        </div>

        <div class="hlevel" id="departments-level" style="display:none;">
          <div class="h-title"><i class="fa fa-university"></i> Departments</div>
          <div class="h-items" id="departments-container"></div>
        </div>

        <div class="hlevel" id="faculties-level" style="display:none;">
          <div class="h-title"><i class="fa fa-graduation-cap"></i> Faculties/Programs</div>
          <div class="h-items" id="faculties-container"></div>
        </div>

        <div class="hlevel" id="semesters-level" style="display:none;">
          <div class="h-title"><i class="fa fa-layer-group"></i> Semesters</div>
          <div class="h-items" id="semesters-container"></div>
        </div>

        <div class="hlevel" id="batches-level" style="display:none;">
          <div class="h-title"><i class="fa fa-users"></i> Batches</div>
          <div class="h-items" id="batches-container"></div>
        </div>

        <div class="hlevel" id="subjects-level" style="display:none;">
          <div class="h-title"><i class="fa fa-book"></i> Subjects (optional)</div>
          <div class="h-items" id="subjects-container"></div>
        </div>
      </div>

      {{-- hidden picks --}}
      <input type="hidden" id="department_head_id" value="">
      <input type="hidden" id="department_id" value="">
      <input type="hidden" id="faculty_id" value="">
      <input type="hidden" id="semester_id" value="">
      <input type="hidden" id="student_batch_id" value="">
      <input type="hidden" id="subject_id" value="">
    </div>
  </div>

  {{-- 3) Report --}}
  <div class="section" id="report-wrap">

    {{-- Report actions --}}
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
          <tr><td>Department</td><td>:</td><td id="hdr-dept">—</td></tr>
          <tr><td>Semester</td>  <td>:</td><td id="hdr-sem">—</td></tr>
          <tr><td>Batch</td>     <td>:</td><td id="hdr-batch">—</td></tr>
        </table>
      </div>
    </div>

    {{-- Dynamic Title + centered Year & Month --}}
    <div id="attn-title" class="attn-title">Class Attendance Sheet</div>
    <div id="report-sub"></div>

    {{-- Teacher / Course / Print Date --}}
    <div class="ph-lines">
      <div class="ph-line">Teacher Name&nbsp;:&nbsp;<span id="hdr-teacher" class="dots">&nbsp;</span></div>
      <div class="ph-line">Course Code/Title&nbsp;:&nbsp;<span id="hdr-course" class="dots">&nbsp;</span></div>
      <div class="ph-line">Print Date&nbsp;:&nbsp;<span id="hdr-print-date" class="dots">&nbsp;</span></div>
    </div>

    <div id="notice" class="notice" role="alert" aria-live="polite"></div>
 {{-- SCREEN legend at bottom (hidden in print) --}}
    <div id="report-meta" class="report-meta-bottom">
      <div id="legend" class="legend"></div>
    </div>
    <div class="table-wrap">
      <div class="loading" id="loading"><div class="spinner"></div></div>
      <table class="report" id="report-table">
        <thead id="thead"></thead>
        <tbody id="tbody"></tbody>
        {{-- PRINT-ONLY legend footer (repeats on every page, centered) --}}
        <tfoot id="tfoot-legend" class="print-legend"></tfoot>
      </table>
    </div>

    {{-- SCREEN legend at bottom (hidden in print) --}}
    {{-- <div id="report-meta" class="report-meta-bottom">
      <div id="legend" class="legend"></div>
    </div> --}}
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
  departments: { url:"{{ route('get-departments') }}", param:"department_head_id", cache:{} },
  faculties:   { url:"{{ route('get-faculties') }}",   param:"department_id",     cache:{} },
  semesters:   { url:"{{ route('get-semesters') }}",   param:"faculty_id",        cache:{} },
  batches:     { url:"{{ route('get-batches') }}",     param:"semester_id",       cache:{} },
  subjects:    { url:"{{ route('get-subjects') }}",    param:"semester_id",       cache:{} },
  data:        { url:"{{ route('attendance.reports.students.monthly.data') }}" }
};

const hiddenMap = {
  department_head:'department_head_id',
  department:'department_id',
  faculty:'faculty_id',
  semester:'semester_id',
  batch:'student_batch_id',
  subject:'subject_id'
};

function singular(key){
  return ({departments:'department', faculties:'faculty', semesters:'semester', batches:'batch', subjects:'subject'})[key]||key.replace(/s$/,'');
}
function setPick(level,id){ $('#'+(hiddenMap[level]||level+'_id')).val(id); }

/* Clear below a level and reset hidden fields */
function clearBelow(level){
  const order=['department_head','department','faculty','semester','batch','subject'];
  const idx=order.indexOf(level);
  for(let i=idx+1;i<order.length;i++){
    const lv=order[i], hid=hiddenMap[lv]||lv+'_id';
    $('#'+lv+'s-level').hide();
    $('#'+lv+'s-container').empty();
    $('#'+hid).val('');
  }
  attendanceAutoLoad();
}

/* Render and fetch lists (with cache) */
function renderList(containerId, level, items){
  const $c=$('#'+containerId).empty();
  if(items && Object.keys(items).length){
    $.each(items,(id,text)=>$c.append(`<div class="hitem" data-level="${level}" data-id="${id}">${text}</div>`));
  }else{
    $c.html('<div class="text-muted">No items</div>');
  }
}
function fetchList(key,parentId){
  const ep=ENDPOINTS[key]; if(!ep) return $.Deferred().resolve({}).promise();
  const level=singular(key); const cacheKey=String(parentId||'');
  if(ep.cache[cacheKey]){
    renderList(key+'-container',level,ep.cache[cacheKey]);
    $('#'+key+'-level').css('display','');
    return $.Deferred().resolve(ep.cache[cacheKey]).promise();
  }
  const data={}; data[ep.param]=parentId;
  return $.get(ep.url,data).then(resp=>{
    ep.cache[cacheKey]=(resp||{});
    renderList(key+'-container',level,ep.cache[cacheKey]);
    $('#'+key+'-level').css('display','');
    return ep.cache[cacheKey];
  });
}

/* Notices */
function showNotice(type,text){ const $n=$('#notice'); $n.removeClass('info error').addClass('notice '+type).text(text).show(); }
function hideNotice(){ $('#notice').hide().text('').removeClass('info error'); }

/* ===== Auto-load (attendanceAutoLoad) ===== */
let debounceTimer=null, inflight=null, lastSig='';
function getParams(){
  return {
    department_head_id: $('#department_head_id').val() || '',
    department_id:      $('#department_id').val()      || '',
    faculty_id:         $('#faculty_id').val()         || '',
    semester_id:        $('#semester_id').val()        || '',
    student_batch_id:   $('#student_batch_id').val()   || '',
    subject_id:         $('#subject_id').val()         || '',
    year:               $('#year').val()               || '',
    month:              $('#month').val()              || ''
  };
}
function isReady(p){ return p.semester_id && p.student_batch_id && p.year && p.month; }

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
  debounceTimer=setTimeout(()=>loadData(p,sig),250);
}

function loadData(params,sig){
  lastSig=sig;
  if(inflight && typeof inflight.abort==='function') inflight.abort();

  $('#report-wrap').show(); $('#loading').css('display','flex');
  $('#btn-print-report').prop('disabled',true); hideNotice();

  inflight=$.get(ENDPOINTS.data.url,params)
    .done(resp=>{
      if(!resp || resp.ok===false){
        const msg=(resp && resp.msg)?resp.msg:'Failed to load Student Monthly Attendance Report.';
        $('#thead').empty(); $('#tbody').empty(); $('#tfoot-legend').empty();
        $('#attn-title').text('Class Attendance Sheet'); $('#report-sub').text(''); $('#legend').empty();
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

/* Click chain */
$(document).on('click','.hitem', async function(){
  const level=$(this).data('level'), id=String($(this).data('id'));
  $(this).siblings().removeClass('active'); $(this).addClass('active'); setPick(level,id);

  if(level==='department_head'){ clearBelow('department_head'); await fetchList('departments',id); return; }
  if(level==='department'){      clearBelow('department');      await fetchList('faculties',id);   return; }
  if(level==='faculty'){         clearBelow('faculty');         await fetchList('semesters',id);   return; }
  if(level==='semester'){        clearBelow('semester');        await $.when(fetchList('batches',id), fetchList('subjects',id)); attendanceAutoLoad(); return; }
  if(level==='batch' || level==='subject'){ attendanceAutoLoad(); }
});
$('#year,#month').on('change', attendanceAutoLoad);

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

/* Build table + legend (tfoot for print, bottom bar for screen) */
function buildTable(resp){
  const days=resp.days||[], rows=resp.rows||[], meta=resp.meta||{};
  const fb=paletteFallback();
  const colors=meta.status_colors||fb.colors;
  const legend=meta.status_legend||fb.legend;

  const y=$('#year option:selected').text()||$('#year').val();
  const m=$('#month option:selected').text()||$('#month').val();
  const semLbl=$('.hitem[data-level="semester"].active').text()||'';
  const batchLbl=$('.hitem[data-level="batch"].active').text()||'—';
  const deptLbl=$('.hitem[data-level="department"].active').text()||'—';
  const subjLbl=$('.hitem[data-level="subject"].active').text()||null;

  // Header details
  $('#hdr-batch').text(batchLbl);
  $('#hdr-dept').text(deptLbl);
  $('#hdr-sem').text(semLbl);
  $('#hdr-teacher').text(meta.teacher ? (' '+meta.teacher) : ' ');
  $('#hdr-course').text(subjLbl ? (' '+subjLbl) : ' ');
  setPrintDate();

  // Title + Year & Month only
  const isSubject = (resp.type==='Subject Attendance');
  $('#attn-title').text(isSubject ? 'Subject Attendance Sheet' : 'Regular Attendance Sheet');
  $('#report-sub').text(`${m} ${y}`);

  // Screen legend HTML
  function makeLegendHtml(list){
    if(!list || !list.length) return '';
    return list.map(s=>{
      const txt = `${s.code} = ${s.label}`;
      return `<span class="badge" style="border-color:${s.color};color:${s.color}">${escapeHtml(txt)}</span>`;
    }).join('');
  }
  const legendHtml = makeLegendHtml(legend);
  $('#legend').html(legendHtml);

  // table head
  const $th=$('#thead').empty();
  const $trH=$('<tr/>');
  $trH.append('<th class="th-strong">Reg No</th>');
  $trH.append('<th class="th-strong">Student Name</th>');
  days.forEach(d=>$trH.append(`<th style="text-align:center">${d}</th>`));
  $trH.append('<th class="th-strong" style="text-align:center">P</th>');
  $trH.append('<th class="th-strong" style="text-align:center">A</th>');
  $trH.append('<th class="th-strong" style="text-align:center">L</th>');
  $trH.append('<th class="th-strong" style="text-align:center">LV</th>');
  $trH.append('<th class="th-strong" style="text-align:center">EL</th>');
  $trH.append('<th class="th-strong" style="text-align:center">H</th>');
  $th.append($trH);

  // PRINT legend in tfoot (repeats bottom of each printed page)
  const colSpan = 2 + days.length + 6;
  const $tf = $('#tfoot-legend').empty();
  $tf.append(
    `<tr>
       <td colspan="${colSpan}" style="text-align:center; padding-top:8px; border-top:1px dashed #cbd5e1;">
         <div class="legend" style="display:inline-block">${legendHtml}</div>
       </td>
     </tr>`
  );

  // table body
  const $tb=$('#tbody').empty();
  if(!rows.length){
    showNotice('info','No students/data found for the selected hierarchy and month.');
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
  $('.hitem').removeClass('active');
  $('#department_head_id,#department_id,#faculty_id,#semester_id,#student_batch_id,#subject_id').val('');
  $('#departments-level,#faculties-level,#semesters-level,#batches-level,#subjects-level').hide();
  $('#departments-container,#faculties-container,#semesters-container,#batches-container,#subjects-container').empty();

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

/* kick off */
attendanceAutoLoad();
setPrintDate();

})(jQuery);
</script>
@endsection
