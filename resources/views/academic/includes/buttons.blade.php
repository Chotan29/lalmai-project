<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('department')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('department') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Department</a>
        <a class="{!! request()->is('department-head')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('department-head') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Faculty Head</a>
        <a class="{!! request()->is('faculty')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('faculty') }}"><i class="fa fa-calculator" aria-hidden="true"></i>&nbsp;Faculty/Program</a>
        <a class="{!! request()->is('semester')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('semester') }}"><i class="fa fa-money" aria-hidden="true"></i>&nbsp;Semester/Sec.</a>
        <a class="{!! request()->is('student-batch')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('student-batch') }}"><i class="fa fa-header" aria-hidden="true"></i>&nbsp;Batch</a>
    </div>
</div>
<hr class="hr-6">