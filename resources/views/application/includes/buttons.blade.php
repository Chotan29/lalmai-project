<div class="clearfix hidden-print " >
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('application/index')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('application.index') }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;Application Detail</a>
        <a class="{!! request()->is('application/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('application.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New Application</a>
        @ability('super-admin','dynamic-application-type-index')
            <a class="{!! request()->is('dynamic/application-type')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.application-type') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Application Type</a>
        @endability
    </div>
</div>
<hr class="hr-4">