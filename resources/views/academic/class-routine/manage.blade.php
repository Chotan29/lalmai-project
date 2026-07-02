@extends('layouts.master')

@section('css')
<style>
    :root{
        --primary:#346da5; --primary-light:#eef2ff; --secondary:#64748b; --success:#10b981;
        --dark:#1e293b; --light:#f8fafc; --border:#e2e8f0;
        --card-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06);
        --transition:all .3s ease;
    }
    *{box-sizing:border-box;font-family:'Inter',sans-serif}
    .container-fluid{margin:0 auto;padding:0 15px}

    /* Header */
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding:0 12px}
    .header h1{font-weight:700;font-size:24px;color:var(--dark);margin:0 0 6px}
    .header p{color:var(--secondary);font-size:14px;margin:0}
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
    .badge-pill{display:inline-block;background:#eef2ff;color:var(--primary);border-radius:9999px;padding:4px 10px;font-weight:700;font-size:.9rem}

    /* Hierarchy */
    .hierarchy-container{display:flex;gap:16px;overflow-x:auto;padding:10px 0;margin-bottom:24px;scrollbar-width:thin;scrollbar-color:#cbd5e1 #f1f5f9}
    .hierarchy-container::-webkit-scrollbar{height:8px}
    .hierarchy-container::-webkit-scrollbar-track{background:#f1f5f9;border-radius:4px}
    .hierarchy-container::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
    .hierarchy-level{min-width:220px;background:#fff;border-radius:12px;box-shadow:var(--card-shadow);flex-shrink:0;border:1px solid var(--border);display:block!important}
    .level-title{color:var(--dark);font-weight:600;padding:10px;border-bottom:1px solid var(--border);background:#f8fafc;border-radius:12px 12px 0 0;display:flex;align-items:center;gap:8px;margin-top:5px!important}
    .level-items{padding:10px;max-height:420px;overflow-y:auto}
    .level-item{padding:6px 10px;border-radius:10px;background:#f8fafc;transition:var(--transition);cursor:pointer;margin-bottom:6px;display:flex;align-items:center;gap:10px;border:1px solid transparent}
    .level-item:hover{background:#f1f5f9;transform:translateY(-2px);border-color:var(--border);box-shadow:0 2px 4px rgba(0,0,0,.05)}
    .level-item.active{background:var(--primary);color:#fff;border-color:var(--primary)}
    .level-item .muted{color:#64748b;font-size:13px}

    /* Schedule */
    .toolbar{display:flex;gap:10px;justify-content:flex-end;margin-bottom:12px}
    .btn-soft{border:1px dashed var(--primary);background:var(--primary-light);color:var(--primary);padding:8px 12px;border-radius:10px;display:inline-flex;gap:6px;align-items:center;font-weight:600}
    .schedule-row{background:#f9fafb;border:1px solid #eceff7;border-left:3px solid #4361ee;border-radius:10px;padding:12px;margin-bottom:12px}
    .row-actions{display:flex;align-items:center;gap:8px;justify-content:flex-end}
    .btn-danger-soft{border:1px solid #ef4444;color:#ef4444;background:#fff5f5;padding:8px 12px;border-radius:8px}
    .btn-danger-soft:hover{background:#ffeaea}
    .small-hint{font-size:.85rem;color:#6b7280;margin-top:6px}

    /* Veil */
    .veil{position:relative}
    .veil:after{content:"Select Batch & Subject first";position:absolute;inset:0;display:none;background:rgba(255,255,255,.85);color:#6b7280;align-items:center;justify-content:center;font-weight:600;border-radius:12px;z-index:10}
    .veil.disabled:after{display:flex}

    /* Loading */
    .loading-indicator{display:none;text-align:center;padding:24px;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--card-shadow);z-index:20}

    .form-label{display:block;font-weight:500;color:var(--dark);margin-bottom:8px}
    .form-select,.form-control{width:100%}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="header">
        <div>
            <h1>Manage Class Routine</h1>
            <p>Edit existing or create new schedule for the selected subject.</p>
        </div>
        <a href="{{ route('routine.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @include('includes.flash_messages')
    @include('includes.validation_error_messages')

    <form id="manage-form" action="{{ route('routine.save') }}" method="POST">
        @csrf

        <!-- Hierarchy -->
        <div class="section">
            <div class="section-head">
                <div class="section-icon"><i class="fas fa-sitemap"></i></div>
                <div>
                    <div class="section-title">Academic Hierarchy</div>
                    <div class="section-sub">Pick Department Head → Department → Faculty → Semester → Batch & Subject</div>
                </div>
                <div class="ms-auto"><span id="mode-badge" class="badge-pill">Create Mode</span></div>
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

                <!-- Dynamic -->
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

            <!-- Hidden composite key -->
            <input type="hidden" name="department_head_id" id="department_head_id" value="{{ old('department_head_id', $prefill['department_head_id'] ?? request('department_head_id')) }}">
            <input type="hidden" name="department_id"     id="department_id"     value="{{ old('department_id',     $prefill['department_id'] ?? request('department_id')) }}">
            <input type="hidden" name="faculty_id"        id="faculty_id"        value="{{ old('faculty_id',        $prefill['faculty_id'] ?? request('faculty_id')) }}">
            <input type="hidden" name="semester_id"       id="semester_id"       value="{{ old('semester_id',       $prefill['semester_id'] ?? request('semester_id')) }}">
            <input type="hidden" name="student_batch_id"  id="student_batch_id"  value="{{ old('student_batch_id',  $prefill['student_batch_id'] ?? request('student_batch_id')) }}">
            <input type="hidden" name="subject_id"        id="subject_id"        value="{{ old('subject_id',        $prefill['subject_id'] ?? request('subject_id')) }}">

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
                    <div class="section-title" id="builder-title">Schedule Details</div>
                    <div class="section-sub" id="builder-subtitle">Add / edit multiple day/time rows for this subject.</div>
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
                    @if (!empty($oldSchedules))
                        @foreach ($oldSchedules as $i => $s)
                            <div class="schedule-row" data-index="{{ $i }}">
                                @if (!empty($s['id']))
                                    <input type="hidden" name="schedules[{{ $i }}][id]" value="{{ $s['id'] }}">
                                @endif
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Day of Week</label>
                                        <select name="schedules[{{ $i }}][day_of_week]" class="form-select @error("schedules.$i.day_of_week") is-invalid @enderror" required>
                                            @foreach (['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                <option value="{{ $day }}" {{ ($s['day_of_week'] ?? '') === $day ? 'selected' : '' }}>{{ $day }}</option>
                                            @endforeach
                                        </select>
                                        @error("schedules.$i.day_of_week")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Period</label>
                                        <input type="text" name="schedules[{{ $i }}][period]" class="form-control @error("schedules.$i.period") is-invalid @enderror" value="{{ $s['period'] ?? '' }}" placeholder="e.g., 1st">
                                        @error("schedules.$i.period")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" name="schedules[{{ $i }}][start_time]" class="form-control @error("schedules.$i.start_time") is-invalid @enderror" value="{{ $s['start_time'] ?? '' }}" required>
                                        @error("schedules.$i.start_time")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">End Time</label>
                                        <input type="time" name="schedules[{{ $i }}][end_time]" class="form-control @error("schedules.$i.end_time") is-invalid @enderror" value="{{ $s['end_time'] ?? '' }}" required>
                                        @error("schedules.$i.end_time")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Room</label>
                                        <input type="text" name="schedules[{{ $i }}][room_number]" class="form-control @error("schedules.$i.room_number") is-invalid @enderror" value="{{ $s['room_number'] ?? '' }}" placeholder="e.g., 4105" required>
                                        @error("schedules.$i.room_number")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Teacher</label>
                                        <select name="schedules[{{ $i }}][teacher_id]" class="form-select @error("schedules.$i.teacher_id") is-invalid @enderror" required>
                                            <option value="">Select Teacher</option>
                                            @foreach ($teachers as $tid => $tname)
                                                <option value="{{ $tid }}" {{ (string)($s['teacher_id'] ?? '') === (string)$tid ? 'selected' : '' }}>{{ $tname }}</option>
                                            @endforeach
                                        </select>
                                        @error("schedules.$i.teacher_id")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        <div class="small-hint mt-1">Teacher conflict is checked on save.</div>
                                    </div>
                                    <div class="col-md-12 row-actions">
                                        <button type="button" class="btn-danger-soft remove-row-btn"><i class="fas fa-trash"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="schedule-row" data-index="0">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Day of Week</label>
                                    <select name="schedules[0][day_of_week]" class="form-select" required>
                                        @foreach (['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
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
                                        @foreach ($teachers as $tid => $tname)
                                            <option value="{{ $tid }}">{{ $tname }}</option>
                                        @endforeach
                                    </select>
                                    <div class="small-hint mt-1">Teacher conflict is checked on save.</div>
                                </div>
                                <div class="col-md-12 row-actions">
                                    <button type="button" class="btn-danger-soft remove-row-btn"><i class="fas fa-trash"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <p class="small-hint mt-2" id="loaded-info" style="display:none;"></p>
            </div>
        </div>

        <div class="d-flex align-center gap-3 mt-2">
            <a href="{{ route('routine.index') }}" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
            <button type="submit" class="btn btn-primary" id="submit-btn"><i class="fas fa-save"></i> Save Schedule</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
/* ===========================================
   Manage page logic (preload-friendly & stable)
   =========================================== */
jQuery(function($){

    /* ---------- helpers ---------- */
    function showLoading(){ $('.loading-indicator').show(); $('#hierarchy-container').css('opacity','0.5'); }
    function hideLoading(){ $('.loading-indicator').hide(); $('#hierarchy-container').css('opacity','1'); }
    function icon(level){ return {department_head:'fa-user-tie',department:'fa-university',faculty:'fa-graduation-cap',semester:'fa-layer-group',batch:'fa-users',subject:'fa-book'}[level]||'fa-circle'; }

    function toggleVeil(){
        const ok = !!$('#student_batch_id').val() && !!$('#subject_id').val();
        $('#schedule-holder').toggleClass('disabled', !ok);
        return ok;
    }

    function setUrl(params){
        const url = new URL(window.location.href);
        const s = new URLSearchParams();
        Object.keys(params).forEach(k => { if(params[k]) s.set(k, params[k]); });
        const next = url.pathname + (s.toString()?('?'+s.toString()):'');
        history.pushState(params,'',next);
    }

    function simpleLoad(url, data, nextLevel, containerId){
        $('#'+nextLevel+'s-level').show();
        const $c=$('#'+containerId).empty().append('<div class="text-muted py-2">Loading...</div>');
        return $.get(url, data).done(function(resp){
            $c.empty();
            if(resp && Object.keys(resp).length){
                $.each(resp, function(k,v){
                    $c.append(`<div class="level-item" data-level="${nextLevel}" data-id="${k}">
                        <i class="fas ${icon(nextLevel)}"></i>${v}</div>`);
                });
            } else {
                $c.html('<div class="text-center py-3 text-muted">No items found</div>');
            }
        }).fail(function(){
            $c.html('<div class="text-center py-3 text-danger">Error loading data</div>');
        });
    }
    function loadBatches(semesterId){
        $('#batches-level').show();
        const $c=$('#batches-container').empty().append('<div class="text-muted py-2">Loading...</div>');
        return $.get("{{ route('get-batches') }}",{semester_id:semesterId}).done(function(data){
            $c.empty();
            if(data && Object.keys(data).length){
                $.each(data,function(k,v){
                    $c.append(`<div class="level-item" data-level="batch" data-id="${k}">
                        <i class="fas ${icon('batch')}"></i>${v}</div>`);
                });
            } else {
                $c.html('<div class="text-center py-3 text-muted">No batches found</div>');
            }
        }).fail(function(){ $c.html('<div class="text-center py-3 text-danger">Error loading batches</div>'); });
    }
    function loadSubjects(semesterId){
        $('#subjects-level').show();
        const $c=$('#subjects-container').empty().append('<div class="text-muted py-2">Loading...</div>');
        return $.get("{{ route('get-subjects') }}",{semester_id:semesterId}).done(function(data){
            $c.empty();
            if(data && Object.keys(data).length){
                $.each(data,function(k,v){
                    $c.append(`<div class="level-item" data-level="subject" data-id="${k}">
                        <i class="fas ${icon('subject')}"></i>${v}</div>`);
                });
            } else {
                $c.html('<div class="text-center py-3 text-muted">No subjects found</div>');
            }
        }).fail(function(){ $c.html('<div class="text-center py-3 text-danger">Error loading subjects</div>'); });
    }

    function hideLower(level){
        const order=['department_head','department','faculty','semester','batch','subject'];
        const idx=order.indexOf(level);
        for(let i=idx+1;i<order.length;i++){
            const lv=order[i];
            $('#'+lv+'s-level').hide();
            $('#'+lv+'s-container').empty();
            $('#'+lv+'_id').val('');
        }
        if(level!=='semester'){ $('#student_batch_id,#subject_id').val(''); }
        toggleVeil();
    }

    /* ---------- schedule builder ---------- */
    const $list = $('#schedule-list');
    const teacherOptions = @json($teachers);
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    function nextIndex(){ let max=-1; $list.find('.schedule-row').each(function(){ max=Math.max(max, parseInt($(this).attr('data-index'),10)); }); return max+1; }
    function dayOptions(selected){ return days.map(d=>`<option value="${d}" ${selected===d?'selected':''}>${d}</option>`).join(''); }
    function teacherOpts(selected){ let html='<option value="">Select Teacher</option>'; Object.keys(teacherOptions).forEach(k=>{ html += `<option value="${k}" ${String(selected)===String(k)?'selected':''}>${teacherOptions[k]}</option>`; }); return html; }

    function buildRow(idx, d){
        d = d || {};
        const idHidden = d.id ? `<input type="hidden" name="schedules[${idx}][id]" value="${d.id}">` : '';
        return `
        <div class="schedule-row" data-index="${idx}">
            ${idHidden}
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Day of Week</label>
                    <select name="schedules[${idx}][day_of_week]" class="form-select" required>${dayOptions(d.day_of_week)}</select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Period</label>
                    <input type="text" name="schedules[${idx}][period]" class="form-control" value="${d.period||''}" placeholder="e.g., 1st">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="schedules[${idx}][start_time]" class="form-control" value="${d.start_time||''}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Time</label>
                    <input type="time" name="schedules[${idx}][end_time]" class="form-control" value="${d.end_time||''}" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Room</label>
                    <input type="text" name="schedules[${idx}][room_number]" class="form-control" value="${d.room_number||''}" placeholder="e.g., 4105" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teacher</label>
                    <select name="schedules[${idx}][teacher_id]" class="form-select" required>${teacherOpts(d.teacher_id)}</select>
                    <div class="small-hint mt-1">Teacher conflict is checked on save.</div>
                </div>
                <div class="col-md-12 row-actions">
                    <button type="button" class="btn-danger-soft remove-row-btn"><i class="fas fa-trash"></i> Remove</button>
                </div>
            </div>
        </div>`;
    }

    $('#add-row').on('click', function(){ if ($('#schedule-holder').hasClass('disabled')) return; $list.append(buildRow(nextIndex(), {})); });
    $('#duplicate-last').on('click', function(){
        if ($('#schedule-holder').hasClass('disabled')) return;
        const $last=$list.find('.schedule-row').last(); const i=nextIndex(); const $new=$(buildRow(i,{}));
        if($last.length){
            ['day_of_week','period','start_time','end_time','room_number','teacher_id'].forEach(function(name){
                const val=$last.find(`[name*="[${name}]"]`).val(); $new.find(`[name="schedules[${i}][${name}]"]`).val(val);
            });
        }
        $list.append($new);
    });
    $('#clear-all').on('click', function(){
        const $rows=$list.find('.schedule-row');
        if($rows.length<=1){ $rows.find('input,select').val(''); } else { $list.html(buildRow(0,{})); }
    });
    $(document).on('click','.remove-row-btn', function(){
        const rows=$list.find('.schedule-row');
        if(rows.length<=1){ $(this).closest('.schedule-row').find('input,select').val(''); return; }
        $(this).closest('.schedule-row').remove();
    });

    // client-side end>start hint
    $(document).on('change','input[name*="[start_time]"], input[name*="[end_time]"]', function(){
        const $row=$(this).closest('.schedule-row');
        const s=$row.find('input[name*="[start_time]"]').val();
        const e=$row.find('input[name*="[end_time]"]').val();
        const $end=$row.find('input[name*="[end_time]"]');
        if(s && e && s>=e){
            $end.addClass('is-invalid');
            if(!$end.next('.invalid-feedback').length){
                $end.after('<div class="invalid-feedback d-block">End time must be after start time.</div>');
            }
        } else {
            $end.removeClass('is-invalid'); $end.next('.invalid-feedback').remove();
        }
    });

    /* ---------- existing schedules ---------- */
    function fetchExisting(){
        const params = {
            department_id:    $('#department_id').val(),
            faculty_id:       $('#faculty_id').val(),
            semester_id:      $('#semester_id').val(),
            student_batch_id: $('#student_batch_id').val(),
            subject_id:       $('#subject_id').val()
        };
        if(!params.department_id || !params.faculty_id || !params.semester_id || !params.student_batch_id || !params.subject_id) return;

        $('#loaded-info').hide().text('');
        $('#mode-badge').text('Create Mode');
        $('#builder-title').text('Schedule Details');
        $('#builder-subtitle').text('Add / edit multiple day/time rows for this subject.');

        $.get("{{ route('routine.existing') }}", params).done(function(resp){
            if(resp && resp.count>0){
                $list.empty();
                resp.schedules.forEach((row,i)=>{ $list.append(buildRow(i,row)); });
                $('#mode-badge').text('Edit Mode');
                $('#builder-title').text('Edit Schedule');
                $('#builder-subtitle').text('These rows are already scheduled. Add, modify, or remove, then Save.');
                $('#loaded-info').text(`Loaded ${resp.count} existing row(s).`).show();
            } else {
                if($list.find('.schedule-row').length===0){ $list.append(buildRow(0, {})); }
                $('#mode-badge').text('Create Mode');
                $('#builder-title').text('Create Schedule');
                $('#builder-subtitle').text('No existing rows found for this selection. Add one or more rows, then Save.');
            }
        });
    }

    /* ---------- click chain ---------- */
    $(document).on('click','.level-item',function(e){
        e.preventDefault();
        const level=$(this).data('level');
        const id=$(this).data('id');

        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        if(level==='batch') $('#student_batch_id').val(id);
        else $('#'+level+'_id').val(id);

        showLoading();

        if(level==='department_head'){
            hideLower('department_head');
            simpleLoad("{{ route('get-departments') }}",{department_head_id:id},'department','departments-container')
                .always(hideLoading);
            setUrl({department_head_id:id});
            return;
        }
        if(level==='department'){
            hideLower('department');
            simpleLoad("{{ route('get-faculties') }}",{department_id:id},'faculty','faculties-container')
                .always(hideLoading);
            const p = new URLSearchParams(window.location.search);
            setUrl({department_head_id:p.get('department_head_id'),department_id:id});
            return;
        }
        if(level==='faculty'){
            hideLower('faculty');
            simpleLoad("{{ route('get-semesters') }}",{faculty_id:id},'semester','semesters-container')
                .always(hideLoading);
            const p = new URLSearchParams(window.location.search);
            setUrl({department_head_id:p.get('department_head_id'),department_id:$('#department_id').val(),faculty_id:id});
            return;
        }
        if(level==='semester'){
            hideLower('semester');
            $('#student_batch_id,#subject_id').val('');
            $.when(loadBatches(id), loadSubjects(id)).always(function(){
                hideLoading();
                const p = new URLSearchParams(window.location.search);
                setUrl({
                    department_head_id:p.get('department_head_id'),
                    department_id:$('#department_id').val(),
                    faculty_id:$('#faculty_id').val(),
                    semester_id:id
                });
                if(toggleVeil()) fetchExisting();
            });
            return;
        }
        if(level==='batch' || level==='subject'){
            hideLoading();
            const p = new URLSearchParams(window.location.search);
            setUrl({
                department_head_id:p.get('department_head_id'),
                department_id:$('#department_id').val(),
                faculty_id:$('#faculty_id').val(),
                semester_id:$('#semester_id').val(),
                student_batch_id:$('#student_batch_id').val(),
                subject_id:$('#subject_id').val()
            });
            if(toggleVeil()) fetchExisting();
            return;
        }
    });

    /* ---------- PRELOAD from URL or hidden fields ---------- */
    (function preload(){
        showLoading();

        const pre = {
            department_head_id: $('#department_head_id').val() || null,
            department_id:      $('#department_id').val()     || null,
            faculty_id:         $('#faculty_id').val()        || null,
            semester_id:        $('#semester_id').val()       || null,
            student_batch_id:   $('#student_batch_id').val()  || null,
            subject_id:         $('#subject_id').val()        || null
        };

        // A) With department_head_id (best path)
        if(pre.department_head_id){
            $(`.level-item[data-level="department_head"][data-id="${pre.department_head_id}"]`).addClass('active');
            simpleLoad("{{ route('get-departments') }}",{department_head_id:pre.department_head_id},'department','departments-container').done(function(){
                if(pre.department_id){
                    $('#department_id').val(pre.department_id);
                    setTimeout(()=>{$(`.level-item[data-level="department"][data-id="${pre.department_id}"]`).addClass('active');},0);
                    simpleLoad("{{ route('get-faculties') }}",{department_id:pre.department_id},'faculty','faculties-container').done(function(){
                        if(pre.faculty_id){
                            $('#faculty_id').val(pre.faculty_id);
                            setTimeout(()=>{$(`.level-item[data-level="faculty"][data-id="${pre.faculty_id}"]`).addClass('active');},0);
                            simpleLoad("{{ route('get-semesters') }}",{faculty_id:pre.faculty_id},'semester','semesters-container').done(function(){
                                if(pre.semester_id){
                                    $('#semester_id').val(pre.semester_id);
                                    setTimeout(()=>{$(`.level-item[data-level="semester"][data-id="${pre.semester_id}"]`).addClass('active');},0);
                                    $.when(loadBatches(pre.semester_id), loadSubjects(pre.semester_id)).done(function(){
                                        if(pre.student_batch_id){
                                            $('#student_batch_id').val(pre.student_batch_id);
                                            setTimeout(()=>{$(`.level-item[data-level="batch"][data-id="${pre.student_batch_id}"]`).addClass('active');},0);
                                        }
                                        if(pre.subject_id){
                                            $('#subject_id').val(pre.subject_id);
                                            setTimeout(()=>{$(`.level-item[data-level="subject"][data-id="${pre.subject_id}"]`).addClass('active');},0);
                                        }
                                        hideLoading();
                                        if(toggleVeil()) fetchExisting();
                                    });
                                }else hideLoading();
                            });
                        }else hideLoading();
                    });
                }else hideLoading();
            });
            return;
        }

        // B) No department_head_id → fallback (works with direct links from index)
        if(pre.department_id && pre.faculty_id){
            // we can skip rendering departments and start lower
            $('#department_id').val(pre.department_id);
            $('#faculty_id').val(pre.faculty_id);
            simpleLoad("{{ route('get-faculties') }}",{department_id:pre.department_id},'faculty','faculties-container').done(function(){
                setTimeout(()=>{$(`.level-item[data-level="faculty"][data-id="${pre.faculty_id}"]`).addClass('active');},0);
                simpleLoad("{{ route('get-semesters') }}",{faculty_id:pre.faculty_id},'semester','semesters-container').done(function(){
                    if(pre.semester_id){
                        $('#semester_id').val(pre.semester_id);
                        setTimeout(()=>{$(`.level-item[data-level="semester"][data-id="${pre.semester_id}"]`).addClass('active');},0);
                        $.when(loadBatches(pre.semester_id), loadSubjects(pre.semester_id)).done(function(){
                            if(pre.student_batch_id){
                                $('#student_batch_id').val(pre.student_batch_id);
                                setTimeout(()=>{$(`.level-item[data-level="batch"][data-id="${pre.student_batch_id}"]`).addClass('active');},0);
                            }
                            if(pre.subject_id){
                                $('#subject_id').val(pre.subject_id);
                                setTimeout(()=>{$(`.level-item[data-level="subject"][data-id="${pre.subject_id}"]`).addClass('active');},0);
                            }
                            hideLoading();
                            if(toggleVeil()) fetchExisting();
                        });
                    }else hideLoading();
                });
            });
            return;
        }

        hideLoading();
        toggleVeil(); // default disabled
    })();

    // back/forward support if you like
    window.addEventListener('popstate', e => {
        // simple approach: reload the page sections according to new URL
        location.reload();
    });
});
</script>
@endsection
