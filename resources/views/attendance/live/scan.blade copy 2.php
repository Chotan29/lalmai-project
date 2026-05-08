@extends('layouts.master')

@section('css')
<style>
:root{
  --primary:#346da5;--primary-light:#eef2ff;--secondary:#64748b;--success:#10b981;
  --warning:#f59e0b;--danger:#ef4444;--dark:#0f172a;--light:#f8fafc;--border:#e2e8f0;
  --shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)
}
*{box-sizing:border-box;font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}

/* page wrapper (fullscreen targets this) */
.page-wrap{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:16px}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.header h1{margin:0;color:var(--dark);font-weight:700;font-size:22px}
.header .sub{color:var(--secondary);font-size:13px;margin-top:2px}

.btn{display:inline-flex;align-items:center;gap:8px;border-radius:12px;padding:8px 12px;cursor:pointer}
.btn-outline{background:#fff;border:1px solid var(--border);color:#0f172a}
.btn-outline:hover{background:#f1f5f9}
.btn-primary{background:var(--primary);border:0;color:#fff}
.btn-primary:hover{background:#264f7c}
.btn-danger{background:var(--danger);color:#fff;border:0}
.btn-ghost{background:transparent;border:1px dashed var(--border);color:#334155}
.btn-ghost:hover{background:#f8fafc}

.split{display:grid;grid-template-columns:1.2fr .8fr;gap:16px}
@media (max-width: 1024px){ .split{grid-template-columns:1fr} }

.card{background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
.card .card-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--border)}
.card .card-bd{padding:14px}

/* --- HERO INPUT --- */
.input-hero{
  position:relative;border-radius:16px;background:#fff;border:2px solid var(--border);
  box-shadow:0 6px 20px rgba(2,6,23,.06);padding:14px 14px 14px 48px;transition:.2s;
}
.input-hero:focus-within{
  border-color:var(--primary);
  box-shadow:0 0 0 4px rgba(52,109,165,.18), 0 10px 30px rgba(2,6,23,.08);
}
.input-hero .ico{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#6b7280;font-size:18px}
.input-hero__field{width:100%;border:0;outline:0;background:transparent;font-size:28px;line-height:1;color:var(--dark)}
.input-hero__field::placeholder{color:#94a3b8}
.input-hero.pulse{box-shadow:0 0 0 4px rgba(52,109,165,.12),0 10px 30px rgba(2,6,23,.08)}

.scan-actions{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap}

/* spinner */
.spin{width:14px;height:14px;border:2px solid #cbd5e1;border-top-color:#346da5;border-radius:50%;display:inline-block;animation:sp 1s linear infinite;vertical-align:middle}
@keyframes sp{to{transform:rotate(360deg)}}

/* log */
.scan-log{margin-top:12px;height:260px;overflow:auto;background:#0b1220;color:#cbd5e1;border-radius:12px;padding:10px;font-family:ui-monospace,Consolas,Menlo,monospace}
.log .row{display:flex;gap:10px;align-items:baseline;padding:6px 10px;border-bottom:1px solid rgba(255,255,255,.04)}
.log .row:last-child{border-bottom:0}
.log .ts{color:#9ca3af;font-size:12px;min-width:82px}
.log .msg{color:#e5e7eb;font-size:14px;white-space:pre-line}
.log .ok .msg{color:#a7f3d0}
.log .err .msg{color:#fecaca}

/* person detail */
.person{display:flex;gap:12px}
.avatar{width:84px;height:84px;border-radius:16px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#475569;font-weight:800;font-size:28px;overflow:hidden;flex-shrink:0;transition:box-shadow .2s}
.avatar img{width:84px;height:84px;object-fit:cover;display:block}
.avatar.ok{box-shadow:0 0 0 3px rgba(16,185,129,.35)}
.avatar.err{box-shadow:0 0 0 3px rgba(239,68,68,.35)}
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

.kbd{display:inline-block;border:1px solid #cbd5e1;border-bottom-width:3px;padding:2px 6px;border-radius:6px;background:#f8fafc;font-family:ui-monospace,Consolas,Menlo,monospace}

/* fullscreen for JUST this page wrap */
#scan-wrap:fullscreen{width:100vw;height:100vh;padding:0;background:#fff}
#scan-wrap:fullscreen .header{padding:16px 12px}
#scan-wrap:fullscreen .split{height:calc(100vh - 96px);overflow:auto}

/* toggle */
.toggle{position:relative;display:inline-flex;align-items:center;gap:8px;cursor:pointer;user-select:none}
.toggle input{display:none}
.toggle .track{width:42px;height:24px;background:#e5e7eb;border-radius:999px;position:relative;transition:.2s}
.toggle .thumb{position:absolute;width:18px;height:18px;border-radius:999px;background:#fff;top:3px;left:3px;box-shadow:0 1px 2px rgba(0,0,0,.15);transition:.2s}
.toggle input:checked + .track{background:#346da5}
.toggle input:checked + .track .thumb{left:21px}
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
      <div class="sub">
        Scan or type <b>REGISTRATION NO.</b> • Today: <b>{{ $today }}</b>
        &nbsp;|&nbsp; Press <span class="kbd">Enter</span> to submit, <span class="kbd">F</span> for Full Screen
      </div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <label class="toggle" title="Toggle beep sound">
        <input type="checkbox" id="sound-toggle" checked>
        <span class="track"><span class="thumb"></span></span>
        <span style="color:#374151;font-weight:600">Beep</span>
      </label>
      <label class="toggle" title="Toggle voice feedback">
        <input type="checkbox" id="voice-toggle" checked>
        <span class="track"><span class="thumb"></span></span>
        <span style="color:#374151;font-weight:600">Voice</span>
      </label>

      <a href="{{ route('attendance.live.index') }}" class="btn btn-outline"><i class="fas fa-list"></i> Manual Mode</a>
      <button type="button" id="fs-btn" class="btn btn-outline"><i class="fas fa-expand"></i> Full Screen</button>
    </div>
  </div>

  <div class="split">
    <!-- LEFT: Scanner -->
    <div class="card">
      <div class="card-hd">
        <div style="font-weight:700;color:var(--dark)"><i class="fas fa-qrcode"></i> Scanner</div>
        <div class="muted" style="color:#64748b">Source: <span class="kbd">scanner</span></div>
      </div>
      <div class="card-bd">
        <div class="input-hero" id="scan-hero">
          <i class="fas fa-qrcode ico"></i>
          <input type="text" id="scan-input" class="input-hero__field" placeholder="Scan or type Reg No …" autocomplete="off" autofocus>
        </div>

        <div class="scan-actions">
          <button id="submit-btn" class="btn btn-primary"><i class="fas fa-bolt"></i> Submit</button>
          <button id="focus-btn" class="btn btn-ghost"><i class="fas fa-i-cursor"></i> Focus</button>
          <button id="clear-btn" class="btn btn-danger"><i class="fas fa-broom"></i> Clear Log</button>
          <span id="busy" style="display:none;margin-left:8px"><span class="spin"></span>
            <span style="color:#64748b;font-weight:700;margin-left:6px">Checking…</span></span>
        </div>

        <div class="scan-log log" id="scan-log" aria-live="polite"></div>

        <!-- Optional pre-recorded voice MP3s (put files at /public/audio/ok.mp3 & /public/audio/error.mp3) -->
        <audio id="ok-audio"   preload="none">
          <source src="{{ asset('audio/ok.mp3') }}" type="audio/mpeg">
        </audio>
        <audio id="err-audio"  preload="none">
          <source src="{{ asset('audio/error.mp3') }}" type="audio/mpeg">
        </audio>
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

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }
  });

  const $input   = $('#scan-input');
  const $hero    = $('#scan-hero');
  const $btn     = $('#submit-btn');
  const $busy    = $('#busy');
  const $log     = $('#scan-log');
  const $soundOn = $('#sound-toggle');
  const $voiceOn = $('#voice-toggle');

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

  // Base beep
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
  // First beep set (existing)
  function successBeep(){
    playBeep({freq:980,  dur:0.11, type:'triangle', gain:0.10});
    setTimeout(()=>playBeep({freq:1260, dur:0.10, type:'triangle', gain:0.10}), 130);
  }
  function errorBeep(){
    playBeep({freq:300, dur:0.18, type:'square', gain:0.14});
  }
  // --- Another beep (extra chime/buzz) ---
  function successChime(){
    playBeep({freq:660, dur:0.08, type:'sine', gain:0.10});
    setTimeout(()=>playBeep({freq:880, dur:0.08, type:'sine', gain:0.11}), 90);
    setTimeout(()=>playBeep({freq:1320,dur:0.09, type:'sine', gain:0.12}), 170);
  }
  function errorBuzz(){
    playBeep({freq:220, dur:0.20, type:'sawtooth', gain:0.12});
    setTimeout(()=>playBeep({freq:180, dur:0.20, type:'square',   gain:0.10}), 140);
  }

  // Voice (prefer MP3, fallback to speech synthesis)
  const okAudio  = document.getElementById('ok-audio');
  const errAudio = document.getElementById('err-audio');

  function speak(text){
    if (!$voiceOn.prop('checked')) return;
    try{
      // Prefer MP3 clip if present and short generic text
      if (text==='OK' && okAudio){ okAudio.currentTime = 0; okAudio.play().catch(()=>{}); return; }
      if (text==='Error' && errAudio){ errAudio.currentTime = 0; errAudio.play().catch(()=>{}); return; }

      // Fallback: Web Speech
      if ('speechSynthesis' in window){
        const u = new SpeechSynthesisUtterance(text);
        u.rate = 1.05; u.pitch = 1.0; u.volume = 1.0;
        try{ window.speechSynthesis.cancel(); }catch(e){}
        window.speechSynthesis.speak(u);
      }
    }catch(e){}
  }

  // ---------- UI helpers ----------
  function pulseOn(){ $hero.addClass('pulse'); }
  function pulseOff(){ $hero.removeClass('pulse'); setTimeout(()=> $hero.removeClass('pulse'), 120); }
  $input.on('keydown', ()=> pulseOn());
  $input.on('keyup',   ()=> pulseOff());

  function now(){ return (new Date()).toLocaleTimeString(); }
  function log(type, lines){
    const text = Array.isArray(lines)? lines.join('\n') : String(lines||'');
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
  function setPersonUI(who, row, token){
    const name = who.name || ('#'+(who.id||''));
    const code = (row.status_code||'—');

    $('#p-name').text(name);
    $('#p-type').text((who.type==='student'?'Student':'Staff') + (who.id?(' · #'+who.id):''));
    $('#p-code').text(who.reg_no || who.code || token || '—');
    $('#p-status').text(code);
    $('#p-in').text(row.check_in_at  ? new Date(row.check_in_at ).toLocaleTimeString() : '—');
    $('#p-out').text(row.check_out_at? new Date(row.check_out_at).toLocaleTimeString() : '—');
    $('#last-status-pill').html(code ? '<span class="badge '+code+'">'+code+'</span>' : '');

    const initials = (name||'?').split(/\s+/).map(s=>s[0]||'').join('').slice(0,2).toUpperCase();
    if (who.image_url) { $('#p-avatar').html('<img src="'+who.image_url+'" alt="">'); }
    else { $('#p-avatar').text(initials); }
  }

  // ---------- submit flow ----------
  function submit(){
    const token = ($input.val()||'').trim();
    if (!token){ $input.focus(); return; }

    $btn.prop('disabled', true); $busy.show();

    $.post(ROUTE_IDENT, { code: token, source:'scanner', date: TODAY })
      .always(()=>{ $btn.prop('disabled', false); $busy.hide(); })
      .done((resp)=>{
        const row = resp && resp.row ? resp.row : {};
        const who = resp && (resp.person || resp.matched) ? (resp.person || resp.matched) : {};

        $('#p-avatar').removeClass('err').addClass('ok');
        setPersonUI(who, row, token);

        // double feedback: beep set + chime, then voice
        successBeep(); setTimeout(successChime, 140);
        speak('OK');

        log('ok', [ (who.reg_no || who.code || token), '['+(row.status_code||'—')+'] OK' ]);
        $input.val('').focus();
      })
      .fail((xhr)=>{
        const msg = niceError(xhr);
        $('#p-avatar').removeClass('ok').addClass('err');

        errorBeep(); setTimeout(errorBuzz, 160);
        speak('Error');

        log('err', [ ($input.val()||''), msg, 'ERR' ]);
        $input.select().focus();
      });
  }

  // buttons / keys
  $('#submit-btn').on('click', submit);
  $('#focus-btn').on('click', ()=> $input.focus().select());
  $('#clear-btn').on('click', ()=> { $log.empty(); log('info','Log cleared'); });

  // fullscreen (page-wrap only)
  const fsBtn  = document.getElementById('fs-btn');
  const fsWrap = document.getElementById('scan-wrap');
  function inFS(){ return document.fullscreenElement === fsWrap; }
  function toggleFS(){
    if (!inFS()){
      fsWrap.requestFullscreen?.().then(()=> fsBtn.innerHTML='<i class="fas fa-compress"></i> Exit Full Screen');
    } else {
      document.exitFullscreen?.().then(()=> fsBtn.innerHTML='<i class="fas fa-expand"></i> Full Screen');
    }
  }
  fsBtn.addEventListener('click', toggleFS);
  document.addEventListener('keydown', (e)=>{
    if (e.key==='Enter'){ e.preventDefault(); submit(); }
    if (e.key==='f' || e.key==='F'){ e.preventDefault(); toggleFS(); }
  });
  document.addEventListener('fullscreenchange', ()=>{
    fsBtn.innerHTML = inFS() ? '<i class="fas fa-compress"></i> Exit Full Screen'
                             : '<i class="fas fa-expand"></i> Full Screen';
  });

  // init
  $input.focus();
  initAudio();
  log('info','Ready');
})();
</script>
@endsection
