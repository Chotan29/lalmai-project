{{-- resources/views/attendance/scanner.blade.php --}}
@extends('layouts.master')

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">
      Live Scanner — {{ $attendanceMaster->date->format('Y-m-d') }}
      ({{ ucfirst($attendanceMaster->type) }})
    </h3>
    <a href="{{ route('attendance.master.show', $attendanceMaster->id) }}" class="btn btn-outline-primary">
      Back to Day
    </a>
  </div>

  <div class="alert alert-warning mb-3 d-none" id="insecureBanner">
    Your browser may block the camera on this origin. Prefer <strong>http://localhost:8000</strong> or HTTPS.
  </div>

  @if($attendanceMaster->is_locked)
    <div class="alert alert-warning mb-3">This day is <strong>locked</strong>. Scanning is disabled.</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      {{-- Tabs we control via our own JS (no Bootstrap JS dependency) --}}
      <ul class="nav nav-tabs" id="scanTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link js-tab active" data-target="manual" href="#manual" role="tab">Manual / USB Scanner</a>
        </li>
        <li class="nav-item">
          <a class="nav-link js-tab" data-target="qr" href="#qr" role="tab">QR (Webcam)</a>
        </li>
        <li class="nav-item">
          <a class="nav-link js-tab" data-target="barcode" href="#barcode" role="tab">Barcode (Webcam)</a>
        </li>
      </ul>

      <div class="tab-content pt-3">
        {{-- Manual / USB --}}
        <div class="tab-pane show active" id="manual" role="tabpanel">
          <div class="form-inline">
            <input type="text" class="form-control mr-2" id="codeInput" placeholder="Focus here, then scan or type"
                   autofocus {{ $attendanceMaster->is_locked ? 'disabled' : '' }}>
            <button id="btnSubmitCode" class="btn btn-primary" {{ $attendanceMaster->is_locked ? 'disabled' : '' }}>Submit</button>
          </div>
          <small class="text-muted d-block mt-2">USB readers act like keyboard input — keep focus in the box and scan.</small>
        </div>

        {{-- QR (Webcam) --}}
        <div class="tab-pane d-none" id="qr" role="tabpanel">
          <div class="mb-2 d-flex flex-wrap align-items-center">
            <label class="mb-0 mr-2">Camera:</label>
            <select id="qr-camera" class="form-control form-control-sm" style="max-width: 360px"></select>
            <button id="qr-start" class="btn btn-sm btn-success ml-2">Start</button>
            <button id="qr-stop"  class="btn btn-sm btn-outline-secondary ml-2">Stop</button>
          </div>
          <div class="position-relative" style="max-width:560px">
            <div id="qr-reader" class="bg-dark w-100" style="min-height:300px;border-radius:8px;"></div>
            <div id="qr-overlay" class="position-absolute" style="inset:0;pointer-events:none;display:none;"></div>
          </div>
          <div id="qr-result" class="mt-2 text-muted">QR ready.</div>
          <div class="mt-3">
            <label class="small mb-1">Or scan from image:</label>
            <input type="file" accept="image/*" id="qr-file" class="form-control-file">
          </div>
        </div>

        {{-- Barcode (Webcam) --}}
        <div class="tab-pane d-none" id="barcode" role="tabpanel">
          <div class="row">
            <div class="col-md-7">
              <div id="barcode-area" class="bg-dark" style="width:100%;height:360px;border-radius:8px;">
                <div id="barcode-video" style="width:100%;height:100%;"></div>
              </div>
              <small class="text-muted d-block mt-2">QuaggaJS live decode (Code128, EAN, EAN-8). Use good lighting.</small>
            </div>
            <div class="col-md-5">
              <div class="alert alert-info">Last Barcode: <span id="barcode-result" class="font-weight-bold">—</span></div>
              <button class="btn btn-sm btn-success" id="barcode-start">Start</button>
              <button class="btn btn-sm btn-outline-secondary" id="barcode-stop">Stop</button>
              <div class="small text-muted mt-2">Tip: Use <code>http://localhost:8000</code> (or HTTPS) so the browser allows camera.</div>
            </div>
          </div>
        </div>
      </div>

      <hr>
      <div id="scan-log" style="max-height:260px; overflow:auto;"></div>
    </div>
  </div>
