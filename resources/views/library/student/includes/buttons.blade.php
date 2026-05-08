<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('library/student*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('library.student') }}"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;Students Member</a>
        <a class="{!! request()->is('library/staff*')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('library.staff') }}"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;Staffs Member</a>
    </div>
</div>
<hr class="hr-6">