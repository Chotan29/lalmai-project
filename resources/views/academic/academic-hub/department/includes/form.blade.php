<div class="form-group">
    {!! Form::label('department', __('academic.child.academic_level.child.department'), ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::text('department', null, ["placeholder" => "e.g.DEPARTMENT OF ELECTRICAL AND ELECTRONIC ENGINEERING", "class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'department'])
    </div>
</div>
<div class="form-group">
    {!! Form::label('sorting', 'Sorting Order', ['class' => 'col-sm-4 control-label']) !!}
    <div class="col-sm-8">
        {!! Form::number('sorting', null, ["class" => "form-control border-form upper","required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'sorting'])
    </div>
</div>

@if(isset($data['faculty']) && $data['faculty']->count() > 0)
    <div class="form-group">
        <label class="col-sm-12 control-label align-left" for="status"> Check Faculties &nbsp;&nbsp;&nbsp;</label>
        @foreach($data['faculty'] as $faculty)
            <div class="row">
                <div class="control-group">
                    <div class="checkbox">
                        <label>
                            @if (!isset($data['row']))
                                {!! Form::checkbox('faculty[]', $faculty->id, false, ['class' => 'ace']) !!}
                            @else
                                {!! Form::checkbox('faculty[]', $faculty->id, array_key_exists($faculty->id, $data['active_faculty']), ['class' => 'ace']) !!}
                            @endif
                            <span class="lbl"> {{ $faculty->faculty }} </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
        @include('includes.form_fields_validation_message', ['name' => 'faculty[]'])
    </div>
@endif

