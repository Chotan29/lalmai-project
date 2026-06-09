<style>
    .badge-not-printed { background:#28a745; color:#fff; padding:3px 8px; border-radius:4px; font-size:0.78rem; }
    .badge-today       { background:#fd7e14; color:#fff; padding:3px 8px; border-radius:4px; font-size:0.78rem; }
    .badge-before      { background:#dc3545; color:#fff; padding:3px 8px; border-radius:4px; font-size:0.78rem; }
    tr.printed-today   { background-color:#fff8e1 !important; }
    tr.printed-before  { background-color:#fdecea !important; }
</style>

<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;TargetExam List</h4>
        {!! Form::open(['route' => 'print-out.exam.admit-card', 'id' => 'bulk_action_form']) !!}
        <div class="clearfix">
            <div class="form-horizontal">
                <div class="clearfix">
                    <div class="form-group">
                        {!! Form::label('years_id', 'Year', ['class' => 'col-sm-1 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::select('years_id', $data['years'], null, ["class" => "form-control border-form","required"]) !!}
                        </div>

                        {!! Form::label('months_id', 'Month', ['class' => 'col-sm-1 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::select('months_id', $data['months'], null, ["class" => "form-control border-form","required"]) !!}
                        </div>

                        {!! Form::label('exams_id', 'Exam', ['class' => 'col-sm-1 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::select('exams_id', $data['exams'], null, ["class" => "form-control border-form chosen-select","required"]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
                        <div class="col-sm-4">
                            {!! Form::select('target_faculty', $data['faculties'], null, ['class' => 'form-control chosen-select', 'onChange' => 'loadSemesters(this);']) !!}
                        </div>

                        <label class="col-sm-1 control-label">{{__('form_fields.student.fields.semester')}}</label>
                        <div class="col-sm-2">
                            <select name="semester_select" class="form-control semester_select">
                                <option> Select {{__('form_fields.student.fields.semester')}} </option>
                            </select>
                        </div>

                        <label class="pos-rel">
                            {!! Form::radio('print_type',1, true, ['class' => 'ace', "required"]) !!}
                            <span class="lbl"></span> <span class="label label-primary">Admit Card</span>
                        </label>

                        <label class="pos-rel">
                            {!! Form::radio('print_type',2, false, ['class' => 'ace', "required"]) !!}
                            <span class="lbl"></span> <span class="label label-success">Admit Card With Schedule</span>
                        </label>
                    </div>
                </div>
                <div class="clearfix form-actions">
                    <div class="align-right">
                        <button class="btn btn-info" type="submit" id="print-btn">
                            <i class="fa fa-print bigger-110"></i> Print Admit Card
                        </button>
                    </div>
                </div>
                <div class="hr hr-24"></div>
            </div>

            {{-- Date filter + quick select --}}
            @if(isset($data['student']) && $data['student']->count() > 0)
            <div class="well well-sm" style="margin-bottom:10px; padding:8px 14px;">
                <div class="row" style="display:flex; align-items:center; flex-wrap:wrap; gap:8px;">
                    <div class="col-xs-12 col-sm-6" style="display:flex; align-items:center; gap:8px;">
                        <label style="margin:0; white-space:nowrap;"><i class="fa fa-calendar"></i> Print Date Filter:</label>
                        <input type="date" id="print_filter_date" class="form-control" style="width:160px; display:inline-block;"
                               value="{{ $data['print_filter_date'] }}" />
                        <button type="button" class="btn btn-default btn-sm" onclick="applyDateFilter()">
                            <i class="fa fa-refresh"></i> Apply
                        </button>
                    </div>
                    <div class="col-xs-12 col-sm-6" style="text-align:right;">
                        <button type="button" class="btn btn-success btn-sm" onclick="selectByBadge('not-printed')">
                            <i class="fa fa-check-square-o"></i> Select Not Printed
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="selectByBadge('before')">
                            <i class="fa fa-check-square-o"></i> Select Old
                        </button>
                        <button type="button" class="btn btn-default btn-sm" onclick="selectAll(false)">
                            <i class="fa fa-square-o"></i> Deselect All
                        </button>
                    </div>
                </div>
                <div style="margin-top:6px; font-size:0.82rem;">
                    <span class="badge-not-printed"><i class="fa fa-circle"></i> Not Printed</span>&nbsp;
                    <span class="badge-today"><i class="fa fa-circle"></i> Printed on filter date</span>&nbsp;
                    <span class="badge-before"><i class="fa fa-circle"></i> Printed on other date</span>
                </div>
            </div>
            @endif

            <hr class="hr-8">
            <span class="pull-right tableTools-container"></span>
        </div>

        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" id="select-all-chk" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{__('form_fields.student.fields.faculty')}}</th>
                        <th>{{__('form_fields.student.fields.semester')}}</th>
                        <th>Reg. Date</th>
                        <th>Reg. Num.</th>
                        <th>Name of Student</th>
                        <th>Print Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                @foreach(isset($data['student']) ? $data['student'] : [] as $student)
                    <?php
                        $badge    = isset($student->print_badge) ? $student->print_badge : 'not-printed';
                        $rowClass = $badge === 'today' ? 'printed-today' : ($badge === 'before' ? 'printed-before' : '');
                        $lastDate = isset($student->last_print_date) ? $student->last_print_date : '';
                        if ($badge === 'not-printed') {
                            $badgeHtml = '<span class="badge-not-printed"><i class="fa fa-print"></i> Not Printed</span>';
                        } elseif ($badge === 'today') {
                            $badgeHtml = '<span class="badge-today"><i class="fa fa-check"></i> '.e($lastDate).'</span>';
                        } else {
                            $badgeHtml = '<span class="badge-before"><i class="fa fa-exclamation"></i> '.e($lastDate).'</span>';
                        }
                    ?>
                    <tr class="{{ $rowClass }}" data-badge="{{ $badge }}">
                        <td class="center first-child">
                            <label>
                                <input type="checkbox" name="chkIds[]" value="{{ $student->id }}" class="ace student-chk" />
                                <span class="lbl"></span>
                            </label>
                        </td>
                        <td>{{ $i }}</td>
                        <td>{{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
                        <td>{{ ViewHelper::getSemesterTitle($student->semester) }}</td>
                        <td>{{ \Carbon\Carbon::parse($student->reg_date)->format('Y-m-d') }}</td>
                        <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a></td>
                        <td>{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</td>
                        <td>{!! $badgeHtml !!}</td>
                        <td>
                            {{ ViewHelper::getAcademicStatus($student->academic_status)}}
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $student->status == 'active' ? 'btn-info' : 'btn-warning' }}">
                                    {{ $student->status == 'active' ? 'Active' : 'In Active' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php $i++; ?>
                @endforeach
                @if(!isset($data['student']) || $data['student']->count() === 0)
                    <tr>
                        <td colspan="10">No {{ $panel }} data found. Please Filter {{ $panel }} to show.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
function selectByBadge(badge) {
    document.querySelectorAll('.student-chk').forEach(function(chk) {
        var row = chk.closest('tr');
        chk.checked = (row.getAttribute('data-badge') === badge);
    });
    syncSelectAll();
}
function selectAll(state) {
    document.querySelectorAll('.student-chk').forEach(function(chk) { chk.checked = state; });
    document.getElementById('select-all-chk').checked = state;
}
function syncSelectAll() {
    var all = document.querySelectorAll('.student-chk');
    var checked = document.querySelectorAll('.student-chk:checked');
    document.getElementById('select-all-chk').checked = all.length > 0 && all.length === checked.length;
}
document.getElementById('select-all-chk').addEventListener('change', function() {
    selectAll(this.checked);
});

function applyDateFilter() {
    var date = document.getElementById('print_filter_date').value;
    var url  = new URL(window.location.href);
    url.searchParams.set('print_filter_date', date);
    window.location.href = url.toString();
}
</script>
