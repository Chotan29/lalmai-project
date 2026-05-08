<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('admin/user')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.user') }}"><i class="icon-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('admin/user/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.user.add') }}"><i class="icon-plus" aria-hidden="true"></i>&nbsp;Create User</a>
    </div>
</div>
<hr class="hr-6">