</div>
@endsection

@section('js')
{{-- We rely on the jQuery loaded by your layout (Bootstrap 3 needs jQuery < 3.0). --}}
{{-- Local html5-qrcode (you added this file in step A) --}}
<script src="{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}"></script>
{{-- Quagga from CDN (works on 127.0.0.1) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
(function(){
  if(!window.jQuery){ console.error('jQuery missing. Load jQuery 2.2.4 in layout.'); return; }
  var $ = window.jQuery;

  // ----- State -----
  var csrf='{{ csrf_token() }}';
  var masterId={{ $attendanceMaster->id }};
  var locked={{ $attendanceMaster->is_locked ? 'true' : 'false' }};
  var lastSent = { value: null, at: 0 };
  var qrScanner = null;       // Html5Qrcode
  var quaggaRunning = false;

  // ----- Helpers -----
  function okOrigin(){
    const ok = (location.protocol === 'https:') || (location.hostname === 'localhost') || (location.hostname === '127.0.0.1');
    $('#insecureBanner').toggleClass('d-none', ok);
    return ok;
  }
  function log(msg, cls){
    var el=$('<div class="small mb-1"></div>').text(new Date().toLocaleTimeString()+' — '+msg);
    if(cls) el.addClass(cls);
    $('#scan-log').prepend(el);
  }
  function throttle(code, ms){
    var now=Date.now();
    if(lastSent.value===code && (now-lastSent.at)<ms) return true;
    lastSent.value=code; lastSent.at=now; return false;
  }
  function submitCode(code, source){
    if(!code || locked) return;
    if(throttle(code, 1200)) return;

    $.post('{{ route('attendance.identify.check') }}',{
      _token: csrf,
      attendance_master_id: masterId,
      code: code
    }).done(function(){
      log('OK ('+(source||'')+'): '+code, 'text-success');
      $('#qr-result').text('Last: '+code);
      $('#barcode-result').text(code);
    }).fail(function(xhr){
      var err=(xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || ('HTTP '+xhr.status);
      log('ERR ('+(source||'')+'): '+code+' — '+err, 'text-danger');
    });
  }

  // ----- Tab switching (our own) -----
  function switchTab(target){
    $('.js-tab').removeClass('active').attr('aria-selected','false');
    $('.js-tab[data-target="'+target+'"]').addClass('active').attr('aria-selected','true');

    $('.tab-pane').removeClass('show active').addClass('d-none');
    $('#'+target).removeClass('d-none').addClass('show active');

    if(target==='qr'){ stopQuagga(); prepareQr(); }
    else if(target==='barcode'){ stopQr(); }
    else { stopQr(); stopQuagga(); setTimeout(function(){ $('#codeInput').trigger('focus'); }, 40); }
  }
  $('.js-tab').on('click', function(e){ e.preventDefault(); switchTab($(this).data('target')); });
  if (location.hash && ['manual','qr','barcode'].indexOf(location.hash.slice(1))>=0) switchTab(location.hash.slice(1));

  // ----- Manual -----
  $('#btnSubmitCode').on('click', function(){
    var code=$('#codeInput').val().trim();
    if(code) submitCode(code, 'manual');
    $('#codeInput').val('').focus();
  });
  $('#codeInput').on('keypress', function(e){ if(e.which===13) $('#btnSubmitCode').click(); });

  // ----- QR -----
  async function prepareQr(){
    okOrigin();
    if (!window.Html5Qrcode) {
      log('html5-qrcode not found at /vendor/html5-qrcode/html5-qrcode.min.js', 'text-danger');
      log('Download it and place it there (see Step A).', 'text-danger');
      return;
    }
    // pre-permission (optional)
    try{
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        const s = await navigator.mediaDevices.getUserMedia({ video:true });
        s.getTracks().forEach(t=>t.stop());
      }
    }catch(e){ /* ignore */ }

    populateQrCameras();
  }

  async function populateQrCameras(){
    try{
      const devices = await Html5Qrcode.getCameras();
      var $sel=$('#qr-camera').empty();
      if(!devices || !devices.length){
        $sel.append('<option value="">No cameras found (you can still press Start)</option>');
        log('No cameras detected (QR). Will try default camera on Start.', 'text-warning');
        return;
      }
      var backIdx = devices.findIndex(d => /back|rear|environment/i.test(d.label));
      devices.forEach(function(d,i){ $sel.append($('<option/>',{value:d.id,text:d.label || ('Camera '+(i+1))})); });
      $sel.val(backIdx>=0 ? devices[backIdx].id : devices[0].id);
    }catch(e){
      log('Camera enumerate failed: ' + (e && e.message ? e.message : e), 'text-danger');
    }
  }

  async function startQr(cameraId){
    if(locked){ log('Locked: QR disabled.','text-warning'); return; }
    if(!window.Html5Qrcode){ log('Html5Qrcode is not available.', 'text-danger'); return; }
    if(!qrScanner) qrScanner = new Html5Qrcode('qr-reader');
    try{
      $('#qr-reader').show();
      await qrScanner.start(
        cameraId ? { deviceId: { exact: cameraId } } : { facingMode: { exact: 'environment' } },
        { fps: 10, qrbox: { width: 260, height: 260 } },
        function(decoded){ submitCode(decoded, 'qr'); },
        function(){}
      );
      $('#qr-overlay').hide();
      log('QR camera started', 'text-success');
    }catch(e1){
      try{
        const devices = await Html5Qrcode.getCameras();
        if(!devices || !devices.length) throw e1;
        await qrScanner.start(
          devices[0].id,
          { fps:10, qrbox: 260 },
          function(decoded){ submitCode(decoded, 'qr'); },
          function(){}
        );
        $('#qr-overlay').hide();
        log('QR camera started (fallback first device)', 'text-success');
      }catch(e2){
        log('QR start failed: ' + (e2 && e2.message ? e2.message : e2), 'text-danger');
        $('#qr-result').text('QR error: ' + (e2 && e2.message ? e2.message : e2));
      }
    }
  }
  async function stopQr(){
    if(qrScanner){
      try{ await qrScanner.stop(); qrScanner.clear(); }catch(e){}
      qrScanner=null; $('#qr-overlay').show();
      log('QR camera stopped', 'text-muted');
    }
  }
  $('#qr-start').on('click', function(){ startQr($('#qr-camera').val()); });
  $('#qr-stop').on('click', stopQr);
  $('#qr-file').on('change', function(ev){
    var file = ev.target.files[0]; if(!file) return;
    if(!window.Html5Qrcode){ log('Html5Qrcode not available for file scan.', 'text-danger'); return; }
    const tmp = new Html5Qrcode('qr-reader');
    tmp.scanFile(file, true)
      .then(text => { submitCode(text, 'qr-file'); log('QR file decoded','text-success'); tmp.clear(); })
      .catch(err => { log('QR file error: '+err,'text-danger'); tmp.clear(); });
  });

  // ----- Barcode -----
  function startQuagga(){
    if(locked){ log('Locked: Barcode disabled.','text-warning'); return; }
    if(!window.Quagga){ log('Quagga is not available.', 'text-danger'); return; }
    if(quaggaRunning) return;
    try{
      Quagga.init({
        inputStream:{
          name:'Live', type:'LiveStream', target:document.querySelector('#barcode-video'),
          constraints:{ facingMode:'environment' }
        },
        decoder:{ readers:['code_128_reader','ean_reader','ean_8_reader'] },
        locate:true
      }, function(err){
        if(err){ log('Quagga init error: '+err, 'text-danger'); return; }
        Quagga.start(); quaggaRunning=true; log('Barcode camera started', 'text-success');
      });
      Quagga.offDetected();
      Quagga.onDetected(function(r){
        if(r && r.codeResult && r.codeResult.code){
          var code=r.codeResult.code;
          $('#barcode-result').text(code);
          submitCode(code, 'barcode');
        }
      });
    }catch(e){
      log('Quagga exception: ' + (e && e.message ? e.message : e), 'text-danger');
    }
  }
  function stopQuagga(){
    if(quaggaRunning){ Quagga.stop(); quaggaRunning=false; log('Barcode camera stopped','text-muted'); }
  }
  $('#barcode-start').on('click', startQuagga);
  $('#barcode-stop').on('click',  stopQuagga);

  // ----- Init -----
  okOrigin();
  log('Scanner ready');
  setTimeout(function(){ $('#codeInput').trigger('focus'); }, 80);
})();
</script>
@endsection
