@extends('layouts.master')

@section('css')
    <style>
        .exd-card { border-radius: 6px; padding: 15px; color: #fff; margin-bottom: 15px; }
        .exd-card h2 { margin: 0; font-size: 28px; font-weight: bold; }
        .exd-card span { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
        .exd-total { background: #337ab7; }
        .exd-complete { background: #5cb85c; }
        .exd-partial { background: #f0ad4e; }
        .exd-pending { background: #d9534f; }
        .exd-publish { background: #6f42c1; }
        .exd-progress-lg { height: 26px; margin-bottom: 5px; }
        .exd-progress-lg .progress-bar { line-height: 26px; font-size: 13px; }
        .exd-table .progress { margin-bottom: 0; height: 16px; }
        .exd-table .progress-bar { font-size: 11px; line-height: 16px; }
        .exd-section { background: #fff; border: 1px solid #e5e5e5; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        .exd-section h4 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 8px; }
        .exd-overdue { border-left: 4px solid #d9534f; }
        .label-lg { font-size: 12px; padding: 4px 8px; }
        .exd-muted { color: #999; font-size: 11px; }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        Exam Dashboard
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Mark Entry Progress
                        </small>
                    </h1>
                </div>

                @include('includes.flash_messages')

                {{-- Filters --}}
                <div class="exd-section">
                    <form method="GET" action="{{ route('exam.dashboard') }}" class="form-inline">
                        <div class="row">
                            <div class="col-md-2 col-sm-4">
                                <label class="exd-muted">Year</label>
                                <select name="years_id" class="form-control" style="width:100%">
                                    <option value="">All Years</option>
                                    @foreach($data['years'] as $year)
                                        <option value="{{ $year->id }}" {{ $data['filter']['years_id'] == $year->id ? 'selected' : '' }}>{{ $year->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="exd-muted">Month</label>
                                <select name="months_id" class="form-control" style="width:100%">
                                    <option value="">All Months</option>
                                    @foreach($data['months'] as $month)
                                        <option value="{{ $month->id }}" {{ $data['filter']['months_id'] == $month->id ? 'selected' : '' }}>{{ $month->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="exd-muted">Exam</label>
                                <select name="exams_id" class="form-control" style="width:100%">
                                    <option value="">All Exams</option>
                                    @foreach($data['exams'] as $exam)
                                        <option value="{{ $exam->id }}" {{ $data['filter']['exams_id'] == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="exd-muted">Department</label>
                                <select name="faculty_id" class="form-control" style="width:100%">
                                    <option value="">All Departments</option>
                                    @foreach($data['faculties'] as $faculty)
                                        <option value="{{ $faculty->id }}" {{ $data['filter']['faculty_id'] == $faculty->id ? 'selected' : '' }}>{{ $faculty->faculty }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="exd-muted">Semester/Class</label>
                                <select name="semesters_id" class="form-control" style="width:100%">
                                    <option value="">All</option>
                                    @foreach($data['semesters'] as $semester)
                                        <option value="{{ $semester->id }}" {{ $data['filter']['semesters_id'] == $semester->id ? 'selected' : '' }}>{{ $semester->semester }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="exd-muted">&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                                <a href="{{ route('exam.dashboard') }}" class="btn btn-default btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Summary Cards --}}
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="exd-card exd-total"><h2>{{ $data['summary']['total'] }}</h2><span>Scheduled Subjects</span></div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="exd-card exd-complete"><h2>{{ $data['summary']['complete'] }}</h2><span>Entry Complete</span></div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="exd-card exd-partial"><h2>{{ $data['summary']['partial'] }}</h2><span>Partial Entry</span></div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="exd-card exd-pending"><h2>{{ $data['summary']['pending'] }}</h2><span>Entry Pending</span></div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="exd-card exd-publish"><h2>{{ $data['summary']['published'] }} / {{ $data['summary']['total'] }}</h2><span>Result Published</span></div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="exd-card" style="background:#20c997;"><h2>{{ $data['summary']['overall_percent'] }}%</h2><span>Overall Progress</span></div>
                    </div>
                </div>

                {{-- Overall progress bar --}}
                <div class="exd-section">
                    <h4><i class="fa fa-tasks"></i> Overall Mark Entry Progress
                        <small class="pull-right">{{ $data['summary']['done_entries'] }} / {{ $data['summary']['expected_entries'] }} entries done</small>
                    </h4>
                    <div class="progress exd-progress-lg">
                        <div class="progress-bar progress-bar-success" style="width: {{ $data['summary']['overall_percent'] }}%">
                            {{ $data['summary']['overall_percent'] }}%
                        </div>
                    </div>
                </div>

                {{-- Overdue Alerts --}}
                @if(count($data['overdue']) > 0)
                    <div class="exd-section exd-overdue">
                        <h4 style="color:#d9534f;"><i class="fa fa-exclamation-triangle"></i> Overdue — Exam date passed but no entry started ({{ count($data['overdue']) }})</h4>
                        <div class="table-responsive">
                            <table class="table table-condensed table-striped" style="margin-bottom:0;">
                                <thead>
                                <tr><th>Subject</th><th>Department</th><th>Semester/Class</th><th>Exam</th><th>Exam Date</th><th>Expected Students</th></tr>
                                </thead>
                                <tbody>
                                @foreach($data['overdue'] as $od)
                                    <tr>
                                        <td><b>{{ $od['schedule']->subject_title }}</b> <span class="exd-muted">{{ $od['schedule']->subject_code }}</span></td>
                                        <td>{{ $od['schedule']->faculty_title }}</td>
                                        <td>{{ $od['schedule']->semester_title }}</td>
                                        <td>{{ $od['schedule']->exam_title }} ({{ $od['schedule']->month_title }} {{ $od['schedule']->year_title }})</td>
                                        <td>{{ \Carbon\Carbon::parse($od['schedule']->date)->format('d M Y') }}</td>
                                        <td>{{ $od['expected'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Result Publish Control --}}
                @ability('super-admin', 'exam-result-publish')
                <div class="exd-section">
                    <h4><i class="fa fa-bullhorn"></i> Result Publish Control
                        <small>students see results (and guardians get SMS) only after publish</small>
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped" style="margin-bottom:0;">
                            <thead>
                            <tr>
                                <th>Exam</th><th>Department</th><th>Semester/Class</th>
                                <th class="text-center">Subjects</th>
                                <th class="text-center">Entry Complete</th>
                                <th class="text-center">Publish Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data['publishGroups'] as $g)
                                @php
                                    $routeParams = [$g['years_id'], $g['months_id'], $g['exams_id'], $g['faculty_id'], $g['semesters_id']];
                                    $incomplete = $g['subjects'] - $g['complete'];
                                @endphp
                                <tr>
                                    <td>{{ $g['exam_title'] }} <span class="exd-muted">({{ $g['month_title'] }} {{ $g['year_title'] }})</span></td>
                                    <td>{{ $g['faculty_title'] }}</td>
                                    <td>{{ $g['semester_title'] }}</td>
                                    <td class="text-center">{{ $g['subjects'] }}</td>
                                    <td class="text-center">
                                        <span class="label {{ $incomplete == 0 ? 'label-success' : 'label-warning' }}">{{ $g['complete'] }} / {{ $g['subjects'] }}</span>
                                        @if($g['remaining_entries'] > 0)
                                            <br><span class="exd-muted">{{ $g['remaining_entries'] }} entries left</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($g['publish_state'] == 'all')
                                            <span class="label label-success label-lg">Published</span>
                                        @elseif($g['publish_state'] == 'mixed')
                                            <span class="label label-warning label-lg">Partially Published</span>
                                        @else
                                            <span class="label label-default label-lg">Not Published</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        @if($g['publish_state'] != 'all')
                                            <a href="{{ route('exam.schedule.result-publish', $routeParams) }}"
                                               class="btn btn-success btn-xs exd-publish-btn"
                                               data-incomplete="{{ $incomplete }}" data-total="{{ $g['subjects'] }}"
                                               data-remaining="{{ $g['remaining_entries'] }}">
                                                <i class="fa fa-check"></i> Publish
                                            </a>
                                        @endif
                                        @if($g['publish_state'] != 'none')
                                            <a href="{{ route('exam.schedule.result-un-publish', $routeParams) }}"
                                               class="btn btn-danger btn-xs exd-unpublish-btn">
                                                <i class="fa fa-times"></i> Unpublish
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">No exam groups found</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endability

                {{-- Subject-wise progress table --}}
                <div class="exd-section">
                    <h4><i class="fa fa-book"></i> Subject-wise Mark Entry Status</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover exd-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Department</th>
                                <th>Semester/Class</th>
                                <th>Exam</th>
                                <th>Exam Date</th>
                                <th class="text-center">Students</th>
                                <th class="text-center" style="min-width:110px;">Entry (T / M / P)</th>
                                <th class="text-center">Remaining<br><span class="exd-muted" style="font-weight:normal;">entries</span></th>
                                <th class="text-center">Absent</th>
                                <th style="min-width:120px;">Progress</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Publish</th>
                                <th>Last Entry</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data['rows'] as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><b>{{ $row['schedule']->subject_title }}</b><br><span class="exd-muted">{{ $row['schedule']->subject_code }}</span></td>
                                    <td>{{ $row['schedule']->faculty_title }}</td>
                                    <td>{{ $row['schedule']->semester_title }}</td>
                                    <td>{{ $row['schedule']->exam_title }}<br><span class="exd-muted">{{ $row['schedule']->month_title }} {{ $row['schedule']->year_title }}</span></td>
                                    <td>
                                        {{ $row['schedule']->date ? \Carbon\Carbon::parse($row['schedule']->date)->format('d M Y') : '-' }}
                                        @if($row['is_overdue'])
                                            <br><span class="label label-danger">Overdue</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $row['expected'] }}</td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        @foreach($row['components'] as $comp)
                                            <span class="label {{ $comp['done'] ? 'label-success' : ($comp['entered'] > 0 ? 'label-warning' : 'label-danger') }}"
                                                  title="{{ $comp['label'] }}: {{ $comp['entered'] }} of {{ $comp['expected'] }} entered">
                                                {{ $comp['short'] }} {{ $comp['entered'] }}/{{ $comp['expected'] }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="text-center">{{ $row['remaining'] }}</td>
                                    <td class="text-center">{{ $row['absent'] }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $row['status'] == 'complete' ? 'progress-bar-success' : ($row['status'] == 'partial' ? 'progress-bar-warning' : 'progress-bar-danger') }}"
                                                 style="width: {{ max($row['percent'], 5) }}%">{{ $row['percent'] }}%</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($row['status'] == 'complete')
                                            <span class="label label-success label-lg">Complete</span>
                                        @elseif($row['status'] == 'partial')
                                            <span class="label label-warning label-lg">Partial</span>
                                        @else
                                            <span class="label label-danger label-lg">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row['schedule']->publish_status == 1)
                                            <span class="label label-success label-lg">Published</span>
                                        @else
                                            <span class="label label-default label-lg">Not Published</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['last_entry_at'])
                                            {{ $row['last_entry_by'] ?: '-' }}<br>
                                            <span class="exd-muted">{{ \Carbon\Carbon::parse($row['last_entry_at'])->format('d M Y h:i A') }}</span>
                                        @else
                                            <span class="exd-muted">No entry yet</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="14" class="text-center">No exam schedule found for selected filters. Schedule an exam first.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    {{-- Department-wise summary --}}
                    <div class="col-md-6">
                        <div class="exd-section">
                            <h4><i class="fa fa-university"></i> Department-wise Progress</h4>
                            <table class="table table-condensed table-striped exd-table">
                                <thead><tr><th>Department</th><th class="text-center">Subjects</th><th class="text-center">Complete</th><th style="min-width:110px;">Progress</th></tr></thead>
                                <tbody>
                                @forelse($data['deptSummary'] as $dept)
                                    @php
                                        $dp = $dept['expected'] > 0 ? round(($dept['entered'] / $dept['expected']) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $dept['title'] }}</td>
                                        <td class="text-center">{{ $dept['total'] }}</td>
                                        <td class="text-center">{{ $dept['complete'] }} / {{ $dept['total'] }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar {{ $dp >= 100 ? 'progress-bar-success' : ($dp > 0 ? 'progress-bar-warning' : 'progress-bar-danger') }}"
                                                     style="width: {{ max($dp, 5) }}%">{{ $dp }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No data</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Teacher-wise summary --}}
                    <div class="col-md-6">
                        <div class="exd-section">
                            <h4><i class="fa fa-users"></i> Teacher/User-wise Entry Summary</h4>
                            <table class="table table-condensed table-striped">
                                <thead><tr><th>Entered By</th><th class="text-center">Subjects</th><th class="text-center">Total Entries</th><th>Last Entry</th></tr></thead>
                                <tbody>
                                @forelse($data['teacherSummary'] as $t)
                                    <tr>
                                        <td>{{ $t->name ?: 'Unknown' }}</td>
                                        <td class="text-center">{{ $t->subjects }}</td>
                                        <td class="text-center">{{ $t->entries }}</td>
                                        <td><span class="exd-muted">{{ $t->last_entry ? \Carbon\Carbon::parse($t->last_entry)->format('d M Y h:i A') : '-' }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No entries yet</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function () {
            /* Safety confirm before publish */
            document.querySelectorAll('.exd-publish-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    var incomplete = parseInt(btn.getAttribute('data-incomplete') || '0', 10);
                    var total = parseInt(btn.getAttribute('data-total') || '0', 10);
                    var remaining = parseInt(btn.getAttribute('data-remaining') || '0', 10);
                    var msg;
                    if (incomplete > 0) {
                        msg = 'WARNING: ' + incomplete + ' of ' + total + ' subjects have INCOMPLETE mark entry (' + remaining + ' entries left).\n\n'
                            + 'Students will see incomplete/wrong results and guardians will receive result SMS.\n\nPublish anyway?';
                    } else {
                        msg = 'All ' + total + ' subjects complete. Publish result?\n(Guardians will receive result SMS.)';
                    }
                    if (!confirm(msg)) e.preventDefault();
                });
            });
            /* Confirm before unpublish */
            document.querySelectorAll('.exd-unpublish-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    if (!confirm('Unpublish this result? Students will no longer see it.')) e.preventDefault();
                });
            });
        })();
    </script>
@endsection
