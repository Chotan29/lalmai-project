<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('admin/info/notice')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.notice') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('admin/info/notice/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('web.admin.info.notice.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Create Notice</a>

    </div>
</div>
<hr class="hr-6">