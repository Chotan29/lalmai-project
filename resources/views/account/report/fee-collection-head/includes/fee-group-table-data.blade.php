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
