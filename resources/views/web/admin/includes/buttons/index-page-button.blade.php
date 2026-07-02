<div class="easy-link-menu align-center">
    <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-list"></i> All {{ $panel }}
    </a>

    <a href="{{ route($base_route.'.add') }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-plus"></i>&nbsp;Add {{ $panel }}
    </a>
</div>

<hr class="hr-4">