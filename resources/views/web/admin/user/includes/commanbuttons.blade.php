{{--@ability('super-admin,admin', 'user-add, role-add')--}}
    <div class="clearfix hidden-print ">
        <div class="easy-link-menu align-center">
            <a class="{!! request()->is('admin/user*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.user') }}"><i class="icon-user" aria-hidden="true"></i>&nbsp;User</a>
            <a class="{!! request()->is('admin/role*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.role') }}"><i class="icon-certificate" aria-hidden="true"></i> Role Accessibility</a>
        </div>
    </div>
    <hr class="hr-6">
{{--
@endability--}}
