@extends('layouts.master')

@section('css')
<style>
    :root{
        --primary:#4f46e5; --primary-light:#eef2ff; --secondary:#64748b;
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

    /* Chips */
    .chips{display:flex;flex-wrap:wrap;gap:10px}
    .chip{display:inline-flex;align-items:center;gap:8px;background:#f8fafc;border:1px solid var(--border);color:#0f172a;border-radius:9999px;padding:8px 12px;font-size:.9rem}
    .chip i{color:var(--primary)}

    /* Hierarchy (match manage page) */
    .hierarchy-container{display:flex;gap:15px;overflow-x:auto;padding:10px 0;margin-bottom:20px;scrollbar-width:thin;scrollbar-color:#cbd5e1 #f1f5f9}
    .hierarchy-container::-webkit-scrollbar{height:8px}
    .hierarchy-container::-webkit-scrollbar-track{background:#f1f5f9;border-radius:4px}
    .hierarchy-container::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
    .hierarchy-level{min-width:250px;background:#fff;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,.05);flex-shrink:0;display:block!important;border:1px solid var(--border)}
    .level-title{color:#4361ee;font-weight:600;padding:12px 15px;border-bottom:1px solid rgba(67,97,238,.1);background:#f8f9fa;border-radius:8px 8px 0 0;display:flex;align-items:center;gap:8px}
    .level-items{padding:10px;max-height:400px;overflow-y:auto}
    .level-item{padding:10px 12px;border-radius:6px;background:rgba(67,97,238,.05);transition:.2s;cursor:pointer;margin-bottom:8px;display:flex;align-items:center;gap:8px;border-left:3px solid transparent}
    .level-item:hover{background:rgba(67,97,238,.1);transform:translateY(-2px);border-left-color:#4361ee}
    .level-item.active{background:#4361ee;color:#fff;border-left-color:#2a4bd1}

    /* Schedule */
    .toolbar{display:flex;gap:10px;justify-content:flex-end;margin-bottom:12px}
    .btn-soft{border:1px dashed var(--primary);background:var(--primary-light);color:var(--primary);padding:8px 12px;border-radius:8px;display:inline-flex;gap:6px;align-items:center}
    .schedule-row{background:#f9fafb;border:1px solid #eceff7;border-left:3px solid #4361ee;border-radius:10px;padding:12px;margin-bottom:12px}
    .row-actions{display:flex;align-items:center;gap:8px;justify-content:flex-end}
    .btn-danger-soft{border:1px solid #ef4444;color:#ef4444;background:#fff5f5;padding:8px 12px;border-radius:8px;display:inline-flex;gap:6px;align-items:center}
    .btn-danger-soft:hover{background:#ffeaea}
    .small-hint{font-size:.85rem;color:#6b7280;margin-top:6px}

    /* Veil until batch+subject picked */
    .veil{position:relative}
    .veil::after{content:"Select Batch & Subject first";position:absolute;inset:0;display:none;background:rgba(255,255,255,.85);color:#6b7280;align-items:center;justify-content:center;font-weight:600;border-radius:12px;z-index:10}
    .veil.disabled::after{display:flex}

    /* Form inputs */
    .form-label{font-weight:500;color:var(--dark);margin-bottom:8px;display:block}
    .form-select,.form-control{border:1px solid var(--border);border-radius:8px;padding:5px 12px;width:100%}
    .form-select:focus,.form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.1);outline:none}

    /* Loading */
    .loading-indicator{display:none;text-align:center;padding:24px;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--card-shadow);z-index:20}

    /* Footer actions */
    .actions{display:flex;gap:12px;justify-content:center;margin-top:18px;}

    @media (max-width:768px){
        .header{flex-direction:column;align-items:flex-start;gap:16px}
        .hierarchy-level{min-width:260px}
        .toolbar{flex-wrap:wrap}
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
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

    <!-- Selection chips -->
    {{-- <div class="section">
        <div class="section-head">
            <div class="section-icon"><i class="fas fa-list"></i></div>
            <div>
                <div class="section-title">Selection Summary</div>
                <div class="section-sub">These update as you click through the hierarchy.</div>
            </div>
        </div>
        <div class="chips" id="selection-chips">
            <span class="chip" data-chip="department"><i class="fas fa-university"></i><b>Department:</b> <span class="v">—</span></span>
            <span class="chip" data-chip="faculty"><i class="fas fa-graduation-cap"></i><b>Faculty:</b> <span class="v">—</span></span>
            <span class="chip" data-chip="semester"><i class="fas fa-layer-group"></i><b>Semester:</b> <span class="v">—</span></span>
            <span class="chip" data-chip="batch"><i class="fas fa-users"></i><b>Batch:</b> <span class="v">—</span></span>
            <span class="chip" data-chip="subject"><i class="fas fa-book"></i><b>Subject:</b> <span class="v">—</span></span>
        </div>
    </div> --}}

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
                    <h3 class="level-title"><i class="fas fa-user-tie"></i> Department Heads</h3>
                    <div class="level-items">
                        @foreach ($department_heads as $id => $name)
                            <div class="level-item" data-level="department_head" data-id="{{ $id }}">
                                <i class="fas fa-user-tie"></i>{{ $name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dynamic levels (ids EXACTLY like manage page) -->
                <div class="hierarchy-level" id="departments-level" style="display:none;">
                    <h3 class="level-title"><i class="fas fa-university"></i> Departments</h3>
                    <div class="level-items" id="departments-container"></div>
                </div>

                <div class="hierarchy-level" id="faculties-level" style="display:none;">
                    <h3 class="level-title"><i class="fas fa-graduation-cap"></i> Faculties/Programs</h3>
                    <div class="level-items" id="faculties-container"></div>
                </div>

                <div class="hierarchy-level" id="semesters-level" style="display:none;">
                    <h3 class="level-title"><i class="fas fa-layer-group"></i> Semesters</h3>
                    <div class="level-items" id="semesters-container"></div>
                </div>

                <div class="hierarchy-level" id="batches-level" style="display:none;">
                    <h3 class="level-title"><i class="fas fa-users"></i> Batches</h3>
                    <div class="level-items" id="batches-container"></div>
                </div>

                <div class="hierarchy-level" id="subjects-level" style="display:none;">
                    <h3 class="level-title"><i class="fas fa-book"></i> Subjects</h3>
                    <div class="level-items" id="subjects-container"></div>
                </div>
            </div>

            <!-- Hidden inputs (used by store) -->
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
        @php /* $teachers is passed from controller as [id => name] */ @endphp
                                        <select name="schedules[{{ $i }}][teacher_id]" class="form-select @error("schedules.$i.teacher_id") is-invalid @enderror" required>
                                            <option value="">Select Teacher</option>
                                            @foreach($teachers as $tid => $tname)
                                                <option value="{{ $tid }}" {{ (string)($s['teacher_id']??'')===(string)$tid?'selected':'' }}>{{ $tname }}</option>
                                            @endforeach
                                        </select>
                                        @error("schedules.$i.teacher_id")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        <div class="small-hint">Teacher conflicts are validated on submit</div>
                                    </div>
                                    <div class="col-md-8 row-actions">
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
    // -------- helpers --------
    const $chips = $('#selection-chips');
    const setChip = (key, label) => $chips.find(`[data-chip="${key}"] .v`).text(label || '—');

    function showLoading(){ $('.loading-indicator').show(); $('#hierarchy-container').css('opacity','.5'); }
    function hideLoading(){ $('.loading-indicator').hide(); $('#hierarchy-container').css('opacity','1'); }

    function toggleVeil(){
        const ok = !!$('#student_batch_id').val() && !!$('#subject_id').val();
        $('#schedule-holder').toggleClass('disabled', !ok);
    }

    function getIcon(level){
        return {department:'fa-university', faculty:'fa-graduation-cap', semester:'fa-layer-group', batch:'fa-users', subject:'fa-book'}[level]||'fa-circle';
    }

    function hideLower(level){
        const order=['department','faculty','semester','batch','subject'];
        const idx=order.indexOf(level);
        if(idx>=0){
            for(let i=idx+1;i<order.length;i++){
                $('#'+order[i]+'s-level').hide();
                $('#'+order[i]+'_id').val('');
                $('#'+order[i]+'s-container').empty();
                setChip(order[i], '');
            }
        }
        if(level!=='semester'){ // when going up before semester, clear batch+subject veil
            $('#student_batch_id').val('');
            $('#subject_id').val('');
            setChip('batch',''); setChip('subject','');
            toggleVeil();
        }
    }

    function item(level, id, text){
        return `<div class="level-item" data-level="${level}" data-id="${id}">
            <i class="fas ${getIcon(level)}"></i>${text}
        </div>`;
    }

    // -------- AJAX loaders (same endpoints as manage) --------
    function simpleLoad(url, data, nextLevel, containerId){
        $('#'+nextLevel+'s-level').show();
        const $c=$('#'+containerId).empty().append('<div class="text-muted py-2">Loading...</div>');
        return $.get(url, data)
            .done(function(resp){
                $c.empty();
                if(resp && Object.keys(resp).length){
                    $.each(resp, function(k,v){
                        $c.append(item(nextLevel,k,v));
                    });
                }else{
                    $c.html('<div class="text-center py-3 text-muted">No items found</div>');
                }
            })
            .fail(function(){ $c.html('<div class="text-center py-3 text-danger">Error loading data</div>'); });
    }

    function loadBatches(semesterId){
        $('#batches-level').show();
        const $c=$('#batches-container').empty().append('<div class="text-muted py-2">Loading...</div>');
        return $.get("{{ route('get-batches') }}",{semester_id:semesterId})
            .done(function(data){
                $c.empty();
                if(data && Object.keys(data).length){
                    $.each(data,function(k,v){ $c.append(item('batch',k,v)); });
                }else $c.html('<div class="text-center py-3 text-muted">No batches found</div>');
            })
            .fail(function(){ $c.html('<div class="text-center py-3 text-danger">Error loading batches</div>'); });
    }

    function loadSubjects(semesterId){
        $('#subjects-level').show();
        const $c=$('#subjects-container').empty().append('<div class="text-muted py-2">Loading...</div>');
        return $.get("{{ route('get-subjects') }}",{semester_id:semesterId})
            .done(function(data){
                $c.empty();
                if(data && Object.keys(data).length){
                    $.each(data,function(k,v){ $c.append(item('subject',k,v)); });
                }else $c.html('<div class="text-center py-3 text-muted">No subjects found</div>');
            })
            .fail(function(){ $c.html('<div class="text-center py-3 text-danger">Error loading subjects</div>'); });
    }

    // -------- hierarchy clicks --------
    $(document).on('click','.level-item',function(e){
        e.preventDefault();
        const level=$(this).data('level');
        const id=$(this).data('id');

        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        if(level==='batch'){
            $('#student_batch_id').val(id);
            setChip('batch', $.trim($(this).text()));
        } else if(level==='subject'){
            $('#subject_id').val(id);
            setChip('subject', $.trim($(this).text()));
        } else {
            $('#'+level+'_id').val(id);
            setChip(level, $.trim($(this).text()));
        }

        showLoading();

        if(level==='department_head'){
            hideLower('department_head');
            simpleLoad("{{ route('get-departments') }}",{department_head_id:id},'department','departments-container').always(hideLoading);
            return;
        }
        if(level==='department'){
            hideLower('department');
            simpleLoad("{{ route('get-faculties') }}",{department_id:id},'faculty','faculties-container').always(hideLoading);
            return;
        }
        if(level==='faculty'){
            hideLower('faculty');
            simpleLoad("{{ route('get-semesters') }}",{faculty_id:id},'semester','semesters-container').always(hideLoading);
            return;
        }
        if(level==='semester'){
            hideLower('semester');
            $('#student_batch_id,#subject_id').val('');
            setChip('batch',''); setChip('subject','');
            $.when(loadBatches(id), loadSubjects(id)).always(function(){ hideLoading(); toggleVeil(); });
            return;
        }
        if(level==='batch' || level==='subject'){
            hideLoading();
            toggleVeil();
            return;
        }
    });

    // -------- schedule builder --------
    const $list = $('#schedule-list');
    const teacherOptions = @json($teachers);
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    function nextIdx(){
        let max=-1;
        $list.find('.schedule-row').each(function(){ max = Math.max(max, parseInt($(this).attr('data-index'),10)); });
        return max+1;
    }
    function teacherOpts(){
        let html = '<option value="">Select Teacher</option>';
        Object.keys(teacherOptions).forEach(function(k){
            html += `<option value="${k}">${teacherOptions[k]}</option>`;
        });
        return html;
    }
    function daysOpts(){
        return days.map(d=>`<option value="${d}">${d}</option>`).join('');
    }
    function rowHtml(i){
        return `
<div class="schedule-row" data-index="${i}">
  <div class="row g-3">
    <div class="col-md-2">
      <label class="form-label">Day of Week</label>
      <select name="schedules[${i}][day_of_week]" class="form-select" required>${daysOpts()}</select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Period</label>
      <input type="text" name="schedules[${i}][period]" class="form-control" placeholder="e.g., 1st">
    </div>
    <div class="col-md-2">
      <label class="form-label">Start Time</label>
      <input type="time" name="schedules[${i}][start_time]" class="form-control" required>
    </div>
    <div class="col-md-2">
      <label class="form-label">End Time</label>
      <input type="time" name="schedules[${i}][end_time]" class="form-control" required>
    </div>
    <div class="col-md-1">
      <label class="form-label">Room</label>
      <input type="text" name="schedules[${i}][room_number]" class="form-control" placeholder="e.g., 4105" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Teacher</label>
      <select name="schedules[${i}][teacher_id]" class="form-select" required>${teacherOpts()}</select>
      <div class="small-hint">Teacher conflicts are validated on submit</div>
    </div>
    <div class="col-md-12 row-actions">
      <button type="button" class="btn-danger-soft btn-remove"><i class="fas fa-trash"></i> Remove</button>
    </div>
  </div>
</div>`;
    }

    $('#add-row').on('click', function(){
        if($('#schedule-holder').hasClass('disabled')){ alert('Please select Batch & Subject first'); return; }
        $list.append(rowHtml(nextIdx()));
    });

    $('#duplicate-last').on('click', function(){
        if($('#schedule-holder').hasClass('disabled')){ alert('Please select Batch & Subject first'); return; }
        const $last = $list.find('.schedule-row').last();
        const i = nextIdx();
        const $new = $(rowHtml(i));
        if($last.length){
            ['day_of_week','period','start_time','end_time','room_number','teacher_id'].forEach(function(name){
                const val = $last.find(`[name*="[${name}]"]`).val();
                $new.find(`[name="schedules[${i}][${name}]"]`).val(val);
            });
        }
        $list.append($new);
    });

    $('#clear-all').on('click', function(){
        const $rows = $list.find('.schedule-row');
        if($rows.length<=1){ $rows.find('input,select').val(''); }
        else { $list.html(rowHtml(0)); }
    });

    $(document).on('click','.btn-remove',function(){
        const rows=$list.find('.schedule-row');
        if(rows.length<=1){ $(this).closest('.schedule-row').find('input,select').val(''); return; }
        $(this).closest('.schedule-row').remove();
    });

    // simple end>start check
    $(document).on('change','input[name*="[start_time]"], input[name*="[end_time]"]',function(){
        const $row=$(this).closest('.schedule-row');
        const s=$row.find('input[name*="[start_time]"]').val();
        const e=$row.find('input[name*="[end_time]"]').val();
        const $end=$row.find('input[name*="[end_time]"]');
        if(s && e && s>=e){
            $end.addClass('is-invalid');
            if(!$end.next('.invalid-feedback').length){
                $end.after('<div class="invalid-feedback d-block">End time must be after start time</div>');
            }
        }else{
            $end.removeClass('is-invalid');
            $end.next('.invalid-feedback').remove();
        }
    });

    // -------- restore old() (progressively load) --------
    @if(old('department_head_id'))
        const dh = "{{ old('department_head_id') }}";
        $(`.level-item[data-level="department_head"][data-id="${dh}"]`).addClass('active');
        setChip('department',''); setChip('faculty',''); setChip('semester',''); setChip('batch',''); setChip('subject','');
        showLoading();
        $.get("{{ route('get-departments') }}",{department_head_id:dh}).done(function(r1){
            $('#departments-level').show();
            const $d=$('#departments-container').empty();
            $.each(r1,(k,v)=>$d.append(item('department',k,v)));
            @if(old('department_id'))
                const d="{{ old('department_id') }}";
                $(`.level-item[data-level="department"][data-id="${d}"]`).addClass('active');
                setChip('department',$.trim($(`.level-item[data-level="department"][data-id="${d}"]`).text()));
                $.get("{{ route('get-faculties') }}",{department_id:d}).done(function(r2){
                    $('#faculties-level').show();
                    const $f=$('#faculties-container').empty();
                    $.each(r2,(k,v)=>$f.append(item('faculty',k,v)));
                    @if(old('faculty_id'))
                        const f="{{ old('faculty_id') }}";
                        $(`.level-item[data-level="faculty"][data-id="${f}"]`).addClass('active');
                        setChip('faculty',$.trim($(`.level-item[data-level="faculty"][data-id="${f}"]`).text()));
                        $.get("{{ route('get-semesters') }}",{faculty_id:f}).done(function(r3){
                            $('#semesters-level').show();
                            const $s=$('#semesters-container').empty();
                            $.each(r3,(k,v)=>$s.append(item('semester',k,v)));
                            @if(old('semester_id'))
                                const s="{{ old('semester_id') }}";
                                $(`.level-item[data-level="semester"][data-id="${s}"]`).addClass('active');
                                setChip('semester',$.trim($(`.level-item[data-level="semester"][data-id="${s}"]`).text()));
                                $.when(loadBatches(s), loadSubjects(s)).done(function(){
                                    @if(old('student_batch_id'))
                                        const b="{{ old('student_batch_id') }}";
                                        $(`.level-item[data-level="batch"][data-id="${b}"]`).addClass('active');
                                        setChip('batch',$.trim($(`.level-item[data-level="batch"][data-id="${b}"]`).text()));
                                    @endif
                                    @if(old('subject_id'))
                                        const sub="{{ old('subject_id') }}";
                                        $(`.level-item[data-level="subject"][data-id="${sub}"]`).addClass('active');
                                        setChip('subject',$.trim($(`.level-item[data-level="subject"][data-id="${sub}"]`).text()));
                                    @endif
                                    toggleVeil();
                                });
                            @endif
                        });
                    @endif
                });
            @endif
        }).always(hideLoading);
    @endif
})(jQuery);
</script>
@endsection
