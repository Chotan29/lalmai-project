@extends('layouts.master')

@section('css')
<style>
  :root{
    --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b; --success:#10b981;
    --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
    --card-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
    --transition:all .3s ease;
  }
  *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
  html,body{height:auto;overflow-y:auto}
  .container-fluid{background:#fff;}

  /* Header & actions */
  .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding:0 12px}
  .header-title h1{font-weight:700;font-size:24px;color:var(--dark);margin-bottom:6px}
  .header-title p{color:var(--secondary);font-size:14px}
  .header-actions{display:flex;gap:12px;align-items:center}
  .chip{background:var(--primary-light);color:var(--primary);border:1px solid var(--border);border-radius:999px;padding:6px 10px;font-size:12px}
  .btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:8px 12px;cursor:pointer}
  .btn-soft{border:1px dashed var(--primary);background:var(--primary-light);color:var(--primary)}
  .btn-soft:hover{filter:brightness(.97)}
  .btn-primary{background:var(--primary);color:#fff;border:none}
  .btn-primary:hover{filter:brightness(.95)}
  .btn-live{border:1px solid var(--border);background:#fff}
  .btn-live.active{background:green !important;border-color:#a7f3d0;color:#065f46}

  /* Sections */
  .section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--card-shadow);padding:18px;margin-bottom:16px}
  .section-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
  .section-head .icon{width:38px;height:38px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary)}
  .section-head .title{font-weight:700;color:var(--dark)}
  .muted{color:#64748b}

  /* Breadcrumbs */
  .breadcrumb-wrap{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:10px 0 6px}
  .breadcrumb-item{display:flex;align-items:center;gap:6px;font-size:13px}
  .breadcrumb-link{background:#fff;border:1px solid var(--border);border-radius:999px;padding:5px 10px;color:var(--primary);text-decoration:none;display:inline-flex;align-items:center;gap:6px}
  .breadcrumb-link:hover{background:var(--primary-light)}
  .breadcrumb-divider{color:#cbd5e1}

  /* Hierarchy */
  .hierarchy-block.collapsed{display:none}
  #hierarchy-block.collapsed{display:none}
  .hierarchy-container{display:flex;gap:16px;overflow-x:auto;padding:10px 0;margin-bottom:12px;scrollbar-width:thin;scrollbar-color:#cbd5e1 #f1f5f9}
  .hierarchy-container::-webkit-scrollbar{height:8px}
  .hierarchy-container::-webkit-scrollbar-track{background:#f1f5f9;border-radius:4px}
  .hierarchy-container::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
  .hierarchy-level{min-width:220px;background:#fff;border-radius:12px;box-shadow:var(--card-shadow);flex-shrink:0;border:1px solid var(--border)}
  .level-title{color:var(--dark);font-weight:600;padding:10px;border-bottom:1px solid var(--border);background:#f8fafc;border-radius:12px 12px 0 0;display:flex;align-items:center;gap:8px}
  .level-items{padding:10px;max-height:420px;overflow-y:auto}
  .level-item{padding:6px 10px;border-radius:10px;background:#f8fafc;transition:var(--transition);cursor:pointer;margin-bottom:6px;display:flex;align-items:center;gap:10px;border:1px solid transparent}
  .level-item:hover{background:#f1f5f9;transform:translateY(-1px);border-color:var(--border)}
  .level-item.active{background:var(--primary);color:#fff;border-color:var(--primary)}

  /* KPI grid */
  .kpis{display:grid;grid-template-columns:repeat(8,1fr);gap:10px}
  .kpi{background:#fff;border:1px solid var(--border);border-radius:12px;padding:12px;box-shadow:var(--card-shadow)}
  .kpi .h{font-size:11px;color:#64748b;margin-bottom:6px}
  .kpi .v{font-size:20px;font-weight:800;color:#0f172a}

  /* Charts */
  .chart-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px}
  .chart-grid-2{display:grid;grid-template-columns:2fr 1fr;gap:12px}
  .chart-box{position:relative;height:300px}
  .chart-box.sm{height:240px}
  .chart-box>canvas{width:100%!important;height:100%!important}

  .list{display:flex;flex-direction:column;gap:8px}
  .rowy{display:flex;justify-content:space-between;align-items:center;background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:10px}
  .badge{padding:2px 8px;border-radius:999px;font-size:11px;border:1px solid var(--border)}
  .badge.ok{color:var(--success);border-color:#a7f3d0;background:#ecfdf5}
  .badge.warn{color:#f59e0b;border-color:#fde68a;background:#fffbeb}
  .badge.danger{color:#ef4444;border-color:#fecaca;background:#fef2f2}

  /* Full Page mode */
  body.fullpage .container-fluid{padding:0 !important}
  body.fullpage .header{margin:0 0 12px 0;padding:10px 12px}
  body.fullpage .section{border-radius:0}
  body.fullpage .chart-box{height:420px}
  body.fullpage .chart-box.sm{height:320px}

  /* ===== Fullscreen scrolling fixes (all major engines) ===== */
  :fullscreen, :-webkit-full-screen, :-ms-fullscreen, :-moz-full-screen { overflow:auto !important; }
  body:fullscreen, body:-webkit-full-screen, body:-ms-fullscreen, body:-moz-full-screen { overflow:auto !important; }
  .container-fluid:fullscreen,
  .container-fluid:-webkit-full-screen,
  .container-fluid:-ms-fullscreen,
  .container-fluid:-moz-full-screen{
    overflow:auto !important;
    -webkit-overflow-scrolling:touch;   /* iOS smooth scroll */
    min-height:100vh; height:auto; max-height:100%;
  }
  /* Give charts more breathing room in fullscreen */
  :fullscreen .chart-box{height:420px}
  :fullscreen .chart-box.sm{height:320px}

  @media(max-width:1200px){
    .kpis{grid-template-columns:repeat(2,1fr)}
    .chart-grid,.chart-grid-2{grid-template-columns:1fr}
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
  <div class="header">
    <div class="header-title">
      <h1>Academic Dashboard</h1>
      <p>Filter via hierarchy to analyze routines by day, subjects, teacher load & modules.</p>
    </div>
    <div class="header-actions">
      <span class="chip" id="now-tz">--:--</span>

      <button class="btn btn-soft" id="btn-hierarchy-toggle" title="Show/Hide Hierarchy">
        <i class="fas fa-eye"></i> Show Hierarchy
      </button>

      <button class="btn btn-soft" id="btn-fullscreen" title="Toggle Fullscreen">
        <i class="fas fa-expand"></i> Fullscreen
      </button>

      <button class="btn btn-soft" id="btn-fullpage" title="Full Page View">
        <i class="fas fa-expand-arrows-alt"></i> Full Page
      </button>

      <button class="btn btn-soft" id="btn-reset" title="Reset filters">
        <i class="fas fa-undo"></i> Reset
      </button>

      <button class="btn btn-live" id="toggle-live" title="Auto-refresh every 15s">
        <i class="fas fa-broadcast-tower"></i> Live: OFF
      </button>
    </div>
  </div>

  <div id="acad-breadcrumbs" style="display:none;">
    <nav class="breadcrumb-wrap"></nav>
  </div>

  <!-- Hierarchy -->
  <div id="hierarchy-block" class="section hierarchy-block collapsed">
    <div class="section-head">
      <div class="icon"><i class="fas fa-sitemap"></i></div>
      <div>
        <div class="title">Hierarchy</div>
        <div class="muted">Department Head → Department → Faculty → Semester → Batch → Subject</div>
      </div>
    </div>

    <div class="hierarchy-container">
      <div class="hierarchy-level">
        <div class="level-title"><i class="fas fa-user-tie"></i> Department Heads</div>
        <div class="level-items" id="heads">
          @foreach ($department_heads as $id => $name)
            <div class="level-item" data-level="department_head" data-id="{{ $id }}"><span>{{ $name }}</span></div>
          @endforeach
        </div>
      </div>

      <div class="hierarchy-level" id="lv-departments" style="display:none;">
        <div class="level-title"><i class="fas fa-university"></i> Departments</div>
        <div class="level-items" id="departments"></div>
      </div>

      <div class="hierarchy-level" id="lv-faculties" style="display:none;">
        <div class="level-title"><i class="fas fa-graduation-cap"></i> Faculties</div>
        <div class="level-items" id="faculties"></div>
      </div>

      <div class="hierarchy-level" id="lv-semesters" style="display:none;">
        <div class="level-title"><i class="fas fa-layer-group"></i> Semesters</div>
        <div class="level-items" id="semesters"></div>
      </div>

      <div class="hierarchy-level" id="lv-batches" style="display:none;">
        <div class="level-title"><i class="fas fa-users"></i> Batches</div>
        <div class="level-items" id="batches"></div>
      </div>

      <div class="hierarchy-level" id="lv-subjects" style="display:none;">
        <div class="level-title"><i class="fas fa-book"></i> Subjects</div>
        <div class="level-items" id="subjects"></div>
      </div>
    </div>
  </div>

  <!-- KPIs -->
  <div class="section">
    <div class="kpis">
      <div class="kpi"><div class="h">Routines</div><div class="v" id="c-routines">0</div></div>
      <div class="kpi"><div class="h">Teachers</div><div class="v" id="c-teachers">0</div></div>
      <div class="kpi"><div class="h">Subjects</div><div class="v" id="c-subjects">0</div></div>
      <div class="kpi"><div class="h">Batches</div><div class="v" id="c-batches">0</div></div>
      <div class="kpi"><div class="h">Semesters</div><div class="v" id="c-semesters">0</div></div>
      <div class="kpi"><div class="h">Faculties</div><div class="v" id="c-faculties">0</div></div>
      <div class="kpi"><div class="h">Departments</div><div class="v" id="c-departments">0</div></div>
      <div class="kpi"><div class="h">% Active</div><div class="v" id="c-active-pct">0%</div></div>
    </div>
  </div>

  <!-- Charts -->
  <div class="chart-grid">
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-calendar-week"></i></div><div class="title">Classes by Day</div></div>
      <div class="chart-box"><canvas id="chart-days"></canvas></div>
    </div>
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-chalkboard-teacher"></i></div><div class="title">Top Teachers</div></div>
      <div class="chart-box sm"><canvas id="chart-teachers"></canvas></div>
    </div>
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-book"></i></div><div class="title">Top Subjects</div></div>
      <div class="chart-box sm"><canvas id="chart-subjects"></canvas></div>
    </div>
  </div>

  <!-- Utilization & Attendance -->
  <div class="chart-grid-2">
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-clock"></i></div><div class="title">Room Utilization (Today)</div></div>
      <div class="chart-box"><canvas id="chart-util"></canvas></div>
    </div>
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-user-check"></i></div><div class="title">Attendance Snapshot (Today)</div></div>
      <div class="chart-box sm"><canvas id="chart-att"></canvas></div>
    </div>
  </div>

  <!-- Sessions & Conflicts -->
  <div class="chart-grid-2">
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-forward"></i></div><div class="title">Next Sessions (Today)</div></div>
      <div class="list" id="next-sessions"></div>
    </div>
    <div class="section">
      <div class="section-head"><div class="icon"><i class="fas fa-exclamation-triangle"></i></div><div class="title">Conflicts (Today)</div></div>
      <div class="list" id="conflicts"></div>
    </div>
  </div>

  <!-- Modules -->
  <div class="section">
    <div class="section-head"><div class="icon"><i class="fas fa-cubes"></i></div><div class="title">Modules Snapshot</div></div>
    <div class="chart-grid-2">
      <div class="chart-box"><canvas id="chart-modules"></canvas></div>
      <div>
        <table class="table table-borderless">
          <thead>
            <tr class="muted"><th>Module</th><th>Table</th><th>Total</th><th>Active</th><th>Inactive</th></tr>
          </thead>
          <tbody id="modules-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function ($) {
  const ENDPOINTS = {
    departments : { url: "{{ route('get-departments') }}", param: "department_head_id", cache: {} },
    faculties   : { url: "{{ route('get-faculties') }}",   param: "department_id",      cache: {} },
    semesters   : { url: "{{ route('get-semesters') }}",   param: "faculty_id",         cache: {} },
    batches     : { url: "{{ route('get-batches') }}",     param: "semester_id",        cache: {} },
    subjects    : { url: "{{ route('get-subjects') }}",    param: "semester_id",        cache: {} },
    summary     : { url: "{{ route('academic.summary.chart') }}" }
  };

  const state = {
    department_head_id: {!! json_encode($init_filters['department_head_id'] ?? null) !!},
    department_id:      {!! json_encode($init_filters['department_id'] ?? null) !!},
    faculty_id:         {!! json_encode($init_filters['faculty_id'] ?? null) !!},
    semester_id:        {!! json_encode($init_filters['semester_id'] ?? null) !!},
    student_batch_id:   {!! json_encode($init_filters['student_batch_id'] ?? null) !!},
    subject_id:         {!! json_encode($init_filters['subject_id'] ?? null) !!},
    status:             {!! json_encode($init_filters['status'] ?? '') !!}
  };

  function showLevel(id){ const el=document.getElementById(id); if(el) el.style.display='block'; }
  function hideLevel(id){ const el=document.getElementById(id); if(el) el.style.display='none'; }

  function setNowTZ() {
    const d = new Date();
    const fmt = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second:'2-digit' });
    const tz  = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local';
    const el  = document.getElementById('now-tz');
    if (el) el.textContent = fmt + ' · ' + tz;
  }

  function syncUrl(){
    const p=new URLSearchParams();
    if(state.department_head_id) p.set('department_head_id', state.department_head_id);
    if(state.department_id)      p.set('department_id',      state.department_id);
    if(state.faculty_id)         p.set('faculty_id',         state.faculty_id);
    if(state.semester_id)        p.set('semester_id',        state.semester_id);
    if(state.student_batch_id)   p.set('student_batch_id',   state.student_batch_id);
    if(state.subject_id)         p.set('subject_id',         state.subject_id);
    if(String(state.status||'').length) p.set('status', state.status);
    const newUrl = location.pathname + (p.toString()?('?'+p.toString()):'');
    window.history.replaceState({},'',newUrl);
  }

  function fetchList(key, parentId) {
    const ep = ENDPOINTS[key];
    if (!ep) return $.Deferred().resolve({}).promise();
    const cacheKey = String(parentId ?? '');
    if (ep.cache[cacheKey]) return $.Deferred().resolve(ep.cache[cacheKey]).promise();
    const data = {}; data[ep.param] = parentId;
    return $.get(ep.url, data).then(resp => (ep.cache[cacheKey] = (resp || {})));
  }

  function renderList(containerId, level, items, activeId=null) {
    const $c = $('#' + containerId).empty();
    if (items && Object.keys(items).length) {
      $.each(items, (id, text) => {
        const act = String(id)===String(activeId) ? ' active' : '';
        $c.append(`<div class="level-item${act}" data-level="${level}" data-id="${id}">${text}</div>`);
      });
    } else {
      $c.append('<div class="muted">No items</div>');
    }
  }

  /* Charts */
  let chartDays, chartTeachers, chartSubjects, chartUtil, chartAtt, chartModules;

  function initCharts() {
    const common = { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } };

    chartDays = new Chart(document.getElementById('chart-days'), {
      type:'bar',
      data:{ labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets:[{ label:'Classes', data:[0,0,0,0,0,0,0], backgroundColor:'rgba(52,109,165,0.5)', borderColor:'#346da5', borderWidth:1, maxBarThickness:24, borderRadius:6 }] },
      options:common
    });

    const horiz = { responsive:true, maintainAspectRatio:false, indexAxis:'y',
      scales:{ x:{ beginAtZero:true, ticks:{ precision:0 } }, y:{ ticks:{ autoSkip:false } } }, plugins:{ legend:{ display:false } } };

    chartTeachers = new Chart(document.getElementById('chart-teachers'), {
      type:'bar',
      data:{ labels:[], datasets:[{ label:'Classes', data:[], backgroundColor:'rgba(52,109,165,0.5)', borderColor:'#346da5', borderWidth:1, maxBarThickness:22, borderRadius:6 }] },
      options:horiz
    });

    chartSubjects = new Chart(document.getElementById('chart-subjects'), {
      type:'bar',
      data:{ labels:[], datasets:[{ label:'Classes', data:[], backgroundColor:'rgba(52,109,165,0.5)', borderColor:'#346da5', borderWidth:1, maxBarThickness:22, borderRadius:6 }] },
      options:horiz
    });

    chartUtil = new Chart(document.getElementById('chart-util'), {
      type:'bar',
      data:{ labels:[], datasets:[{ label:'Rooms in Use', data:[], backgroundColor:'rgba(52,109,165,0.5)', borderColor:'#346da5', borderWidth:1, maxBarThickness:24, borderRadius:6 }] },
      options:common
    });

    chartAtt = new Chart(document.getElementById('chart-att'), {
      type:'pie',
      data:{ labels:['Present','Absent','Late'], datasets:[{ data:[0,0,0] }] },
      options:{ responsive:true, maintainAspectRatio:false }
    });

    chartModules = new Chart(document.getElementById('chart-modules'), {
      type:'bar',
      data:{ labels:[], datasets:[
        { label:'Active', data:[], backgroundColor:'rgba(16,185,129,0.5)', borderColor:'#10b981', borderWidth:1, maxBarThickness:26, borderRadius:6 },
        { label:'Inactive', data:[], backgroundColor:'rgba(148,163,184,0.4)', borderColor:'#94a3b8', borderWidth:1, maxBarThickness:26, borderRadius:6 }
      ]},
      options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:true, position:'bottom' } } }
    });
  }

  function refreshSummary() {
    syncUrl();
    $.get(ENDPOINTS.summary.url, state).done(function (resp) {
      $('#c-routines').text(resp?.counters?.routines ?? 0);
      $('#c-teachers').text(resp?.counters?.teachers ?? 0);
      $('#c-subjects').text(resp?.counters?.subjects ?? 0);
      $('#c-batches').text(resp?.counters?.batches ?? 0);
      $('#c-semesters').text(resp?.counters?.semesters ?? 0);
      $('#c-faculties').text(resp?.counters?.faculties ?? 0);
      $('#c-departments').text(resp?.counters?.departments ?? 0);
      $('#c-active-pct').text(((resp?.counters?.active_pct ?? 0)) + '%');

      chartDays.data.datasets[0].data = resp?.classesByDay || [0,0,0,0,0,0,0];
      chartDays.update();

      const tt = Array.isArray(resp?.topTeachers) ? resp.topTeachers : [];
      chartTeachers.data.labels = tt.map(t => String(t.label ?? '—'));
      chartTeachers.data.datasets[0].data = tt.map(t => Number(t.value ?? 0));
      chartTeachers.options.scales.x.suggestedMax = Math.max(5, ...(chartTeachers.data.datasets[0].data || [0]));
      chartTeachers.update();

      const ts = Array.isArray(resp?.topSubjects) ? resp.topSubjects : [];
      chartSubjects.data.labels = ts.map(s => String(s.label ?? '—'));
      chartSubjects.data.datasets[0].data = ts.map(s => Number(s.value ?? 0));
      chartSubjects.options.scales.x.suggestedMax = Math.max(5, ...(chartSubjects.data.datasets[0].data || [0]));
      chartSubjects.update();

      chartUtil.data.labels = resp?.roomUtilization?.labels || [];
      chartUtil.data.datasets[0].data = (resp?.roomUtilization?.data || []).map(Number);
      chartUtil.update();

      const a = resp?.attendance || { present: 0, absent: 0, late: 0 };
      chartAtt.data.datasets[0].data = [Number(a.present||0), Number(a.absent||0), Number(a.late||0)];
      chartAtt.update();

      const $ns = $('#next-sessions').empty();
      (resp?.nextSessions || []).forEach(s => {
        $ns.append(`<div class="rowy">
          <div><span class="badge ok">${s.start_time}–${s.end_time}</span></div>
          <div><strong>${s.subject_code || ''}</strong> ${s.subject_title ? ('— ' + s.subject_title) : ''}</div>
          <div class="muted">${s.teacher_name || '—'}</div>
          <div class="badge">${s.room_number || '—'}</div>
        </div>`);
      });
      if (!$ns.children().length) $ns.append('<div class="muted">No upcoming sessions today.</div>');

      const $cf = $('#conflicts').empty();
      (resp?.conflicts || []).forEach(c => {
        $cf.append(`<div class="rowy">
          <div class="badge danger">Conflict</div>
          <div class="muted">${c.cohort || ''}</div>
          <div class="muted">A: ${c.a_time || ''}</div>
          <div class="muted">B: ${c.b_time || ''}</div>
        </div>`);
      });
      if (!$cf.children().length) $cf.append('<div class="muted">No overlaps detected.</div>');

      chartModules.data.labels = resp?.modulesBar?.labels || [];
      chartModules.data.datasets[0].data = (resp?.modulesBar?.active || []).map(Number);
      chartModules.data.datasets[1].data = (resp?.modulesBar?.inactive || []).map(Number);
      chartModules.update();

      const $mb = $('#modules-body').empty();
      (resp?.modules || []).forEach(m => {
        $mb.append(`<tr>
          <td>${m.label}</td>
          <td class="muted">${m.table || '—'}</td>
          <td>${Number(m.total||0)}</td>
          <td><span class="badge ok">${Number(m.active||0)}</span></td>
          <td><span class="badge warn">${Number(m.inactive||0)}</span></td>
        </tr>`);
      });
      if (!$mb.children().length) $mb.append('<tr><td colspan="5" class="muted">No module data</td></tr>');
    });
  }

  /* Breadcrumbs + hierarchy */
  const NAME_CACHE = { department_head:{}, department:{}, faculty:{}, semester:{}, batch:{}, subject:{} };
  const LABELS = { department_head:null, department:null, faculty:null, semester:null, batch:null, subject:null };

  function cacheHeadsFromDom(){
    $('#heads .level-item').each(function(){
      NAME_CACHE.department_head[String($(this).data('id'))] = $.trim($(this).text());
    });
  }
  function refreshLabelsFromState(){
    LABELS.department_head = state.department_head_id ? NAME_CACHE.department_head[String(state.department_head_id)] : null;
    LABELS.department      = state.department_id      ? NAME_CACHE.department[String(state.department_id)]         : null;
    LABELS.faculty         = state.faculty_id         ? NAME_CACHE.faculty[String(state.faculty_id)]               : null;
    LABELS.semester        = state.semester_id        ? NAME_CACHE.semester[String(state.semester_id)]             : null;
    LABELS.batch           = state.student_batch_id   ? NAME_CACHE.batch[String(state.student_batch_id)]           : null;
    LABELS.subject         = state.subject_id         ? NAME_CACHE.subject[String(state.subject_id)]               : null;
  }
  function renderBreadcrumbs(){
    refreshLabelsFromState();
    const $wrap = $('#acad-breadcrumbs'), $nav=$wrap.find('.breadcrumb-wrap').empty();
    const crumbs=[];
    if(state.department_head_id && LABELS.department_head) crumbs.push({level:'department_head',label:LABELS.department_head});
    if(state.department_id && LABELS.department) crumbs.push({level:'department',label:LABELS.department});
    if(state.faculty_id && LABELS.faculty) crumbs.push({level:'faculty',label:LABELS.faculty});
    if(state.semester_id && LABELS.semester) crumbs.push({level:'semester',label:LABELS.semester});
    if(state.student_batch_id && LABELS.batch) crumbs.push({level:'batch',label:LABELS.batch});
    if(state.subject_id && LABELS.subject) crumbs.push({level:'subject',label:LABELS.subject});

    if(!crumbs.length){ $wrap.hide(); return; }
    $wrap.show();
    $nav.append(`<span class="breadcrumb-item"><a href="#" class="breadcrumb-link" data-level="root"><i class="fas fa-home"></i> All</a></span>`);
    crumbs.forEach(c=>{
      $nav.append(`<span class="breadcrumb-divider">›</span>`);
      $nav.append(`<span class="breadcrumb-item"><a href="#" class="breadcrumb-link" data-level="${c.level}">${c.label}</a></span>`);
    });
  }
  $(document).on('click','.breadcrumb-link', async function(e){
    e.preventDefault();
    const lvl=$(this).data('level');
    if(lvl==='root'){
      state.department_head_id=state.department_id=state.faculty_id=state.semester_id=state.student_batch_id=state.subject_id=null;
    }else if(lvl==='department_head'){
      state.department_id=state.faculty_id=state.semester_id=state.student_batch_id=state.subject_id=null;
    }else if(lvl==='department'){
      state.faculty_id=state.semester_id=state.student_batch_id=state.subject_id=null;
    }else if(lvl==='faculty'){
      state.semester_id=state.student_batch_id=state.subject_id=null;
    }else if(lvl==='semester'){
      state.student_batch_id=state.subject_id=null;
    }else if(lvl==='batch'){
      state.subject_id=null;
    }
    await rebuildHierarchyFromState();
    refreshSummary();
    renderBreadcrumbs();
  });

  $(document).on('click', '.level-item', async function () {
    const level = $(this).data('level');
    const id    = String($(this).data('id'));
    $(this).siblings().removeClass('active'); $(this).addClass('active');

    const label=$.trim($(this).text());

    if (level === 'department_head') {
      state.department_head_id = id;
      state.department_id = state.faculty_id = state.semester_id = state.student_batch_id = state.subject_id = null;

      const deps = await fetchList('departments', id); NAME_CACHE.department=deps;
      renderList('departments', 'department', deps, state.department_id);

      showLevel('lv-departments'); hideLevel('lv-faculties'); hideLevel('lv-semesters'); hideLevel('lv-batches'); hideLevel('lv-subjects');
    }

    if (level === 'department') {
      state.department_id = id;
      state.faculty_id = state.semester_id = state.student_batch_id = state.subject_id = null;

      const fac = await fetchList('faculties', id); NAME_CACHE.faculty=fac;
      renderList('faculties', 'faculty', fac, state.faculty_id);

      showLevel('lv-faculties'); hideLevel('lv-semesters'); hideLevel('lv-batches'); hideLevel('lv-subjects');
    }

    if (level === 'faculty') {
      state.faculty_id = id;
      state.semester_id = state.student_batch_id = state.subject_id = null;

      const sem = await fetchList('semesters', id); NAME_CACHE.semester=sem;
      renderList('semesters', 'semester', sem, state.semester_id);

      showLevel('lv-semesters'); hideLevel('lv-batches'); hideLevel('lv-subjects');
    }

    if (level === 'semester') {
      state.semester_id = id;
      state.student_batch_id = state.subject_id = null;

      const [batches, subjects] = await Promise.all([
        fetchList('batches',  id),
        fetchList('subjects', id),
      ]);

      NAME_CACHE.batch=batches; NAME_CACHE.subject=subjects;
      renderList('batches',  'batch',  batches,  state.student_batch_id);
      renderList('subjects', 'subject', subjects, state.subject_id);

      showLevel('lv-batches'); showLevel('lv-subjects');
    }

    if (level === 'batch')   state.student_batch_id = id;
    if (level === 'subject') state.subject_id = id;

    if(level==='department_head') NAME_CACHE.department_head[id]=label;
    if(level==='department')      NAME_CACHE.department[id]=label;
    if(level==='faculty')         NAME_CACHE.faculty[id]=label;
    if(level==='semester')        NAME_CACHE.semester[id]=label;
    if(level==='batch')           NAME_CACHE.batch[id]=label;
    if(level==='subject')         NAME_CACHE.subject[id]=label;

    renderBreadcrumbs();
    refreshSummary();
  });

  async function rebuildHierarchyFromState(){
    $('.level-items .level-item').removeClass('active');
    if(!state.department_head_id){
      $('#lv-departments,#lv-faculties,#lv-semesters,#lv-batches,#lv-subjects').hide();
      return;
    }
    $('#heads .level-item[data-id="'+state.department_head_id+'"]').addClass('active');

    const deps=await fetchList('departments',state.department_head_id);
    NAME_CACHE.department=deps;
    renderList('departments','department',deps,state.department_id);
    showLevel('lv-departments');

    if(!state.department_id){ hideLevel('lv-faculties'); hideLevel('lv-semesters'); hideLevel('lv-batches'); hideLevel('lv-subjects'); return; }

    const fac=await fetchList('faculties',state.department_id);
    NAME_CACHE.faculty=fac;
    renderList('faculties','faculty',fac,state.faculty_id);
    showLevel('lv-faculties');

    if(!state.faculty_id){ hideLevel('lv-semesters'); hideLevel('lv-batches'); hideLevel('lv-subjects'); return; }

    const sem=await fetchList('semesters',state.faculty_id);
    NAME_CACHE.semester=sem;
    renderList('semesters','semester',sem,state.semester_id);
    showLevel('lv-semesters');

    if(!state.semester_id){ hideLevel('lv-batches'); hideLevel('lv-subjects'); return; }

    const [batches, subjects] = await Promise.all([
      fetchList('batches', state.semester_id),
      fetchList('subjects', state.semester_id),
    ]);
    NAME_CACHE.batch=batches; NAME_CACHE.subject=subjects;
    renderList('batches','batch',batches,state.student_batch_id);
    renderList('subjects','subject',subjects,state.subject_id);
    showLevel('lv-batches'); showLevel('lv-subjects');

    if(state.department_id)     $('#departments .level-item[data-id="'+state.department_id+'"]').addClass('active');
    if(state.faculty_id)        $('#faculties .level-item[data-id="'+state.faculty_id+'"]').addClass('active');
    if(state.semester_id)       $('#semesters .level-item[data-id="'+state.semester_id+'"]').addClass('active');
    if(state.student_batch_id)  $('#batches .level-item[data-id="'+state.student_batch_id+'"]').addClass('active');
    if(state.subject_id)        $('#subjects .level-item[data-id="'+state.subject_id+'"]').addClass('active');
  }

  /* Hierarchy show/hide (sticky) */
  const HIER_KEY = 'academic.hierarchy.visible';
  function isHierarchyVisible(){ return localStorage.getItem(HIER_KEY) === '1'; }
  function setHierarchyVisible(on){ localStorage.setItem(HIER_KEY, on?'1':'0'); updateHierarchyToggleUI(); }
  function updateHierarchyToggleUI(){
    const on = isHierarchyVisible();
    const $b = $('#hierarchy-block');
    const $bt= $('#btn-hierarchy-toggle');
    $b.toggleClass('collapsed', !on);
    if($bt.length){
      $bt.html(`<i class="fas ${on?'fa-eye-slash':'fa-eye'}"></i> ${on?'Hide Hierarchy':'Show Hierarchy'}`);
    }
    setTimeout(()=>{ try{ window.dispatchEvent(new Event('resize')); }catch(e){} },60);
  }
  $(document).on('click','#btn-hierarchy-toggle', function(e){
    e.preventDefault(); setHierarchyVisible(!isHierarchyVisible());
  });

  /* Full-page (sticky) */
  const FP_KEY='academic.fullpage';
  function isFullPage(){ return localStorage.getItem(FP_KEY) === '1'; }
  function setFullPage(on){ localStorage.setItem(FP_KEY, on?'1':'0'); updateFullPageUI(); }
  function updateFullPageUI(){
    const on=isFullPage();
    document.body.classList.toggle('fullpage', on);
    const $btn=$('#btn-fullpage');
    if($btn.length){ $btn.html(`<i class="fas ${on?'fa-compress-arrows-alt':'fa-expand-arrows-alt'}"></i> ${on?'Exit Full Page':'Full Page'}`); }
    setTimeout(()=>{ try{ window.dispatchEvent(new Event('resize')); }catch(e){} },60);
  }
  $(document).on('click','#btn-fullpage', function(e){ e.preventDefault(); setFullPage(!isFullPage()); });

  /* Fullscreen */
  function isFullscreen(){ return !!(document.fullscreenElement||document.webkitFullscreenElement||document.msFullscreenElement); }
  function fsContainer(){ return document.querySelector('.container-fluid') || document.documentElement; }
  function reqFs(el){ if(el.requestFullscreen) return el.requestFullscreen(); if(el.webkitRequestFullscreen) return el.webkitRequestFullscreen(); if(el.msRequestFullscreen) return el.msRequestFullscreen(); }
  function exitFs(){ if(document.exitFullscreen) return document.exitFullscreen(); if(document.webkitExitFullscreen) return document.webkitExitFullscreen(); if(document.msExitFullscreen) return document.msExitFullscreen(); }

  function ensureFsScroll(){
    const el = fsContainer();
    if(isFullscreen() && el){
      el.style.overflowY = 'auto';
      el.style.webkitOverflowScrolling = 'touch';
    }else if(el){
      el.style.overflowY = '';
      el.style.webkitOverflowScrolling = '';
    }
  }
  function updateFsBtn(){
    const on = isFullscreen();
    const btn = document.getElementById('btn-fullscreen');
    if(btn){ btn.innerHTML = `<i class="fas ${on?'fa-compress':'fa-expand'}"></i> ${on?'Exit Fullscreen':'Fullscreen'}`; }
    ensureFsScroll();
    setTimeout(function(){
      [chartDays,chartTeachers,chartSubjects,chartUtil,chartAtt,chartModules].forEach(c=>{ if(c && typeof c.resize==='function') c.resize(); });
      window.dispatchEvent(new Event('resize'));
    },80);
  }
  function toggleFullscreen(e){
    if(e) e.preventDefault();
    const target = fsContainer();
    if(isFullscreen()) exitFs(); else reqFs(target);
  }
  $(document).on('click','#btn-fullscreen', toggleFullscreen);
  document.addEventListener('fullscreenchange', updateFsBtn);
  document.addEventListener('webkitfullscreenchange', updateFsBtn);
  document.addEventListener('msfullscreenchange', updateFsBtn);

  /* Live refresh */
  let liveTimer=null, liveOn=false;
  $('#toggle-live').on('click', function(){
    liveOn = !liveOn;
    $(this).toggleClass('active', liveOn)
           .html(`<i class="${liveOn?'fa fa-spinner fa-spin':'fas fa-broadcast-tower'}"></i> Live: ${liveOn?'ON':'OFF'}`);
    if(liveOn){ refreshSummary(); liveTimer = setInterval(refreshSummary, 15000); }
    else{ if(liveTimer){ clearInterval(liveTimer); liveTimer=null; } }
  });

  /* Reset */
  $('#btn-reset').on('click', function(e){
    e.preventDefault();
    const base = location.origin + location.pathname;
    window.location.href = base;
  });

  /* Boot */
  $(function () {
    setNowTZ(); setInterval(setNowTZ, 1000);
    initCharts();
    updateFullPageUI();
    updateHierarchyToggleUI();
    cacheHeadsFromDom();

    (async function bootFromUrl(){
      if(state.department_head_id){
        $('#heads .level-item[data-id="'+state.department_head_id+'"]').addClass('active');

        const deps = await fetchList('departments', state.department_head_id); NAME_CACHE.department=deps;
        renderList('departments','department',deps,state.department_id); if(state.department_id) showLevel('lv-departments');

        if(state.department_id){
          const fac = await fetchList('faculties', state.department_id); NAME_CACHE.faculty=fac;
          renderList('faculties','faculty',fac,state.faculty_id); showLevel('lv-faculties');
        }
        if(state.faculty_id){
          const sem = await fetchList('semesters', state.faculty_id); NAME_CACHE.semester=sem;
          renderList('semesters','semester',sem,state.semester_id); showLevel('lv-semesters');
        }
        if(state.semester_id){
          const [batches, subjects] = await Promise.all([
            fetchList('batches', state.semester_id),
            fetchList('subjects', state.semester_id),
          ]);
          NAME_CACHE.batch=batches; NAME_CACHE.subject=subjects;
          renderList('batches','batch',batches,state.student_batch_id); showLevel('lv-batches');
          renderList('subjects','subject',subjects,state.subject_id);   showLevel('lv-subjects');
        }
      }
      renderBreadcrumbs();
      refreshSummary();
    })();
  });

})(jQuery);
</script>
@endsection
