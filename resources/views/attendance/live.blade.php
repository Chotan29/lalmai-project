{{-- resources/views/attendance/live.blade.php --}}
@extends('layouts.master')

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-1">Live Attendance — {{ $today }}</h3>
      <div class="text-muted">Current day only · Real-time</div>
    </div>
    <div class="d-flex align-items-center">
      <div class="custom-control custom-radio mr-3">
        <input type="radio" class="custom-control-input" name="type" id="tStudent" value="student" checked>
        <label class="custom-control-label" for="tStudent">Students</label>
      </div>
      <div class="custom-control custom-radio mr-3">
        <input type="radio" class="custom-control-input" name="type" id="tStaff" value="staff">
        <label class="custom-control-label" for="tStaff">Staff</label>
      </div>
      <a href="#" class="btn btn-outline-secondary ml-2" id="btnReload">Reload</a>
    </div>
  </div>

  {{-- Hierarchy filters (AJAX populates) --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="form-row">
        <div class="col-md-3 mb-2">
          <label class="small">Department</label>
          <select id="fDepartment" class="form-control form-control-sm">
            <option value="">All</option>
          </select>
        </div>
        <div class="col-md-3 mb-2">
          <label class="small">Faculty</label>
          <select id="fFaculty" class="form-control form-control-sm"><option value="">All</option></select>
        </div>
        <div class="col-md-3 mb-2">
          <label class="small">Semester</label>
          <select id="fSemester" class="form-control form-control-sm"><option value="">All</option></select>
        </div>
        <div class="col-md-3 mb-2">
          <label class="small">Batch</label>
          <select id="fBatch" class="form-control form-control-sm"><option value="">All</option></select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Left: scanner --}}
    <div class="col-lg-4 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <ul class="nav nav-tabs" id="scanTabs" role="tablist">
            <li class="nav-item"><a class="nav-link js-tab active" data-target="manual" href="#manual" role="tab">Manual / USB</a></li>
            <li class="nav-item"><a class="nav-link js-tab" data-target="qr" href="#qr" role="tab">QR (Webcam)</a></li>
            <li class="nav-item"><a class="nav-link js-tab" data-target="barcode" href="#barcode" role="tab">Barcode (Webcam)</a></li>
          </ul>

          <div class="tab-content pt-3">
            <div class="tab-pane show active" id="manual">
              <div class="form-inline">
                <input type="text" class="form-control mr-2" id="codeInput" placeholder="Focus here and scan/type reg_no" autofocus>
                <button id="btnSubmitCode" class="btn btn-primary">Submit</button>
              </div>
              <small class="text-muted d-block mt-2">USB readers behave like keyboard input.</small>
            </div>

            <div class="tab-pane d-none" id="qr">
              <div class="alert alert-warning d-none" id="insecureBanner">
                Your browser may block camera on this origin. Prefer <strong>http://localhost:8000</strong> or HTTPS.
              </div>
              <div class="form-inline mb-2">
                <select id="qr-camera" class="form-control form-control-sm" style="max-width:320px"></select>
                <button class="btn btn-sm btn-success ml-2" id="qr-start">Start</button>
                <button class="btn btn-sm btn-outline-secondary ml-2" id="qr-stop">Stop</button>
              </div>
              <div id="qr-reader" class="bg-dark" style="width:100%;height:300px;border-radius:8px;"></div>
              <div class="mt-2 text-muted" id="qr-result">QR ready.</div>
              <div class="mt-3">
                <label class="small mb-1">Scan from image:</label>
                <input type="file" id="qr-file" class="form-control-file" accept="image/*">
              </div>
            </div>

            <div class="tab-pane d-none" id="barcode">
              <div id="barcode-video" class="bg-dark" style="width:100%;height:300px;border-radius:8px;"></div>
              <div class="alert alert-info mt-2">Detected: <strong id="barcode-result">—</strong></div>
              <button class="btn btn-sm btn-success" id="barcode-start">Start</button>
              <button class="btn btn-sm btn-outline-secondary" id="barcode-stop">Stop</button>
              <small class="text-muted d-block mt-2">QuaggaJS supports Code128 / EAN / EAN-8.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mt-3">
        <div class="card-body" style="max-height:240px;overflow:auto;">
          <div class="small text-muted mb-2">Live log</div>
          <div id="scan-log"></div>
        </div>
      </div>
    </div>

    {{-- Right: grid --}}
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="grid">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Reg/Code</th>
                <th>Status</th>
                <th>In</th>
                <th>Out</th>
                <th style="width:240px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="7" class="text-muted">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card shadow-sm mt-3">
        <div class="card-body">
          <div class="small text-muted mb-2">Legend</div>
          @foreach($statuses as $s)
            <span class="badge badge-light" style="border:1px solid #ddd;background: {{ $s->color ?: '#e9ecef' }};">{{ $s->code }} — {{ $s->label }}</span>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
{{-- jQuery should already be in your layout. If using Bootstrap 3, jQuery 2.x is fine. --}}
<script src="{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
(function(){
  if(!window.jQuery){ console.error('jQuery required'); return; }
  var $=jQuery;

  // ---------- State ----------
  var listType = 'student';
  var masterId = null; // internal
  var csrf='{{ csrf_token() }}';
  var qrScanner=null, quaggaRunning=false;

  // ---------- Helpers ----------
  function okOrigin(){
    var ok = (location.protocol==='https:') || (location.hostname==='localhost') || (location.hostname==='127.0.0.1');
    $('#insecureBanner').toggleClass('d-none', ok);
  }
  function log(msg, cls){
    var el=$('<div class="mb-1"></div>').text(new Date().toLocaleTimeString()+' — '+msg);
    if(cls) el.addClass(cls);
    $('#scan-log').prepend(el);
  }
  function currentFilters(){
    return {
      department_id: $('#fDepartment').val() || '',
      faculty_id:    $('#fFaculty').val()    || '',
      semester_id:   $('#fSemester').val()   || '',
      batch_id:      $('#fBatch').val()      || ''
    };
  }
  function renderGrid(rows){
    var $tb=$('#grid tbody').empty();
    if(!rows || !rows.length){
      $tb.append('<tr><td colspan="7" class="text-muted">No records</td></tr>');
      return;
    }
    rows.forEach(function(r,i){
      var status = r.status || '—';
      var rid = r.id ? ' data-row-id="'+r.id+'"' : '';
      $tb.append(
        '<tr'+rid+' data-pid="'+r.pid+'" data-code="'+(status||'')+'">'
        +'<td>'+(i+1)+'</td>'
        +'<td class="font-weight-bold">'+ (r.name||'—') +'</td>'
        +'<td><span class="text-monospace">'+(r.code||'')+'</span></td>'
        +'<td><span class="badge badge-pill badge-light">'+status+'</span></td>'
        +'<td>'+(r.in||'')+'</td>'
        +'<td>'+(r.out||'')+'</td>'
        +'<td>'
          +'<div class="btn-group btn-group-sm">'
            +'<button class="btn btn-outline-success act-mark" data-code="P">P</button>'
            +'<button class="btn btn-outline-warning act-mark" data-code="L">L</button>'
            +'<button class="btn btn-outline-info act-mark" data-code="E">E</button>'
            +'<button class="btn btn-outline-secondary act-mark" data-code="HL">HL</button>'
            +'<button class="btn btn-outline-danger act-mark" data-code="A">A</button>'
            +'<button class="btn btn-dark act-check">Check</button>'
          +'</div>'
        +'</td>'
        +'</tr>'
      );
    });
  }

  function loadList(){
    var url='{{ route('attendance.live.list') }}';
    var q = $.param($.extend({type:listType}, currentFilters()));
    $('#grid tbody').html('<tr><td colspan="7" class="text-muted">Loading…</td></tr>');
    $.get(url+'?'+q, function(res){
      masterId = res.master_id || null;
      renderGrid(res.data || []);
    }).fail(function(xhr){
      $('#grid tbody').html('<tr><td colspan="7" class="text-danger">Failed to load.</td></tr>');
    });
  }

  function submitCode(code, source){
    code = String(code||'').trim();
    if(!code) return;
    var payload = $.extend({ _token:csrf, code:code, source:source||'manual' }, currentFilters());
    $.post('{{ route('attendance.identify') }}', payload, function(res){
      log('OK ('+(source||'')+'): '+code, 'text-success');
      $('#qr-result').text('Last: '+code);
      $('#barcode-result').text(code);
      loadList(); // refresh grid
    }).fail(function(xhr){
      var err=(xhr.responseJSON && (xhr.responseJSON.message||xhr.responseJSON.error)) || ('HTTP '+xhr.status);
      log('ERR ('+(source||'')+'): '+code+' — '+err, 'text-danger');
    });
  }

  // ---------- Tabs ----------
  function switchTab(target){
    $('.js-tab').removeClass('active');
    $('.js-tab[data-target="'+target+'"]').addClass('active');
    $('.tab-pane').removeClass('show active').addClass('d-none');
    $('#'+target).removeClass('d-none').addClass('show active');

    if (target==='qr'){ stopQuagga(); startQrPrep(); }
    else if (target==='barcode'){ stopQr(); }
    else { stopQr(); stopQuagga(); setTimeout(function(){ $('#codeInput').trigger('focus'); }, 50); }
  }
  $('.js-tab').on('click', function(e){ e.preventDefault(); switchTab($(this).data('target')); });
  if (location.hash && ['manual','qr','barcode'].indexOf(location.hash.slice(1))>=0) switchTab(location.hash.slice(1));

  // ---------- Manual ----------
  $('#btnSubmitCode').on('click', function(){
    var code=$('#codeInput').val(); submitCode(code, 'manual');
    $('#codeInput').val('').focus();
  });
  $('#codeInput').on('keypress', function(e){ if(e.which===13) $('#btnSubmitCode').click(); });

  // ---------- QR ----------
  function startQrPrep(){
    okOrigin();
    if (!window.Html5Qrcode){ log('html5-qrcode not loaded from /vendor/html5-qrcode/html5-qrcode.min.js','text-danger'); return; }
    Html5Qrcode.getCameras().then(function(devices){
      var $sel=$('#qr-camera').empty();
      if(!devices || !devices.length){ $sel.append('<option value="">Default camera</option>'); return; }
      var back = devices.find(function(d){ return /back|rear|environment/i.test(d.label); });
      devices.forEach(function(d,i){ $sel.append($('<option/>',{value:d.id,text:d.label||('Camera '+(i+1))})); });
      $sel.val(back ? back.id : devices[0].id);
    }).catch(function(e){
      log('Camera enumerate failed: '+ (e && e.message ? e.message : e), 'text-danger');
    });
  }
  function startQr(cameraId){
    if(qrScanner) return;
    qrScanner = new Html5Qrcode('qr-reader');
    var cfg = { fps:10, qrbox: { width:240, height:240 } };
    var cam = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: { exact:'environment' } };

    qrScanner.start(cam, cfg,
      function(decoded){ submitCode(decoded, 'qr'); },
      function(){}).then(function(){ log('QR camera started','text-success'); })
      .catch(function(err){
        log('QR start failed: '+ (err && err.message ? err.message : err), 'text-danger');
      });
  }
  function stopQr(){
    if(qrScanner){
      qrScanner.stop().then(function(){ qrScanner.clear(); qrScanner=null; log('QR camera stopped','text-muted'); })
      .catch(function(){ qrScanner=null; });
    }
  }
  $('#qr-start').on('click', function(){ startQr($('#qr-camera').val()); });
  $('#qr-stop').on('click', stopQr);
  $('#qr-file').on('change', function(ev){
    var file=ev.target.files[0]; if(!file) return;
    if(!window.Html5Qrcode) return;
    var tmp=new Html5Qrcode('qr-reader');
    tmp.scanFile(file, true)
      .then(function(text){ submitCode(text,'qr-file'); log('QR image decoded','text-success'); tmp.clear(); tmp=null; })
      .catch(function(e){ log('QR image error: '+e,'text-danger'); tmp && tmp.clear(); tmp=null; });
  });

  // ---------- Barcode (Quagga) ----------
  function startQuagga(){
    if(quaggaRunning) return;
    if(!window.Quagga){ log('Quagga not available','text-danger'); return; }
    try{
      Quagga.init({
        inputStream:{ name:'Live', type:'LiveStream', target:document.querySelector('#barcode-video'), constraints:{ facingMode:'environment' } },
        decoder:{ readers:['code_128_reader','ean_reader','ean_8_reader'] },
        locate:true
      }, function(err){
        if(err){ log('Quagga init error: '+err, 'text-danger'); return; }
        Quagga.start(); quaggaRunning=true; log('Barcode camera started','text-success');
      });
      Quagga.offDetected();
      Quagga.onDetected(function(r){
        if(r && r.codeResult && r.codeResult.code){
          var code=r.codeResult.code;
          $('#barcode-result').text(code);
          submitCode(code,'barcode');
        }
      });
    }catch(e){ log('Quagga exception: '+e,'text-danger'); }
  }
  function stopQuagga(){ if(quaggaRunning){ Quagga.stop(); quaggaRunning=false; log('Barcode camera stopped','text-muted'); } }
  $('#barcode-start').on('click', startQuagga);
  $('#barcode-stop').on('click',  stopQuagga);

  // ---------- Grid Row Actions ----------
  $(document).on('click','.act-mark',function(){
    var $tr=$(this).closest('tr');
    var id=$tr.data('row-id'); if(!id){ log('No row yet. Do a scan first.','text-danger'); return; }
    var code=$(this).data('code');
    $.post('{{ route('attendance.row.mark',['attendance'=>'__ID__']) }}'.replace('__ID__',id), {_token:csrf, code:code}, function(res){
      loadList();
    });
  });
  $(document).on('click','.act-check',function(){
    var $tr=$(this).closest('tr');
    var id=$tr.data('row-id'); if(!id){ log('No row yet. Do a scan first.','text-danger'); return; }
    $.post('{{ route('attendance.row.check',['attendance'=>'__ID__']) }}'.replace('__ID__',id), {_token:csrf}, function(){
      loadList();
    });
  });

  // ---------- Hierarchy filters ----------
  function loadDepartments(){
    return $.get('{{ route('get-departments') }}',{},function(res){
      var $s=$('#fDepartment').empty().append('<option value="">All</option>');
      $.each(res || {}, function(id, label){ $s.append($('<option/>',{value:id, text:label})); });
    });
  }
  function loadFaculties(depId){
    $('#fFaculty').empty().append('<option value="">All</option>');
    $('#fSemester').empty().append('<option value="">All</option>');
    $('#fBatch').empty().append('<option value="">All</option>');
    if(!depId) return;
    $.get('{{ route('get-faculties') }}',{ department_id: depId }, function(res){
      var $s=$('#fFaculty').empty().append('<option value="">All</option>');
      $.each(res || {}, function(id,label){ $s.append($('<option/>',{value:id,text:label})); });
    });
  }
  function loadSemesters(facId){
    $('#fSemester').empty().append('<option value="">All</option>');
    $('#fBatch').empty().append('<option value="">All</option>');
    if(!facId) return;
    $.get('{{ route('get-semesters') }}',{ faculty_id: facId }, function(res){
      var $s=$('#fSemester').empty().append('<option value="">All</option>');
      $.each(res || {}, function(id,label){ $s.append($('<option/>',{value:id,text:label})); });
    });
  }
  function loadBatches(semId){
    $('#fBatch').empty().append('<option value="">All</option>');
    if(!semId) return;
    $.get('{{ route('get-batches') }}',{ semester_id: semId }, function(res){
      var $s=$('#fBatch').empty().append('<option value="">All</option>');
      $.each(res || {}, function(id,label){ $s.append($('<option/>',{value:id,text:label})); });
    });
  }

  $('#fDepartment').on('change', function(){ loadFaculties($(this).val()); loadList(); });
  $('#fFaculty').on('change', function(){ loadSemesters($(this).val()); loadList(); });
  $('#fSemester').on('change', function(){ loadBatches($(this).val()); loadList(); });
  $('#fBatch').on('change', loadList);

  // ---------- Type switch ----------
  $('input[name="type"]').on('change', function(){
    listType = $(this).val()==='staff' ? 'staff' : 'student';
    loadList();
  });

  // ---------- Boot ----------
  $('#btnReload').on('click', function(e){ e.preventDefault(); loadList(); });
  okOrigin();
  loadDepartments().then(function(){ loadList(); });

})();
</script>
@endsection
