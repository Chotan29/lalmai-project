<h4 class="header large lighter blue"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Create {{ $panel }}</h4>

{!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
    'id' => 'validation-form']) !!}
    @include($view_path.'.includes.form')
    <div class="clearfix form-actions align-right">
        <div class="col-md-12">
            <button class="btn btn-default" type="reset">
                <i class="fa fa-undo bigger-110"></i>
                Reset
            </button>
            <button class="btn btn-info" type="submit">
                <i class="fa fa-save bigger-110"></i>
                Submit
            </button>
        </div>
    </div>
    <div class="hr hr-24"></div>
{!! Form::close() !!}
