<div class="easy-link-menu align-center">
    <a href="{{ route($base_route) }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-list-alt bigger-110"></i>&nbsp;All {{$panel}}
    </a>

    <a href="{{ route($base_route.'.add') }}" class="btn btn-xs btn-primary">
        <i class="ace-icon  fa fa-plus bigger-120"></i>&nbsp;Add {{ $panel }}
    </a>

    <a href="{{ route($base_route.'.view', ['id' => encrypt($data['row']->id)]) }}" class="btn btn-xs btn-primary">
        <i class="ace-icon fa fa-eye bigger-120"></i>&nbsp;View {{ $panel }}
    </a>
</div>
<hr class="hr-4">