{{-- resources/views/attendance/live/index.blade.php --}}
@extends('layouts.master')

@section('css')
<style>
  :root{
    --primary:#2563eb; --primary-2:#1d4ed8; --success:#10b981; --danger:#ef4444; --warning:#f59e0b; --info:#0ea5e9;
    --muted:#64748b; --border:#e5e7eb; --bg:#f8fafc; --card:#ffffff; --shadow:0 8px 20px rgba(2,6,23,.06);
  }
  body{background:var(--bg);}
  .page-wrap{padding:16px;}
  .head{
    display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:14px;
  }
  .title h3{margin:0;font-weight:800;color:#0f172a}
  .title small{color:var(--muted)}
  .actions .btn{margin-left:8px}
  .card{
    background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);
  }
  .card-head{padding:12px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
  .card-body{padding:14px}
  .tabs{
    display:flex;gap:8px;border-bottom:1px solid var(--border);padding:10px 12px;margin-bottom:10px;overflow:auto
  }
  .tab-btn{
    border:1px solid var(--border);background:#fff;border-radius:999px;padding:8px 14px;cursor:pointer;font-weight:600;color:#334155;
  }
  .tab-btn.active{background:var(--primary);color:#fff;border-color:var(--primary)}
  .tab-pane{display:none}
  .tab-pane.active{display:block}
  .legend .badge{
    display:inline-block;border:1px solid var(--border);border-radius:999px;padding:2px 8px;margin-right:6px;margin-bottom:6px;font-size:12px
  }
  .filters{display:flex;flex-wrap:wrap;gap:8px}
  .chip{border:1px dashed var(--border);border-radius:10px;padding:6px 10px;color:#334155;background:#fff}
  .chip strong{color:#0f172a}
  .hier{display:flex;gap:10px;overflow:auto}
  .hier-col{min-width:220px;border:1px solid var(--border);border-radius:12px;background:#fff}
  .hier-col .h{padding:10px 12px;border-bottom:1px solid var(--border);font-weight:700;color:#0f172a}
  .hier-col .items{max-height:300px;overflow:auto;padding:10px}
  .tag{padding:8px 10px;border-radius:10px;background:#f8fafc;margin-bottom:6px;cursor:pointer;border:1px solid transparent}
  .tag:hover{background:#eef2ff;border-color:#dbeafe}
  .tag.active{background:#2563eb;color:#fff;border-color:#2563eb}
  .list-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
  .seg{display:inline-flex;border:1px solid var(--border);border-radius:999px;overflow:hidden}
  .seg button{padding:6px 12px;border:none;background:#fff;color:#334155;cursor:pointer}
  .seg button.active{background:var(--primary);color:#fff}
  .tbl{width:100%;border-collapse:separate;border-spacing:0 6px}
  .tbl thead th{font-size:12px;text-transform:uppercase;color:#475569;padding:6px 10px}
  .tbl tbody tr{background:#fff;border:1px solid var(--border)}
  .tbl tbody td{padding:8px 10px;border-top:1px solid var(--border)}
  .pill{border-radius:999px;padding:2px 8px;border:1px solid var(--border);font-size:12px}
  .p-success{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
  .p-warning{background:#fffbeb;color:#92400e;border-color:#fde68a}
  .p-danger{background:#fef2f2;color:#991b1b;border-color:#fecaca}
  .p-info{background:#eff6ff;color:#1e40af;border-color:#bfdbfe}
  .btn{display:inline-flex;align-items:center;gap:6px;border-radius:8px;padding:8px 12px;cursor:pointer;border:1px solid var(--border);background:#fff}
  .btn.primary{background:var(--primary);border-color:var(--primary);color:#fff}
  .btn.muted{color:#334155}
  .btn.icon{padding:6px 10px}
  .btn-group .btn{margin-right:6px}
  .muted{color:var(--muted)}
  .scan-log{max-height:180px;overflow:auto;background:#0b1220;color:#cbd5e1;border-radius:10px;padding:10px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace}
  .scan-log .ok{color:#a7f3d0}
  .scan-log .err{color:#fecaca}
  .help{font-size:12px;color:var(--muted)}
  .notice{background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12;padding:8px 10px;border-radius:8px}
  @media (min-width:1200px){
    .grid{display:grid;grid-template-columns:2fr 1.2fr;gap:14px}
  }
</style>
@endsection

@section('content')
@php
  /** Data passed via CollegeBaseController pattern */
  $today    = $data['today']    ?? now()->toDateString();
  $statuses = $data['statuses'] ?? collect();
@endphp

<div class="page-wrap">
  <div class="head">
    <div class="title">
      <h3>Live Attendance — {{ $today }}</h3>
      <small>Unified student & staff capture • Real-time • Manual / QR / Barcode / TIPSOI</small>
    </div>
    <div class="actions">
      <button class="btn muted" id="btn-origin-check" title="Camera help">
        <i class="fas fa-camera"></i> Camera Help
      </button>
      <button class="btn primary" id="btn-refresh">
        <i class="fas fa-sync-alt"></i> Refresh List
      </button>
    </div>
  </div>

  {{-- Filters + legend --}}
  <div class="card" style="margin-bottom:12px">
    <div class="card-body">
      <div class="filters" style="margin-bottom:10px">
        <div class="chip">Date: <strong>{{ $today }}</strong> (today only)</div>
        <div class="chip" id="chip-summary">Present: <strong id="sum-present">0</strong> · Absent: <strong id="sum-absent">0</strong></div>
        <div class="legend">
          @foreach($statuses as $s)
            <span class="badge" style="background: {{ $s->color }}">{{ $s->code }} — {{ $s->label }}</span>
          @endforeach
        </div>
      </div>

      {{-- Academic hierarchy --}}
      <div class="hier">
        <div class="hier-col">
          <div class="h"><i class="fas fa-university"></i> Departments</div>
          <div class="items" id="lv-departments"></div>
        </div>
        <div class="hier-col">
          <div class="h"><i class="fas fa-graduation-cap"></i> Faculties</div>
          <div class="items" id="lv-faculties"></div>
        </div>
        <div class="hier-col">
          <div class="h"><i class="fas fa-layer-group"></i> Semesters</div>
          <div class="items" id="lv-semesters"></div>
        </div>
        <div class="hier-col">
          <div class="h"><i class="fas fa-users"></i> Batches</div>
          <div class="items" id="lv-batches"></div>
        </div>
      </div>

      <div class="help">Select a hierarchy to filter students. Staff list ignores faculty/semester/batch.</div>
    </div>
  </div>

  <div class="grid">
    {{-- LEFT: list --}}
    <div class="card">
      <div class="card-head">
        <div class="list-head">
          <div class="seg" id="seg-type">
            <button data-type="all" class="active" aria-pressed="true">All</button>
            <button data-type="student">Students</button>
            <button data-type="staff">Staff</button>
          </div>
        </div>
        <div>
          <input id="q" type="text" class="form-control" placeholder="Search name / reg no" style="max-width:240px">
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="tbl" id="grid">
            <thead>
              <tr>
                <th>#</th>
                <th>Person</th>
                <th>Type</th>
                <th>Reg No</th>
                <th>Status</th>
                <th>In</th>
                <th>Out</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="grid-body">
              <tr><td colspan="8" class="muted" id="placeholder-row">Select filters or start scanning…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- RIGHT: scanner --}}
    <div class="card">
      <div class="card-head">
        <div><strong>Scanner</strong></div>
        <div class="notice" id="origin-hint" style="display:none">
          Your browser may block camera on <strong>http://127.0.0.1</strong>. Prefer <strong>http://localhost</strong> or <strong>HTTPS</strong>.
        </div>
      </div>
      <div class="card-body">

        {{-- tabs --}}
        <div class="tabs" role="tablist" aria-label="Scanner Tabs">
          <button class="tab-btn active" data-tab="manual"  id="tab-manual">Manual / USB</button>
          <button class="tab-btn"        data-tab="qr"      id="tab-qr">QR (Webcam)</button>
          <button class="tab-btn"        data-tab="barcode" id="tab-barcode">Barcode (Webcam)</button>
        </div>

        {{-- Manual / USB --}}
        <div class="tab-pane active" id="pane-manual" role="tabpanel" aria-labelledby="tab-manual">
          <div style="display:flex;gap:8px;align-items:center">
            <input type="text" id="manual-code" class="form-control" placeholder="Focus here then scan / type reg no" autofocus style="max-width:320px">
            <button class="btn primary" id="btn-manual"><i class="fas fa-paper-plane"></i> Submit</button>
          </div>
          <div class="help" style="margin-top:6px">USB barcode readers act like keyboard input. Keep focus on the box and scan.</div>
        </div>

        {{-- QR --}}
        <div class="tab-pane" id="pane-qr" role="tabpanel" aria-labelledby="tab-qr">
          <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px">
            <select id="qr-cameras" class="form-control" style="max-width:320px"></select>
            <button class="btn primary" id="qr-start"><i class="fas fa-video"></i> Start</button>
            <button class="btn" id="qr-stop"><i class="fas fa-stop"></i> Stop</button>
          </div>
          <div id="qr-view" style="max-width:460px"></div>
          <div class="help" style="margin-top:6px">Supported by <code>html5-qrcode</code>. If you see “blocked origin”, use <strong>http://localhost</strong> or HTTPS.</div>
        </div>

        {{-- Barcode --}}
        <div class="tab-pane" id="pane-barcode" role="tabpanel" aria-labelledby="tab-barcode">
          <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px">
            <button class="btn primary" id="bc-start"><i class="fas fa-video"></i> Start</button>
            <button class="btn" id="bc-stop"><i class="fas fa-stop"></i> Stop</button>
          </div>
          <div id="bc-wrap" style="width:100%;max-width:520px;height:320px;background:#0b1220;border-radius:10px;display:flex;align-items:center;justify-content:center">
            <div id="bc-video" style="width:100%;height:100%"></div>
          </div>
          <div class="help" style="margin-top:6px">Supported via QuaggaJS (Code128 / EAN / EAN-8).</div>
        </div>

        <hr>
        <div class="scan-log" id="scan-log" aria-live="polite"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
{{-- Font Awesome (icons) – optional if not already in your layout --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

{{-- QR & Barcode libs --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
(function () {
  "use strict";

  // ---------- Blade-safe constants ----------
  const CSRF  = '{{ csrf_token() }}';
  const TODAY = @json($data['today'] ?? ($today ?? now()->toDateString()));

  // Dump raw statuses from PHP, then normalize in JS (avoids PHP mapping syntax issues)
  const RAW_STATUSES = @json($data['statuses'] ?? ($statuses ?? []));
  const STATUSES = (Array.isArray(RAW_STATUSES) ? RAW_STATUSES : []).map(function(s){
    return {
      id:    (s && (s.id   !== undefined)) ? s.id   : (s && s['id']   !== undefined ? s['id']   : null),
      code:  (s && (s.code !== undefined)) ? s.code : (s && s['code'] !== undefined ? s['code'] : null),
      label: (s && (s.label!== undefined)) ? s.label: (s && s['label']!== undefined ? s['label']: null),
      color: (s && (s.color!== undefined && s.color)) ? s.color : (s && s['color'] ? s['color'] : '#e5e7eb'),
    };
  });

  const ROUTES = {
    list:     '{{ route('attendance.live.list') }}',
    identify: '{{ route('attendance.identify') }}',
    rowBase:  "{{ url('attendance/row') }}/" // + {id}/mark or {id}/check
  };

  // ---------- Utilities ----------
  function qs(sel, ctx){ return (ctx||document).querySelector(sel); }
  function qsa(sel, ctx){ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }
  function el(tag, cls){ const n=document.createElement(tag); if(cls) n.className=cls; return n; }

  const logBox = qs('#scan-log');
  function log(msg, type) {
    if(!logBox) return;
    const row = el('div','small mb-1');
    if (type==='ok')   row.classList.add('text-success');
    if (type==='err')  row.classList.add('text-danger');
    if (type==='warn') row.classList.add('text-warning');
    row.textContent = new Date().toLocaleTimeString() + ' — ' + msg;
    logBox.prepend(row);
  }

  function statusByCode(code) {
    if(!code) return {code:'—', label:'—', color:'#e5e7eb'};
    const m = STATUSES.find(function(s){ return String(s.code).toUpperCase() === String(code).toUpperCase(); });
    return m || {code:String(code).toUpperCase(), label:String(code).toUpperCase(), color:'#e5e7eb'};
  }

  // ---------- List + table rendering ----------
  const tbody = qs('#grid tbody');

  function currentFilters() {
    const get = function(name){
      const x = qs('[name="'+name+'"]');
      const v = x ? (x.value || '').trim() : '';
      return v ? v : null;
    };
    return {
      department_head_id: get('department_head_id'),
      department_id:      get('department_id'),
      faculty_id:         get('faculty_id'),
      semester_id:        get('semester_id'),
      student_batch_id:   get('student_batch_id'),
      q:                  (qs('#quick-search')?.value || '').trim() || null,
      date: TODAY
    };
  }

  function badgeHTML(code) {
    const s = statusByCode(code);
    const b = el('span','badge badge-pill status-pill');
    b.style.background = s.color || '#e9ecef';
    b.textContent = s.code || '—';
    return b.outerHTML;
  }

  function renderRows(rows) {
    if(!tbody) return;
    tbody.innerHTML = '';
    if(!rows || !rows.length){
      const tr = el('tr');
      const td = el('td','text-muted');
      td.colSpan = 6;
      td.textContent = 'No people found with current filters.';
      tr.appendChild(td); tbody.appendChild(tr); return;
    }
    rows.forEach(function(r, idx){
      const tr = el('tr');
      tr.setAttribute('data-row-id', r.attendance_id);
      tr.setAttribute('data-code',  r.status_code || '—');
      tr.innerHTML =
        '<td>'+(idx+1)+'</td>'+
        '<td>'+
          '<div class="font-weight-bold">'+ (r.display_name || r.name || '—') +'</div>'+
          '<small class="text-muted">'+ (r.type ? r.type.toUpperCase() : '—') +' #'+ (r.link_id || r.id || '') +'</small>'+
        '</td>'+
        '<td>'+ badgeHTML(r.status_code) +'</td>'+
        '<td>'+ (r.check_in_at  ? r.check_in_at.substr(11,5)  : '') +'</td>'+
        '<td>'+ (r.check_out_at ? r.check_out_at.substr(11,5) : '') +'</td>'+
        '<td>'+
          '<div class="btn-group btn-group-sm">'+
            '<button class="btn btn-outline-success btn-mark"  data-code="P"  title="Present">P</button>'+
            '<button class="btn btn-outline-warning btn-mark"  data-code="L"  title="Late">L</button>'+
            '<button class="btn btn-outline-info btn-mark"     data-code="E"  title="Excused">E</button>'+
            '<button class="btn btn-outline-secondary btn-mark"data-code="HL" title="Half Leave">HL</button>'+
            '<button class="btn btn-outline-danger btn-mark"   data-code="A"  title="Absent">A</button>'+
            '<button class="btn btn-outline-dark btn-check"    title="Check-in/out now">Check</button>'+
          '</div>'+
        '</td>';
      tbody.appendChild(tr);
    });
    refreshSummaryBadges();
  }

  function refreshSummaryBadges(){
    const wrap = qs('#summary-pills');
    if(!wrap) return;
    const counts = {};
    qsa('#grid tbody tr').forEach(function(tr){
      const code = tr.getAttribute('data-code') || '—';
      counts[code] = (counts[code]||0)+1;
    });
    wrap.innerHTML = '';
    Object.keys(counts).sort().forEach(function(code){
      const s = statusByCode(code);
      const pill = el('span','badge mr-1 mb-1');
      pill.style.background = s.color || '#e9ecef';
      pill.textContent = (s.code||'—') + ' ' + counts[code];
      wrap.appendChild(pill);
    });
  }

  async function loadList() {
    try{
      const params = currentFilters();
      const q = new URLSearchParams();
      Object.keys(params).forEach(function(k){ if(params[k]!==null && params[k]!==undefined) q.set(k, params[k]); });
      const res = await fetch(ROUTES.list + '?' + q.toString(), { credentials:'same-origin' });
      if(!res.ok) throw new Error('List load failed: HTTP '+res.status);
      const json = await res.json();
      renderRows(json.rows || []);
      log('People list loaded ('+(json.rows?json.rows.length:0)+')','ok');
    }catch(e){
      console.error(e);
      log('Failed to load people list','err');
    }
  }

  // ---------- Inline actions ----------
  async function markRow(attId, code) {
    try{
      if(!attId || !code) return;
      const form = new FormData();
      form.append('_token', CSRF);
      form.append('code', code);
      const res = await fetch(ROUTES.rowBase + attId + '/mark', {
        method:'POST', body: form, credentials:'same-origin'
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      const json = await res.json();
      const tr = qs('tr[data-row-id="'+attId+'"]');
      if(tr){
        const finalCode = (json.row && json.row.status && json.row.status.code) ? json.row.status.code : code;
        tr.setAttribute('data-code', finalCode || '—');
        const pill = tr.querySelector('.status-pill');
        if (pill) {
          const s = statusByCode(finalCode);
          pill.style.background = s.color || '#e9ecef';
          pill.textContent = s.code || '—';
        }
      }
      refreshSummaryBadges();
      log('Marked #'+attId+' as '+code,'ok');
    }catch(e){
      console.error(e);
      log('Failed to mark row '+attId,'err');
    }
  }

  async function checkRow(attId) {
    try{
      if(!attId) return;
      const form = new FormData();
      form.append('_token', CSRF);
      const res = await fetch(ROUTES.rowBase + attId + '/check', {
        method:'POST', body: form, credentials:'same-origin'
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      const json = await res.json();
      const tr = qs('tr[data-row-id="'+attId+'"]');
      if(tr){
        if(json.row && json.row.check_in_at)  tr.children[3].textContent = json.row.check_in_at.substr(11,5);
        if(json.row && json.row.check_out_at) tr.children[4].textContent = json.row.check_out_at.substr(11,5);
        if(json.row && json.row.attendance_status_id){
          const code = tr.getAttribute('data-code') || 'P';
          const s = statusByCode(code);
          const pill = tr.querySelector('.status-pill');
          if (pill){ pill.style.background=s.color; pill.textContent=s.code; }
        }
      }
      log('Checked #'+attId,'ok');
    }catch(e){
      console.error(e);
      log('Failed to check row '+attId,'err');
    }
  }

  document.addEventListener('click', function(ev){
    const btn = ev.target.closest('.btn-mark');
    if(btn){
      ev.preventDefault();
      const tr = ev.target.closest('tr');
      const id = tr ? tr.getAttribute('data-row-id') : null;
      const code = btn.getAttribute('data-code');
      markRow(id, code);
      return;
    }
    const chk = ev.target.closest('.btn-check');
    if(chk){
      ev.preventDefault();
      const tr = ev.target.closest('tr');
      const id = tr ? tr.getAttribute('data-row-id') : null;
      checkRow(id);
      return;
    }
  });

  // ---------- Identify endpoint (manual/QR/barcode/SDK) ----------
  async function submitIdentify(code, source) {
    if(!code) return;
    try{
      const form = new FormData();
      form.append('_token', CSRF);
      form.append('code', code);
      form.append('date', TODAY);
      form.append('source', source || 'manual');

      const res = await fetch(ROUTES.identify, {
        method:'POST', body: form, credentials:'same-origin'
      });
      const json = await res.json();
      if(!res.ok){
        const msg = json && json.message ? json.message : ('HTTP '+res.status);
        throw new Error(msg);
      }

      if(json.row){
        const rid = json.row.attendance_id || json.row.id;
        let tr = qs('tr[data-row-id="'+rid+'"]');
        if(!tr){
          await loadList();
        }else{
          tr.setAttribute('data-code', json.row.status_code || tr.getAttribute('data-code') || 'P');
          tr.children[2].innerHTML = badgeHTML(json.row.status_code || 'P');
          if(json.row.check_in_at)  tr.children[3].textContent = json.row.check_in_at.substr(11,5);
          if(json.row.check_out_at) tr.children[4].textContent = json.row.check_out_at.substr(11,5);
        }
      }

      const who = (json.matched && (json.matched.name || json.matched.display_name)) ? (' '+(json.matched.name || json.matched.display_name)) : '';
      log('OK ('+(source||'manual')+'): '+code+who,'ok');
      return true;
    }catch(e){
      log('ERR ('+(source||'manual')+'): '+code+' — '+e.message,'err');
      return false;
    }
  }

  // Manual / USB input
  const manualInput = qs('#manual-code');
  const manualBtn   = qs('#btn-submit-manual');
  if (manualBtn && manualInput) {
    manualBtn.addEventListener('click', function(){
      const v = manualInput.value.trim();
      manualInput.value = '';
      manualInput.focus();
      if(v) submitIdentify(v,'manual');
    });
    manualInput.addEventListener('keypress', function(e){
      if(e.which===13 || e.key==='Enter'){ e.preventDefault(); manualBtn.click(); }
    });
  }

  // ---------- Tab handling ----------
  function onTabShown(id, cb){
    qsa('a[data-toggle="tab"]').forEach(function(a){
      a.addEventListener('shown.bs.tab', function(){
        if(a.getAttribute('href')===id && typeof cb==='function') cb();
      });
    });
  }

  // ---------- html5-qrcode (QR Webcam) ----------
  const QR = { started: false, instance: null, lastDeviceId: null };
  const QR_SOURCES = [
    'https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js',
    '{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}'
  ];
  function loadScriptSeq(sources) {
    return sources.reduce(function(chain, src){
      return chain.catch(function(){
        return new Promise(function(resolve, reject){
          const s = document.createElement('script');
          s.src = src; s.async = true;
          s.onload = function(){ resolve(true); };
          s.onerror= function(){ reject(new Error('Failed to load '+src)); };
          document.head.appendChild(s);
        });
      });
    }, Promise.reject(new Error('start')));
  }
  function ensureHtml5Qrcode() {
    if (window.Html5Qrcode) return Promise.resolve(true);
    return loadScriptSeq(QR_SOURCES).then(function(){ return !!window.Html5Qrcode; }).catch(function(){
      log('Failed to load html5-qrcode from all sources.','err'); return false;
    });
  }
  function originHintIfNeeded(){
    const host = location.hostname;
    if (host === '127.0.0.1') {
      log('Camera may be blocked on this origin. Prefer http://localhost:8000 or HTTPS.','warn');
    }
  }

  const qrStartBtn = qs('#btn-start-qr');
  const qrStopBtn  = qs('#btn-stop-qr');
  const qrBox      = qs('#qr-reader');
  const qrResult   = qs('#qr-result');

  async function startQR() {
    if(QR.started) return;
    try{
      originHintIfNeeded();
      const ok = await ensureHtml5Qrcode();
      if(!ok || !window.Html5Qrcode) throw new Error('html5-qrcode not available');
      if(!qrBox) throw new Error('QR mount element missing');

      const cams = await Html5Qrcode.getCameras();
      if(!cams || !cams.length) throw new Error('No camera found (permission?)');
      const useId = QR.lastDeviceId || cams[0].id;

      QR.instance = new Html5Qrcode("qr-reader", { verbose: false });
      await QR.instance.start(
        useId,
        { fps: 10, qrbox: 250 },
        function(decodedText){
          if (decodedText) {
            if(qrResult) qrResult.textContent = 'Last: '+decodedText;
            submitIdentify(decodedText,'qr');
          }
        },
        function(){ /* ignore frame errors */ }
      );
      QR.started = true;
      QR.lastDeviceId = useId;
      if(qrStartBtn) qrStartBtn.disabled = true;
      if(qrStopBtn)  qrStopBtn.disabled  = false;
      log('QR camera started','ok');
    }catch(e){
      log('Failed to start QR: '+e.message,'err');
    }
  }
  async function stopQR() {
    try{
      if(QR.instance){ await QR.instance.stop(); await QR.instance.clear(); }
    }catch(_){} finally{
      QR.instance = null; QR.started = false;
      if(qrStartBtn) qrStartBtn.disabled = false;
      if(qrStopBtn)  qrStopBtn.disabled  = true;
      log('QR camera stopped');
    }
  }
  if(qrStartBtn) qrStartBtn.addEventListener('click', startQR);
  if(qrStopBtn)  qrStopBtn.addEventListener('click',  stopQR);
  onTabShown('#qr-pane', function(){ /* no auto-start; user must click Start */ });

  // ---------- Quagga (Barcode Webcam) ----------
  const BARC = { running: false };
  const BARC_SOURCES = [
    'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js',
    '{{ asset('vendor/quagga/quagga.min.js') }}'
  ];
  function ensureQuagga() {
    if (window.Quagga) return Promise.resolve(true);
    return loadScriptSeq(BARC_SOURCES).then(function(){ return !!window.Quagga; }).catch(function(){
      log('Failed to load Quagga from all sources.','err'); return false;
    });
  }
  const bcStartBtn = qs('#btn-start-barcode');
  const bcStopBtn  = qs('#btn-stop-barcode');
  const bcTarget   = qs('#barcode-video');
  const bcResult   = qs('#barcode-result');

  async function startBarcode() {
    if(BARC.running) return;
    try{
      const ok = await ensureQuagga();
      if(!ok || !window.Quagga) throw new Error('Quagga not available');
      if(!bcTarget) throw new Error('Barcode mount element missing');

      Quagga.init({
        inputStream: {
          name: "Live",
          type: "LiveStream",
          target: bcTarget,
          constraints: { facingMode: "environment" }
        },
        decoder: { readers: ["code_128_reader","ean_reader","ean_8_reader"] },
        locate: true
      }, function(err){
        if (err) { log('Quagga init error: '+err.message,'err'); return; }
        Quagga.start();
        BARC.running = true;
        if(bcStartBtn) bcStartBtn.disabled = true;
        if(bcStopBtn)  bcStopBtn.disabled  = false;
        log('Barcode camera started','ok');
      });

      Quagga.onDetected(function(r){
        if(!r || !r.codeResult || !r.codeResult.code) return;
        const code = r.codeResult.code;
        if (bcResult) bcResult.textContent = code;
        submitIdentify(code,'barcode');
      });
    }catch(e){
      log('Failed to start Barcode: '+e.message,'err');
    }
  }
  function stopBarcode() {
    try{
      if(window.Quagga && BARC.running){ Quagga.stop(); }
    }catch(_){} finally{
      BARC.running=false;
      if(bcStartBtn) bcStartBtn.disabled = false;
      if(bcStopBtn)  bcStopBtn.disabled  = true;
      log('Barcode camera stopped');
    }
  }
  if(bcStartBtn) bcStartBtn.addEventListener('click', startBarcode);
  if(bcStopBtn)  bcStopBtn.addEventListener('click',  stopBarcode);
  onTabShown('#barcode-pane', function(){ /* no auto-start */ });

  // ---------- Tab hash watcher (stop inactive cameras) ----------
  window.addEventListener('hashchange', function(){
    const h = (location.hash||'').toLowerCase();
    if(h !== '#qr')      stopQR();
    if(h !== '#barcode') stopBarcode();
  });

  // ---------- Filters auto-reload (if present) ----------
  qsa('.filter-trigger').forEach(function(el){
    el.addEventListener('change', function(){ loadList(); });
  });

  // ---------- Init ----------
  loadList();

  // Helpful origin hint for camera permissions
  (function(){
    const host = location.hostname;
    if (host === '127.0.0.1') {
      log('Your browser may block the camera on this origin. Prefer http://localhost:8000 or HTTPS.','warn');
    }
  })();

})();
</script>
@endsection