<div class="form-group">
    {!! Form::label('question', 'Question', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('question', null, ["placeholder" => "Enter Question", "class" => "form-control border-form"]) !!}

        @include('includes.form_fields_validation_message', ['name' => 'question'])
    </div>
</div>


<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('answer', 'Answer', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('answer', null, ["placeholder" => "Give Answer", "class" => "form-control border-form summernote"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'answer'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('rank', 'Faq Rank', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('rank', null, ["placeholder" => "Enter Rank", "class" => "form-control border-form", 'min' => 0, 'required']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'rank'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Status </label>

    <div class="col-sm-10">
        <div class="control-group">

            <div class="radio">
                <label>
                    {!! Form::radio('status', 'active', true, ['class' => 'ace']) !!}
                    <span class="lbl"> Active</span>
                </label>
                <label>
                    {!! Form::radio('status', 'in-active', false, ['class' => 'ace']) !!}
                    <span class="lbl"> Inactive</span>
                </label>
            </div>
            @include('includes.form_fields_validation_message', ['name' => 'status'])
        </div>
    </div>
</div>

<div class="space-4"></div>