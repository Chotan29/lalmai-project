<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('inventory/purchase')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.purchase') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Detail</a>
        <a class="{!! request()->is('inventory/purchase/registration*')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('inventory.purchase.registration') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add</a>
    </div>
    <hr class="hr-6 ">
</div>
