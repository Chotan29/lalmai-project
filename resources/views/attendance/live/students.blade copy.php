@extends('layouts.master')

@section('css')
<style>
:root{
  --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b;
  --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
  --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
  --purple:#7c3aed; --blue:#3b82f6;
  --shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
}

.container-fluid{padding:12px 14px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.header h1{font-weight:800;font-size:22px;color:var(--dark);margin:0}
.header .sub{color:#64748b;font-size:13px}

.btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:8px 12px;cursor:pointer}
.btn-outline{background:#fff;border:1px solid var(--border);color:#0f172a}
.btn-outline:hover{background:#f1f5f9}
.pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:999px;padding:6px 10px;background:#fff;font-weight:700;cursor:pointer}
.pill.active{background:var(--primary-light);color:var(--primary);border-color:var(--primary)}
.input,.select{border:1px solid var(--border);border-radius:10px;padding:8px 10px;background:#fff}
.select{min-width:160px}
.section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);padding:16px;margin-bottom:12px}
.empty{padding:20px;text-align:center;color:#64748b;border:1px dashed var(--border);border-radius:12px;background:#fff}

.nav-tabs{border-bottom:1px solid var(--border);margin-bottom:12px}
.nav-tabs .nav-link{border:0;color:#334155;font-weight:700;cursor:pointer;padding:10px 12px;display:inline-block}
.nav-tabs .nav-link.active{color:#fff;background:var(--primary);border-radius:10px}

.list{display:grid;grid-template-columns:repeat(1,1fr);gap:10px}
@media (min-width:768px){.list{grid-template-columns:repeat(2,1fr)}}
@media (min-width:1200px){.list{grid-template-columns:repeat(3,1fr)}}

.rowcard{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--border);border-left:4px solid #cbd5e1;background:#f8fafc;border-radius:12px;padding:12px;transition:background .12s, border-color .12s}
.rowcard .avatar{width:52px;height:52px;border-radius:12px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-weight:800;color:#475569;flex-shrink:0;overflow:hidden}
.rowcard .avatar img{width:52px;height:52px;object-fit:cover;border-radius:12px;display:block}
.rowcard .meta{flex:1}
.rowcard .meta h4{margin:0 0 2px;font-size:16px;color:#0f172a}
.rowcard .meta .muted{font-size:12px;color:#64748b}
.badge{display:inline-block;border-radius:999px;padding:3px 8px;font-size:12px;font-weight:700;background:#eef2ff;color:#4338ca}

.actions{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}
.actions .status{border:1px solid var(--border);background:#fff;border-radius:999px;padding:6px 10px;font-weight:700;cursor:pointer;opacity:.95}
.actions .status.active{outline:2px solid var(--primary);opacity:1}
.actions .status[data-code="P"]{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
.actions .status[data-code="A"]{background:#fef2f2;color:#991b1b;border-color:#fecaca}
.actions .status[data-code="L"]{background:#fffbeb;color:#92400e;border-color:#fde68a}
.actions .status[data-code="E"]{background:#eff6ff;color:#1e40af;border-color:#bfdbfe}
.actions .status[data-code="HL"]{background:#f5f3ff;color:#5b21b6;border-color:#ddd6fe}
.btn-xs{padding:6px 8px;border-radius:8px;border:1px solid var(--border);background:#fff;cursor:pointer}
.btn-xs:hover{background:#f1f5f9}

.rowcard.tint-P{ background:#ecfdf5 !important; border-left-color:#10b981 !important; }
.rowcard.tint-A{ background:#fef2f2 !important; border-left-color:#ef4444 !important; }
.rowcard.tint-L{ background:#fffbeb !important; border-left-color:#f59e0b !important; }
.rowcard.tint-E{ background:#eff6ff !important; border-left-color:#3b82f6 !important; }
.rowcard.tint-HL{ background:#f5f3ff !important; border-left-color:#7c3aed !important; }

/* Indicator bar (like Scan page) */
.topbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:10px}
.stat{display:inline-flex;gap:8px;align-items:center;border:1px solid var(--border);background:#fff;border-radius:999px;padding:6px 12px;font-weight:800;color:#0f172a}
.stat .dot{width:10px;height:10px;border-radius:50%}
.dot-all{background:#6366f1}
.dot-P{background:#10b981}
.dot-A{background:#ef4444}
.dot-L{background:#f59e0b}
.dot-E{background:#3b82f6}
.dot-HL{background:#7c3aed}
/* online/offline */
#net-stat .dot{width:10px;height:10px;border-radius:50%}
.net-ok{background:#10b981!important}
.net-bad{background:#ef4444!important}

.notice{background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12;padding:8px 10px;border-radius:8px}
.help{font-size:12px;color:#64748b}
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
        'code'  => strtoupper($arr ? ($s['code'] ?? '') : ($s->code ?? '')),
        'label' => $arr ? ($s['label'] ?? null) : ($s->label ?? null),
        'color' => $arr ? ($s['color'] ?? null) : ($s->color ?? null),
      ];
  })->values();
@endphp

<div class="container-fluid">
  <div class="header">
    <div>
      <h1>Live Attendance — Students</h1>
      <div class="sub">Today: <b>{{ $today }}</b></div>
    </div>
    <div class="controls">
      <div class="toggle-pill">
        <label class="toggle-label" for="beep-toggle">
          <input type="checkbox" id="beep-toggle" class="toggle-check" checked>
          <span class="switch"></span><span>Beep</span>
        </label>
      </div>
      <div class="toggle-pill">
        <label class="toggle-label" for="voice-toggle">
          <input type="checkbox" id="voice-toggle" class="toggle-check" checked>
          <span class="switch"></span><span>Voice</span>
        </label>
      </div>
      <a class="btn btn-outline" href="{{ route('attendance.scan') }}"><i class="fas fa-qrcode"></i> Scanner</a>
      <a class="btn btn-outline" href="{{ route('attendance.live.staff') }}"><i class="fas fa-briefcase"></i> Staff Page</a>
      <button type="button" class="btn btn-outline" id="refresh-btn"><i class="fas fa-sync"></i> Refresh</button>
    </div>
  </div>

  {{-- INDICATOR BAR --}}
  <div class="topbar" id="indicator-bar">
    <div class="stat"><span class="dot dot-all"></span> All: <span id="ind-all">0</span></div>
    <div class="stat"><span class="dot dot-P"></span> P: <span id="ind-P">0</span></div>
    <div class="stat"><span class="dot dot-A"></span> A: <span id="ind-A">0</span></div>
    <div class="stat"><span class="dot dot-L"></span> L: <span id="ind-L">0</span></div>
    <div class="stat"><span class="dot dot-E"></span> E: <span id="ind-E">0</span></div>
    <div class="stat"><span class="dot dot-HL"></span> HL: <span id="ind-HL">0</span></div>
    <div class="stat" id="net-stat"><span id="net-dot" class="dot net-ok"></span> Server: <span id="net-label">Online</span></div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  <div class="section">
    <div class="controls" style="margin-bottom:10px">
      <input type="text" id="search" class="input" placeholder="Search name / reg no / code …">
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
    </div>

    {{-- Hierarchy --}}
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
        <div class="head"><i class="fas fa-book"></i> Subjects (required)</div>
        <div class="items" id="subj-items"></div>
      </div>
    </div>

    <div id="subject-msg" class="notice" style="display:none;margin-top:10px;"></div>
    <div id="hier-help" class="help" style="margin-top:6px">Select Department → Faculty → Semester → Batch → <b>Subject</b> to load students. Students are shown only if the selected subject is scheduled right now.</div>
  </div>

  <div class="tab-content">
    <div class="tab-pane active" id="tab-students">
      <div class="section">
        <div class="controls" style="justify-content:space-between;gap:8px">
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <label><input type="checkbox" id="sel-all-students"> Select all on page</label>
            <span class="count" id="sel-count-students">0 selected</span>
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="pill js-bulk" data-kind="mark" data-code="P">Present</button>
            <button class="pill js-bulk" data-kind="mark" data-code="A">Absent</button>
            <button class="pill js-bulk" data-kind="mark" data-code="L">Late</button>
            <button class="pill js-bulk" data-kind="mark" data-code="E">Excused</button>
            <button class="pill js-bulk" data-kind="mark" data-code="HL">Half-Leave</button>
            <button class="pill js-bulk" data-kind="check"><i class="fas fa-sign-in-alt"></i> Check-in/out</button>
          </div>
        </div>

        <div id="students-meta" class="small" style="color:#64748b;margin-bottom:6px"></div>
        <div id="students-list" class="list"></div>
        <div id="students-empty" class="empty" style="display:none">No students match your filters.</div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  "use strict";

  /* ---------- AJAX ---------- */
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' } });

  /* ---------- Network indicator ---------- */
  function setNet(ok){
    $('#net-dot').toggleClass('net-ok', ok).toggleClass('net-bad', !ok);
    $('#net-label').text(ok ? 'Online' : 'Offline');
  }
  window.addEventListener('online',  ()=> setNet(true));
  window.addEventListener('offline', ()=> setNet(false));
  setNet(navigator.onLine === true);

  /* ---------- Server constants ---------- */
  var TODAY    = @json($today);
  var DEFAULT_PER_PAGE = @json($DEFAULT_PER_PAGE);
  var STATUSES = @json($__STATUSES);

  /* ---------- Routes ---------- */
  var ROUTE_LIST   = @json(route('attendance.live.list'));
  var ROUTE_MARK_T = @json(route('attendance.row.mark', ['attendance' => 'ATTENDANCE_ID']));
  var ROUTE_CHK_T  = @json(route('attendance.row.check', ['attendance' => 'ATTENDANCE_ID']));
  var ROUTE_QMARK  = @json(route('attendance.quick.mark'));
  var ROUTE_QCHECK = @json(route('attendance.quick.check'));
  var R_DEPTS   = @json(route('get-departments'));
  var R_FACS    = @json(route('get-faculties'));
  var R_SEMS    = @json(route('get-semesters'));
  var R_BATCHES = @json(route('get-batches'));
  var R_SUBJS   = @json(route('get-subjects'));
  var ROUTE_BULK_MARK  = @json(route('attendance.bulk.mark'));
  var ROUTE_BULK_CHECK = @json(route('attendance.bulk.check'));

  /* ---------- State ---------- */
  var state = {
    type: 'students',
    search: '',
    per_page: DEFAULT_PER_PAGE || 100,
    status_code: '',
    dh_id:'', dept_id:'', fac_id:'', sem_id:'', batch_id:'', subj_id:'',
    sel_students: new Set(),
    last_list: []
  };

  /* ---------- Audio: Beep + Voice ---------- */
  var beepOn = true, voiceOn = true;
  $('#beep-toggle').on('change', e => beepOn = e.target.checked);
  $('#voice-toggle').on('change', e => voiceOn = e.target.checked);

  var ac, acReady=false;
  function ensureAC(){ if(acReady) return; try{ ac=ac||new (window.AudioContext||window.webkitAudioContext)(); if(ac.state==='suspended') ac.resume(); acReady=true; }catch(e){ acReady=false; } }
  ['click','keydown','pointerdown','touchstart'].forEach(ev=>document.addEventListener(ev,()=>{ if(!acReady) ensureAC(); },{once:true}));
  function beep(freq=980, dur=.1, type='triangle', gain=.1){ if(!beepOn) return; ensureAC(); if(!ac) return; var o=ac.createOscillator(), g=ac.createGain(); o.type=type; o.frequency.value=freq; g.gain.value=gain; o.connect(g); g.connect(ac.destination); var t=ac.currentTime; o.start(t); o.stop(t+dur); }

  // Robust TTS
  var TTS = { ready:false, voice:null };
  function pickVoice(list){
    return list.find(v=>/en(-|_)?(US|GB|IN)?/i.test(v.lang||'')) || list.find(v=>/English/i.test(v.name||'')) || list[0] || null;
  }
  function loadVoicesOnce(){
    if (!('speechSynthesis' in window)) return;
    var voices = window.speechSynthesis.getVoices();
    if (voices && voices.length){
      TTS.voice = pickVoice(voices);
      TTS.ready = true; return;
    }
    setTimeout(loadVoicesOnce, 150);
  }
  function initTTS(){
    if (!('speechSynthesis' in window)) return;
    try{ window.speechSynthesis.onvoiceschanged = function(){ loadVoicesOnce(); }; }catch(e){}
    loadVoicesOnce();
  }
  function speak(text){
    if (!voiceOn) return;
    if (!('speechSynthesis' in window)) return;
    if (!TTS.ready){ setTimeout(()=>speak(text), 200); return; }
    try{
      var u = new SpeechSynthesisUtterance(text);
      if (TTS.voice) u.voice = TTS.voice;
      u.rate=1.04; u.pitch=1.0; u.volume=1.0;
      try{ speechSynthesis.cancel(); }catch(e){}
      speechSynthesis.speak(u);
    }catch(e){}
  }
  ['click','keydown','pointerdown','touchstart'].forEach(ev=>document.addEventListener(ev,()=>initTTS(),{once:true}));
  initTTS();

  var CODE_WORDS = { P:'Present', A:'Absent', L:'Late', E:'Excused', HL:'Half-Leave' };
  function statusWord(code){
    var c = String(code||'').toUpperCase();
    var hit = (STATUSES||[]).find(s => String(s.code||'').toUpperCase()===c);
    return (hit && hit.label) ? hit.label : (CODE_WORDS[c] || c);
  }
  function speakStatus(code, name){ speak(statusWord(code) + (name ? (' for ' + name) : '')); }

  /* ---------- Tint helpers ---------- */
  var TINTS={ P:{bg:'#ecfdf5', b:'#10b981'}, A:{bg:'#fef2f2', b:'#ef4444'}, L:{bg:'#fffbeb', b:'#f59e0b'}, E:{bg:'#eff6ff', b:'#3b82f6'}, HL:{bg:'#f5f3ff', b:'#7c3aed'} };
  var ALL='tint-P tint-A tint-L tint-E tint-HL';
  function tintRow($card, code){
    code=(code||'').toUpperCase();
    $card.attr('data-status', code);
    $card.removeClass(ALL);
    if(code) $card.addClass('tint-'+code);
    var t=TINTS[code];
    if(t){ $card.css({ background:t.bg, borderLeftColor:t.b }); }
    else { $card.css({ background:'#f8fafc', borderLeftColor:'#cbd5e1' }); }
    // sync
    $card.find('.actions .status').removeClass('active').filter('[data-code="'+code+'"]').addClass('active');
    var $m=$card.find('.meta .muted'), $b=$m.find('.badge');
    if(code){ if($b.length) $b.text(code); else $m.append(' · <span class="badge">'+code+'</span>'); } else { $b.remove(); }
  }

  /* ---------- Indicator refresh ---------- */
  function refreshIndicators(){
    var cards = $('#students-list .rowcard');
    var total = cards.length, P=0,A=0,L=0,E=0,HL=0;
    cards.each(function(){
      var s = ($(this).attr('data-status')||'').toUpperCase();
      if (s==='P') P++; else if (s==='A') A++; else if (s==='L') L++; else if (s==='E') E++; else if (s==='HL') HL++;
    });
    $('#ind-all').text(total);
    $('#ind-P').text(P);
    $('#ind-A').text(A);
    $('#ind-L').text(L);
    $('#ind-E').text(E);
    $('#ind-HL').text(HL);
  }

  /* ---------- Small util ---------- */
  function initials(n){ if(!n) return '?'; return n.split(/\s+/).map(s=>s[0]||'').join('').slice(0,2).toUpperCase(); }
  function esc(s){ return $('<div>').text(s||'').html(); }
  function setActivePill(code){ document.querySelectorAll('.pill[data-code]').forEach(p=>p.classList.remove('active')); var el=document.querySelector('.pill[data-code="'+(code||'')+'"]'); if(el) el.classList.add('active'); }
  setActivePill('');

  /* ---------- Render row ---------- */
  function renderRow(item){
    var attendanceId = item.attendance_id || '';
    var name  = item.name || '—';
    var code  = item.code || '';
    var last  = (item.last_status_code || '').toUpperCase();
    var updated = item.updated_at ? (' · '+item.updated_at) : '';
    var personType = 'student';
    var pid = item.pid;
    var imgUrl = item.image_url || item.avatar_url || null;

    var list = (STATUSES && STATUSES.length) ? STATUSES : [
      {code:'P',label:'Present'},{code:'A',label:'Absent'},{code:'L',label:'Late'},{code:'E',label:'Excused'},{code:'HL',label:'Half-Leave'}
    ];

    var statusBtns = '';
    list.forEach(function(s){
      var activeCls = (s.code === last) ? ' active' : '';
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

    var selected = state.sel_students.has(pid) ? 'checked' : '';

    return ''+
    '<div class="rowcard'+(last?(' tint-'+last):'')+'" data-attendance="'+(attendanceId||'')+'" data-type="'+personType+'" data-pid="'+pid+'" data-name="'+esc(name)+'" data-status="'+(last||'')+'">'+
      '<div><input type="checkbox" class="sel-row" '+selected+'></div>'+
      (imgUrl?('<div class="avatar"><img src="'+imgUrl+'" alt=""></div>'):('<div class="avatar">'+initials(name)+'</div>'))+
      '<div class="meta">'+
        '<h4>'+ esc(name) +'</h4>'+
        '<div class="muted">'+ (code?('Code: '+esc(code)):'') + (last?(' · <span class="badge">'+last+'</span>'):'') + updated +'</div>'+
        '<div class="actions">'+ statusBtns +' '+ checkBtn +'</div>'+
      '</div>'+
    '</div>';
  }

  function applyList(where, list, emptySel){
    state.last_list = list || [];
    var $wrap = $(where), $empty = $(emptySel);
    $wrap.empty();
    if (list && list.length){
      list.forEach(function(item){ $wrap.append(renderRow(item)); });
      $wrap.find('.rowcard').each(function(){ tintRow($(this), $(this).attr('data-status')||''); });
      $empty.hide();
    } else {
      $empty.show();
    }
    updateSelCounts();
    refreshIndicators();
  }

  function setMeta(){
    var scope = [];
    if (state.dept_id)  scope.push('Dept#'+state.dept_id);
    if (state.fac_id)   scope.push('Fac#'+state.fac_id);
    if (state.sem_id)   scope.push('Sem#'+state.sem_id);
    if (state.batch_id) scope.push('Batch#'+state.batch_id);
    if (state.subj_id)  scope.push('Subj#'+state.subj_id);
    var txt = 'Students · '+TODAY+(scope.length?(' · '+scope.join(' / ')):'' );
    $('#students-meta').text(txt);
  }

  /* ---------- List load ---------- */
  function listParams(){
    return {
      date: TODAY,
      type: 'student',
      q: state.search,
      per_page: state.per_page,
      page: 1,
      status: state.status_code,
      department_head_id: state.dh_id || '',
      department_id:      state.dept_id || '',
      faculty_id:         state.fac_id || '',
      semester_id:        state.sem_id || '',
      batch_id:           state.batch_id || '',
      subject_id:         state.subj_id || ''
    };
  }

  function loadList(){
    $.get(ROUTE_LIST, listParams()).done(function(resp){
      setNet(true);
      var subjMsg = $('#subject-msg'); subjMsg.hide().text('');

      if (resp && resp.meta && resp.meta.need_hierarchy){
        applyList('#students-list', [], '#students-empty');
        $('#students-empty').text('Select Department → Faculty → Semester → Batch → Subject.');
        setMeta();
        return;
      }

      if (resp && resp.meta && resp.meta.subject_selected && resp.meta.subject_scheduled === false){
        subjMsg.text(resp.meta.subject_message || 'Subject is not scheduled for this time.').show();
        applyList('#students-list', [], '#students-empty');
        setMeta();
        return;
      }

      var list = (resp && (resp.data || resp.items)) ? resp.data : [];
      applyList('#students-list', list, '#students-empty');
      setMeta();
    }).fail(function(){
      setNet(false);
      $('#students-list').html('<div class="empty">Failed to load. Click Refresh.</div>');
    });
  }

  /* ---------- Actions (existing row) ---------- */
  $(document).on('click', '.js-mark', function(e){
    e.preventDefault();
    var $btn = $(this), url = $btn.data('url'), code = String($btn.data('code')||'').toUpperCase();
    if (!url || !code) return;

    var $card = $btn.closest('.rowcard');
    var prev  = ($card.attr('data-status')||'').toUpperCase();
    var who   = $card.attr('data-name') || '';

    tintRow($card, code);
    beep();

    $btn.prop('disabled', true);
    $.post(url, { code: code, subject_id: (state.subj_id || '') })
      .always(function(){ $btn.prop('disabled', false); refreshIndicators(); })
      .done(function(){ setNet(true); speakStatus(code, who); })
      .fail(function(xhr){
        setNet(false);
        tintRow($card, prev);
        Swal.fire('Mark failed', xhr.responseJSON?.message || 'Error','error');
        refreshIndicators();
      });
  });

  $(document).on('click', '.js-check', function(e){
    e.preventDefault();
    var $btn = $(this), url = $btn.data('url');
    if (!url) return;
    beep();
    $btn.prop('disabled', true);
    $.post(url, {})
      .always(function(){ $btn.prop('disabled', false); })
      .done(function(){ setNet(true); })
      .fail(function(xhr){ setNet(false); Swal.fire('Check failed', xhr.responseJSON?.message || 'Error','error'); });
  });

  /* ---------- Actions (no row yet → quick) ---------- */
  $(document).on('click', '.js-qmark', function(e){
    e.preventDefault();
    var $btn = $(this), type = $btn.data('type'), pid = $btn.data('pid'), code = String($btn.data('code')||'').toUpperCase();
    var $card = $btn.closest('.rowcard'), who = $card.attr('data-name') || '';
    var prev = ($card.attr('data-status')||'').toUpperCase();

    tintRow($card, code);
    beep();

    $btn.prop('disabled', true);
    $.post(ROUTE_QMARK, { type: type, person_id: pid, code: code, date: TODAY, subject_id: (state.subj_id || '') })
      .always(function(){ $btn.prop('disabled', false); refreshIndicators(); })
      .done(function(){ setNet(true); speakStatus(code, who); })
      .fail(function(xhr){
        setNet(false);
        tintRow($card, prev);
        Swal.fire('Mark failed', xhr.responseJSON?.message || 'Error','error');
        refreshIndicators();
      });
  });

  $(document).on('click', '.js-qcheck', function(e){
    e.preventDefault();
    var $btn = $(this), type = $btn.data('type'), pid = $btn.data('pid');
    beep();
    $btn.prop('disabled', true);
    $.post(ROUTE_QCHECK, { type: type, person_id: pid, date: TODAY })
      .always(function(){ $btn.prop('disabled', false); })
      .done(function(){ setNet(true); })
      .fail(function(xhr){ setNet(false); Swal.fire('Check failed', xhr.responseJSON?.message || 'Error','error'); });
  });

  /* ---------- Selection ---------- */
  function updateSelCounts(){
    var c = state.sel_students.size;
    $('#sel-count-students').text(c+' selected');
    $('#sel-all-students').prop('checked', c>0 && c === state.last_list.length);
  }
  function clearSelectionStudents(){
    state.sel_students.clear();
    $('#sel-all-students').prop('checked', false);
    $('.rowcard .sel-row').prop('checked', false);
    updateSelCounts();
  }
  $(document).on('change', '.sel-row', function(){
    var card = $(this).closest('.rowcard');
    var pid = parseInt(card.data('pid'),10);
    if ($(this).is(':checked')) state.sel_students.add(pid);
    else state.sel_students.delete(pid);
    updateSelCounts();
  });
  $('#sel-all-students').on('change', function(){
    if (this.checked) {
      state.sel_students.clear();
      state.last_list.forEach(function(it){ state.sel_students.add(it.pid); });
      $('.rowcard[data-type="student"] .sel-row').prop('checked', true);
    } else {
      clearSelectionStudents();
    }
    updateSelCounts();
  });

  /* ---------- Bulk ---------- */
  $(document).on('click', '.js-bulk', function(){
    var kind = $(this).data('kind');
    var code = String($(this).data('code')||'').toUpperCase();
    var ids  = Array.from(state.sel_students);
    if (!ids.length) {
      Swal.fire('No items selected','Please select at least one row.','warning');
      return;
    }

    Swal.fire({
      title: '<i class="fa fa-exclamation-circle text-danger mr-2"></i> Confirm Bulk Action',
      html: `
        <div class="swal-custom-alert alert-warning">
          <i class="fa fa-exclamation-triangle mr-2"></i>
          You are about to perform: <strong>${kind==='check'?'Bulk Check-in/out':'Mark '+statusWord(code)}</strong>
        </div>
        <div class="swal-custom-alert alert-light mt-3">
          <i class="fa fa-info-circle text-info mr-2"></i>
          Target: <strong>${ids.length}</strong> selected item(s).
        </div>
        <p class="text-center mt-3 mb-0">
          <i class="fa fa-question-circle text-primary mr-2"></i>
          Are you sure you want to proceed?
        </p>
      `,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: '<i class="fa fa-check mr-2"></i> Yes, proceed',
      cancelButtonText: '<i class="fa fa-times mr-2"></i> Cancel',
      confirmButtonColor: '#0ea5e9',
      cancelButtonColor: '#6c757d',
      reverseButtons: true
    }).then(function(res){
      if (!res.isConfirmed) return;

      if (kind === 'check') {
        beep();
        $.post(ROUTE_BULK_CHECK, { type: 'student', person_ids: ids, date: TODAY })
          .done(function(){
            setNet(true);
            clearSelectionStudents();
            speak('Bulk check complete for '+ids.length+' students');
          })
          .fail(function(xhr){
            setNet(false);
            Swal.fire('Bulk check failed', xhr.responseJSON?.message || 'Error','error');
          });
      } else {
        ids.forEach(function(pid){ tintRow($('.rowcard[data-pid="'+pid+'"]'), code); });
        beep();
        $.post(ROUTE_BULK_MARK, { type: 'student', person_ids: ids, code: code, date: TODAY, subject_id: (state.subj_id || '') })
          .done(function(){
            setNet(true);
            clearSelectionStudents();
            refreshIndicators();
            speak(statusWord(code)+' set for '+ids.length+' students');
          })
          .fail(function(xhr){
            setNet(false);
            Swal.fire('Bulk mark failed', xhr.responseJSON?.message || 'Error','error');
            loadList(); // revert to server truth
          });
      }
    });
  });

  /* ---------- Filters ---------- */
  $('#search').on('input', function(){ state.search = $(this).val(); loadList(); });
  $('#per_page').on('change', function(){ state.per_page = parseInt($(this).val(),10) || DEFAULT_PER_PAGE; loadList(); });
  document.querySelectorAll('.pill[data-code]').forEach(function(p){
    p.addEventListener('click', function(){
      state.status_code = this.getAttribute('data-code') || '';
      setActivePill(state.status_code);
      loadList();
    });
  });
  $('#refresh-btn').on('click', function(){ loadList(); });

  /* ---------- Hierarchy (students) ---------- */
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
      $.get(R_DEPTS, { department_head_id: id }).done(function(data){ fill('#dept-items', data||{}, 'department'); $('#hl-departments').show(); setNet(true); }).fail(()=>setNet(false));
    }
    if(level==='department'){ state.dept_id = id; resetBelow('department');
      $.get(R_FACS, { department_id: id }).done(function(data){ fill('#fac-items', data||{}, 'faculty'); $('#hl-faculties').show(); setNet(true); }).fail(()=>setNet(false));
    }
    if(level==='faculty'){ state.fac_id = id; resetBelow('faculty');
      $.get(R_SEMS, { faculty_id: id }).done(function(data){ fill('#sem-items', data||{}, 'semester'); $('#hl-semesters').show(); setNet(true); }).fail(()=>setNet(false));
    }
    if(level==='semester'){ state.sem_id = id; resetBelow('semester');
      $.when($.get(R_BATCHES, { semester_id: id }), $.get(R_SUBJS, { semester_id: id }))
        .done(function(batches, subjects){
          fill('#batch-items', batches[0]||{}, 'batch'); $('#hl-batches').show();
          fill('#subj-items', subjects[0]||{}, 'subject'); $('#hl-subjects').show();
          setNet(true);
        }).fail(()=>setNet(false));
    }
    if(level==='batch'){ state.batch_id = id; }
    if(level==='subject'){ state.subj_id = id; }

    loadList();
  });

  /* ---------- Init ---------- */
  initTTS();
  loadList();
})();
</script>
@endsection
