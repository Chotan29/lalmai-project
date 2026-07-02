{{--
@if (session()->has('message_warning'))

    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>{{ session()->get('message_warning') }}
    </div>

@endif

@if (session()->has('message_success'))

    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>{{ session()->get('message_success') }}
    </div>

@endif

@if (session()->has('message_danger'))

    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>{{ session()->get('message_danger') }}
    </div>

@endif--}}

@if (session()->has('message_warning'))

    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <i class="icon-warning-sign bigger-120" aria-hidden="true"></i>
        {{ session()->get('message_warning') }}
    </div>

@endif

@if (session()->has('message_success'))

    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <i class="icon-check bigger-120" aria-hidden="true"></i>
        {{ session()->get('message_success') }}
    </div>

@endif

@if (session()->has('message_danger'))

    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <i class="icon-hand-right bigger-120" aria-hidden="true"></i>
        {{ session()->get('message_danger') }}
    </div>

@endif
