@extends('layouts.master')

@section('css')
<style>
:root{
  --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b; --success:#10b981;
  --warning:#f59e0b; --danger:#ef4444; --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
  --shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.header h1{font-weight:700;font-size:22px;color:var(--dark);margin:0}
.header .sub{color:var(--secondary);font-size:13px}
.btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:8px 12px}
.btn-outline{background:#fff;border:1px solid var(--border);color:#0f172a}
.btn-outline:hover{background:#f1f5f9}
.btn-primary{background:var(--primary);border:0;color:#fff}
.btn-primary:hover{background:#264f7c}

/* Tabs */
.nav-tabs{border-bottom:1px solid var(--border);margin-bottom:12px}
.nav-tabs .nav-link{border:0;color:#334155;font-weight:600;cursor:pointer;padding:10px 12px;display:inline-block}
.nav-tabs .nav-link.active{color:#fff;background:var(--primary);border-radius:10px}
.tab-pane{display:none}
.tab-pane.active{display:block}

/* Sections & controls */
.section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);padding:16px;margin-bottom:16px}
.controls{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.input,.select{border:1px solid var(--border);border-radius:10px;padding:8px 10px;background:#fff}
.select{min-width:160px}
.pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:999px;padding:6px 10px;background:#fff;font-weight:600;cursor:pointer}
.pill.active{background:#e7eefb;color:#204876;border-color:#204876}
.pill.mode{font-size:12px}
.notice{background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12;padding:8px 10px;border-radius:8px}
.help{font-size:12px;color:#64748b}

/* Bulk */
.bulkbar{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:8px}
.bulkbar .count{font-weight:700;color:#0f172a}
.bulkbar .btn{padding:6px 10px}

/* Lists */
.list{display:grid;grid-template-columns:repeat(1,1fr);gap:10px}
@media (min-width:768px){.list{grid-template-columns:repeat(2,1fr)}}
@media (min-width:1200px){.list{grid-template-columns:repeat(3,1fr)}}
.rowcard{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--border);border-left:4px solid #cbd5e1;background:#f8fafc;border-radius:12px;padding:12px;position:relative}
.rowcard.just-updated{border-left-color:var(--primary);box-shadow:0 0 0 3px rgba(52,109,165,.12)}
.rowcard .avatar{width:52px;height:52px;border-radius:12px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-weight:800;color:#475569;flex-shrink:0;overflow:hidden}
.rowcard .avatar img{width:100%;height:100%;object-fit:cover;display:block}
.rowcard .meta{flex:1}
.rowcard .meta h4{margin:0 0 2px;font-size:16px;color:#0f172a}
.rowcard .meta .muted{font-size:12px;color:#64748b}
.badge{display:inline-block;border-radius:999px;padding:3px 8px;font-size:12px;font-weight:700;background:#eef2ff;color:#4338ca}
.btn-xs{padding:6px 8px;border-radius:8px;border:1px solid var(--border);background:#fff;cursor:pointer}
.btn-xs:hover{background:#f1f5f9}
.actions{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}
.actions .status{border:1px solid var(--border);background:#fff;border-radius:999px;padding:6px 10px;font-weight:700;cursor:pointer}
.actions .status[data-code="P"]{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
.actions .status[data-code="A"]{background:#fef2f2;color:#991b1b;border-color:#fecaca}
.actions .status[data-code="L"]{background:#fffbeb;color:#92400e;border-color:#fde68a}
.actions .status[data-code="E"]{background:#eff6ff;color:#1e40af;border-color:#bfdbfe}
.actions .status[data-code="HL"]{background:#f5f3ff;color:#5b21b6;border-color:#ddd6fe}
.actions .status.active{outline:2px solid rgba(0,0,0,.06)}
.empty{padding:20px;text-align:center;color:#64748b;border:1px dashed var(--border);border-radius:12px;background:#fff}

/* Hierarchy */
.hierarchy{display:flex;gap:12px;overflow-x:auto;padding-bottom:4px}
.hlevel{min-width:220px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow)}
.hlevel .head{padding:8px 10px;border-bottom:1px solid var(--border);font-weight:700;background:#f8fafc;border-radius:12px 12px 0 0}
.hlevel .items{max-height:340px;overflow-y:auto;padding:8px}
.hitem{padding:6px 10px;border-radius:10px;background:#f8fafc;margin-bottom:6px;cursor:pointer;display:flex;justify-content:space-between;border:1px solid transparent}
.hitem:hover{background:#f1f5f9;border-color:var(--border)}
.hitem.active{background:#346da5;color:#fff;border-color:#346da5}

/* Scan log */
.scan-log{max-height:160px;overflow:auto;background:#0b1220;color:#cbd5e1;border-radius:10px;padding:10px;font-family:ui-monospace,Consolas,Menlo,monospace}
</style>
@endsection

@section('content')
@php
  $today   = $data['today'] ?? now()->toDateString();
  $statuses= $data['statuses'] ?? collect();
  $heads   = $data['department_heads'] ?? collect();
  $DEFAULT_PER_PAGE = (int)($data['default_per_page'] ?? 100);

  $__STATUSES = collect($statuses ?? [])->map(function($s){
      $arr = is_array($s);
      return [
        'id'    => $arr ? ($s['id']    ?? null) : ($s->id    ?? null),
        'code'  => $arr ? ($s['code']  ?? null) : ($s->code  ?? null),
        'label' => $arr ? ($s['label'] ?? null) : ($s->label ?? null),
        'color' => $arr ? ($s['color'] ?? null) : ($s->color ?? null),
      ];
  })->values();
@endphp

<div class="container-fluid py-3">

  <div class="header">
    <div>
      <h1>Live Attendance</h1>
      <div class="sub">Today’s stream, quick actions & inline updates.</div>
    </div>
    <div class="controls">
      <span class="pill"><i class="far fa-calendar"></i> {{ $today }}</span>
      <button type="button" class="btn btn-outline" id="refresh-btn"><i class="fas fa-sync"></i> Refresh</button>
    </div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  <div class="section">
    <ul class="nav nav-tabs" role="tablist" id="entity-tabs">
      <li class="nav-item"><a class="nav-link active" data-tab-target="#tab-students">Students</a></li>
      <li class="nav-item"><a class="nav-link" data-tab-target="#tab-staff">Staff</a></li>
    </ul>

    <div class="controls" style="margin-bottom:10px">
      <input type="text" id="search" class="input" placeholder="Search name / code …">
      <select id="per_page" class="select">
        @foreach([15,30,60,100,150,200] as $opt)
          <option value="{{ $opt }}" {{ $opt == $DEFAULT_PER_PAGE ? 'selected' : '' }}>{{ $opt }} / page</option>
        @endforeach
      </select>

      <div class="pill" data-code="">All</div>
      <div class="pill" data-code="P">Present</div>
      <div class="pill" data-code="A">Absent</div>
      <div class="pill" data-code="L">Late</div>
      <div class="pill" data-code="E">Excused</div>
      <div class="pill" data-code="HL">Half-Leave</div>

      <span style="flex:1"></span>
      <span class="pill mode" data-mode="auto"   id="mode-auto">Mode: Auto</span>
      <span class="pill mode" data-mode="create" id="mode-create">Create</span>
      <span class="pill mode" data-mode="modify" id="mode-modify">Modify</span>

      <div class="ms-auto" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <input type="text" id="identify-code" class="input" placeholder="Manual / QR / Barcode token">
        <button class="btn btn-primary" id="identify-btn"><i class="fas fa-bolt"></i> Identify</button>
      </div>
    </div>

    {{-- Student hierarchy --}}
    <div id="hier-section" class="hierarchy" aria-live="polite">
      <div class="hlevel">
        <div class="head"><i class="fas fa-user-tie"></i> Department Heads</div>
        <div class="items" id="heads-items">
          @forelse($heads as $id => $label)
            <div class="hitem" data-level="department_head" data-id="{{ $id }}"><span>{{ $label }}</span></div>
          @empty
            <div class="empty">No heads.</div>
          @endforelse
        </div>
      </div>

      <div class="hlevel" id="hl-departments" style="display:none">
        <div class="head"><i class="fas fa-university"></i> Departments</div>
        <div class="items" id="dept-items"></div>
      </div>

      <div class="hlevel" id="hl-faculties" style="display:none">
        <div class="head"><i class="fas fa-graduation-cap"></i> Faculties / Programs</div>
        <div class="items" id="fac-items"></div>
      </div>

      <div class="hlevel" id="hl-semesters" style="display:none">
        <div class="head"><i class="fas fa-layer-group"></i> Semesters</div>
        <div class="items" id="sem-items"></div>
      </div>

      <div class="hlevel" id="hl-batches" style="display:none">
        <div class="head"><i class="fas fa-users"></i> Batches</div>
        <div class="items" id="batch-items"></div>
      </div>

      <div class="hlevel" id="hl-subjects" style="display:none">
        <div class="head"><i class="fas fa-book"></i> Subjects (optional)</div>
        <div class="items" id="subj-items"></div>
      </div>
    </div>

    <div id="subject-msg" class="notice" style="display:none;margin-top:10px;"></div>
    <div id="hier-help" class="help" style="margin-top:6px">Select Department → Faculty → Semester → Batch to load students. If a Subject is chosen and not scheduled today, **Modify** mode still lets you edit existing rows.</div>
  </div>

  <div class="tab-content">
    <div class="tab-pane active" id="tab-students">
      <div class="section">
        <div class="bulkbar">
          <label><input type="checkbox" id="sel-all-students"> Select all on page</label>
          <span class="count" id="sel-count-students">0 selected</span>
          <span style="flex:1"></span>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="P">Present</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="A">Absent</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="L">Late</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="E">Excused</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="HL">Half-Leave</button>
          <button class="btn btn-primary  js-bulk" data-kind="check">Check-in/out</button>
        </div>

        <div id="students-meta" class="small" style="color:#64748b;margin-bottom:6px"></div>
        <div id="students-list" class="list"></div>
        <div id="students-empty" class="empty" style="display:none">No students match your filters.</div>
      </div>
    </div>

    <div class="tab-pane" id="tab-staff">
      <div class="section">
        <div class="bulkbar">
          <label><input type="checkbox" id="sel-all-staff"> Select all on page</label>
          <span class="count" id="sel-count-staff">0 selected</span>
          <span style="flex:1"></span>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="P">Present</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="A">Absent</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="L">Late</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="E">Excused</button>
          <button class="btn btn-outline js-bulk" data-kind="mark" data-code="HL">Half-Leave</button>
          <button class="btn btn-primary  js-bulk" data-kind="check">Check-in/out</button>
        </div>

        <div id="staff-meta" class="small" style="color:#64748b;margin-bottom:6px"></div>
        <div id="staff-list" class="list"></div>
        <div id="staff-empty" class="empty" style="display:none">No staff match your filters.</div>
      </div>
    </div>
  </div>

  <div class="section">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <input type="text" id="manual-box" class="input" placeholder="Focus here, then scan/type…" style="min-width:280px">
      <button class="btn btn-primary" id="manual-send"><i class="fas fa-paper-plane"></i> Submit</button>
    </div>
    <div class="scan-log" id="scan-log" style="margin-top:10px"></div>
  </div>

</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
(function(){
  "use strict";

  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

  var TODAY = @json($today);
  var DEFAULT_PER_PAGE = @json($DEFAULT_PER_PAGE);
  var STATUSES = @json($__STATUSES);

  var ROUTE_LIST   = @json(route('attendance.live.list'));
  var ROUTE_IDENT  = @json(route('attendance.identify'));
  var ROUTE_MARK_T = @json(route('attendance.row.mark', ['attendance' => 'ATTENDANCE_ID']));
  var ROUTE_CHK_T  = @json(route('attendance.row.check', ['attendance' => 'ATTENDANCE_ID']));
  var ROUTE_QMARK  = @json(route('attendance.quick.mark'));
  var ROUTE_QCHECK = @json(route('attendance.quick.check'));
  var ROUTE_BMARK  = @json(route('attendance.bulk.mark'));
  var ROUTE_BCHECK = @json(route('attendance.bulk.check'));

  var R_DEPTS   = @json(route('get-departments'));
  var R_FACS    = @json(route('get-faculties'));
  var R_SEMS    = @json(route('get-semesters'));
  var R_BATCHES = @json(route('get-batches'));
  var R_SUBJS   = @json(route('get-subjects'));

  var state = {
    type: 'students',
    search: '',
    per_page: DEFAULT_PER_PAGE || 100,
    status_code: '',
    dh_id:'', dept_id:'', fac_id:'', sem_id:'', batch_id:'', subj_id:'',
    sel_students: new Set(),
    sel_staff: new Set(),
    last_list: [],
    mode: 'auto',
    mode_effective: 'create',
    lastChanged: null
  };

  function qs(s,ctx){return (ctx||document).querySelector(s);}
  function qsa(s,ctx){return Array.prototype.slice.call((ctx||document).querySelectorAll(s));}

  function setActivePill(code){
    qsa('.pill[data-code]').forEach(function(p){ p.classList.remove('active'); });
    var el = document.querySelector('.pill[data-code="'+(code||'')+'"]');
    if (el) el.classList.add('active');
  }
  function setModePills(){
    qsa('.pill.mode').forEach(function(p){ p.classList.remove('active'); });
    var el = document.querySelector('.pill.mode[data-mode="'+state.mode+'"]');
    if (el) el.classList.add('active');
  }
  setActivePill('');
  setModePills();

  // Tabs
  qsa('.nav-tabs .nav-link').forEach(function(link){
    link.addEventListener('click', function(){
      qsa('.nav-tabs .nav-link').forEach(function(a){a.classList.remove('active');});
      link.classList.add('active');
      var target = link.getAttribute('data-tab-target') || '#tab-students';
      qsa('.tab-pane').forEach(function(p){p.classList.remove('active');});
      var pane = qs(target); if (pane) pane.classList.add('active');

      var hier = qs('#hier-section'), help = qs('#hier-help'), subjMsg = qs('#subject-msg');
      if (target === '#tab-students') { if (hier) hier.style.display = ''; if (help) help.style.display = ''; }
      else { if (hier) hier.style.display = 'none'; if (help) help.style.display = 'none'; if (subjMsg) subjMsg.style.display = 'none'; }

      state.type = (target==='#tab-staff') ? 'staff' : 'students';
      updateSelCounts();
      loadList();
    });
  });

  function initials(name){ if(!name) return '?'; return name.split(/\s+/).map(function(s){return s[0];}).slice(0,2).join('').toUpperCase(); }

  function renderRow(item){
    var attendanceId = item.attendance_id || '';
    var name = item.name || '—';
    var code = item.code || '';
    var last = item.last_status_code || '';
    var updated = item.updated_at ? (' · '+item.updated_at) : '';
    var personType = item.person_type || (state.type==='students'?'student':'staff');
    var pid = item.pid;
    var just = (state.lastChanged && state.lastChanged.type===personType && state.lastChanged.pid===pid) ? ' just-updated' : '';

    var statusBtns = '';
    var list = (STATUSES && STATUSES.length) ? STATUSES : [{code:'P',label:'Present'},{code:'A',label:'Absent'},{code:'L',label:'Late'},{code:'E',label:'Excused'},{code:'HL',label:'Half-Leave'}];
    list.forEach(function(s){
      var activeCls = (last && last===s.code) ? ' active' : '';
      if (attendanceId) {
        var url = ROUTE_MARK_T.replace('ATTENDANCE_ID', attendanceId);
        statusBtns += '<button class="status js-mark'+activeCls+'" data-url="'+url+'" data-code="'+s.code+'">'+(s.label||s.code)+'</button>';
      } else {
        statusBtns += '<button class="status js-qmark'+activeCls+'" data-type="'+personType+'" data-pid="'+pid+'" data-code="'+s.code+'">'+(s.label||s.code)+'</button>';
      }
    });

    var checkBtn = attendanceId
      ? '<button class="btn-xs js-check" data-url="'+ROUTE_CHK_T.replace('ATTENDANCE_ID', attendanceId)+'"><i class="fas fa-sign-in-alt"></i> Check-in/out</button>'
      : '<button class="btn-xs js-qcheck" data-type="'+personType+'" data-pid="'+pid+'"><i class="fas fa-sign-in-alt"></i> Check-in/out</button>';

    var selected = (personType==='student' ? state.sel_students.has(pid) : state.sel_staff.has(pid)) ? 'checked' : '';
    var avatarHtml = item.avatar_url ? ('<img src="'+item.avatar_url+'" alt="">') : initials(name);

    return ''+
    '<div class="rowcard'+just+'" data-attendance="'+(attendanceId||'')+'" data-type="'+personType+'" data-pid="'+pid+'">'+
      '<div><input type="checkbox" class="sel-row" '+selected+'></div>'+
      '<div class="avatar">'+ avatarHtml +'</div>'+
      '<div class="meta">'+
        '<h4>'+ name +'</h4>'+
        '<div class="muted">'+ (code?('Code: '+code+' · '):'') + (last?('Last: <span class="badge">'+last+'</span>'):'No mark yet') + updated +'</div>'+
        '<div class="actions">'+ statusBtns +' '+ checkBtn +'</div>'+
      '</div>'+
    '</div>';
  }

  function applyList(where, list, emptySel){
    state.last_list = list || [];
    var $wrap = qs(where), $empty = qs(emptySel);
    if (!$wrap) return;
    $wrap.innerHTML = '';
    if (list && list.length){
      list.forEach(function(item){ $wrap.insertAdjacentHTML('beforeend', renderRow(item)); });
      if ($empty) $empty.style.display = 'none';
    } else {
      if ($empty) $empty.style.display = '';
    }
    updateSelCounts();
  }

  function setMeta(){
    var scope = [];
    if (state.dept_id)  scope.push('Dept#'+state.dept_id);
    if (state.fac_id)   scope.push('Fac#'+state.fac_id);
    if (state.sem_id)   scope.push('Sem#'+state.sem_id);
    if (state.batch_id) scope.push('Batch#'+state.batch_id);
    if (state.subj_id)  scope.push('Subj#'+state.subj_id);

    var modeTag = (state.mode==='auto')
      ? ('Mode: '+(state.mode_effective==='modify'?'Modify (auto)':'Create (auto)'))
      : ('Mode: '+state.mode.charAt(0).toUpperCase()+state.mode.slice(1));

    var txt = (state.type==='students'?'Students':'Staff')+' · '+TODAY+' · '+modeTag+(scope.length?(' · '+scope.join(' / ')):'');
    (state.type==='students'?qs('#students-meta'):qs('#staff-meta')).textContent = txt;

    setModePills();
  }

  function listParams(){
    return {
      date: TODAY,
      type: state.type==='students' ? 'student' : 'staff',
      q: state.search,
      per_page: state.per_page,
      page: 1,
      status: state.status_code,
      department_head_id: state.dh_id || '',
      department_id:      state.dept_id || '',
      faculty_id:         state.fac_id || '',
      semester_id:        state.sem_id || '',
      batch_id:           state.batch_id || '',
      subject_id:         state.subj_id || '',
      mode:               state.mode || 'auto'
    };
  }

  function loadList(){
    var params = listParams();
    $.get(ROUTE_LIST, params).done(function(resp){
      var subjMsg = qs('#subject-msg'); if (subjMsg){ subjMsg.style.display='none'; subjMsg.textContent=''; }

      if (resp && resp.meta) {
        state.mode_effective = resp.meta.mode_effective || state.mode;
      }

      if (resp && resp.meta && resp.meta.need_hierarchy && state.type==='students'){
        applyList('#students-list', [], '#students-empty');
        qs('#students-empty').textContent = 'Select Department → Faculty → Semester → Batch.';
        setMeta();
        return;
      }

      if (resp && resp.meta && resp.meta.subject_selected && resp.meta.subject_scheduled === false){
        if (state.mode !== 'modify') {
          if (subjMsg){ subjMsg.textContent = resp.meta.subject_message || 'Subject is not scheduled today.'; subjMsg.style.display=''; }
          applyList('#students-list', [], '#students-empty');
          setMeta();
          return;
        }
      }

      var list = (resp && (resp.data || resp.items)) ? resp.data : [];
      if (state.type==='students'){
        applyList('#students-list', list, '#students-empty');
      } else {
        applyList('#staff-list', list, '#staff-empty');
      }
      setMeta();
    }).fail(function(){
      if (state.type==='students'){
        qs('#students-list').innerHTML = '<div class="empty">Failed to load. Click Refresh.</div>';
      } else {
        qs('#staff-list').innerHTML = '<div class="empty">Failed to load. Click Refresh.</div>';
      }
    });
  }

  function afterAction(cardRef){
    state.mode = 'modify';          // force modify so rows stay and are editable
    state.status_code = '';         // clear filter so nothing disappears
    setActivePill('');
    if (cardRef) state.lastChanged = cardRef;
    loadList();
  }

  // Existing row actions
  $(document).on('click', '.js-mark', function(e){
    e.preventDefault();
    var $btn = $(this), url = $btn.data('url'), code = $btn.data('code');
    var card = $btn.closest('.rowcard');
    var type = card.data('type'), pid = parseInt(card.data('pid'),10);
    if (!url || !code) return;
    $btn.prop('disabled', true);
    $.post(url, { code: code, subject_id: (state.subj_id || '') })
      .always(function(){ $btn.prop('disabled', false); })
      .done(function(){ afterAction({type:type, pid:pid}); })
      .fail(function(xhr){ alert('Mark failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error')); });
  });

  $(document).on('click', '.js-check', function(e){
    e.preventDefault();
    var $btn = $(this), url = $btn.data('url');
    var card = $btn.closest('.rowcard');
    var type = card.data('type'), pid = parseInt(card.data('pid'),10);
    if (!url) return;
    $btn.prop('disabled', true);
    $.post(url, {})
      .always(function(){ $btn.prop('disabled', false); })
      .done(function(){ afterAction({type:type, pid:pid}); })
      .fail(function(xhr){ alert('Check failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error')); });
  });

  // Quick actions (create row then mark/check)
  $(document).on('click', '.js-qmark', function(e){
    e.preventDefault();
    var $btn = $(this), type = $btn.data('type'), pid = parseInt($btn.data('pid'),10), code = $btn.data('code');
    $btn.prop('disabled', true);
    $.post(ROUTE_QMARK, { type: type, person_id: pid, code: code, date: TODAY, subject_id: (state.subj_id || '') })
      .always(function(){ $btn.prop('disabled', false); })
      .done(function(){ afterAction({type:type, pid:pid}); })
      .fail(function(xhr){ alert('Mark failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error')); });
  });

  $(document).on('click', '.js-qcheck', function(e){
    e.preventDefault();
    var $btn = $(this), type = $btn.data('type'), pid = parseInt($btn.data('pid'),10);
    $btn.prop('disabled', true);
    $.post(ROUTE_QCHECK, { type: type, person_id: pid, date: TODAY })
      .always(function(){ $btn.prop('disabled', false); })
      .done(function(){ afterAction({type:type, pid:pid}); })
      .fail(function(xhr){ alert('Check failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error')); });
  });

  // Selection & Bulk
  function updateSelCounts(){
    var c = (state.type==='students') ? state.sel_students.size : state.sel_staff.size;
    (state.type==='students' ? $('#sel-count-students') : $('#sel-count-staff')).text(c+' selected');
    (state.type==='students' ? $('#sel-all-students') : $('#sel-all-staff'))
      .prop('checked', c>0 && c === state.last_list.length);
  }

  $(document).on('change', '.sel-row', function(){
    var card = $(this).closest('.rowcard');
    var pid = parseInt(card.data('pid'),10);
    var type = card.data('type');
    if ($(this).is(':checked')) {
      (type==='student' ? state.sel_students : state.sel_staff).add(pid);
    } else {
      (type==='student' ? state.sel_students : state.sel_staff).delete(pid);
    }
    updateSelCounts();
  });

  $('#sel-all-students').on('change', function(){
    if ($(this).is(':checked')) {
      state.sel_students.clear();
      state.last_list.forEach(function(it){ state.sel_students.add(it.pid); });
      $('.rowcard[data-type="student"] .sel-row').prop('checked', true);
    } else {
      state.sel_students.clear();
      $('.rowcard[data-type="student"] .sel-row').prop('checked', false);
    }
    updateSelCounts();
  });
  $('#sel-all-staff').on('change', function(){
    if ($(this).is(':checked')) {
      state.sel_staff.clear();
      state.last_list.forEach(function(it){ state.sel_staff.add(it.pid); });
      $('.rowcard[data-type="staff"] .sel-row').prop('checked', true);
    } else {
      state.sel_staff.clear();
      $('.rowcard[data-type="staff"] .sel-row').prop('checked', false);
    }
    updateSelCounts();
  });

  $(document).on('click', '.js-bulk', function(){
    var kind = $(this).data('kind');
    var code = $(this).data('code') || '';
    var ids  = Array.from(state.type==='students' ? state.sel_students : state.sel_staff);
    if (!ids.length) { alert('Select at least one row.'); return; }

    if (kind === 'check') {
      $.post(ROUTE_BCHECK, { type: (state.type==='students'?'student':'staff'), person_ids: ids, date: TODAY })
        .done(function(){ state.sel_students.clear(); state.sel_staff.clear(); afterAction(null); })
        .fail(function(xhr){ alert('Bulk check failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error')); });
    } else {
      $.post(ROUTE_BMARK, { type: (state.type==='students'?'student':'staff'), person_ids: ids, code: code, date: TODAY, subject_id: (state.subj_id || '') })
        .done(function(){ state.sel_students.clear(); state.sel_staff.clear(); afterAction(null); })
        .fail(function(xhr){ alert('Bulk mark failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error')); });
    }
  });

  // Identify
  function log(msg, cls){
    var box = qs('#scan-log'); if (!box) return;
    var row = document.createElement('div');
    row.textContent = (new Date()).toLocaleTimeString()+' — '+msg;
    row.style.color = (cls==='ok') ? '#a7f3d0' : (cls==='err' ? '#fecaca' : '#cbd5e1');
    box.prepend(row);
  }
  function doIdentify(source){
    var val = (qs('#identify-code').value || '').trim();
    if (!val) { qs('#identify-code').focus(); return; }
    var payload = { code: val, source: source||'manual', date: TODAY, type: (state.type==='students'?'student':'staff') };
    $('#identify-btn').prop('disabled', true);
    $.post(ROUTE_IDENT, payload)
      .always(function(){ $('#identify-btn').prop('disabled', false); })
      .done(function(){ qs('#identify-code').value=''; afterAction(null); log('OK ('+(source||'manual')+'): '+val, 'ok'); })
      .fail(function(xhr){ var m=(xhr.responseJSON&&xhr.responseJSON.message)?xhr.responseJSON.message:'Invalid'; log('ERR ('+(source||'manual')+'): '+val+' — '+m, 'err'); });
  }
  $('#identify-btn').on('click', function(){ doIdentify('manual'); });
  $('#manual-send').on('click', function(){
    var v = (qs('#manual-box').value || '').trim();
    if (!v) { qs('#manual-box').focus(); return; }
    $('#manual-send').prop('disabled', true);
    $.post(ROUTE_IDENT, { code: v, source:'manual', date:TODAY, type:(state.type==='students'?'student':'staff') })
      .always(function(){ $('#manual-send').prop('disabled', false); })
      .done(function(){ qs('#manual-box').value=''; afterAction(null); log('OK (manual): '+v, 'ok'); })
      .fail(function(xhr){ var m=(xhr.responseJSON&&xhr.responseJSON.message)?xhr.responseJSON.message:'Invalid'; log('ERR (manual): '+v+' — '+m, 'err'); });
  });

  // Filters
  $('#search').on('input', function(){ state.search = $(this).val(); loadList(); });
  $('#per_page').on('change', function(){ state.per_page = parseInt($(this).val(),10) || DEFAULT_PER_PAGE; loadList(); });
  qsa('.pill[data-code]').forEach(function(p){
    p.addEventListener('click', function(){
      state.status_code = p.getAttribute('data-code') || '';
      setActivePill(state.status_code);
      loadList();
    });
  });

  // Mode
  qsa('.pill.mode').forEach(function(p){
    p.addEventListener('click', function(){
      var m = p.getAttribute('data-mode');
      state.mode = m;
      setModePills();
      loadList();
    });
  });

  $('#refresh-btn').on('click', function(){ loadList(); });

  // Hierarchy
  function resetBelow(level){
    var order=['department_head','department','faculty','semester','batch','subject'];
    var idx = order.indexOf(level);
    for (var i=idx+1; i<order.length; i++){
      var lv = order[i];
      if(lv==='department'){ $('#hl-departments').hide(); $('#dept-items').empty(); state.dept_id=''; }
      if(lv==='faculty'){ $('#hl-faculties').hide(); $('#fac-items').empty(); state.fac_id=''; }
      if(lv==='semester'){ $('#hl-semesters').hide(); $('#sem-items').empty(); state.sem_id=''; }
      if(lv==='batch'){ $('#hl-batches').hide(); $('#batch-items').empty(); state.batch_id=''; }
      if(lv==='subject'){ $('#hl-subjects').hide(); $('#subj-items').empty(); state.subj_id=''; }
    }
  }
  function fill(container, obj, level){
    var $c = $(container).empty();
    $.each(obj||{}, function(k,v){
      $c.append('<div class="hitem" data-level="'+level+'" data-id="'+k+'"><span>'+v+'</span></div>');
    });
  }

  $(document).on('click','.hitem', function(){
    var $it = $(this);
    var level = $it.data('level'), id = String($it.data('id')||'');
    $it.closest('.items').find('.hitem').removeClass('active'); $it.addClass('active');

    if(level==='department_head'){ state.dh_id = id; resetBelow('department_head');
      $.get(R_DEPTS, { department_head_id: id }).done(function(data){ fill('#dept-items', data||{}, 'department'); $('#hl-departments').show(); });
    }
    if(level==='department'){ state.dept_id = id; resetBelow('department');
      $.get(R_FACS, { department_id: id }).done(function(data){ fill('#fac-items', data||{}, 'faculty'); $('#hl-faculties').show(); });
    }
    if(level==='faculty'){ state.fac_id = id; resetBelow('faculty');
      $.get(R_SEMS, { faculty_id: id }).done(function(data){ fill('#sem-items', data||{}, 'semester'); $('#hl-semesters').show(); });
    }
    if(level==='semester'){ state.sem_id = id; resetBelow('semester');
      $.when($.get(R_BATCHES, { semester_id: id }), $.get(R_SUBJS, { semester_id: id }))
        .done(function(batches, subjects){
          fill('#batch-items', batches[0]||{}, 'batch'); $('#hl-batches').show();
          fill('#subj-items', subjects[0]||{}, 'subject'); $('#hl-subjects').show();
        });
    }
    if(level==='batch'){ state.batch_id = id; }
    if(level==='subject'){ state.subj_id = id; }

    loadList();
  });

  loadList();
})();
</script>
@endsection
