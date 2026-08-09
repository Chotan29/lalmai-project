{{-- Everything the report prints sits inside one wrapper, so page margins, alignment and
     page breaks can be set for this report alone without disturbing the other print screens
     that share paper.css. --}}
<div class="fg-sheet">

    <div class="fg-title">
        <h2>{{ $data['fee_title'] ?? $data['print_head'] }}</h2>
        @if(!empty($data['fg_period']))
            <div class="fg-sub">Collection period&nbsp; {{ $data['fg_period'] }}</div>
        @endif
    </div>

    {{-- The three figures the office actually reads, before the detail. On paper they land at
         the top of page one, so the answer is visible without turning the sheet over. --}}
    <div class="fg-summary">
        <div class="fg-card">
            <span class="fg-card-h">College</span>
            <span class="fg-card-v">{{ number_format($data['college_total'] ?? 0, 2) }}</span>
        </div>
        <div class="fg-card">
            <span class="fg-card-h">Department</span>
            <span class="fg-card-v">{{ number_format($data['department_total'] ?? 0, 2) }}</span>
        </div>
        <div class="fg-card fg-card-grand">
            <span class="fg-card-h">Grand Total</span>
            <span class="fg-card-v">{{ number_format($data['fee_collection_total'] ?? 0, 2) }}</span>
        </div>
    </div>

    {{-- Whose money this is. The head table says how much landed in each head; this says how
         many students it came from, and from where. Both are needed to check the sheet: a head
         divided by its rate should land on the student count, and where it does not, the two
         count columns below usually say why. --}}
    @if(isset($data['fee_group_departments']) && $data['fee_group_departments']->count() > 0)
        @php
            $deptRows = $data['fee_group_departments'];
            $totStudents = $deptRows->sum('students');
            $totDeptStudents = $deptRows->sum('dept_students');
        @endphp

        <div class="fg-section-h">Students behind these figures</div>

        <table class="fee-group-report fg-dept-table">
            <colgroup>
                <col style="width:34%">
                <col style="width:11%">
                <col style="width:13%">
                <col style="width:14%">
                <col style="width:14%">
                <col style="width:14%">
            </colgroup>
            <thead>
                <tr>
                    <th class="fg-head">{{ __('form_fields.student.fields.faculty') }}</th>
                    <th class="fg-by">Students</th>
                    <th class="fg-by">Paid Dept. Part</th>
                    <th class="fg-amt">College</th>
                    <th class="fg-amt">Department</th>
                    <th class="fg-amt">Total</th>
                </tr>
            </thead>
            <tbody>
            @foreach($deptRows as $dept)
                <tr>
                    <td class="fg-head">{{ $dept->department }}</td>
                    <td class="fg-by fg-count">{{ number_format($dept->students) }}</td>
                    {{-- Flagged when fewer students paid the department part than paid at all:
                         that difference is money the departments have not received. --}}
                    <td class="fg-by fg-count {{ $dept->dept_students < $dept->students ? 'fg-short' : '' }}">
                        {{ number_format($dept->dept_students) }}
                    </td>
                    <td class="fg-amt">{{ number_format($dept->college_amount, 2) }}</td>
                    <td class="fg-amt {{ $dept->department_amount == 0 ? 'fg-zero' : '' }}">{{ number_format($dept->department_amount, 2) }}</td>
                    <td class="fg-amt">{{ number_format($dept->total_amount, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="fg-total-lbl">All Departments</td>
                    <td class="fg-by fg-count">{{ number_format($totStudents) }}</td>
                    <td class="fg-by fg-count">{{ number_format($totDeptStudents) }}</td>
                    <td class="fg-amt fg-total-val">{{ number_format($data['college_total'] ?? 0, 2) }}</td>
                    <td class="fg-amt fg-total-val">{{ number_format($data['department_total'] ?? 0, 2) }}</td>
                    <td class="fg-amt fg-total-val">{{ number_format($data['fee_collection_total'] ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        @if($totDeptStudents < $totStudents)
            <div class="fg-note">
                {{ number_format($totStudents - $totDeptStudents) }} student(s) paid the college
                part but not the department part.
            </div>
        @endif

        <div class="fg-section-h">Head by head</div>
    @endif

    <table class="fee-group-report">
        {{-- Fixed widths so the columns land in the same place on screen and on paper. --}}
        <colgroup>
            <col style="width:6%">
            <col style="width:42%">
            <col style="width:14%">
            <col style="width:18%">
            <col style="width:20%">
        </colgroup>
        <thead>
            <tr>
                <th class="fg-sn">S.N.</th>
                <th class="fg-head">Head</th>
                <th class="fg-by">Collected By</th>
                <th class="fg-fee">Fee Amount</th>
                <th class="fg-amt">Collected</th>
            </tr>
        </thead>
        <tbody>
        @if (isset($data['fee_group_rows']) && $data['fee_group_rows']->count() > 0)
            @php($i = 1)
            @php($shown = 'college')
            @foreach($data['fee_group_rows'] as $feeRow)
                {{-- The college heads end and the department heads begin: the subtotal goes in
                     between, because that boundary is where a short payment stops. --}}
                @if($shown == 'college' && $feeRow->collected_by == 'department')
                    <tr class="fg-subtotal">
                        <td colspan="4" class="fg-total-lbl">College Total</td>
                        <td class="fg-total-val fg-amt">{{ number_format($data['college_total'], 2) }}</td>
                    </tr>
                    @php($shown = 'department')
                @endif
                <tr>
                    <td class="fg-sn">{{ $i }}</td>
                    <td class="fg-head">{{ $feeRow->title }}</td>
                    <td class="fg-by">
                        <span class="fg-tag {{ $feeRow->collected_by == 'department' ? 'fg-tag-dept' : '' }}">{{ ucfirst($feeRow->collected_by) }}</span>
                    </td>
                    <td class="fg-fee">{{ number_format($feeRow->fee_amount, 2) }}</td>
                    {{-- A head that received nothing is greyed rather than removed: twenty-six
                         heads that quietly become twenty-three cannot be reconciled. --}}
                    <td class="fg-amt {{ $feeRow->amount == 0 ? 'fg-zero' : '' }}">{{ number_format($feeRow->amount, 2) }}</td>
                </tr>
                @php($i++)
            @endforeach

            @if($shown == 'department')
                <tr class="fg-subtotal">
                    <td colspan="4" class="fg-total-lbl">Department Total</td>
                    <td class="fg-total-val fg-amt">{{ number_format($data['department_total'], 2) }}</td>
                </tr>
            @endif
        @else
            <tr>
                <td colspan="5" class="fg-empty">No {{ $panel }} data found. Please Filter {{ $panel }} to show.</td>
            </tr>
        @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="fg-total-lbl">Grand Total</td>
                <td class="fg-total-val fg-amt">{{ number_format($data['fee_collection_total'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Shown only on paper: a printed sheet that leaves the office should say what it is and
         when it was taken, or it cannot be trusted a month later. --}}
    <div class="fg-print-foot">
        <span>{{ $data['fee_title'] ?? $data['print_head'] }}</span>
        <span>Printed {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</span>
    </div>

    <div class="fg-sign">
        <div class="fg-sign-box">Prepared By</div>
        <div class="fg-sign-box">Accounts Officer</div>
        <div class="fg-sign-box">Principal</div>
    </div>
</div>
