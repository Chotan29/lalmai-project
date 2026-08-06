@extends('layouts.master')

@section('css')
    <style>
        .adm-wrap { font-size: 13px; }
        .adm-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:16px; }
        .adm-head h2 { margin:0; font-size:22px; font-weight:600; color:#1f2e44; }
        .adm-head .sub { font-size:12.5px; color:#6c757d; margin-top:3px; }

        .adm-cards { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
        .adm-card {
            flex:1 1 150px; background:#fff; border:1px solid #e6ebf2; border-left:4px solid #1f5aa6;
            border-radius:8px; padding:14px 16px; min-width:150px;
        }
        .adm-card .lb { font-size:11.5px; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; }
        .adm-card .vl { font-size:26px; font-weight:700; color:#1f2e44; line-height:1.25; }
        .adm-card .ex { font-size:11.5px; color:#8a94a3; }
        .adm-card.ok { border-left-color:#2e9f6f; }
        .adm-card.warn { border-left-color:#c98a1c; }
        .adm-card.dang { border-left-color:#ce4b51; }
        .adm-card.info { border-left-color:#16a3b7; }

        .adm-box { background:#fff; border:1px solid #e6ebf2; border-radius:8px; margin-bottom:18px; }
        .adm-box > .hd {
            padding:12px 16px; border-bottom:1px solid #eef1f5; font-weight:600; color:#1f2e44;
            display:flex; align-items:center; justify-content:space-between; gap:10px;
        }
        .adm-box > .hd a { font-size:12px; font-weight:normal; }
        .adm-box > .bd { padding:14px 16px; }
        .adm-box table { width:100%; margin:0; }
        .adm-box table th { background:#f6f8fb; color:#41506a; font-size:12px; border-bottom:1px solid #e6ebf2 !important; padding:8px 10px; }
        .adm-box table td { padding:8px 10px; border-top:1px solid #f0f3f7; vertical-align:middle; }
        .adm-badge { padding:2px 9px; border-radius:10px; font-size:11px; font-weight:600; display:inline-block; }
        .b-green { background:#e8f6ed; color:#1c7a43; }
        .b-red { background:#fdecea; color:#a3271f; }
        .b-blue { background:#e9f1fb; color:#1a4f8a; }
        .b-gray { background:#f0f2f5; color:#5c6470; }
        .b-amber { background:#fdf3e2; color:#8a6100; }

        .adm-links { display:flex; flex-wrap:wrap; gap:10px; }
        .adm-links a {
            flex:1 1 200px; display:flex; align-items:center; gap:10px; text-decoration:none;
            border:1px solid #e0e6ef; border-radius:8px; padding:12px 14px; background:#fff; color:#1f2e44;
            transition:border-color .15s ease, box-shadow .15s ease;
        }
        .adm-links a:hover { border-color:#b9cdea; box-shadow:0 4px 12px rgba(31,90,166,.10); color:#1f5aa6; }
        .adm-links a i { font-size:17px; color:#1f5aa6; width:22px; text-align:center; }
        .adm-links a small { display:block; color:#8a94a3; font-size:11.5px; }

        .adm-alert { border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13px; }
        .adm-alert.warn { background:#fff8e5; border:1px solid #f0d9a0; color:#7a5800; }
        .adm-alert.dang { background:#fdecea; border:1px solid #f3c2bd; color:#8f2018; }

        .adm-bar { display:flex; align-items:flex-end; gap:4px; height:70px; }
        .adm-bar div { flex:1; background:#cfe0f5; border-radius:3px 3px 0 0; position:relative; min-height:2px; }
        .adm-bar div span { position:absolute; top:-16px; left:0; right:0; text-align:center; font-size:10px; color:#6c757d; }
        .adm-bar-x { display:flex; gap:4px; margin-top:4px; }
        .adm-bar-x div { flex:1; text-align:center; font-size:9.5px; color:#98a1ad; }
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')

            <div class="adm-wrap">
                @include('includes.flash_messages')

                @php
                    $s = $data['stats'];
                    $batchTitle = $data['selected_batch'] && isset($data['batches'][$data['selected_batch']])
                        ? $data['batches'][$data['selected_batch']] : 'All sessions';
                    $maxTrend = 1;
                    foreach ($data['trend'] as $t) { $maxTrend = max($maxTrend, (int) $t->c); }
                @endphp

                <div class="adm-head">
                    <div>
                        <h2><i class="fa fa-graduation-cap" style="color:#1f5aa6;"></i> Admission Dashboard</h2>
                        <div class="sub">Session: <b>{{ $batchTitle }}</b> &middot; everything about admission in one place</div>
                    </div>
                    <form method="GET" action="{{ route('admission-dashboard') }}" class="form-inline">
                        <select name="batch" class="form-control input-sm" onchange="this.form.submit()">
                            <option value="">All sessions</option>
                            @foreach($data['batches'] as $id => $title)
                                <option value="{{ $id }}" {{ (string) $data['selected_batch'] === (string) $id ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Things that need attention --}}
                @if($data['pending_payments'] > 0)
                    <div class="adm-alert dang">
                        <i class="fa fa-exclamation-circle"></i>
                        <b>{{ $data['pending_payments'] }} paid application(s) were never completed.</b>
                        These students paid but no student record was created.
                        <a href="{{ route('registration-payment-recovery') }}">Open Payment Recovery &rarr;</a>
                    </div>
                @endif

                @if(count($data['fee_missing']) > 0)
                    <div class="adm-alert warn">
                        <i class="fa fa-exclamation-triangle"></i>
                        <b>No admission fee is set for:</b> {{ implode(', ', $data['fee_missing']->toArray()) }}.
                        Students of these departments cannot pay.
                        <a href="{{ route('setting.online-registration') }}">Set fee &rarr;</a>
                    </div>
                @endif

                {{-- Headline numbers --}}
                <div class="adm-cards">
                    <div class="adm-card">
                        <div class="lb">Total Admitted</div>
                        <div class="vl">{{ number_format($s['total']) }}</div>
                        <div class="ex">{{ $batchTitle }}</div>
                    </div>
                    <div class="adm-card ok">
                        <div class="lb">Active</div>
                        <div class="vl">{{ number_format($s['active']) }}</div>
                        <div class="ex">verified by office</div>
                    </div>
                    <div class="adm-card warn">
                        <div class="lb">Waiting Activation</div>
                        <div class="vl">{{ number_format($s['inactive']) }}</div>
                        <div class="ex">{{ $data['inactive_logins'] }} logins locked</div>
                    </div>
                    <div class="adm-card info">
                        <div class="lb">New / Old</div>
                        <div class="vl">{{ number_format($s['new']) }} <small style="font-size:15px;color:#8a94a3;">/ {{ number_format($s['old']) }}</small></div>
                        <div class="ex">new admission / returning</div>
                    </div>
                    <div class="adm-card">
                        <div class="lb">Today</div>
                        <div class="vl">{{ number_format($s['today']) }}</div>
                        <div class="ex">{{ number_format($s['week']) }} in last 7 days</div>
                    </div>
                    <div class="adm-card ok">
                        <div class="lb">Fee Collected</div>
                        <div class="vl">&#2547;{{ number_format($data['payment']['total'], 0) }}</div>
                        <div class="ex">{{ $data['payment']['count'] }} online payment(s)</div>
                    </div>
                    <a href="#stuck-payments" class="adm-card dang" style="text-decoration:none; display:block;">
                        <div class="lb">Stuck Payments</div>
                        <div class="vl">{{ number_format($data['pending_payments']) }}</div>
                        <div class="ex">paid, not completed &mdash; see list &darr;</div>
                    </a>
                </div>

                {{-- Quick actions --}}
                <div class="adm-box">
                    <div class="hd">Quick Actions</div>
                    <div class="bd">
                        <div class="adm-links">
                            <a href="{{ route('student.registration') }}"><i class="fa fa-user-plus"></i>
                                <span>Add Student<small>Register a student manually</small></span></a>
                            <a href="{{ route('student') }}"><i class="fa fa-users"></i>
                                <span>All Students<small>Search, edit, activate, print ID card</small></span></a>
                            <a href="{{ route('registration-payment-recovery') }}"><i class="fa fa-medkit"></i>
                                <span>Payment Recovery<small>Finish a paid but stuck registration</small></span></a>
                            <a href="{{ route('setting.online-registration') }}"><i class="fa fa-cogs"></i>
                                <span>Registration Setting<small>Window, departments and fees</small></span></a>
                            <a href="{{ route('account.fees.online-payment') }}"><i class="fa fa-credit-card"></i>
                                <span>Online Payments<small>Verify collected fees</small></span></a>
                            <a href="{{ route('online-registration.registration') }}" target="_blank"><i class="fa fa-external-link"></i>
                                <span>Public Form<small>Open the student application form</small></span></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Department-wise --}}
                    <div class="col-md-7">
                        <div class="adm-box">
                            <div class="hd">
                                Department-wise Admission
                                <a href="{{ route('setting.online-registration') }}">Manage fees</a>
                            </div>
                            <div class="bd" style="padding:0;">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th style="text-align:center;">Total</th>
                                        <th style="text-align:center;">New</th>
                                        <th style="text-align:center;">Old</th>
                                        <th style="text-align:right;">Fee (New)</th>
                                        <th style="text-align:right;">Expected</th>
                                        <th style="text-align:right;">Collected</th>
                                        <th style="text-align:right;">Due</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($data['departments'] as $d)
                                        <tr>
                                            <td>
                                                {{ $d->faculty_name ?: 'Unassigned' }}
                                                @if($d->program_open)<span class="adm-badge b-green">open</span>@endif
                                            </td>
                                            <td style="text-align:center;"><b>{{ $d->total }}</b></td>
                                            <td style="text-align:center;">{{ $d->new_count }}</td>
                                            <td style="text-align:center;">{{ $d->old_count }}</td>
                                            <td style="text-align:right;">
                                                @if($d->new_fee)&#2547;{{ number_format($d->new_fee, 0) }}
                                                @else<span class="adm-badge b-amber">not set</span>@endif
                                            </td>
                                            <td style="text-align:right;">&#2547;{{ number_format($d->expected, 0) }}</td>
                                            {{-- What the students actually paid, and what is still owed. --}}
                                            <td style="text-align:right;"><b>&#2547;{{ number_format($d->collected, 0) }}</b></td>
                                            <td style="text-align:right;">
                                                @if($d->due > 0)
                                                    <span class="adm-badge b-amber">&#2547;{{ number_format($d->due, 0) }}</span>
                                                @else
                                                    <span style="color:#8a94a3;">&#2547;0</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" style="text-align:center; color:#8a94a3;">No admission data for this session.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Trend + payment summary --}}
                    <div class="col-md-5">
                        <div class="adm-box">
                            <div class="hd">Applications &mdash; last 14 days</div>
                            <div class="bd">
                                @if(count($data['trend']) > 0)
                                    <div class="adm-bar">
                                        @foreach($data['trend'] as $t)
                                            <div style="height: {{ max(4, (int) round(($t->c / $maxTrend) * 100)) }}%;" title="{{ $t->d }}: {{ $t->c }}">
                                                <span>{{ $t->c }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="adm-bar-x">
                                        @foreach($data['trend'] as $t)
                                            <div>{{ \Carbon\Carbon::parse($t->d)->format('d/m') }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="color:#8a94a3;">No applications in the last 14 days.</div>
                                @endif
                            </div>
                        </div>

                        <div class="adm-box">
                            <div class="hd">
                                Payment Summary
                                <a href="{{ route('account.fees.online-payment') }}">View all</a>
                            </div>
                            <div class="bd">
                                <table class="table" style="margin:0;">
                                    <tr><td>Total collected</td><td style="text-align:right;"><b>&#2547;{{ number_format($data['payment']['total'], 2) }}</b></td></tr>
                                    <tr><td>Payments received</td><td style="text-align:right;">{{ $data['payment']['count'] }}</td></tr>
                                    <tr><td>Verified</td><td style="text-align:right;"><span class="adm-badge b-green">{{ $data['payment']['verified'] }}</span></td></tr>
                                    <tr><td>Waiting verification</td><td style="text-align:right;"><span class="adm-badge b-amber">{{ $data['payment']['unverified'] }}</span></td></tr>
                                    <tr><td><a href="#stuck-payments" style="color:#1f2e44;">Paid but not completed</a></td><td style="text-align:right;"><a href="#stuck-payments"><span class="adm-badge b-red">{{ $data['pending_payments'] }}</span></a></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Recent applications --}}
                    <div class="col-md-7">
                        <div class="adm-box">
                            <div class="hd">Recent Applications <a href="{{ route('student') }}">See all</a></div>
                            <div class="bd" style="padding:0;">
                                <table class="table">
                                    <thead>
                                    <tr><th>Reg. No</th><th>Name</th><th>Department</th><th style="text-align:center;">Type</th><th style="text-align:center;">Status</th></tr>
                                    </thead>
                                    <tbody>
                                    @forelse($data['recent'] as $r)
                                        <tr>
                                            <td style="font-family:monospace; font-size:11.5px;">{{ $r->reg_no }}</td>
                                            <td>
                                                {{ trim($r->first_name . ' ' . $r->last_name) }}
                                                <div style="font-size:11px;color:#98a1ad;">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y, h:i A') }}</div>
                                            </td>
                                            <td>{{ $r->faculty_name ?: '-' }}</td>
                                            <td style="text-align:center;">
                                                <span class="adm-badge {{ $r->student_type === 'old' ? 'b-gray' : 'b-blue' }}">{{ ucfirst($r->student_type ?: 'new') }}</span>
                                            </td>
                                            <td style="text-align:center;">
                                                <span class="adm-badge {{ $r->status ? 'b-green' : 'b-amber' }}">{{ $r->status ? 'Active' : 'Pending' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" style="text-align:center; color:#8a94a3;">No applications yet.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Waiting for activation + stuck payments --}}
                    <div class="col-md-5">
                        <div class="adm-box">
                            <div class="hd">Waiting for Activation <a href="{{ route('student') }}">Activate</a></div>
                            <div class="bd" style="padding:0;">
                                <table class="table">
                                    <tbody>
                                    @forelse($data['waiting_activation'] as $w)
                                        <tr>
                                            <td>
                                                <b>{{ trim($w->first_name . ' ' . $w->last_name) }}</b>
                                                <div style="font-size:11px;color:#98a1ad;">{{ $w->reg_no }} &middot; {{ $w->faculty_name ?: '-' }}</div>
                                            </td>
                                            <td style="text-align:right; font-size:11.5px; color:#8a94a3;">
                                                {{ \Carbon\Carbon::parse($w->created_at)->format('d M') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td style="text-align:center; color:#1c7a43;">Everyone is activated.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="adm-box">
                            <div class="hd">Locked Student Logins</div>
                            <div class="bd">
                                <div style="font-size:26px; font-weight:700; color:#1f2e44;">{{ number_format($data['inactive_logins']) }}</div>
                                <div style="font-size:12px; color:#8a94a3;">
                                    Students who cannot log in until the office activates them.
                                    Setting a password from the student profile activates the login automatically.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Full list of paid-but-unfinished applications --}}
                <div class="adm-box" id="stuck-payments">
                    <div class="hd">
                        Paid but Not Completed &mdash; {{ count($data['recent_pending']) }} application(s)
                        <a href="{{ route('registration-payment-recovery') }}">Open Payment Recovery &rarr;</a>
                    </div>
                    <div class="bd" style="padding:0;">
                        @if(count($data['recent_pending']) > 0)
                            <div style="padding:10px 16px; font-size:12px; color:#8a6100; background:#fff8e5; border-bottom:1px solid #f0d9a0;">
                                These students paid at SSLCommerz but no student record was created.
                                Take the transaction ID from SSLCommerz and complete each one from Payment Recovery.
                            </div>
                            <div style="max-height:420px; overflow-y:auto;">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Student</th>
                                        <th>Reference</th>
                                        <th style="text-align:center; width:80px;">Type</th>
                                        <th style="text-align:right; width:90px;">Amount</th>
                                        <th style="width:130px;">Started</th>
                                        <th style="width:90px;"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data['recent_pending'] as $i => $p)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <b>{{ $p->name }}</b>
                                                <div style="font-size:11px;color:#98a1ad;">
                                                    {{ $p->email }}{{ $p->mobile ? ' · '.$p->mobile : '' }}
                                                </div>
                                            </td>
                                            <td style="font-family:monospace; font-size:10.5px; color:#6c757d;">{{ $p->ref }}</td>
                                            <td style="text-align:center;">
                                                <span class="adm-badge {{ $p->type === 'old' ? 'b-gray' : 'b-blue' }}">{{ ucfirst($p->type ?: 'new') }}</span>
                                            </td>
                                            <td style="text-align:right;">&#2547;{{ number_format((float) $p->amount, 0) }}</td>
                                            <td style="font-size:11.5px; color:#8a94a3;">{{ \Carbon\Carbon::parse($p->at)->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <a href="{{ route('registration-payment-recovery') }}" class="btn btn-xs btn-success">Recover</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div style="padding:18px; text-align:center; color:#1c7a43;">
                                <i class="fa fa-check-circle"></i> No stuck payment. Every paid application was completed.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
