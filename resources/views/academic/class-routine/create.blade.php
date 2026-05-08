@extends('layouts.master')

@section('css')
    <style>
        :root{
            --primary: #346da5; --primary-light:#eef2ff; --secondary:#64748b;
            --success:#10b981; --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
            --card-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
            --transition:all .3s ease;
        }
        *{box-sizing:border-box;font-family:'Inter',sans-serif}
        .container-fluid{margin:0 auto;padding:0 15px}

        /* Header */
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
        .section-head{display:flex;align-items:center;padding-bottom:16px;margin-bottom:20px;border-bottom:1px solid var(--border)}
        .section-icon{width:40px;height:40px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;margin-right:12px}
        .section-title{font-weight:700;color:var(--dark);font-size:1.2rem}
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

        /* Schedule */
        .toolbar{display:flex;gap:10px;justify-content:flex-end;margin-bottom:12px}
        .btn-soft{border:1px dashed var(--primary);background:var(--primary-light);color:var(--primary);padding:8px 12px;border-radius:8px;display:inline-flex;gap:6px;align-items:center}
        .schedule-row{background:#f9fafb;border:1px solid #eceff7;border-left:3px solid #4361ee;border-radius:10px;padding:12px;margin-bottom:12px}
        .row-actions{display:flex;align-items:center;gap:8px;justify-content:flex-end}
        .btn-danger-soft{border:1px solid #ef4444;color:#ef4444;background:#fff5f5;padding:8px 12px;border-radius:8px}
        .btn-danger-soft:hover{background:#ffeaea}
        .small-hint{font-size:.85rem;color:#6b7280;margin-top:6px}

        /* Veil until batch+subject picked */
        .veil{position:relative}
        .veil::after{content:"Select Batch & Subject first";position:absolute;inset:0;display:none;background:rgba(255,255,255,.85);color:#6b7280;align-items:center;justify-content:center;font-weight:600;border-radius:12px;z-index:10}
        .veil.disabled::after{display:flex}

        /* Loading */
        .loading-indicator{display:none;text-align:center;padding:24px;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--card-shadow);z-index:20}

        /* Footer */
        .actions{display:flex;gap:12px;justify-content:center;margin-top:18px}

        /* Labels stack above fields (including selects) */
        .form-label{display:block;font-weight:500;color:var(--dark);margin-bottom:8px}
        .form-select,.form-control{width:100%}
        @media (max-width:768px){
            .header{flex-direction:column;align-items:flex-start;gap:16px}
            .hierarchy-level{min-width:260px}
            .toolbar{flex-wrap:wrap}
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="header">
        <div class="header-title">
            <h1>Create Class Routine</h1>
            <p>Add a new class schedule after picking the hierarchy.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('routine.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @include('includes.flash_messages')
    @include('includes.validation_error_messages')

    <form id="routine-create-form" action="{{ route('routine.store') }}" method="POST">
        @csrf

        <!-- Hierarchy -->
        <div class="section">
            <div class="section-head">
                <div class="section-icon"><i class="fas fa-sitemap"></i></div>
                <div>
                    <div class="section-title">Academic Hierarchy</div>
                    <div class="section-sub">Pick Department Head → Department → Faculty → Semester → Batch & Subject</div>
                </div>
            </div>

            <div class="hierarchy-container" id="hierarchy-container">
                <!-- Department Heads -->
                <div class="hierarchy-level">
                    <h4 class="level-title"><i class="fas fa-user-tie"></i> Department Heads</h4>
                    <div class="level-items">
                        @foreach ($department_heads as $id => $name)
                            <div class="level-item" data-level="department_head" data-id="{{ $id }}">
                                <i class="fas fa-user-tie"></i>{{ $name }}
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

            <!-- Hidden inputs -->
            <input type="hidden" name="department_head_id" id="department_head_id" value="{{ old('department_head_id') }}">
            <input type="hidden" name="department_id"     id="department_id"     value="{{ old('department_id') }}">
            <input type="hidden" name="faculty_id"        id="faculty_id"        value="{{ old('faculty_id') }}">
            <input type="hidden" name="semester_id"       id="semester_id"       value="{{ old('semester_id') }}">
            <input type="hidden" name="student_batch_id"  id="student_batch_id"  value="{{ old('student_batch_id') }}">
            <input type="hidden" name="subject_id"        id="subject_id"        value="{{ old('subject_id') }}">

            <div class="loading-indicator">
                <i class="fas fa-spinner fa-spin text-primary"></i>
                <div class="mt-2">Loading…</div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="section">
            <div class="section-head">
                <div class="section-icon"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="section-title">Schedule Details</div>
                    <div class="section-sub">Add one or more day/time rows for the same subject.</div>
                </div>
            </div>

            <div class="toolbar">
                <button type="button" id="add-row" class="btn-soft"><i class="fas fa-plus"></i> Add Day</button>
                <button type="button" id="duplicate-last" class="btn-soft"><i class="fas fa-copy"></i> Duplicate Last</button>
                <button type="button" id="clear-all" class="btn-soft"><i class="fas fa-eraser"></i> Clear All</button>
            </div>

            <div id="schedule-holder" class="veil {{ old('student_batch_id') && old('subject_id') ? '' : 'disabled' }}">
                <div id="schedule-list">
                    @php $oldSchedules = old('schedules', []); @endphp

                    @if(!empty($oldSchedules))
                        @foreach($oldSchedules as $i => $s)
                            <div class="schedule-row" data-index="{{ $i }}">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Day of Week</label>
                                        <select name="schedules[{{ $i }}][day_of_week]" class="form-select @error("schedules.$i.day_of_week") is-invalid @enderror" required>
                                            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                <option value="{{ $day }}" {{ ($s['day_of_week']??'')===$day?'selected':'' }}>{{ $day }}</option>
                                            @endforeach
                                        </select>
                                        @error("schedules.$i.day_of_week")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Period</label>
                                        <input type="text" name="schedules[{{ $i }}][period]" class="form-control @error("schedules.$i.period") is-invalid @enderror" value="{{ $s['period']??'' }}" placeholder="e.g., 1st">
                                        @error("schedules.$i.period")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" name="schedules[{{ $i }}][start_time]" class="form-control @error("schedules.$i.start_time") is-invalid @enderror" value="{{ $s['start_time']??'' }}" required>
                                        @error("schedules.$i.start_time")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">End Time</label>
                                        <input type="time" name="schedules[{{ $i }}][end_time]" class="form-control @error("schedules.$i.end_time") is-invalid @enderror" value="{{ $s['end_time']??'' }}" required>
                                        @error("schedules.$i.end_time")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Room</label>
                                        <input type="text" name="schedules[{{ $i }}][room_number]" class="form-control @error("schedules.$i.room_number") is-invalid @enderror" value="{{ $s['room_number']??'' }}" placeholder="e.g., 4105" required>
                                        @error("schedules.$i.room_number")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Teacher</label>
                                        <select name="schedules[{{ $i }}][teacher_id]" class="form-select @error("schedules.$i.teacher_id") is-invalid @enderror" required>
                                            <option value="">Select Teacher</option>
                                            @foreach($teachers as $tid => $tname)
                                                <option value="{{ $tid }}" {{ (string)($s['teacher_id']??'')===(string)$tid?'selected':'' }}>{{ $tname }}</option>
                                            @endforeach
                                        </select>
                                        @error("schedules.$i.teacher_id")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        <div class="small-hint">Teacher conflicts are validated on submit</div>
                                    </div>
                                    <div class="col-md-12 row-actions">
                                        <button type="button" class="btn-danger-soft btn-remove"><i class="fas fa-trash"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- default one row -->
                        <div class="schedule-row" data-index="0">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Day of Week</label>
                                    <select name="schedules[0][day_of_week]" class="form-select" required>
                                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Period</label>
                                    <input type="text" name="schedules[0][period]" class="form-control" placeholder="e.g., 1st">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Start Time</label>
                                    <input type="time" name="schedules[0][start_time]" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">End Time</label>
                                    <input type="time" name="schedules[0][end_time]" class="form-control" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Room</label>
                                    <input type="text" name="schedules[0][room_number]" class="form-control" placeholder="e.g., 4105" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Teacher</label>
                                    <select name="schedules[0][teacher_id]" class="form-select" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $tid => $tname)
                                            <option value="{{ $tid }}">{{ $tname }}</option>
                                        @endforeach
                                    </select>
                                    <div class="small-hint">Teacher conflicts are validated on submit</div>
                                </div>
                                <div class="col-md-12 row-actions">
                                    <button type="button" class="btn-danger-soft btn-remove"><i class="fas fa-trash"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <p class="small-hint mt-2">Tip: Add multiple rows for labs/tutorials or split sessions.</p>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('routine.index') }}" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Routine</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
(function($){
    /* ---------- hierarchy caching (no redundant reloads) ---------- */
    const KEY = { department:'departments', faculty:'faculties', semester:'semesters', batch:'batches', subject:'subjects' };
    const HID = {
        department_head:'#department_head_id', department:'#department_id',
        faculty:'#faculty_id', semester:'#semester_id',
        batch:'#student_batch_id', subject:'#subject_id'
    };
    const ENDPOINTS = {
        departments: { url:"{{ route('get-departments') }}", param:"department_head_id", cache:{} },
        faculties:   { url:"{{ route('get-faculties') }}",   param:"department_id",     cache:{} },
        semesters:   { url:"{{ route('get-semesters') }}",   param:"faculty_id",        cache:{} },
        batches:     { url:"{{ route('get-batches') }}",     param:"semester_id",       cache:{} },
        subjects:    { url:"{{ route('get-subjects') }}",    param:"semester_id",       cache:{} },
    };

    function icon(level){
        return {department_head:'fa-user-tie', department:'fa-university', faculty:'fa-graduation-cap', semester:'fa-layer-group', batch:'fa-users', subject:'fa-book'}[level]||'fa-circle';
    }
    function item(level, id, text){
        return `<div class="level-item" data-level="${level}" data-id="${id}">
                 <i class="fas ${icon(level)}"></i>${text}
               </div>`;
    }
    function showLoading(){ $('.loading-indicator').show(); $('#hierarchy-container').css('opacity','.5'); }
    function hideLoading(){ $('.loading-indicator').hide(); $('#hierarchy-container').css('opacity','1'); }
    function toggleVeil(){ const ok=!!$(HID.batch).val()&&!!$(HID.subject).val(); $('#schedule-holder').toggleClass('disabled',!ok); }
    function getParamsFromUrl(){ const p=new URLSearchParams(window.location.search), o={}; for(const [k,v] of p.entries()) o[k]=v; return o; }
    function pushUrl(state){ const url=new URL(window.location.href), sp=new URLSearchParams(); Object.keys(state).forEach(k=>{ if(state[k]) sp.set(k,state[k]); }); history.pushState(state,'',url.pathname+(sp.toString()?('?'+sp.toString()):'')); }
    function replaceUrl(state){ const url=new URL(window.location.href), sp=new URLSearchParams(); Object.keys(state).forEach(k=>{ if(state[k]) sp.set(k,state[k]); }); history.replaceState(state,'',url.pathname+(sp.toString()?('?'+sp.toString()):'')); }
    function clearBelow(level){
        const order=['department_head','department','faculty','semester','batch','subject'];
        const idx=order.indexOf(level);
        for(let i=idx+1;i<order.length;i++){
            const lv=order[i], key=KEY[lv];
            if(key){ $('#'+key+'-level').hide(); $('#'+key+'-container').empty(); }
            $(HID[lv]).val('');
        }
        if(level!=='semester'){ $(HID.batch).val(''); $(HID.subject).val(''); }
        toggleVeil();
    }
    function renderList(level, items){
        const key=KEY[level], $lvl=$('#'+key+'-level'), $c=$('#'+key+'-container');
        $lvl.show(); $c.empty();
        if(items && Object.keys(items).length){ $.each(items,(k,v)=> $c.append(item(level,k,v))); }
        else { $c.html('<div class="text-center py-3 text-muted">No items found</div>'); }
    }
    function fetchList(level,parentId){
        const key=KEY[level], ep=ENDPOINTS[key]; if(!ep) return $.Deferred().resolve({}).promise();
        if(ep.cache[parentId]){ renderList(level,ep.cache[parentId]); return $.Deferred().resolve(ep.cache[parentId]).promise(); }
        showLoading();
        return $.get(ep.url,{ [ep.param]:parentId })
            .done(resp=>{ ep.cache[parentId]=resp||{}; renderList(level,ep.cache[parentId]); })
            .always(hideLoading);
    }

    $(document).on('click','.level-item',async function(e){
        e.preventDefault();
        const level=String($(this).data('level')), id=String($(this).data('id'));
        if(HID[level]) $(HID[level]).val(id);
        $(this).siblings().removeClass('active'); $(this).addClass('active');

        const st=getParamsFromUrl();
        if(level==='department_head'){
            st.department_head_id=id; delete st.department_id; delete st.faculty_id; delete st.semester_id; delete st.student_batch_id; delete st.subject_id;
            pushUrl(st); clearBelow('department_head'); await fetchList('department',id);
        } else if(level==='department'){
            st.department_id=id; delete st.faculty_id; delete st.semester_id; delete st.student_batch_id; delete st.subject_id;
            pushUrl(st); clearBelow('department'); await fetchList('faculty',id);
        } else if(level==='faculty'){
            st.faculty_id=id; delete st.semester_id; delete st.student_batch_id; delete st.subject_id;
            pushUrl(st); clearBelow('faculty'); await fetchList('semester',id);
        } else if(level==='semester'){
            st.semester_id=id; delete st.student_batch_id; delete st.subject_id;
            pushUrl(st); clearBelow('semester'); await Promise.all([fetchList('batch',id), fetchList('subject',id)]); toggleVeil();
        } else if(level==='batch'){
            st.student_batch_id=id; pushUrl(st); toggleVeil();
        } else if(level==='subject'){
            st.subject_id=id; pushUrl(st); toggleVeil();
        }
    });

    async function buildFromState(st){
        clearBelow('department_head');
        if(st.department_head_id){ await fetchList('department',st.department_head_id); $(`.level-item[data-level="department_head"][data-id="${st.department_head_id}"]`).addClass('active'); $(HID.department_head).val(st.department_head_id); } else { toggleVeil(); return; }
        if(st.department_id){ await fetchList('faculty',st.department_id); $(`.level-item[data-level="department"][data-id="${st.department_id}"]`).addClass('active'); $(HID.department).val(st.department_id); } else { toggleVeil(); return; }
        if(st.faculty_id){ await fetchList('semester',st.faculty_id); $(`.level-item[data-level="faculty"][data-id="${st.faculty_id}"]`).addClass('active'); $(HID.faculty).val(st.faculty_id); } else { toggleVeil(); return; }
        if(st.semester_id){ await Promise.all([fetchList('batch',st.semester_id), fetchList('subject',st.semester_id)]); $(`.level-item[data-level="semester"][data-id="${st.semester_id}"]`).addClass('active'); $(HID.semester).val(st.semester_id); } else { toggleVeil(); return; }
        if(st.student_batch_id){ $(`.level-item[data-level="batch"][data-id="${st.student_batch_id}"]`).addClass('active'); $(HID.batch).val(st.student_batch_id); }
        if(st.subject_id){ $(`.level-item[data-level="subject"][data-id="${st.subject_id}"]`).addClass('active'); $(HID.subject).val(st.subject_id); }
        toggleVeil();
    }
    window.addEventListener('popstate', e => buildFromState(e.state||{}));

    $(async function(){
        const st=getParamsFromUrl();
        if(Object.keys(st).length){ await buildFromState(st); }
        else{
            const oldState = {
                @if(old('department_head_id')) department_head_id: "{{ old('department_head_id') }}", @endif
                @if(old('department_id'))      department_id:      "{{ old('department_id') }}",      @endif
                @if(old('faculty_id'))         faculty_id:         "{{ old('faculty_id') }}",         @endif
                @if(old('semester_id'))        semester_id:        "{{ old('semester_id') }}",        @endif
                @if(old('student_batch_id'))   student_batch_id:   "{{ old('student_batch_id') }}",   @endif
                @if(old('subject_id'))         subject_id:         "{{ old('subject_id') }}",         @endif
            };
            if(Object.keys(oldState).length){ replaceUrl(oldState); await buildFromState(oldState); }
            else { toggleVeil(); }
        }
    });

    /* ---------- schedule builder ---------- */
    const $list=$('#schedule-list');
    const teacherOptions=@json($teachers);
    const days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    function nextIdx(){ let max=-1; $list.find('.schedule-row').each(function(){ max=Math.max(max,parseInt($(this).attr('data-index'),10)); }); return max+1; }
    function teacherOpts(){ let html='<option value="">Select Teacher</option>'; Object.keys(teacherOptions).forEach(k=> html+=`<option value="${k}">${teacherOptions[k]}</option>`); return html; }
    function daysOpts(){ return days.map(d=>`<option value="${d}">${d}</option>`).join(''); }
    function rowHtml(i){ return `
<div class="schedule-row" data-index="${i}">
  <div class="row g-3">
    <div class="col-md-2"><label class="form-label">Day of Week</label><select name="schedules[${i}][day_of_week]" class="form-select" required>${daysOpts()}</select></div>
    <div class="col-md-2"><label class="form-label">Period</label><input type="text" name="schedules[${i}][period]" class="form-control" placeholder="e.g., 1st"></div>
    <div class="col-md-2"><label class="form-label">Start Time</label><input type="time" name="schedules[${i}][start_time]" class="form-control" required></div>
    <div class="col-md-2"><label class="form-label">End Time</label><input type="time" name="schedules[${i}][end_time]" class="form-control" required></div>
    <div class="col-md-1"><label class="form-label">Room</label><input type="text" name="schedules[${i}][room_number]" class="form-control" placeholder="e.g., 4105" required></div>
    <div class="col-md-3"><label class="form-label">Teacher</label><select name="schedules[${i}][teacher_id]" class="form-select" required>${teacherOpts()}</select><div class="small-hint">Teacher conflicts are validated on submit</div></div>
    <div class="col-md-12 row-actions"><button type="button" class="btn-danger-soft btn-remove"><i class="fas fa-trash"></i> Remove</button></div>
  </div>
</div>`; }
    $('#add-row').on('click', function(){ if($('#schedule-holder').hasClass('disabled')){ alert('Please select Batch & Subject first'); return; } $list.append(rowHtml(nextIdx())); });
    $('#duplicate-last').on('click', function(){ if($('#schedule-holder').hasClass('disabled')){ alert('Please select Batch & Subject first'); return; }
        const $last=$list.find('.schedule-row').last(), i=nextIdx(), $new=$(rowHtml(i));
        if($last.length){ ['day_of_week','period','start_time','end_time','room_number','teacher_id'].forEach(n=>{ const v=$last.find(`[name*="[${n}]"]`).val(); $new.find(`[name="schedules[${i}][${n}]"]`).val(v); }); }
        $list.append($new);
    });
    $('#clear-all').on('click', function(){ const $rows=$list.find('.schedule-row'); if($rows.length<=1){ $rows.find('input,select').val(''); } else { $list.html(rowHtml(0)); } });
    $(document).on('click','.btn-remove', function(){ const rows=$list.find('.schedule-row'); if(rows.length<=1){ $(this).closest('.schedule-row').find('input,select').val(''); return; } $(this).closest('.schedule-row').remove(); });
    $(document).on('change','input[name*="[start_time]"], input[name*="[end_time]"]',function(){ const $row=$(this).closest('.schedule-row'), s=$row.find('input[name*="[start_time]"]').val(), e=$row.find('input[name*="[end_time]"]').val(), $end=$row.find('input[name*="[end_time]"]'); if(s&&e&&s>=e){ $end.addClass('is-invalid'); if(!$end.next('.invalid-feedback').length){ $end.after('<div class="invalid-feedback d-block">End time must be after start time</div>'); } } else { $end.removeClass('is-invalid'); $end.next('.invalid-feedback').remove(); } });
})(jQuery);
</script>
@endsection
