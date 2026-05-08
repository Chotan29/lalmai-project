<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('certificate/issue')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.issue') }}"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i> Issue</a>
        <a class="{!! request()->is('certificate/attendance*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.attendance') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Attendance</a>
        <a class="{!! request()->is('certificate/transfer*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.transfer') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Transfer</a>
        <a class="{!! request()->is('certificate/character*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.character') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Character</a>
        <a class="{!! request()->is('certificate/bonafide*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.bonafide') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Bonafide</a>
        <a class="{!! request()->is('certificate/course-completion*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.course-completion') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Course Completion</a>
        <a class="{!! request()->is('certificate/nirgam-utara*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.nirgam-utara') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Nirgam Utara</a>
        <a class="{!! request()->is('certificate/provisional*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.provisional') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Provisional</a>
        <a class="{!! request()->is('certificate/testimonial*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.testimonial') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Testimonial</a>
        <a class="{!! request()->is('certificate/moi*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.moi') }}"><i class="fa fa-certificate" aria-hidden="true"></i> MOI</a>
        <a class="{!! request()->is('certificate/transcript*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.transcript') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Transcript</a>
        <a class="{!! request()->is('certificate/issue-history*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.issue-history') }}"><i class="fa fa-certificate" aria-hidden="true"></i> History</a>
        <a class="{!! request()->is('certificate/generate*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.generate') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Custom</a>
        <a class="{!! request()->is('certificate/template*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('certificate.template') }}"><i class="fa fa-certificate" aria-hidden="true"></i> Template</a>
    </div>
</div>
<hr class="hr-4">
