<h4 class="header large lighter blue"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit {{ $panel }}</h4>

@if($data['row']->is_locked)
    {{-- Money has been taken against this fee. Changing it now would rewrite what students
         already paid for, so it is read-only; a new session is a copy, not an edit. --}}
    <div class="alert alert-warning">
        <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;
        Fees have been collected against this {{ $panel }}, so it can no longer be changed.
        Use <b>Copy</b> to make the next session's version.
    </div>

    <div class="form-horizontal">
        @include($view_path.'.includes.form')
    </div>

    <div class="clearfix form-actions align-right">
        <div class="col-md-12">
            <a class="btn btn-default" href="{{ route($base_route) }}">
                <i class="fa fa-arrow-left bigger-110"></i>
                Back
            </a>
        </div>
    </div>
@else
    {!! Form::open(['route' => [$base_route.'.update', encrypt($data['row']->id)], 'method' => 'POST',
        'class' => 'form-horizontal', 'id' => 'validation-form']) !!}
        {!! Form::hidden('row_id', $data['row']->id) !!}
        @include($view_path.'.includes.form')
        <div class="clearfix form-actions align-right">
            <div class="col-md-12">
                <a class="btn btn-default" href="{{ route($base_route) }}">
                    <i class="fa fa-undo bigger-110"></i>
                    Cancel
                </a>
                <button class="btn btn-info" type="submit">
                    <i class="fa fa-save bigger-110"></i>
                    Update
                </button>
            </div>
        </div>
        <div class="hr hr-24"></div>
    {!! Form::close() !!}
@endif
