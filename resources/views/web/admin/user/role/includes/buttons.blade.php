<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('admin/role')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.role') }}"><i class="icon-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('admin/role/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.role.add') }}"><i class="icon-plus" aria-hidden="true"></i>&nbsp;Create Role</a>

    </div>
</div>
<hr class="hr-6">