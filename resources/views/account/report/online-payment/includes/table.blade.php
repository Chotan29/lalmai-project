@php
    /* Worked out here from the rows already loaded, so the screen can lead with the three
       figures the office looks for without another trip to the database. */
    $rows        = isset($data['student']) ? $data['student'] : collect();
    $totalAmount = $rows->sum('amount');
    $verified    = $rows->filter(function ($r) { return (int) $r->payment_status === 1; });
    $pending     = $rows->filter(function ($r) { return (int) $r->payment_status !== 1; });
@endphp

{{-- Styling lives in assets/css/paper.css with the rest of the print design, not inline.
     report-sheet is the design every report wears; op-sheet adds what only this one needs. --}}
<div class="report-sheet op-sheet">

    <div class="op-title">
        <h2>Online Fee Payment Report</h2>

        {{-- The department the sheet covers, said once and said large. On paper this is the
             first thing anyone needs to know, and the filter boxes do not print. --}}
        <div class="op-dept-name">{{ $data['op_department'] ?? 'All Departments' }}</div>

        @if(!empty($data['op_meta']))
            <div class="op-meta">
                @foreach($data['op_meta'] as $meta)
                    <span class="op-meta-item">
                        <span class="op-meta-l">{{ $meta['label'] }}</span>
                        {{-- Escaped: the gateway and the dates in here come from the query string. --}}
                        <span class="op-meta-v">{{ $meta['value'] }}</span>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="op-summary">
        <div class="op-card">
            <span class="op-card-h">Payments</span>
            <span class="op-card-v">{{ number_format($rows->count()) }}</span>
        </div>
        <div class="op-card op-card-ok">
            <span class="op-card-h">Verified</span>
            <span class="op-card-v">&#2547;{{ number_format($verified->sum('amount'), 2) }}</span>
            <span class="op-card-n">{{ $verified->count() }} payment(s)</span>
        </div>
        <div class="op-card op-card-wait">
            <span class="op-card-h">Not Verified</span>
            <span class="op-card-v">&#2547;{{ number_format($pending->sum('amount'), 2) }}</span>
            <span class="op-card-n">{{ $pending->count() }} payment(s)</span>
        </div>
        <div class="op-card op-card-grand">
            <span class="op-card-h">Total</span>
            <span class="op-card-v">&#2547;{{ number_format($totalAmount, 2) }}</span>
        </div>
    </div>

    <table class="op-report">
        {{-- Fixed widths so the columns land in the same place on screen and on paper. --}}
        <colgroup>
            <col style="width:5%">
            <col style="width:28%">
            <col style="width:20%">
            <col style="width:11%">
            <col style="width:11%">
            <col style="width:11%">
            <col style="width:14%">
        </colgroup>
        <thead>
            <tr>
                <th class="op-sn">S.N.</th>
                <th class="op-stu">Student</th>
                <th class="op-dept">{{__('form_fields.student.fields.faculty')}}</th>
                <th class="op-date">Date</th>
                <th class="op-gw">Gateway</th>
                <th class="op-status">{{ __('common.status')}}</th>
                <th class="op-amt">Amount</th>
            </tr>
        </thead>
        <tbody>
        @if ($rows->count() > 0)
            @php($i = 1)
            @foreach($rows as $student)
                <tr>
                    <td class="op-sn">{{ $i }}</td>
                    <td class="op-stu">
                        {{-- Name and roll on one line each: a reg no on its own tells nobody
                             who paid, and a name on its own is not unique. --}}
                        <span class="op-name">{{ trim($student->first_name.' '.$student->middle_name.' '.$student->last_name) }}</span>
                        <span class="op-reg">{{ $student->reg_no }}</span>
                    </td>
                    <td class="op-dept">
                        {{-- Read from the lists the controller built. The helper behind the old
                             call runs a find() per row, which is two queries a payment. --}}
                        <span class="op-name">{{ $data['faculty_titles'][$student->faculty] ?? ViewHelper::getFacultyTitle($student->faculty) }}</span>
                        <span class="op-reg">{{ $data['semester_titles'][$student->semester] ?? ViewHelper::getSemesterTitle($student->semester) }}</span>
                    </td>
                    <td class="op-date">{{ \Carbon\Carbon::parse($student->date)->format('d M Y') }}</td>
                    <td class="op-gw">{{ $student->payment_gateway ?: '&mdash;' }}</td>
                    <td class="op-status">
                        @if((int) $student->payment_status === 1)
                            <span class="op-pill op-pill-ok">Verified</span>
                        @else
                            <span class="op-pill op-pill-wait">Not Verified</span>
                        @endif
                    </td>
                    <td class="op-amt">{{ number_format($student->amount, 2) }}</td>
                </tr>
                @php($i++)
            @endforeach
        @else
            <tr>
                <td colspan="7" class="op-empty">No {{ $panel }} data found. Please Filter {{ $panel }} to show.</td>
            </tr>
        @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="op-total-lbl">Grand Total</td>
                <td class="op-amt op-total-val">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Shown only on paper: a sheet that leaves the office should say what it is and when it
         was taken, or it cannot be trusted a month later. --}}
    <div class="op-print-foot">
        <span>Online Fee Payment Report</span>
        <span>Printed {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</span>
    </div>

    <div class="op-sign">
        <div class="op-sign-box">Prepared By</div>
        <div class="op-sign-box">Accounts Officer</div>
        <div class="op-sign-box">Principal</div>
    </div>
</div>
