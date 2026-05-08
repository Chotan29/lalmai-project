<div class="easy-link-menu align-center">
  {{--  <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
        <i class="icon-list"></i>&nbsp;All {{$panel}}
    </a>

    <a href="{{ route($base_route.'.add') }}" class="btn btn-xs btn-primary">
        <i class="fa fa-plus bigger-120"></i>&nbsp;Add {{ $panel }}
    </a>--}}
    <a class="btn btn-xs {!! request()->is('admin/info/smsemail')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.smsemail') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
    <a class="btn btn-xs {!! request()->is('admin/info/smsemail/staff')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.smsemail.staff') }}"><i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp;Staff</a>
    <a class="btn btn-xs {!! request()->is('admin/info/smsemail/create')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.smsemail.create') }}"><i class="fa fa-group" aria-hidden="true"></i>&nbsp;Contact Group</a>
    <a class="btn btn-xs {!! request()->is('admin/info/smsemail/individual')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.smsemail.individual') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Individual</a>
    <a class="btn btn-xs {!! request()->is('admin/info/smsemail/subscriber')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.smsemail.subscriber') }}"><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp;Subscribers</a>
</div>
{{--<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">

        --}}{{--<a class="{!! request()->is('info/smsemail/student-guardian')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('info.smsemail.student-guardian') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Student & Guardian</a>--}}{{--
        --}}{{--<a class="{!! request()->is('info/smsemail/checkSmsCredit')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('info.smsemail.checkSmsCredit') }}"><i class="fa fa-credit-card" aria-hidden="true"></i>&nbsp;Check SMS Credit</a>--}}{{--

    </div>
</div>--}}
<hr class="hr-6">