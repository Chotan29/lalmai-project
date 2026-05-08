@extends('layouts.master')

@section('css')
<style>
:root{
  --bg:#0b1220; --panel:#0f172a; --muted:#94a3b8; --ok:#22c55e; --err:#ef4444; --cyan:#06b6d4; --border:#1f2737;
}
*{box-sizing:border-box;font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
.wrap{max-width:860px;margin:24px auto;padding:0 16px}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.title{color:#e2e8f0;font-weight:800;font-size:22px}
.date{color:#94a3b8;font-weight:600;font-size:14px}
.panel{background:var(--panel);border:1px solid var(--border);border-radius:14px;padding:18px}
.row{display:flex;gap:14px;flex-wrap:wrap;align-items:center}
.input{
  flex:1 1 420px;background:#0b1220;border:1px solid #334155;color:#e5e7eb;border-radius:12px;
  padding:14px 14px;font-size:18px;outline:none
}
.input:focus{border-color:#06b6d4;box-shadow:0 0 0 4px rgba(6,182,212,.15)}
.btn{background:#06b6d4;border:0;color:#0b1220;padding:12px 16px;border-radius:12px;font-weight:800}
.btn:hover{filter:brightness(1.05)}
.tog{display:inline-flex;gap:8px;align-items:center;color:#cbd5e1}
.small{color:#94a3b8;font-size:12px}
.log{margin-top:14px;background:#0b1220;border:1px solid #233049;border-radius:12px;max-height:380px;overflow:auto}
.logitem{display:flex;gap:10px;padding:10px 12px;border-bottom:1px dashed #1f2937;align-items:flex-start}
.logitem:last-child{border-bottom:none}
.time{font-family:ui-monospace,Consolas,Menlo,monospace;color:#94a3b8;min-width:92px}
.msg{color:#e5e7eb;white-space:pre-wrap;word-break:break-word}
.ok .msg{color:#22c55e}
.err .msg{color:#ef4444}
.badge{display:inline-block;background:#111827;color:#e5e7eb;border:1px solid #374151;border-radius:999px;padding:2px 8px;margin-left:8px;font-size:12px}
.person{display:flex;gap:10px;align-items:center}
.avatar{width:44px;height:44px;border-radius:12px;overflow:hidden;border:1px solid #1f2737;background:#111827;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-weight:800}
.avatar img{width:100%;height:100%;object-fit:cover}
.help{margin-top:10px;color:#94a3b8}
</style>
@endsection

@section('content')
@php
  $today = $data['today'] ?? now()->toDateString();
@endphp

<div class="wrap">
  <div class="header">
    <div class="title">Scanner Mode</div>
    <div class="date"><i class="far fa-calendar"></i> {{ $today }}</div>
  </div>

  <div class="panel">
    <div class="row">
      <input id="scan-input" class="input" placeholder="Focus here, scan barcode / QR, or type & press Enter">
      <button id="scan-btn" class="btn">Submit</button>
      <label class="tog"><input type="checkbox" id="autoclear" checked> Auto-clear</label>
      <label class="tog"><input type="checkbox" id="beep" checked> Beep</label>
    </div>
    <div class="small help">Tip: We detect both Students and Staff by <strong>reg_no</strong> automatically. Uses the same /attendance/identify endpoint.</div>

    <div class="log" id="log"></div>
  </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

{{-- tiny embedded beeps (base64) --}}
<audio id="beep-ok" preload="auto">
  <source src="data:audio/mp3;base64,//uQZAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAACcQAA" type="audio/mp3">
</audio>
<audio id="beep-err" preload="auto">
  <source src="data:audio/mp3;base64,//uQZAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAACcQAA" type="audio/mp3">
</audio>

<script>
(function(){
  "use strict";

  // CSRF for AJAX
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

  var TODAY = @json($today);
  var ROUTE_IDENT = @json(route('attendance.identify'));

  var $input = $('#scan-input');
  var $btn   = $('#scan-btn');
  var $log   = $('#log');

  function nowTime(){ return new Date().toLocaleTimeString(); }

  function playOK(){ if (!$('#beep').is(':checked')) return; var a = document.getElementById('beep-ok'); a && a.play().catch(()=>{}); }
  function playERR(){ if (!$('#beep').is(':checked')) return; var a = document.getElementById('beep-err'); a && a.play().catch(()=>{}); }

  function avatarHtml(imgUrl, initials){
    if (imgUrl) return '<div class="avatar"><img src="'+imgUrl+'" alt=""></div>';
    return '<div class="avatar">'+(initials || '?')+'</div>';
  }
  function initials(name){ if(!name) return '?'; return name.split(/\s+/).map(s=>s[0]).slice(0,2).join('').toUpperCase(); }

  function logOK(token, payload){
    var who = (payload && payload.matched) ? payload.matched : null;
    var name = who ? (who.name || '') : '';
    var badge = (payload && payload.row && payload.row.status_code) ? ('<span class="badge">['+payload.row.status_code+']</span>') : '';
    var img = null;

    // Optional enhancement: if your identify returns image in future, plug here
    var av = avatarHtml(img, initials(name));

    var html = ''+
      '<div class="logitem ok">'+
        '<div class="time">'+ nowTime() +'</div>'+
        '<div class="person">'+ av +'<div class="msg"><strong>'+ (name || token) +'</strong> '+ badge +'<br>OK</div></div>'+
      '</div>';
    $log.prepend(html);
    playOK();
  }

  function logERR(token, message){
    var html = ''+
      '<div class="logitem err">'+
        '<div class="time">'+ nowTime() +'</div>'+
        '<div class="msg"><strong>'+ token +'</strong><br>'+ (message || 'ERR') +'</div>'+
      '</div>';
    $log.prepend(html);
    playERR();
  }

  function submitToken(src){
    var token = ($input.val() || '').trim();
    if (!token) { $input.focus(); return; }

    $btn.prop('disabled', true);
    $.post(ROUTE_IDENT, { code: token, source: (src||'scan'), date: TODAY })
      .always(function(){ $btn.prop('disabled', false); if ($('#autoclear').is(':checked')) $input.val(''); $input.focus(); })
      .done(function(resp){
        // IMPORTANT: controller returns 'row.status_code' safely (no $row->status->code direct)
        logOK(token, resp);
      })
      .fail(function(xhr){
        var msg = 'ERR';
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        logERR(token, msg);
      });
  }

  $btn.on('click', function(){ submitToken('manual'); });
  $input.on('keydown', function(e){ if (e.key === 'Enter'){ submitToken('scan'); } });

  // autofocus
  setTimeout(function(){ $input.focus(); }, 200);
})();
</script>
@endsection
