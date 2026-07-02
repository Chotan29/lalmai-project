<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-plus"></i>Profile Pictures</h4>
<div class="form-group">
    {!! Form::label('student_main_image', 'Student Profile Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('student_main_image', ["class" => "form-control border-form", "id" => "student_main_image"]) !!}
        <small class="help-block">Max file size: 5MB | Minimum resolution: 300x400px | Passport ratio: 35x45</small>
        <ul class="help-block" style="margin-top:8px; margin-bottom:0; padding-left:18px;">
            <li>Use a recent passport-size portrait photo.</li>
            <li>Keep the face straight and clearly visible.</li>
            <li>Use a plain white background and make both ears visible when possible.</li>
        </ul>
        @include('includes.form_fields_validation_message', ['name' => 'student_main_image'])
    </div>

    @if (isset($data['row']))
        @if ($data['row']->student_image)
            <img id="student-photo-preview"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$data['row']->student_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="student-photo-preview" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>


<div class="form-group">
    {!! Form::label('student_signature_main_image', 'Student Signature', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('student_signature_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'student_signature_main_image'])
    </div>

    @if (isset($data['row']))
        @if ($data['row']->student_signature)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$data['row']->student_signature) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>

<div class="form-group">
    {!! Form::label('father_main_image', 'Father Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('father_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'father_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->father_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['row']->father_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>

<div class="form-group">
    {!! Form::label('mother_main_image', 'Mother Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('mother_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'mother_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->mother_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['row']->mother_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>

<div class="form-group">
    {!! Form::label('guardian_main_image', 'Guardian Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('guardian_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'guardian_main_image'])
    </div>
    @if (isset($data['row']))
        @if ($data['row']->guardian_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['row']->guardian_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
    @endif
</div>