<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('super-suit/user-activity')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('super-suit.user-activity') }}"><i class="fa fa-users" aria-hidden="true"></i> <i class="fa fa-history" aria-hidden="true"></i>&nbsp;Users Activity</a>
        <a class="{!! request()->is('super-suit/cleaner')?'btn-danger':'btn-primary' !!} btn-sm" href="{{ route('super-suit.cleaner') }}"> <i class="ace-icon fa fa-eraser bigger-110 white"></i>&nbsp;Cleaner</a>
        <a class="{!! request()->is('developer-info/index')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('developer-info') }}"><i class="fa fa-users" aria-hidden="true"></i> <i class="fa fa-history" aria-hidden="true"></i>&nbsp;Developer Info</a>
    </div>
    <hr class="hr-6 ">
</div>
