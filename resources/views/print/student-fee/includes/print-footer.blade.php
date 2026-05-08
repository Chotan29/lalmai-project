<div class="text-center">
    @php
        $printFooter = $generalSetting->print_footer ?? '';
        if ($printFooter && !empty($generalSetting->institute)) {
            $printFooter = preg_replace('/CCN\s+UNIVERSITY/i', $generalSetting->institute, $printFooter);
        }
    @endphp
    @if(!empty($printFooter))
        <div class="hr hr-2"></div>
        {!! $printFooter !!}
    @endif
    <span class="invoice-info-label">User:</span>
    <span class="red">{{isset(auth()->user()->name)?auth()->user()->name:""}}</span>,
    <span class="invoice-info-label">Date:</span>
    <span class="blue">{{$date =  \Carbon\Carbon::parse(now())->format('Y-m-d')}}</span>

</div>
