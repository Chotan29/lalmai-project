@extends('layouts.master')

@section('css')
<style>
:root{ --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b;
  --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
  --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
  --shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
}
.container-fluid{padding:12px 14px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.header h1{font-weight:800;font-size:22px;color:var(--dark);margin:0}
.header .sub{color:var(--secondary);font-size:13px}
.btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:8px 12px;cursor:pointer}
.btn-outline{background:#fff;border:1px solid var(--border);color:#0f172a}
.btn-outline:hover{background:#f1f5f9}
.pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:999px;padding:6px 10px;background:#fff;font-weight:700;cursor:pointer}
.pill.active{background:var(--primary-light);color:var(--primary);border-color:var(--primary)}
.input,.select{border:1px solid var(--border);border-radius:10px;padding:8px 10px;background:#fff}
.select{min-width:160px}
.section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);padding:16px;margin-bottom:16px}
.empty{padding:20px;text-align:center;color:#64748b;border:1px dashed var(--border);border-radius:12px;background:#fff}
.list{display:grid;grid-template-columns:repeat(1,1fr);gap:10px}
@media (min-width:768px){.list{grid-template-columns:repeat(2,1fr)}}
@media (min-width:1200px){.list{grid-template-columns:repeat(3,1fr)}}
.rowcard{
  display:flex;gap:12px;align-items:flex-start;border:1px solid var(--border);
  border-left:4px solid #cbd5e1;background:#f8fafc;border-radius:12px;padding:12px;
  transition:background .12s, border-color .12s;
}
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

.toggle-pill{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:999px;padding:6px 10px;background:#fff}
.toggle-label{display:inline-flex;align-items:center;gap:8px;cursor:pointer;user-select:none}
.toggle-label input.toggle-check{position:absolute;opacity:0;width:1px;height:1px}
.toggle-label .switch{position:relative;width:38px;height:22px;background:#e2e8f0;border-radius:999px;transition:all .2s}
.toggle-label .switch::after{content:"";position:absolute;top:3px;left:3px;width:16px;height:16px;background:#fff;border-radius:50%;box-shadow:0 1px 2px rgba(0,0,0,.2);transition:left .2s}
.toggle-label .toggle-check:checked + .switch{background:var(--primary)}
.toggle-label .toggle-check:checked + .switch::after{left:19px}

.hierarchy{display:flex;gap:12px;overflow-x:auto;padding-bottom:4px}
.hlevel{min-width:220px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow)}
.hlevel .head{padding:8px 10px;border-bottom:1px solid var(--border);font-weight:700;background:#f8fafc;border-radius:12px 12px 0 0}
.hlevel .items{max-height:340px;overflow-y:auto;padding:8px}
.hitem{padding:6px 10px;border-radius:10px;background:#f8fafc;margin-bottom:6px;cursor:pointer;display:flex;justify-content:space-between;border:1px solid transparent}
.hitem:hover{background:#f1f5f9;border-color:var(--border)}
.hitem.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.notice{background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12;padding:8px 10px;border-radius:8px}
.help{font-size:12px;color:#64748b}

/* Schedule banner */
.schedule-banner{border:1px dashed var(--border); background:#f8fafc; border-radius:10px; padding:10px; margin-top:10px; color:#334155}
.schedule-banner .live{font-weight:800; color:#10b981}
.schedule-banner .closed{font-weight:800; color:#ef4444}

/* Stats bar (like scan topbar) */
.statsbar{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin:8px 0 14px}
.statsbar .stat{display:inline-flex;gap:8px;align-items:center;border:1px solid var(--border);background:#fff;border-radius:999px;padding:6px 12px;font-weight:800;color:#0f172a}
.statsbar .dot{width:10px;height:10px;border-radius:50%}
.dot-all{background:#6366f1}
.dot-P{background:#10b981}
.dot-A{background:#ef4444}
.dot-L{background:#f59e0b}
.dot-E{background:#3b82f6}
.dot-HL{background:#7c3aed}
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
      <a class="btn btn-outline" href="{{ route('attendance.live.staff') }}"><i class="fas fa-users"></i> Staff Page</a>
      <button type="button" class="btn btn-outline" id="refresh-btn"><i class="fas fa-sync"></i> Refresh</button>
    </div>
  </div>

  {{-- LIVE COUNTS --}}
  <div class="statsbar" id="statsbar">
    <div class="stat"><span class="dot dot-all"></span>Total: <span id="cnt-total">0</span></div>
    <div class="stat"><span class="dot dot-P"></span>P: <span id="cnt-P">0</span></div>
    <div class="stat"><span class="dot dot-A"></span>A: <span id="cnt-A">0</span></div>
    <div class="stat"><span class="dot dot-L"></span>L: <span id="cnt-L">0</span></div>
    <div class="stat"><span class="dot dot-E"></span>E: <span id="cnt-E">0</span></div>
    <div class="stat"><span class="dot dot-HL"></span>HL: <span id="cnt-HL">0</span></div>
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

      {{-- STATUS FILTERS --}}
      <div class="pill-group" id="status-filter-group" style="display:inline-flex;gap:6px;flex-wrap:wrap;margin-left:6px">
        <div class="pill filter-pill" data-code="">All</div>
        <div class="pill filter-pill" data-code="P">Present</div>
        <div class="pill filter-pill" data-code="A">Absent</div>
        <div class="pill filter-pill" data-code="L">Late</div>
        <div class="pill filter-pill" data-code="E">Excused</div>
        <div class="pill filter-pill" data-code="HL">Half-Leave</div>
      </div>
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
        <div class="head"><i class="fas fa-book"></i> Subjects (from today’s routine)</div>
        <div class="items" id="subj-items"></div>
      </div>
    </div>

    <div id="subject-msg" class="notice" style="display:none;margin-top:10px;"></div>
    <div id="schedule-info" class="schedule-banner" style="display:none;"></div>
    <div id="hier-help" class="help" style="margin-top:6px">
      Select Department Head → Department → Faculty → Semester → Batch → <b>Subject</b> to load students.
    </div>
  </div>

  <div class="section">
    <div class="controls" style="justify-content:space-between;gap:8px">
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <label><input type="checkbox" id="sel-all-students"> Select all on page</label>
        <span class="count" id="sel-count-students">0 selected</span>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="pill js-bulk" data-kind="mark" data-code="P" data-label="Mark Present">Present</button>
        <button class="pill js-bulk" data-kind="mark" data-code="A" data-label="Mark Absent">Absent</button>
        <button class="pill js-bulk" data-kind="mark" data-code="L" data-label="Mark Late">Late</button>
        <button class="pill js-bulk" data-kind="mark" data-code="E" data-label="Mark Excused">Excused</button>
        <button class="pill js-bulk" data-kind="mark" data-code="HL" data-label="Mark Half-Leave">Half-Leave</button>
        <button class="pill js-bulk" data-kind="check" data-label="Bulk Check-in/out"><i class="fas fa-sign-in-alt"></i> Check-in/out</button>
      </div>
    </div>

    <div id="students-meta" class="small" style="color:#64748b;margin-bottom:6px"></div>
    <div id="students-list" class="list"></div>
    <div id="students-empty" class="empty" style="display:none">No students match your filters.</div>
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

  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' } });

  var TODAY    = @json($today);
  var DEFAULT_PER_PAGE = @json($DEFAULT_PER_PAGE);
  var STATUSES = @json($__STATUSES);

  var ROUTE_LIST   = @json(route('attendance.live.list'));
  var ROUTE_MARK_T = @json(route('attendance.row.mark', ['attendance' => 'ATTENDANCE_ID']));
  var ROUTE_CHK_T  = @json(route('attendance.row.check', ['attendance' => 'ATTENDANCE_ID']));
  var ROUTE_QMARK  = @json(route('attendance.quick.mark'));
  var ROUTE_QCHECK = @json(route('attendance.quick.check'));
  var ROUTE_BULK_MARK  = @json(route('attendance.bulk.mark'));
  var ROUTE_BULK_CHECK = @json(route('attendance.bulk.check'));

  var R_DEPTS   = @json(route('get-departments'));
  var R_FACS    = @json(route('get-faculties'));
  var R_SEMS    = @json(route('get-semesters'));
  var R_BATCHES = @json(route('get-batches'));
  var R_SUBJS   = @json(route('get-subjects'));

  /* ---------- State ---------- */
  var state = {
    type: 'students',
    search: '',
    per_page: DEFAULT_PER_PAGE || 100,
    status_code: '',
    dh_id:'', dept_id:'', fac_id:'', sem_id:'', batch_id:'', subj_id:'',
    sel_students: new Set(),
    last_list: [],
    within_schedule: false,
    active_period: null,
    schedule_periods: [],
    counts: { total:0, P:0, A:0, L:0, E:0, HL:0 }
  };

  /* ---------- Audio (beep) ---------- */
  var beepOn = true;
  $('#beep-toggle').on('change', function(e){ beepOn = e.target.checked; });
  var ac, acReady=false;
  function ensureAC(){ if(acReady) return; try{ ac=ac||new (window.AudioContext||window.webkitAudioContext)(); if(ac.state==='suspended') ac.resume(); acReady=true; }catch(e){ acReady=false; } }
  ['pointerdown','keydown','touchstart'].forEach(function(ev){ document.addEventListener(ev,function(){ if(!acReady) ensureAC(); },{once:true}); });
  function beep(freq, dur, type, gain){ if(freq===void 0) freq=980; if(dur===void 0) dur=.08; if(type===void 0) type='triangle'; if(gain===void 0) gain=.08; if(!beepOn) return; ensureAC(); if(!ac) return; var o=ac.createOscillator(), g=ac.createGain(); o.type=type; o.frequency.value=freq; g.gain.value=gain; o.connect(g); g.connect(ac.destination); var t=ac.currentTime; o.start(t); o.stop(t+dur); }

  /* ---------- Voice ---------- */
  var voiceOn = true;
  $('#voice-toggle').on('change', function(e){ voiceOn = e.target.checked; });
  var TTS = { ready:false, voice:null };
  function pickVoice(list){ for(var i=0;i<list.length;i++){ if(/en(-|_)?(US|GB|IN)?/i.test(list[i].lang||'')) return list[i]; } return list[0]||null; }
  function loadVoicesOnce(){ if(!('speechSynthesis' in window)) return; var vs = window.speechSynthesis.getVoices(); if(vs && vs.length){ TTS.voice = pickVoice(vs); TTS.ready = true; return; } setTimeout(loadVoicesOnce, 150); }
  function initTTS(){ if(!('speechSynthesis' in window)) return; try{ window.speechSynthesis.onvoiceschanged=function(){ loadVoicesOnce(); }; }catch(e){} loadVoicesOnce(); }
  function speak(text){ if(!voiceOn) return; if(!('speechSynthesis' in window)) return; if(!TTS.ready){ setTimeout(function(){ speak(text); },200); return; } try{ var u=new SpeechSynthesisUtterance(text); if(TTS.voice) u.voice=TTS.voice; u.rate=1.04; u.pitch=1.0; u.volume=1.0; try{ speechSynthesis.cancel(); }catch(e){} speechSynthesis.speak(u); }catch(e){} }
  ['pointerdown','keydown','touchstart'].forEach(function(ev){ document.addEventListener(ev,function(){ initTTS(); },{once:true}); });
  initTTS();

  function statusWord(code){
    var CODE_WORDS = { P:'Present', A:'Absent', L:'Late', E:'Excused', HL:'Half-Leave' };
    var c = String(code||'').toUpperCase();
    for (var i=0;i<STATUSES.length;i++){ if(String(STATUSES[i].code||'').toUpperCase()===c) return STATUSES[i].label||c; }
    return CODE_WORDS[c] || c;
  }
  function speakStatus(code, name){ speak(statusWord(code) + (name ? (' for ' + name) : '')); }

  /* ---------- Tints & helpers ---------- */
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
    $card.find('.actions .status').removeClass('active').filter('[data-code="'+code+'"]').addClass('active');
    var $m=$card.find('.meta .muted'), $b=$m.find('.badge');
    if(code){ if($b.length) $b.text(code); else $m.append(' · <span class="badge">'+code+'</span>'); } else { $b.remove(); }
  }
  function initials(n){ if(!n) return '?'; return n.split(/\s+/).map(function(s){return s[0]||''}).join('').slice(0,2).toUpperCase(); }
  function esc(s){ return $('<div>').text(s||'').html(); }

  function setActiveFilterPill(code){
    $('.filter-pill').removeClass('active');
    $('.filter-pill[data-code="'+(code||'')+'"]').addClass('active');
  }
  setActiveFilterPill('');

  /* ---------- COUNTS ---------- */
  function resetCounts(){ state.counts = { total:0,P:0,A:0,L:0,E:0,HL:0 }; }
  function renderCounts(){
    $('#cnt-total').text(state.counts.total);
    $('#cnt-P').text(state.counts.P); $('#cnt-A').text(state.counts.A);
    $('#cnt-L').text(state.counts.L); $('#cnt-E').text(state.counts.E);
    $('#cnt-HL').text(state.counts.HL);
  }
  function countsFromList(list){
    resetCounts();
    var i, c;
    state.counts.total = list.length;
    for(i=0;i<list.length;i++){
      c = String(list[i].last_status_code||'').toUpperCase();
      if(state.counts.hasOwnProperty(c)) state.counts[c] += 1;
    }
    renderCounts();
  }
  function applyCountAdjust(oldCode, newCode){
    oldCode = String(oldCode||'').toUpperCase();
    newCode = String(newCode||'').toUpperCase();
    if(state.counts.hasOwnProperty(oldCode) && state.counts[oldCode]>0) state.counts[oldCode]--;
    if(state.counts.hasOwnProperty(newCode)) state.counts[newCode]++;
    renderCounts();
  }

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
    for (var i=0;i<list.length;i++){
      var s = list[i], activeCls = (s.code === last) ? ' active' : '';
      if (attendanceId) {
        var url = ROUTE_MARK_T.replace('ATTENDANCE_ID', attendanceId);
        statusBtns += '<button class="status js-mark'+activeCls+'" data-url="'+url+'" data-code="'+s.code+'">'+(s.label||s.code)+'</button>';
      } else {
        statusBtns += '<button class="status js-qmark'+activeCls+'" data-type="'+personType+'" data-pid="'+pid+'" data-code="'+s.code+'">'+(s.label||s.code)+'</button>';
      }
    }

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
      for (var i=0;i<list.length;i++){ $wrap.append(renderRow(list[i])); }
      $wrap.find('.rowcard').each(function(){ tintRow($(this), $(this).attr('data-status')||''); });
      $empty.hide();
    } else {
      $empty.show();
    }
    countsFromList(state.last_list);  // update counters from new list
    updateSelCounts();
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

  function renderScheduleBanner(meta){
    var box = $('#schedule-info');
    var periods = meta.schedule_periods || [];
    var within = !!meta.within_schedule;
    state.within_schedule = within;
    state.active_period = meta.active_period || null;
    state.schedule_periods = periods || [];

    if (!meta.subject_selected) {
      $('#subject-msg').text('Select a scheduled subject to load students.').show();
      box.hide();
      return;
    }
    if (!meta.subject_scheduled) {
      $('#subject-msg').text(meta.schedule_message || 'Subject is not scheduled today.').show();
      box.hide();
      return;
    }

    $('#subject-msg').hide();

    var parts = periods.map(function(p){ return p.start_hm+'–'+p.end_hm; });
    var statusChip = within ? '<span class="live">LIVE</span>' : '<span class="closed">CLOSED</span>';
    var nowText = meta.server_now ? ' (server: '+meta.server_now+')' : '';

    var msg = statusChip+' • Periods today: '+parts.join(', ')+nowText;
    if (within && meta.active_period) {
      msg += ' • Current: '+meta.active_period.start_hm+'–'+meta.active_period.end_hm;
    } else {
      msg += ' • Attendance is locked outside period time.';
    }
    box.html(msg).show();
  }

  function loadList(){
    $.get(ROUTE_LIST, listParams()).done(function(resp){
      renderScheduleBanner(resp.meta || {});
      if (resp.meta && resp.meta.subject_selected && resp.meta.subject_scheduled === false){
        applyList('#students-list', [], '#students-empty');
        setMeta();
        return;
      }
      if (resp.meta && resp.meta.need_hierarchy){
        applyList('#students-list', [], '#students-empty');
        $('#students-empty').text('Select Department Head → Department → Faculty → Semester → Batch → Subject.');
        setMeta();
        return;
      }
      var list = (resp && (resp.data || resp.items)) ? resp.data : [];
      applyList('#students-list', list, '#students-empty');
      setMeta();
    }).fail(function(){
      $('#students-list').html('<div class="empty">Failed to load. Click Refresh.</div>');
    });
  }

  /* ---------- Selection ---------- */
  function updateSelCounts(){
    var c = state.sel_students.size;
    $('#sel-count-students').text(c+' selected');
    $('#sel-all-students').prop('checked', c>0 && c === state.last_list.length);
  }
  function clearSelection(){
    state.sel_students.clear();
    $('#sel-all-students').prop('checked', false);
    $('.rowcard .sel-row').prop('checked', false);
    updateSelCounts();
  }
  $(document).on('change', '.sel-row', function(){
    var card = $(this).closest('.rowcard');
    var pid = parseInt(card.data('pid'),10);
    if ($(this).is(':checked')) { state.sel_students.add(pid); }
    else { state.sel_students.delete(pid); }
    updateSelCounts();
  });
  $('#sel-all-students').on('change', function(){
    if (this.checked) {
      state.sel_students.clear();
      state.last_list.forEach(function(it){ state.sel_students.add(it.pid); });
      $('.rowcard[data-type="student"] .sel-row').prop('checked', true);
    } else {
      clearSelection();
    }
  });

  /* ---------- Guard clicks when attendance is locked ---------- */
  function lockedAlert(){
    var periods = state.schedule_periods || [];
    var txt = periods.length ? periods.map(function(p){return p.start_hm+'–'+p.end_hm}).join(', ') : 'scheduled time only';
    return Swal.fire({ title:'Attendance Locked', text:'Allowed time: '+txt, icon:'warning' });
  }
  function guardOr(fn){
    if (!state.subj_id) { Swal.fire({ title:'Select Subject', text:'Please pick a scheduled subject first.', icon:'info' }); return; }
    if (!state.within_schedule) { lockedAlert(); return; }
    fn();
  }

  /* ---------- Row actions ---------- */
  $(document).on('click', '.js-mark', function(e){
    e.preventDefault();
    var run = () => {
      var $btn = $(this), url = $btn.data('url'), code = String($btn.data('code')||'').toUpperCase();
      if (!url || !code) return;
      var $card = $btn.closest('.rowcard'), prev = ($card.attr('data-status')||'').toUpperCase();
      var who   = $card.attr('data-name') || '';
      tintRow($card, code);
      applyCountAdjust(prev, code);
      beep();
      $btn.prop('disabled', true);
      $.post(url, { code: code, subject_id: (state.subj_id || '') })
        .always(function(){ $btn.prop('disabled', false); })
        .done(function(){ speakStatus(code, who); })
        .fail(function(xhr){ tintRow($card, prev); applyCountAdjust(code, prev); Swal.fire('Mark failed', (xhr.responseJSON && xhr.responseJSON.message) || 'Error','error'); });
    };
    guardOr(run);
  });

  $(document).on('click', '.js-qmark', function(e){
    e.preventDefault();
    var run = () => {
      var $btn = $(this), type = $btn.data('type'), pid = $btn.data('pid'), code = String($btn.data('code')||'').toUpperCase();
      var $card = $btn.closest('.rowcard'), prev = ($card.attr('data-status')||'').toUpperCase();
      var who   = $card.attr('data-name') || '';
      tintRow($card, code); applyCountAdjust(prev, code); beep();
      $btn.prop('disabled', true);
      $.post(ROUTE_QMARK, { type: type, person_id: pid, code: code, date: TODAY, subject_id: (state.subj_id || '') })
        .always(function(){ $btn.prop('disabled', false); })
        .done(function(){ speakStatus(code, who); })
        .fail(function(xhr){ tintRow($card, prev); applyCountAdjust(code, prev); Swal.fire('Mark failed', (xhr.responseJSON && xhr.responseJSON.message) || 'Error','error'); });
    };
    guardOr(run);
  });

  // Check-in/out (send subject_id so subject out_at can be filled)
  $(document).on('click', '.js-check', function(e){
    e.preventDefault();
    var run = () => {
      var $btn = $(this), url = $btn.data('url'); if (!url) return;
      beep(); $btn.prop('disabled', true);
      $.post(url, { subject_id: (state.subj_id || '') })
        .always(function(){ $btn.prop('disabled', false); })
        .fail(function(xhr){ Swal.fire('Check failed', (xhr.responseJSON && xhr.responseJSON.message) || 'Error','error'); });
    };
    guardOr(run);
  });

  $(document).on('click', '.js-qcheck', function(e){
    e.preventDefault();
    var run = () => {
      var $btn = $(this), type = $btn.data('type'), pid = $btn.data('pid');
      beep(); $btn.prop('disabled', true);
      $.post(ROUTE_QCHECK, { type: type, person_id: pid, date: TODAY, subject_id: (state.subj_id || '') })
        .always(function(){ $btn.prop('disabled', false); })
        .fail(function(xhr){ Swal.fire('Check failed', (xhr.responseJSON && xhr.responseJSON.message) || 'Error','error'); });
    };
    guardOr(run);
  });

  /* ---------- Bulk ---------- */
  $(document).on('click', '.js-bulk', function(e){
    e.preventDefault(); e.stopPropagation();

    var run = () => {
      var kind = $(this).data('kind');
      var code = String($(this).data('code')||'').toUpperCase();
      var label= $(this).data('label') || (kind==='check' ? 'Bulk Check-in/out' : ('Mark '+(code||'')));
      var ids  = Array.from(state.sel_students);

      if (!ids.length) { Swal.fire('No items selected','','warning'); return; }

      if (kind === 'check') {
        beep();
        $.post(ROUTE_BULK_CHECK, { type: 'student', person_ids: ids, date: TODAY })
          .done(function(){ clearSelection(); speak('Bulk check complete for '+ids.length+' students'); })
          .fail(function(xhr){ Swal.fire('Bulk check failed', (xhr.responseJSON && xhr.responseJSON.message) || 'Error','error'); });
      } else {
        // optimistic tint + counts
        ids.forEach(function(pid){
          var $c = $('.rowcard[data-pid="'+pid+'"]');
          var prev = ($c.attr('data-status')||'').toUpperCase();
          tintRow($c, code); applyCountAdjust(prev, code);
        });
        beep();
        $.post(ROUTE_BULK_MARK, { type: 'student', person_ids: ids, code: code, date: TODAY, subject_id: (state.subj_id || '') })
          .done(function(){ clearSelection(); speak(statusWord(code)+' set for '+ids.length+' students'); })
          .fail(function(xhr){
            Swal.fire('Bulk mark failed', (xhr.responseJSON && xhr.responseJSON.message) || 'Error','error');
            loadList(); // revert to server state
          });
      }
    };

    guardOr(run);
  });

  /* ---------- Filters & hierarchy ---------- */
  $('#search').on('input', function(){ state.search = $(this).val(); loadList(); });
  $('#per_page').on('change', function(){ state.per_page = parseInt($(this).val(),10) || DEFAULT_PER_PAGE; loadList(); });
  document.querySelectorAll('.filter-pill').forEach(function(p){
    p.addEventListener('click', function(ev){
      ev.preventDefault(); ev.stopPropagation();
      state.status_code = this.getAttribute('data-code') || '';
      setActiveFilterPill(state.status_code);
      loadList();
    });
  });
  $('#refresh-btn').on('click', function(){ loadList(); });

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

  // Init
  loadList();

})();
</script>
@endsection
