<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('inventory/vendor')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.vendor') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('inventory/vendor/registration*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.vendor.registration') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add</a>
        <a class="{!! request()->is('inventory/vendor/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.vendor.import') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Bulk Import</a>
        <a class="{!! request()->is('inventory/vendor/document*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.vendor.document') }}"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;Documents</a>
        <a class="{!! request()->is('inventory/vendor/note*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.vendor.note') }}"><i class="fa fa-sticky-note" aria-hidden="true"></i>&nbsp;Notes</a>
    </div>
    <hr class="hr-6 ">
</div>
