<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input field are required. </span>
<hr class="hr-16">
<div class="form-group">
    <label class="col-sm-1 control-label">Subject<span class="red">*</span></label>
    <div class="col-sm-5">
        {!! Form::select('subjects_id', $data['subjects'], null, ["placeholder" => "Search Sub/Course...", "class" => "col-xs-12 col-sm-12"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'subjects_id'])
    </div>
    <label class="col-sm-1 control-label">Group<span class="red">*</span></label>
    <div class="col-sm-2">
        {!! Form::select('mcq_question_groups_id', $data['group'], null, ['class' => 'form-control subcategory']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mcq_question_groups_id'])
    </div>
    <label class="col-sm-1 control-label">Level<span class="red">*</span></label>
    <div class="col-sm-2">
        {!! Form::select('mcq_question_levels_id', $data['level'], null, ['class' => 'form-control']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mcq_question_levels_id'])
    </div>
</div>

<div class="form-group">
    <label class="col-sm-1 control-label">Question<span class="red">*</span></label>
    <div class="col-sm-11">
        {!! Form::textarea('question', null, ["class" => "form-control border-form summernote", "rows"=>"2"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'question'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('main_image', 'Image', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-3">
        {!! Form::file('main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'main_image'])
    </div>
    @if (isset($data['row']) && $data['row']->image)
        <img src="{{ asset('images/'.$folder_name.'/'.$data['row']->image) }}" height="100px">
    @endif

</div>
<div class="form-group">
    {!! Form::label('explanation', 'Explanation', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-11">
        {!! Form::textarea('explanation', null, ["class" => "form-control border-form summernote", "rows"=>"2"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'explanation'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('hints', 'Hints', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-11">
        {!! Form::textarea('hints', null, ["class" => "form-control border-form summernote", "rows"=>"2"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'hints'])
    </div>
</div>

<div class="form-group">
    <label class="col-sm-1 control-label">Mark<span class="red">*</span></label>
    <div class="col-sm-2">
        {!! Form::number('mark', null, ["class" => "form-control border-form","step"=>"any"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mark'])
    </div>

    <label class="col-sm-1 control-label">Answer Type<span class="red">*</span></label>
    <div class="col-sm-3">
        {!! Form::select('type', [''=>'Choose Answer type...','single'=>'Single','multiple'=>'Multiple'],null, ["class" => "form-control border-form", "onChange" =>"appendOption(this)"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'type'])
    </div>
</div>

@include($view_path.'.registration.includes.option')