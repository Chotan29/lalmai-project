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

/* Top controls */
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding:0 8px}
.header h1{font-weight:700;font-size:22px;margin:0;color:var(--dark)}
.header p{color:var(--secondary);margin:4px 0 0}
.controls{display:flex;gap:10px;flex-wrap:wrap}
.select{border:1px solid var(--border);border-radius:8px;padding:8px 10px}
.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:8px 12px;cursor:pointer;border:1px solid var(--border);background:#fff}
.btn-primary{background:#346da5;color:#fff;border:none}
.btn:disabled{opacity:.6;cursor:not-allowed}

/* Sections */
.section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);padding:16px;margin-bottom:16px}
.section-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.section-icon{width:36px;height:36px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center}
.section-title{font-weight:700;color:var(--dark)}

/* Hierarchy list */
.hierarchy{display:flex;gap:12px;overflow-x:auto;padding:6px 0}
.hlevel{min-width:220px;border:1px solid var(--border);border-radius:10px;box-shadow:var(--shadow);flex-shrink:0}
.hlevel .h-title{background:#f8fafc;padding:8px 10px;border-bottom:1px solid var(--border);font-weight:600}
.hlevel .h-items{max-height:360px;overflow:auto;padding:8px}
.hitem{padding:6px 10px;border-radius:8px;background:#f8fafc;margin-bottom:6px;cursor:pointer;border:1px solid transparent}
.hitem:hover{background:#f1f5f9;border-color:var(--border)}
.hitem.active{background:var(--primary);color:#fff;border-color:var(--primary)}

#report-wrap{display:none}

/* ===== Compact single-row header ===== */
.mini-header{
  display:flex; align-items:center; justify-content:space-between; gap:16px;
  border-bottom:1px solid #e5e7eb; padding:8px 4px; margin-bottom:8px;
  page-break-inside:avoid;
}
.mini-left{display:flex; align-items:center; gap:10px; min-width:0; flex:1}
.mini-logo img{max-height:50px; width:auto; display:block}
.mini-text{min-width:0}
.mini-name{font-weight:800; font-size:18px; line-height:1.1; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis}
.mini-slogan{font-size:11px; color:#6b7280; line-height:1.2}
.mini-address{font-size:11px; color:#6b7280; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis}

.mini-right{font-size:12px; flex-shrink:0}
.mini-meta{border-collapse:collapse}
.mini-meta td{padding:2px 6px; white-space:nowrap; line-height:1.2}
.mini-meta td:first-child{color:#334155}

/* Small title and teacher/course row */
.attn-title{ text-align:center; font-weight:700; text-transform:uppercase; font-size:18px; margin:6px 0 6px }
.ph-lines{display:flex; gap:16px; justify-content:space-between; margin:0 0 6px}
.ph-line{flex:1; font-size:12px}
.dots{display:inline-block;border-bottom:1px dotted #111827;min-width:200px;line-height:1.2}

/* Meta + legend above table */
#report-meta{display:flex;justify-content:space-between;align-items:center;margin:6px 0 8px}
.badge{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:3px 8px;font-size:.8rem}
.badge.info{background:#eef2ff;color:#1d4ed8;border-color:#c7d2fe}
.legend .badge{margin-left:6px;margin-bottom:4px}

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

/* Notices + loading */
.notice{display:none;border-radius:10px;padding:10px 12px;margin:10px 0;border:1px solid}
.notice.info{background:#eef2ff;color:#1d4ed8;border-color:#bfdbfe}
.notice.error{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
.loading{display:none;position:absolute;inset:0;background:rgba(255,255,255,.7);align-items:center;justify-content:center;z-index:10}
.loading .spinner{border:4px solid #e5e7eb;border-top:4px solid var(--primary);border-radius:50%;width:36px;height:36px;animation:spin 1s linear infinite}
@keyframes spin{100%{transform:rotate(360deg)}}

/* Print */
@media print{
  .no-print{display:none!important}
  #report-wrap{display:block}
  .table-wrap{overflow:visible}
  .mini-name{font-size:16pt}
  .mini-slogan,.mini-address,.mini-right{font-size:10pt}
  .attn-title{font-size:16pt; margin:4px 0}
  table.report th, table.report td{font-size:10px;padding:3px 5px}
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
      <button id="btn-print" class="btn btn-primary" disabled><i class="fa fa-print"></i> Print</button>
      <button id="btn-reset" class="btn"><i class="fa fa-undo"></i> Reset</button>
    </div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  {{-- 1) Year & Month FIRST --}}
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

  {{-- 3) Report --}}
  <div class="section" id="report-wrap">

    {{-- compact single-row header --}}
    <div class="mini-header">
      <div class="mini-left">
        {{-- <div class="mini-logo">
          @if(isset($generalSetting->logo))
            <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}"
                 alt="{{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Logo' }}">
          @endif
        </div> --}}
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
          {{-- <tr><td>Faculty</td>   <td>:</td><td id="hdr-fac">—</td></tr> --}}
          <tr><td>Semester</td>  <td>:</td><td id="hdr-sem">—</td></tr>
          <tr><td>Batch</td>     <td>:</td><td id="hdr-batch">—</td></tr>
        </table>
      </div>
    </div>

    <div class="attn-title">Class Attendance Sheet</div>
    <div class="ph-lines">
      <div class="ph-line">Teacher Name&nbsp;:&nbsp;<span id="hdr-teacher" class="dots">&nbsp;</span></div>
      <div class="ph-line">Course Code/Title&nbsp;:&nbsp;<span id="hdr-course" class="dots">&nbsp;</span></div>
    </div>

    <div id="report-meta">
      <div>
        <div id="report-title" class="badge info">Attendance</div>
        <div id="report-sub" style="color:#475569;margin-top:4px;font-size:.9rem"></div>
      </div>
      <div id="legend" class="legend"></div>
    </div>

    <div id="notice" class="notice" role="alert" aria-live="polite"></div>

    <div class="table-wrap">
      <div class="loading" id="loading"><div class="spinner"></div></div>
      <table class="report" id="report-table">
        <thead id="thead"></thead>
        <tbody id="tbody"></tbody>
      </table>
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
function clearBelow(level){
  const order=['department_head','department','faculty','semester','batch','subject'];
  const idx=order.indexOf(level);
  for(let i=idx+1;i<order.length;i++){
    const lv=order[i], hid=hiddenMap[lv]||lv+'_id';
    $('#'+lv+'s-level').hide(); $('#'+lv+'s-container').empty(); $('#'+hid).val('');
  }
  scheduleAutoLoad();
}
function renderList(containerId, level, items){
  const $c=$('#'+containerId).empty();
  if(items && Object.keys(items).length){
    $.each(items,(id,text)=>$c.append(`<div class="hitem" data-level="${level}" data-id="${id}">${text}</div>`));
  }else $c.html('<div class="text-muted">No items</div>');
}
function fetchList(key,parentId){
  const ep=ENDPOINTS[key]; if(!ep) return $.Deferred().resolve({}).promise();
  const level=singular(key); const cacheKey=String(parentId||'');
  if(ep.cache[cacheKey]){ renderList(key+'-container',level,ep.cache[cacheKey]); $('#'+key+'-level').show(); return $.Deferred().resolve(ep.cache[cacheKey]).promise(); }
  const data={}; data[ep.param]=parentId;
  return $.get(ep.url,data).then(resp=>{ ep.cache[cacheKey]=(resp||{}); renderList(key+'-container',level,ep.cache[cacheKey]); $('#'+key+'-level').show(); return ep.cache[cacheKey]; });
}

function showNotice(type,text){ const $n=$('#notice'); $n.removeClass('info error').addClass('notice '+type).text(text).show(); }
function hideNotice(){ $('#notice').hide().text('').removeClass('info error'); }

/* auto-load */
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

function scheduleAutoLoad(){
  const p=getParams();
  if(!isReady(p)){ $('#report-wrap').hide(); $('#btn-print').prop('disabled',true); hideNotice(); return; }
  const sig=JSON.stringify(p);
  if(sig===lastSig) return;
  if(debounceTimer) clearTimeout(debounceTimer);
  debounceTimer=setTimeout(()=>loadData(p,sig),250);
}

function loadData(params,sig){
  lastSig=sig;
  if(inflight && typeof inflight.abort==='function') inflight.abort();

  $('#report-wrap').show(); $('#loading').css('display','flex'); $('#btn-print').prop('disabled',true); hideNotice();

  inflight=$.get(ENDPOINTS.data.url,params)
    .done(resp=>{
      if(!resp || resp.ok===false){
        const msg=(resp && resp.msg)?resp.msg:'Failed to load Student Monthly Attendance Report.';
        $('#thead').empty(); $('#tbody').empty(); $('#report-title').text('Attendance'); $('#report-sub').text(''); $('#legend').empty();
        showNotice('error',msg); $('#btn-print').prop('disabled',true); return;
      }
      hideNotice(); buildTable(resp); $('#btn-print').prop('disabled',false);
    })
    .fail((xhr,status)=>{
      if(status!=='abort'){ $('#thead').empty(); $('#tbody').empty(); showNotice('error','Network/server error while loading the report.'); $('#btn-print').prop('disabled',true); }
    })
    .always(()=>{ $('#loading').hide(); inflight=null; });
}

/* clicks */
$(document).on('click','.hitem', async function(){
  const level=$(this).data('level'), id=String($(this).data('id'));
  $(this).siblings().removeClass('active'); $(this).addClass('active'); setPick(level,id);
  if(level==='department_head'){ clearBelow('department_head'); await fetchList('departments',id); return; }
  if(level==='department'){      clearBelow('department');      await fetchList('faculties',id);   return; }
  if(level==='faculty'){         clearBelow('faculty');         await fetchList('semesters',id);   return; }
  if(level==='semester'){        clearBelow('semester');        await $.when(fetchList('batches',id), fetchList('subjects',id)); scheduleAutoLoad(); return; }
  if(level==='batch' || level==='subject'){ scheduleAutoLoad(); }
});
$('#year,#month').on('change', scheduleAutoLoad);

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
  const facLbl=$('.hitem[data-level="faculty"].active').text()||'—';
  const subjLbl=$('.hitem[data-level="subject"].active').text()||null;

  // fill compact header right meta + lines
  $('#hdr-batch').text(batchLbl);
  $('#hdr-dept').text(deptLbl);
  $('#hdr-fac').text(facLbl);
  $('#hdr-sem').text(semLbl);
  $('#hdr-teacher').text(meta.teacher ? (' '+meta.teacher) : ' ');
  $('#hdr-course').text(subjLbl ? (' '+subjLbl) : ' ');

  // top badge + subtitle
  $('#report-title').text(resp.type||'Attendance');
  let sub = `${semLbl ? (semLbl + ' • ') : ''}${batchLbl} • ${m} ${y}`;
  if (resp.type==='Subject Attendance' && subjLbl) sub = `${sub} • ${subjLbl}`;
  $('#report-sub').text(sub);

  // legend
  const $leg=$('#legend').empty();
  if(legend && legend.length){
    legend.forEach(s=>{
      const txt = `${s.code} = ${s.label}`;
      $leg.append(`<span class="badge" style="border-color:${s.color};color:${s.color}">${escapeHtml(txt)}</span>`);
    });
  }

  // table head
  const $th=$('#thead').empty();
  const $trH=$('<tr/>');
  $trH.append('<th>Reg No</th>');
  $trH.append('<th>Student Name</th>');
  days.forEach(d=>$trH.append(`<th style="text-align:center">${d}</th>`));
  $trH.append('<th style="text-align:center">P</th>');
  $trH.append('<th style="text-align:center">A</th>');
  $trH.append('<th style="text-align:center">L</th>');
  $trH.append('<th style="text-align:center">LV</th>');
  $trH.append('<th style="text-align:center">EL</th>');
  $trH.append('<th style="text-align:center">H</th>');
  $th.append($trH);

  // table body
  const $tb=$('#tbody').empty();
  if(!rows.length){
    showNotice('info','No students/data found for the selected hierarchy and month.');
    $tb.append('<tr><td colspan="'+(2+days.length+6)+'" class="text-muted" style="padding:10px">No rows to display</td></tr>');
    return;
  }

  rows.forEach(r=>{
    const $tr=$('<tr/>');
    $tr.append(`<td>${escapeHtml(r.reg_no||'—')}</td>`);
    $tr.append(`<td>${escapeHtml(r.name||'—')}</td>`);
    days.forEach(d=>{
      const code=(r.d && r.d[d]) ? r.d[d] : '';
      const col=colors[code]||'';
      const style= col ? ` style="color:${col}"` : '';
      $tr.append(`<td style="text-align:center"><span class="symbol"${style}>${code||''}</span></td>`);
    });
    const c=r.count||{};
    $tr.append(`<td style="text-align:center">${c.P||0}</td>`);
    $tr.append(`<td style="text-align:center">${c.A||0}</td>`);
    $tr.append(`<td style="text-align:center">${c.L||0}</td>`);
    $tr.append(`<td style="text-align:center">${c.LV||0}</td>`);
    $tr.append(`<td style="text-align:center">${c.EL||0}</td>`);
    $tr.append(`<td style="text-align:center">${c.H||0}</td>`);
    $tb.append($tr);
  });
}

/* buttons */
$('#btn-print').on('click', ()=>window.print());
$('#btn-reset').on('click', function(){
  $('.hitem').removeClass('active');
  $('#department_head_id,#department_id,#faculty_id,#semester_id,#student_batch_id,#subject_id').val('');
  $('#departments-level,#faculties-level,#semesters-level,#batches-level,#subjects-level').hide();
  $('#departments-container,#faculties-container,#semesters-container,#batches-container,#subjects-container').empty();

  const now=new Date();
  $('#year').val(String(now.getFullYear()));
  $('#month').val(String(now.getMonth()+1));

  lastSig=''; $('#report-wrap').hide(); $('#btn-print').prop('disabled',true); hideNotice();
});

/* kick off */
scheduleAutoLoad();

})(jQuery);
</script>
@endsection
