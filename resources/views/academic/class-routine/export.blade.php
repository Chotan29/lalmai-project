@extends('layouts.master')

@section('css')
<style>
  :root{
    --primary: #346da5; --primary-light:#eef2ff; --secondary:#64748b; --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
    --card-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
  }
  *{box-sizing:border-box;font-family:'Inter',sans-serif}

  /* Header (same as create/manage/import) */
  .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding:0 12px}
  .header-title h1{font-weight:700;font-size:24px;color:var(--dark);margin:0 0 6px}
  .header-title p{color:var(--secondary);font-size:14px;margin:0}
  .header-actions{display:flex;gap:12px;align-items:center}
  .btn-outline{background:#fff;border:1px solid var(--border);color:#0f172a;padding:10px 16px;border-radius:10px;display:inline-flex;gap:8px;align-items:center}
  .btn-outline:hover{background:#f1f5f9}
  .btn-primary{background:var(--primary);border:none;color:#fff;padding:10px 16px;border-radius:10px;display:inline-flex;gap:8px;align-items:center}
  .btn-primary:hover{background:#4338ca}

  /* Sections */
  .section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--card-shadow);padding:20px;margin-bottom:24px;position:relative}
  .section-head{display:flex;align-items:center;padding-bottom:12px;margin-bottom:16px;border-bottom:1px solid var(--border)}
  .section-icon{width:40px;height:40px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;margin-right:12px}
  .section-title{font-weight:700;color:var(--dark);font-size:1.05rem}
  .section-sub{color:var(--secondary);font-size:.92rem}

/* Hierarchy container */
        .hierarchy-container {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 10px 0;
            margin-bottom: 24px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .hierarchy-container::-webkit-scrollbar {
            height: 8px;
        }

        .hierarchy-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .hierarchy-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .hierarchy-level {
            min-width: 200px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            flex-shrink: 0;
            border: 1px solid var(--border);
        }

        .level-title {
            color: var(--dark);
            font-weight: 600;
            padding: 10px;
            border-bottom: 1px solid var(--border);
            background: #f8fafc;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px !important;
        }

        .level-items {
            padding: 10px;
            max-height: 420px;
            overflow-y: auto;
        }

        .level-item {
            padding: 4px 10px;
            border-radius: 10px;
            background: #f8fafc;
            transition: var(--transition);
            cursor: pointer;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
        }

        .level-item:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
            border-color: var(--border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .level-item.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .level-item.active .muted {
            color: rgba(255, 255, 255, 0.8);
        }

        .level-item .muted {
            color: #64748b;
            font-size: 13px;
        }

  /* Chips */
  .chips{display:flex;flex-wrap:wrap;gap:8px}
  .chip{display:inline-flex;align-items:center;gap:8px;background:#f8fafc;border:1px solid var(--border);color:#0f172a;border-radius:9999px;padding:6px 10px;font-size:.9rem}
  .chip i{color:var(--primary)}

  /* Form fields */
  .form-label{display:block;font-weight:500;color:var(--dark);margin-bottom:8px}
  .form-control,.form-select{border:1px solid var(--border);border-radius:8px;padding:10px 12px;width:100%}
  .form-control:focus,.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.1);outline:none}

  /* Loading */
  .loading-indicator{display:none;text-align:center;padding:24px;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--card-shadow);z-index:20}

  @media (max-width:768px){.hierarchy-level{min-width:260px}}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
  <!-- Header -->
  <div class="header">
    <div class="header-title">
      <h1>Export Class Routines</h1>
      <p>Select a scope via the hierarchy, then download XLSX/CSV.</p>
    </div>
    <div class="header-actions">
      <a href="{{ route('routine.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Back to List
      </a>
    </div>
  </div>

  @include('includes.flash_messages')
  @include('includes.validation_error_messages')

  <!-- Selection Summary -->
  <div class="section">
    <div class="section-head">
      <div class="section-icon"><i class="fas fa-list"></i></div>
      <div>
        <div class="section-title">Selection Summary</div>
        <div class="section-sub">These define the export scope. Leave higher levels blank to export more broadly.</div>
      </div>
    </div>
    <div class="chips" id="selection-chips">
      <span class="chip" data-chip="department"><i class="fas fa-university"></i><b>Department:</b> <span class="v">—</span></span>
      <span class="chip" data-chip="faculty"><i class="fas fa-graduation-cap"></i><b>Faculty:</b> <span class="v">—</span></span>
      <span class="chip" data-chip="semester"><i class="fas fa-layer-group"></i><b>Semester:</b> <span class="v">—</span></span>
      <span class="chip" data-chip="batch"><i class="fas fa-users"></i><b>Batch:</b> <span class="v">—</span></span>
      <span class="chip" data-chip="subject"><i class="fas fa-book"></i><b>Subject:</b> <span class="v">—</span></span>
    </div>
  </div>

  <!-- Hierarchy -->
  <div class="section">
    <div class="section-head">
      <div class="section-icon"><i class="fas fa-sitemap"></i></div>
      <div>
        <div class="section-title">Academic Hierarchy</div>
        <div class="section-sub">Department Head → Department → Faculty → Semester → Batch → (Subject optional)</div>
      </div>
    </div>

    <div class="hierarchy-container" id="hierarchy-container">
      <!-- Department Heads -->
      <div class="hierarchy-level">
        <h4 class="level-title"><i class="fas fa-user-tie"></i> Department Heads</h4>
        <div class="level-items">
          @foreach ($department_heads as $id => $department_head)
            <div class="level-item" data-level="department_head" data-id="{{ $id }}">
              <i class="fas fa-user-tie"></i>{{ $department_head }}
            </div>
          @endforeach
        </div>
      </div>

      <!-- Dynamic levels -->
      <div class="hierarchy-level" id="departments-level" style="display:none;">
        <h4 class="level-title"><i class="fas fa-university"></i> Departments</h4>
        <div class="level-items" id="departments-container"></div>
      </div>
      <div class="hierarchy-level" id="faculties-level" style="display:none;">
        <h4 class="level-title"><i class="fas fa-graduation-cap"></i> Faculties/Programs</h4>
        <div class="level-items" id="faculties-container"></div>
      </div>
      <div class="hierarchy-level" id="semesters-level" style="display:none;">
        <h4 class="level-title"><i class="fas fa-layer-group"></i> Semesters</h4>
        <div class="level-items" id="semesters-container"></div>
      </div>
      <div class="hierarchy-level" id="batches-level" style="display:none;">
        <h4 class="level-title"><i class="fas fa-users"></i> Batches</h4>
        <div class="level-items" id="batches-container"></div>
      </div>
      <div class="hierarchy-level" id="subjects-level" style="display:none;">
        <h4 class="level-title"><i class="fas fa-book"></i> Subjects</h4>
        <div class="level-items" id="subjects-container"></div>
      </div>
    </div>

    <!-- Hidden scope fields (sent with the form below) -->
    <input type="hidden" name="department_id"     id="x_department_id" form="routine-export-form">
    <input type="hidden" name="faculty_id"        id="x_faculty_id"    form="routine-export-form">
    <input type="hidden" name="semester_id"       id="x_semester_id"   form="routine-export-form">
    <input type="hidden" name="student_batch_id"  id="x_batch_id"      form="routine-export-form">
    <input type="hidden" name="subject_id"        id="x_subject_id"    form="routine-export-form">

    <div class="loading-indicator">
      <i class="fas fa-spinner fa-spin text-primary"></i>
      <div class="mt-2">Loading…</div>
    </div>
  </div>

  <!-- Export form -->
  <form id="routine-export-form" action="{{ route('routine.export.download') }}" method="GET" class="section">
    <div class="section-head">
      <div class="section-icon"><i class="fas fa-file-export"></i></div>
      <div>
        <div class="section-title">Filters & Format</div>
        <div class="section-sub">Scope is taken from the hierarchy above. You can further filter and pick a format.</div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Day</label>
        <select name="day_of_week" class="form-select">
          <option value="">-- Any --</option>
          @foreach(['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'] as $d)
            <option value="{{ $d }}">{{ $d }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">-- Any --</option>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">Format</label>
        <select name="format" class="form-select" required>
          <option value="xlsx">XLSX</option>
          <option value="csv">CSV</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Teacher ID (optional)</label>
        <input type="number" name="teacher_id" class="form-control" placeholder="ID">
      </div>

      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100"><i class="fas fa-file-export"></i> Download</button>
      </div>

      <div class="col-12">
        <small class="text-muted">
          Scope fields (Department, Faculty, Semester, Batch, Subject) auto-fill from the hierarchy. Selecting fewer levels exports a broader dataset.
        </small>
      </div>
    </div>
  </form>

  <!-- Notes -->
  <div class="section">
    <div class="section-head">
      <div class="section-icon"><i class="fas fa-info-circle"></i></div>
      <div>
        <div class="section-title">What gets exported?</div>
        <div class="section-sub">Rows that match your scope and optional filters, in the format you choose.</div>
      </div>
    </div>
    <ul class="mb-0">
      <li>If you select only a Department, all its faculties/semesters/batches are included.</li>
      <li>Select a specific Subject to export only that subject’s rows.</li>
      <li>Use Day/Status/Teacher filters to narrow results further.</li>
    </ul>
  </div>
</div>
@endsection

@section('js')
<script>
(function($){
  /* ---- chips helper ---- */
  const $chips = $('#selection-chips');
  const setChip = (key, label) => $chips.find(`[data-chip="${key}"] .v`).text(label || '—');

  /* ---- endpoints + simple cache (same pattern as import/create/manage) ---- */
  const KEY = { department:'departments', faculty:'faculties', semester:'semesters', batch:'batches', subject:'subjects' };
  const ENDPOINTS = {
    departments: { url:"{{ route('get-departments') }}", param:"department_head_id", cache:{} },
    faculties:   { url:"{{ route('get-faculties') }}",   param:"department_id",     cache:{} },
    semesters:   { url:"{{ route('get-semesters') }}",   param:"faculty_id",        cache:{} },
    batches:     { url:"{{ route('get-batches') }}",     param:"semester_id",       cache:{} },
    subjects:    { url:"{{ route('get-subjects') }}",    param:"semester_id",       cache:{} },
  };

  function icon(level){
    return {
      department_head:'fa-user-tie', department:'fa-university', faculty:'fa-graduation-cap',
      semester:'fa-layer-group', batch:'fa-users', subject:'fa-book'
    }[level] || 'fa-circle';
  }
  function item(level, id, text){
    return `<div class="level-item" data-level="${level}" data-id="${id}">
              <i class="fas ${icon(level)}"></i>${text}
            </div>`;
  }
  function showLoading(){ $('.loading-indicator').show(); $('#hierarchy-container').css('opacity','.5'); }
  function hideLoading(){ $('.loading-indicator').hide(); $('#hierarchy-container').css('opacity','1'); }

  /* ---- URL state helpers (deep-link + back/forward) ---- */
  function getState(){ const p=new URLSearchParams(window.location.search), o={}; for(const [k,v] of p.entries()) o[k]=v; return o; }
  function setUrlState(state, replace=false){
    const url=new URL(window.location.href);
    const sp=new URLSearchParams();
    Object.keys(state).forEach(k=>{ if(state[k]) sp.set(k,state[k]); });
    const next=url.pathname+(sp.toString()?('?'+sp.toString()):'');
    if(replace) history.replaceState(state,'',next); else history.pushState(state,'',next);
  }

  /* ---- sync scope hidden fields for the export form ---- */
  function setScope(level, id){
    if(level==='department') $('#x_department_id').val(id||'');
    if(level==='faculty')    $('#x_faculty_id').val(id||'');
    if(level==='semester')   $('#x_semester_id').val(id||'');
    if(level==='batch')      $('#x_batch_id').val(id||'');
    if(level==='subject')    $('#x_subject_id').val(id||'');
  }

  /* ---- clear lower levels visually + chips + hidden fields ---- */
  function clearBelow(level){
    const order=['department_head','department','faculty','semester','batch','subject'];
    const idx=order.indexOf(level);
    for(let i=idx+1;i<order.length;i++){
      const lv=order[i], key=KEY[lv];
      if(key){ $('#'+key+'-level').hide(); $('#'+key+'-container').empty(); }
      if(lv==='department'){ setChip('department',''); setScope('department',''); }
      if(lv==='faculty'){ setChip('faculty',''); setScope('faculty',''); }
      if(lv==='semester'){ setChip('semester',''); setScope('semester',''); }
      if(lv==='batch'){ setChip('batch',''); setScope('batch',''); }
      if(lv==='subject'){ setChip('subject',''); setScope('subject',''); }
    }
  }

  /* ---- render/fetch with cache ---- */
  function renderList(level, items){
    const key=KEY[level], $lvl=$('#'+key+'-level'), $c=$('#'+key+'-container');
    $lvl.show(); $c.empty();
    if(items && Object.keys(items).length){ $.each(items,(k,v)=> $c.append(item(level,k,v))); }
    else { $c.html('<div class="text-center py-3 text-muted">No items found</div>'); }
  }

  function fetchList(level, parentId){
    const key=KEY[level]; if(!key) return $.Deferred().resolve({}).promise();
    const ep=ENDPOINTS[key];
    if(ep.cache[parentId]){ renderList(level, ep.cache[parentId]); return $.Deferred().resolve(ep.cache[parentId]).promise(); }
    showLoading();
    return $.get(ep.url, { [ep.param]: parentId })
      .done(resp => { ep.cache[parentId] = resp || {}; renderList(level, ep.cache[parentId]); })
      .always(hideLoading);
  }

  /* ---- clicks ---- */
  $(document).on('click','.level-item', async function(e){
    e.preventDefault();
    const level=String($(this).data('level')), id=String($(this).data('id'));
    $(this).siblings().removeClass('active'); $(this).addClass('active');

    const state=getState();

    if(level==='department_head'){
      state.department_head_id=id;
      delete state.department_id; delete state.faculty_id; delete state.semester_id; delete state.student_batch_id; delete state.subject_id;
      setUrlState(state);
      clearBelow('department_head');
      await fetchList('department', id);
      return;
    }
    if(level==='department'){
      state.department_id=id; setChip('department', $.trim($(this).text())); setScope('department', id);
      delete state.faculty_id; delete state.semester_id; delete state.student_batch_id; delete state.subject_id;
      setUrlState(state);
      clearBelow('department');
      await fetchList('faculty', id);
      return;
    }
    if(level==='faculty'){
      state.faculty_id=id; setChip('faculty', $.trim($(this).text())); setScope('faculty', id);
      delete state.semester_id; delete state.student_batch_id; delete state.subject_id;
      setUrlState(state);
      clearBelow('faculty');
      await fetchList('semester', id);
      return;
    }
    if(level==='semester'){
      state.semester_id=id; setChip('semester', $.trim($(this).text())); setScope('semester', id);
      delete state.student_batch_id; delete state.subject_id;
      setUrlState(state);
      clearBelow('semester');
      await Promise.all([fetchList('batch', id), fetchList('subject', id)]);
      return;
    }
    if(level==='batch'){
      state.student_batch_id=id; setChip('batch', $.trim($(this).text())); setScope('batch', id);
      setUrlState(state);
      return;
    }
    if(level==='subject'){
      state.subject_id=id; setChip('subject', $.trim($(this).text())); setScope('subject', id);
      setUrlState(state);
      return;
    }
  });

  /* ---- rebuild from URL (deep-link & back/forward) ---- */
  async function buildFromState(st){
    if(st.department_head_id){
      $(`.level-item[data-level="department_head"][data-id="${st.department_head_id}"]`).addClass('active');
      await fetchList('department', st.department_head_id);
    } else return;

    if(st.department_id){
      $(`.level-item[data-level="department"][data-id="${st.department_id}"]`).addClass('active');
      setChip('department', $.trim($(`.level-item[data-level="department"][data-id="${st.department_id}"]`).text()));
      setScope('department', st.department_id);
      await fetchList('faculty', st.department_id);
    } else return;

    if(st.faculty_id){
      $(`.level-item[data-level="faculty"][data-id="${st.faculty_id}"]`).addClass('active');
      setChip('faculty', $.trim($(`.level-item[data-level="faculty"][data-id="${st.faculty_id}"]`).text()));
      setScope('faculty', st.faculty_id);
      await fetchList('semester', st.faculty_id);
    } else return;

    if(st.semester_id){
      $(`.level-item[data-level="semester"][data-id="${st.semester_id}"]`).addClass('active');
      setChip('semester', $.trim($(`.level-item[data-level="semester"][data-id="${st.semester_id}"]`).text()));
      setScope('semester', st.semester_id);
      await Promise.all([fetchList('batch', st.semester_id), fetchList('subject', st.semester_id)]);
    } else return;

    if(st.student_batch_id){
      $(`.level-item[data-level="batch"][data-id="${st.student_batch_id}"]`).addClass('active');
      setChip('batch', $.trim($(`.level-item[data-level="batch"][data-id="${st.student_batch_id}"]`).text()));
      setScope('batch', st.student_batch_id);
    }
    if(st.subject_id){
      $(`.level-item[data-level="subject"][data-id="${st.subject_id}"]`).addClass('active');
      setChip('subject', $.trim($(`.level-item[data-level="subject"][data-id="${st.subject_id}"]`).text()));
      setScope('subject', st.subject_id);
    }
  }

  window.addEventListener('popstate', e => buildFromState(e.state || {}));

  $(async function(){
    const st=getState();
    if(Object.keys(st).length){
      await buildFromState(st);
      setUrlState(st, true); // normalize initial history entry
    }
  });
})(jQuery);
</script>
@endsection
