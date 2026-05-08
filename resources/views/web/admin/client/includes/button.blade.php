<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('admin/client')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route($base_route) }}">
            <i class="icon-list" aria-hidden="true"></i>&nbsp;List
        </a>
        <a class="{!! request()->is('admin/client/add*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route($base_route.'.add') }}">
            <i class="icon icon-plus" aria-hidden="true"></i>&nbsp;Add
        </a>
    </div>
</div>
<hr class="hr-6">