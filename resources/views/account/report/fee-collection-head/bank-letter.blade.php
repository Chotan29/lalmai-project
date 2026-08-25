{{--
    One transfer letter, as a sheet.

    Laid out the way the college already writes it by hand: the addressing block, the subject with
    the number of accounts in it, the body, the table, the total in words, then the signature. The
    table is the only part that changes from one letter to the next.

    Rendered as html and handed to Excel rather than built cell by cell - the layout is a letter,
    not a grid, and describing it in markup is both shorter and easier to correct later. The look
    of it - bold labels, a bordered table, a real number format on the money column, column widths,
    a printable page - is finished afterwards in BankTransferLetterExport, which reads this same
    markup back and styles it by what the text says rather than by row number. That way the two
    cannot drift apart: whatever this file prints is what gets bolded and boxed.
--}}
@php
    $s = $letter->settings;
    $count = count($lines);
    $total = array_sum(array_map(function ($l) { return $l->amount; }, $lines));
    $today = \App\Support\BanglaNumber::digits(now()->format('d-m-Y'));
@endphp

<table>
    <tr><td colspan="4">{{ $s->college_name }}</td></tr>
    <tr><td colspan="4">{{ $s->college_address }}</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr><td colspan="2">সূত্র:</td><td colspan="2">তারিখ: {{ $today }}</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr><td colspan="4">প্রেরক:</td></tr>
    <tr><td colspan="4">{{ $s->from_designation }}</td></tr>
    <tr><td colspan="4">{{ $s->college_name }}</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr><td colspan="4">প্রাপক:</td></tr>
    <tr><td colspan="4">{{ $s->to_designation }}</td></tr>
    <tr><td colspan="4">{{ $s->bank_name }}</td></tr>
    <tr><td colspan="4">{{ $s->branch_name }}</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    {{-- The subject says how many accounts the letter covers. Counted from the table below rather
         than typed, because a letter that says twenty-one over a table of twenty-three is the kind
         of thing a bank sends back. --}}
    <tr>
        <td colspan="4">
            বিষয় : হিসাবের নাম- {{ $s->source_account_title }},
            হিসাব নম্বর- {{ $s->source_account_no }} হতে
            {{ \App\Support\BanglaNumber::digits(str_pad($count, 2, '0', STR_PAD_LEFT)) }} টি হিসাবে
            {{ $heading }} বাবদ টাকা স্থানান্তর প্রসঙ্গে।
        </td>
    </tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr><td colspan="4">জনাব,</td></tr>
    <tr><td colspan="4">{{ $s->body_text }}</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr>
        <th>ক্রম</th>
        <th>হিসাবের নাম</th>
        <th>হিসাব নম্বর</th>
        <th>টাকা</th>
    </tr>

    @foreach($lines as $i => $line)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $line->account_name }}</td>
            <td>{{ $line->account_no }}</td>
            <td>{{ round($line->amount) }}</td>
        </tr>
    @endforeach

    {{-- The college letter closes with the two department fees. They carry their money but no
         account number: that money is split between the departments and moved by the second
         letter, so there is no one account to write against either line. Listing them with what
         they hold is what lets the office check the letter covers the whole fee. --}}
    @foreach($tail as $t)
        <tr>
            <td>{{ count($lines) + $loop->iteration }}</td>
            <td>{{ $t->title }} (বিভাগীয় হিসাবে, পৃথক পত্রে)</td>
            <td></td>
            <td>{{ round($t->amount) }}</td>
        </tr>
    @endforeach

    {{-- Three totals rather than one, because there are three different numbers here and a letter
         that shows only one of them invites the bank to guess which. The first is what this letter
         actually asks to be moved; the last is the fee as a whole, so the two letters can be
         checked against the report without adding them up by hand. --}}
    <tr>
        <td></td>
        <td>{{ count($tail) ? 'সর্বমোট (এই পত্রে স্থানান্তরযোগ্য)' : 'সর্বমোট' }}</td>
        <td></td>
        <td>{{ round($total) }}</td>
    </tr>

    @if(count($tail))
        @php $tailTotal = array_sum(array_map(function ($t) { return $t->amount; }, $tail)); @endphp
        <tr>
            <td></td>
            <td>বিভাগীয় হিসাবে, পৃথক পত্রে</td>
            <td></td>
            <td>{{ round($tailTotal) }}</td>
        </tr>
        <tr>
            <td></td>
            <td>সম্পূর্ণ ফি</td>
            <td></td>
            <td>{{ round($total + $tailTotal) }}</td>
        </tr>
    @endif

    {{-- In words, the amount this letter moves - not the fee as a whole. The words are what the
         bank reads back when the figures are disputed. --}}
    <tr><td colspan="4">কথায় : {{ \App\Support\BanglaNumber::taka($total) }}</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>
    <tr><td colspan="4">&nbsp;</td></tr>

    <tr><td colspan="2"></td><td colspan="2">{{ $s->principal_name }}</td></tr>
    <tr><td colspan="2"></td><td colspan="2">{{ $s->principal_designation }}</td></tr>
    <tr><td colspan="2"></td><td colspan="2">{{ $s->college_name }}</td></tr>

    @if(!empty($s->contact_mobile))
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">মোবাইল: {{ $s->contact_mobile }}</td></tr>
    @endif
</table>
