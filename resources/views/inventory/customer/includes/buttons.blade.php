<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('inventory/customer')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.customer') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('inventory/customer/registration*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.customer.registration') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Registration</a>
        <a class="{!! request()->is('inventory/customer/import*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.customer.import') }}"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Bulk Registration</a>
        <a class="{!! request()->is('inventory/customer/document*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.customer.document') }}"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;Documents</a>
        <a class="{!! request()->is('inventory/customer/note*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.customer.note') }}"><i class="fa fa-sticky-note" aria-hidden="true"></i>&nbsp;Notes</a>
    </div>
    <hr class="hr-6 ">
</div>
