{{-- The receive history as a printed table.

     Thirteen columns, not the screen's sixteen: Note, the user who entered it and the row's
     action button are working tools, not part of a report, and dropping them is what lets the
     rest stay readable on A4.

     Every row the filter matched is here - the paging belongs to the screen. The total row is
     counted in the database over that same filter, so it is the answer for the whole report and
     not for whatever happened to be on one page. --}}
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th style="width: 3%;">S.N.</th>
        <th style="width: 8%;">Reg. Num.</th>
        <th style="width: 15%;">Name of Student</th>
        <th style="width: 11%;">{{__('form_fields.student.fields.semester')}}</th>
        <th style="width: 12%;">Fee Head</th>
        <th style="width: 7%;">Date</th>
        <th style="width: 7%;" class="text-right">Amount</th>
        <th style="width: 5%;" class="text-right">Fine</th>
        <th style="width: 6%;" class="text-right">Discount</th>
        <th style="width: 8%;">Method</th>
        <th style="width: 8%;">Ref. No.</th>
        <th style="width: 8%;">Bank Ref.</th>
        <th style="width: 6%;">Status</th>
    </tr>
    </thead>
    <tbody>
    @if (isset($data['feesCollection']) && count($data['feesCollection']) > 0)
        @php($i = 1)
        @foreach($data['feesCollection'] as $feesCollection)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $feesCollection->reg_no }}</td>
                <td>{{ trim($feesCollection->first_name.' '.$feesCollection->middle_name.' '.$feesCollection->last_name) }}</td>
                <td>{{ ViewHelper::getSemesterTitle($feesCollection->semester) }}</td>
                <td>{{ ViewHelper::getFeeHeadById($feesCollection->fee_head) }}</td>
                <td>{{ \Carbon\Carbon::parse($feesCollection->date)->format('Y-m-d') }}</td>
                <td class="text-right">{{ number_format($feesCollection->paid_amount, 2) }}</td>
                <td class="text-right">{{ number_format($feesCollection->fine, 2) }}</td>
                <td class="text-right">{{ number_format($feesCollection->discount, 2) }}</td>
                <td>
                    {{ $feesCollection->payment_method }}
                    {{-- A bank payment nobody has verified yet is worth saying in print, where
                         the screen's coloured badge means nothing. --}}
                    @if($feesCollection->payment_method == 'Bank' && !isset($feesCollection->verified_at))
                        <small>(unverified)</small>
                    @endif
                </td>
                <td>{{ $feesCollection->ref_no }}</td>
                <td>{{ $feesCollection->external_ref_no }}</td>
                <td>{{ $feesCollection->fc_status == 1 ? 'Success' : 'Cancelled' }}</td>
            </tr>
            @php($i++)
        @endforeach
    @else
        <tr>
            <td colspan="13" class="text-center">No receipt matches this filter.</td>
        </tr>
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6" class="text-right"><strong>Total ({{ number_format($data['totals']['row_count']) }} receipts)</strong></td>
        <td class="text-right"><strong>{{ number_format($data['totals']['paid_amount'], 2) }}</strong></td>
        <td class="text-right"><strong>{{ number_format($data['totals']['fine'], 2) }}</strong></td>
        <td class="text-right"><strong>{{ number_format($data['totals']['discount'], 2) }}</strong></td>
        <td colspan="4"></td>
    </tr>
    </tfoot>
</table>
