@extends('layouts.master')

@section('css')
<style>
:root{
  --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b;
  --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
  --dark:#0f172a; --light:#f8fafc; --border:#e2e8f0;
  --shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
}

/* page */
.page-wrap{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:16px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.header h1{margin:0;color:var(--dark);font-weight:800;font-size:22px}
.header .sub{color:var(--secondary);font-size:13px;margin-top:2px}

.btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:8px 12px;cursor:pointer}
.btn-outline{background:#fff;border:1px solid var(--border);color:#0f172a}
.btn-outline:hover{background:#f1f5f9}
.btn-primary{background:var(--primary);border:0;color:#fff}
.btn-primary:hover{background:#264f7c}
.btn-danger{background:var(--danger);color:#fff;border:0}

/* stats / toggles row */
.topbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:10px}
.stat{display:inline-flex;gap:8px;align-items:center;border:1px solid var(--border);background:#fff;border-radius:999px;padding:6px 12px;font-weight:800;color:#0f172a}
.stat .dot{width:10px;height:10px;border-radius:50%}
.dot-all{background:#6366f1}
.dot-ok{background:#10b981}
.dot-err{background:#ef4444}
.toggle{display:inline-flex;gap:8px;align-items:center;border:1px solid var(--border);background:#fff;border-radius:999px;padding:6px 10px}
.toggle input{accent-color:var(--primary)}

/* online/offline */
#net-stat .dot{width:10px;height:10px;border-radius:50%}
.net-ok{background:#10b981!important}
.net-bad{background:#ef4444!important}

/* layout split */
.split{display:grid;grid-template-columns:1.2fr .8fr;gap:16px}
@media (max-width: 1024px){ .split{grid-template-columns:1fr} }

/* cards */
.card{background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
.card .card-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--border)}
.card .card-bd{padding:14px}

/* scanner input */
.input-xl{width:100%;font-size:28px;line-height:1;border-radius:14px;border:2px solid var(--border);padding:14px 16px;outline:none;transition:border-color .15s, box-shadow .15s}
.input-xl:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(52,109,165,.15)}
.input-xl.pulse{box-shadow:0 0 0 3px rgba(52,109,165,.18)}
.input-xl.ok{border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.18)}
.input-xl.err{border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.18)}
.scan-actions{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap}
.kbd{display:inline-block;border:1px solid #cbd5e1;border-bottom-width:3px;padding:2px 6px;border-radius:6px;background:#f8fafc;font-family:ui-monospace,Consolas,Menlo,monospace}

/* log */
.scan-log{margin-top:12px;height:260px;overflow:auto;background:#0b1220;color:#cbd5e1;border-radius:12px;padding:10px;font-family:ui-monospace,Consolas,Menlo,monospace}
.scan-log .row{display:flex;gap:10px;align-items:baseline;padding:6px 10px;border-bottom:1px solid rgba(255,255,255,.04)}
.scan-log .row:last-child{border-bottom:0}
.scan-log .ts{color:#9ca3af;font-size:12px;min-width:82px}
.scan-log .msg{color:#e5e7eb;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.scan-log .ok .msg{color:#a7f3d0}
.scan-log .err .msg{color:#fecaca}
#busy{display:none;margin-left:6px;color:#64748b}

/* person pane */
.person{display:flex;gap:12px}
.avatar{width:84px;height:84px;border-radius:16px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:800;font-size:28px;overflow:hidden;flex-shrink:0}
.avatar img{width:84px;height:84px;object-fit:cover;display:block}
.pmeta h3{margin:0 0 2px;font-size:18px;color:var(--dark);font-weight:800}
.pmeta .muted{color:#64748b;font-size:12px}
.badge{display:inline-block;border-radius:999px;padding:4px 10px;font-weight:700;font-size:12px;margin-right:6px}
.badge.P{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
.badge.A{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
.badge.L{background:#fffbeb;color:#92400e;border:1px solid #fde68a}
.badge.E{background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe}
.badge.HL{background:#f5f3ff;color:#5b21b6;border:1px solid #ddd6fe}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px}
.kv{border:1px dashed var(--border);border-radius:10px;padding:8px}
.kv .k{display:block;color:#64748b;font-size:11px;margin-bottom:4px}
.kv .v{font-weight:700;color:#0f172a}

/* fullscreen only this box */
#scan-wrap:fullscreen{width:100vw;height:100vh;padding:0;background:#fff}
#scan-wrap:fullscreen .header{padding:16px 12px}
#scan-wrap:fullscreen .split{height:calc(100vh - 96px);overflow:auto}
</style>
@endsection

@section('content')
@php
  $today = $data['today'] ?? now()->toDateString();
@endphp

<div class="page-wrap" id="scan-wrap">
  <div class="header">
    <div>
      <h1>Attendance Scanner</h1>
      <div class="sub">Scan or type <b>REG. NO.</b> (students & staff) • Today: {{ $today }}</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <button type="button" id="fs-btn" class="btn btn-outline"><i class="fas fa-expand"></i> Full Screen</button>
    </div>
  </div>

  <div class="topbar">
    <div class="stat"><span class="dot dot-all"></span> Scans: <span id="stat-scans">0</span></div>
    <div class="stat"><span class="dot dot-ok"></span> OK: <span id="stat-ok">0</span></div>
    <div class="stat"><span class="dot dot-err"></span> ERR: <span id="stat-err">0</span></div>
    <div class="stat" id="net-stat"><span id="net-dot" class="dot net-ok"></span> Server: <span id="net-label">Online</span></div>

    <div class="toggle" title="short tones">
      <input type="checkbox" id="sound-toggle" checked> <label for="sound-toggle" style="margin:0;cursor:pointer">Beep</label>
    </div>
    <div class="toggle" title="voice guidance or ok/err cue">
      <input type="checkbox" id="voice-toggle" checked> <label for="voice-toggle" style="margin:0;cursor:pointer">Voice</label>
    </div>
  </div>

  <div class="split">
    <!-- LEFT: Scanner -->
    <div class="card">
      <div class="card-hd">
        <div style="font-weight:700;color:var(--dark)"><i class="fas fa-qrcode"></i> Scanner</div>
        <div class="muted" style="color:#64748b">Press <span class="kbd">Enter</span> to submit — <span class="kbd">Esc</span> clear — <span class="kbd">Ctrl/⌘+K</span> focus</div>
      </div>
      <div class="card-bd">
        <input type="text" id="scan-input" class="input-xl" placeholder="Scan or type Reg No …" autocomplete="off" autofocus>
        <div class="scan-actions">
          <button id="submit-btn" class="btn btn-primary"><i class="fas fa-bolt"></i> Submit</button>
          <button id="focus-btn" class="btn btn-outline"><i class="fas fa-i-cursor"></i> Focus Input</button>
          <button type="button" id="clear-btn" class="btn btn-danger"><i class="fas fa-broom"></i> Clear Log</button>
          <span id="busy"><i class="fas fa-circle-notch fa-spin"></i> checking…</span>
        </div>
        <div class="scan-log" id="scan-log" aria-live="polite"></div>
      </div>
    </div>

    <!-- RIGHT: Person Detail -->
    <div class="card">
      <div class="card-hd">
        <div style="font-weight:700;color:var(--dark)"><i class="fas fa-user-check"></i> Last Match</div>
        <div id="last-status-pill"></div>
      </div>
      <div class="card-bd" id="person-pane">
        <div class="person">
          <div class="avatar" id="p-avatar">?</div>
          <div class="pmeta" style="flex:1">
            <h3 id="p-name">No record yet</h3>
            <div class="muted" id="p-type">—</div>
            <div style="margin-top:6px" id="p-badges"></div>
          </div>
        </div>

        <div class="detail-grid" style="margin-top:12px">
          <div class="kv">
            <span class="k">Reg / Code</span>
            <span class="v" id="p-code">—</span>
          </div>
          <div class="kv">
            <span class="k">Today Status</span>
            <span class="v" id="p-status">—</span>
          </div>
          <div class="kv">
            <span class="k">Check-in</span>
            <span class="v" id="p-in">—</span>
          </div>
          <div class="kv">
            <span class="k">Check-out</span>
            <span class="v" id="p-out">—</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
  "use strict";

  const ROUTE_IDENT = @json(route('attendance.identify'));
  const TODAY       = @json($today);
  const CSRF        = @json(csrf_token());

  // AJAX headers (prevent dashboard redirects)
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }
  });

  // Elements
  const $wrap    = $('#scan-wrap');
  const $input   = $('#scan-input');
  const $btn     = $('#submit-btn');
  const $busy    = $('#busy');
  const $log     = $('#scan-log');
  const $soundOn = $('#sound-toggle');
  const $voiceOn = $('#voice-toggle');

  // Stats
  const stat = { scans:0, ok:0, err:0 };
  function updStats(){
    $('#stat-scans').text(stat.scans);
    $('#stat-ok').text(stat.ok);
    $('#stat-err').text(stat.err);
  }

  // Online/offline indicator
  function setNet(ok){
    $('#net-dot').toggleClass('net-ok', ok).toggleClass('net-bad', !ok);
    $('#net-label').text(ok ? 'Online' : 'Offline');
  }
  window.addEventListener('online',  ()=> setNet(true));
  window.addEventListener('offline', ()=> setNet(false));
  setNet(navigator.onLine === true);

  // ---------- AUDIO ----------
  let audioCtx, unlocked = false;
  function initAudio(){
    try{
      if (!audioCtx) audioCtx = new (window.AudioContext||window.webkitAudioContext)();
      if (audioCtx.state === 'suspended') audioCtx.resume();
      unlocked = true;
    }catch(e){ unlocked = false; }
  }
  ['pointerdown','keydown','click'].forEach(ev =>
    document.addEventListener(ev, () => { if (!unlocked) initAudio(); }, { once:true })
  );

  function playBeep({freq=880, dur=0.14, type='sine', gain=0.12}={}){
    if (!$soundOn.prop('checked')) return;
    if (!unlocked) initAudio();
    if (!audioCtx) return;
    const osc = audioCtx.createOscillator();
    const g   = audioCtx.createGain();
    osc.type = type; osc.frequency.value = freq;
    osc.connect(g); g.connect(audioCtx.destination);
    const t = audioCtx.currentTime;
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(gain, t+0.01);
    g.gain.exponentialRampToValueAtTime(0.0001, t+dur);
    osc.start(t); osc.stop(t+dur+0.01);
  }
  function successBeep(){ playBeep({freq:980,  dur:0.11, type:'triangle', gain:0.10}); setTimeout(()=>playBeep({freq:1260, dur:0.10, type:'triangle', gain:0.10}), 130); }
  function errorBeep(){   playBeep({freq:300,  dur:0.18, type:'square',   gain:0.14}); }
  function successChime(){ playBeep({freq:660, dur:0.08, type:'sine', gain:0.10}); setTimeout(()=>playBeep({freq:880, dur:0.08, type:'sine', gain:0.11}), 90); setTimeout(()=>playBeep({freq:1320,dur:0.09, type:'sine', gain:0.12}), 170); }
  function errorBuzz(){   playBeep({freq:220, dur:0.20, type:'sawtooth', gain:0.12}); setTimeout(()=>playBeep({freq:180, dur:0.20, type:'square', gain:0.10}), 140); }

  function speak(kind, whoName, code){
    if (!$voiceOn.prop('checked')) return;
    try{
      if (!('speechSynthesis' in window)) return;
      //const first = String(whoName||'').trim().split(/\s+/)[0] || '';
      const first = String(whoName||'');
      const phrase = (kind==='ok')
        ? (code ? (code==='P'?'Present':'Marked') : 'OK') + (first ? (' for ' + first) : '')
        : 'Error';
      const u = new SpeechSynthesisUtterance(phrase);
      u.rate = 1.05; u.pitch = 1.0; u.volume = 1.0;
      try{ speechSynthesis.cancel(); }catch(e){}
      speechSynthesis.speak(u);
    }catch(e){}
  }

  // ---------- UI helpers ----------
  function pulseOn(){ $input.addClass('pulse'); }
  function pulseOff(){ setTimeout(()=> $input.removeClass('pulse'), 120); }
  $input.on('keydown', ()=> pulseOn());
  $input.on('keyup',   ()=> pulseOff());
  function flash(kind){ $input.removeClass('ok err'); if (kind){ $input.addClass(kind); setTimeout(()=> $input.removeClass(kind), 800); } }

  function now(){ return (new Date()).toLocaleTimeString(); }
  function log(type, parts){
    const text = (Array.isArray(parts) ? parts.filter(Boolean).join(' - ') : String(parts||''));
    const cls  = type==='ok' ? 'ok' : (type==='err' ? 'err' : '');
    const $r = $('<div class="row">').addClass(cls)
      .append($('<div class="ts">').text(now()))
      .append($('<div class="msg">').text(text));
    $log.prepend($r);
  }

  function niceError(xhr){
    if (xhr.status===419) return 'Session expired. Refresh the page.';
    if (xhr.status===401) return 'Unauthorized.';
    if (xhr.status===429) return 'Too many scans. Slow down.';
    return (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error';
  }

  function initials(n){ if(!n) return '?'; return n.split(/\s+/).map(s=>s[0]||'').join('').slice(0,2).toUpperCase(); }

  function setPersonUI(who, row, token){
    const name = who.name || ('#'+(who.id||''));
    const type = (who.type==='student'?'Student': (who.type==='staff'?'Staff':'—'));
    const code = row.status_code || '—';

    // Photo strategy: explicit image_url -> student_image/staff_image -> initials
    let img = who.image_url || '';
    if (!img){
      if (who.student_image) img = '/images/studentProfile/' + String(who.student_image).trim();
      else if (who.staff_image) img = '/images/staff/' + String(who.staff_image).trim();
    }
    if (img){
      const el = new Image();
      el.onload  = ()=> $('#p-avatar').html('<img src="'+img+'" alt="">');
      el.onerror = ()=> $('#p-avatar').text(initials(name));
      el.src = img;
    } else {
      $('#p-avatar').text(initials(name));
    }

    $('#p-name').text(name);
    $('#p-type').text(type + (who.id?(' · #'+who.id):''));
    $('#p-code').text(who.reg_no || who.code || token || '—');
    $('#p-status').text(code);
    $('#p-in').text(row.check_in_at  ? new Date(row.check_in_at ).toLocaleTimeString() : '—');
    $('#p-out').text(row.check_out_at? new Date(row.check_out_at).toLocaleTimeString() : '—');

    const pill = code ? '<span class="badge '+code+'">'+code+'</span>' : '';
    $('#last-status-pill').html(pill);
    $('#p-badges').html('<span class="badge" style="background:#eef2ff;color:#334155;border:1px solid #dbeafe">'+type+'</span>' + pill);
  }

  // Full screen (just page-wrap)
  const fsBtn  = document.getElementById('fs-btn');
  function inFS(){ return document.fullscreenElement === $wrap[0]; }
  function toggleFS(){
    if (!inFS()){
      $wrap[0].requestFullscreen?.().then(()=> fsBtn.innerHTML = '<i class="fas fa-compress"></i> Exit Full Screen');
    }else{
      document.exitFullscreen?.().then(()=> fsBtn.innerHTML = '<i class="fas fa-expand"></i> Full Screen');
    }
  }
  fsBtn.addEventListener('click', toggleFS);
  document.addEventListener('fullscreenchange', ()=> {
    fsBtn.innerHTML = inFS() ? '<i class="fas fa-compress"></i> Exit Full Screen' : '<i class="fas fa-expand"></i> Full Screen';
  });

  // Keyboard: Enter submit, F fullscreen, Esc clear, Ctrl/Cmd+K focus
  document.addEventListener('keydown', (e)=>{
    if (e.key==='Enter'){ e.preventDefault(); submit(); }
    if (e.key==='f' || e.key==='F'){ e.preventDefault(); toggleFS(); }
    if (e.key==='Escape'){ e.preventDefault(); $input.val(''); }
    if ((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='k'){ e.preventDefault(); $input.focus().select(); }
  });

  // Debounce / guard
  let lock = false, lastSubmitAt = 0;

  // Submit flow
  function submit(){
    const token = ($input.val()||'').trim();
    if (!token){ $input.focus(); return; }

    const nowMs = Date.now();
    if (lock || nowMs - lastSubmitAt < 250) return; // debounce
    lock = true; lastSubmitAt = nowMs;

    stat.scans++; updStats();
    $btn.prop('disabled', true); $busy.show();

    $.post(ROUTE_IDENT, { code: token, source:'scanner', date: TODAY })
      .always(()=>{ $btn.prop('disabled', false); $busy.hide(); lock=false; })
      .done((resp)=>{
        const row = resp && resp.row ? resp.row : {};
        const who = resp && (resp.person || resp.matched) ? (resp.person || resp.matched) : {};
        setNet(true);

        setPersonUI(who, row, token);
        successBeep(); setTimeout(successChime, 140);
        speak('ok');
        //speak('ok', who.name, row.status_code);
        flash('ok');

        const reg   = who.reg_no || who.code || token;
        const name  = who.name || ('#'+(who.id||'')); 
        const type  = who.type ? (who.type==='student'?'Student':'Staff') : '—';
        const statTxt = '['+(row.status_code||'—')+'] OK';
        log('ok', [reg, name, type, statTxt]);

        stat.ok++; updStats();
        $input.val('').focus();
      })
      .fail((xhr)=>{
        const msg = niceError(xhr);
        setNet(false);
        errorBeep(); setTimeout(errorBuzz, 160);
        speak('err');
        flash('err');

        const tokenNow = ($input.val()||'').trim();
        if (/No matching Student\/Staff/i.test(msg)){
          log('err', [tokenNow, 'Not found', 'ERR']);
        }else{
          log('err', [tokenNow, msg, 'ERR']);
        }

        stat.err++; updStats();
        //$input.select().focus();
        $input.val('').focus();
      });
  }

  // Buttons
  $('#submit-btn').on('click', submit);
  $('#focus-btn').on('click', ()=> $input.focus().select());
  $('#clear-btn').on('click', ()=> { $log.empty(); log('info','Log cleared'); });

  // init
  initAudio();
  updStats();
  $input.focus();
  log('info','Ready');
})();
</script>
@endsection
