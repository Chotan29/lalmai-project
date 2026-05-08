<div class="easy-link-menu align-center">
    <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-list"></i>&nbsp;All {{$panel}}
    </a>

    <a href="{{ route($base_route.'.add') }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-plus"></i> Add {{ $panel }}
    </a>

    <a href="{{ route($base_route.'.edit', ['id' => encrypt($data['row']->id)]) }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-edit"></i>&nbsp;Edit {{ $panel }}
    </a>
</div>
<hr class="hr-4">