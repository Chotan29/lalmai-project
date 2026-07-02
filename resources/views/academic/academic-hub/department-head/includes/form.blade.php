<div class="form-group">
    {!! Form::label('department_head', __('academic.child.academic_level.child.department_head'), ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('department_head', null, ["placeholder" => "e.g.Engineering", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'department_head'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('sorting', 'Sorting Order', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::number('sorting', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'sorting'])
    </div>
</div>

@if(isset($data['department']) && $data['department']->count() > 0)
    <div class="form-group">
        <label class="col-sm-12 control-label align-left" for="status"> Check Department Under Head &nbsp;&nbsp;&nbsp;</label>
        @foreach($data['department'] as $department)
            <div class="row">
                <div class="control-group">
                    <div class="checkbox">
                        <label>
                            @if (!isset($data['row']))
                                {!! Form::checkbox('department[]', $department->id, false, ['class' => 'ace']) !!}
                            @else
                                {!! Form::checkbox('department[]', $department->id, array_key_exists($department->id, $data['active_department']), ['class' => 'ace']) !!}
                            @endif
                            <span class="lbl"> {{ $department->department }} </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
        @include('includes.form_fields_validation_message', ['name' => 'department[]'])
    </div>
@endif

