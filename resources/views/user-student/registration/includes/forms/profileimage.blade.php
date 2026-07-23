<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-plus"></i>Profile Pictures</h4>
<div class="form-group">
    {!! Form::label('student_main_image', 'Student Profile Picture', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('student_main_image', ["class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'student_main_image'])
    </div>

    @if (isset($data['row']))
        @if ($data['row']->student_image)
            <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR.$data['row']->student_image) }}" class="img-responsive" width="100px">
        @endif
    @else
        <img id="" class="img-responsive" alt="Avatar" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" width="100px">
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

{{-- Hard client-side guard: student photo must be a passport-size portrait, JPG/PNG, max 1MB.
     Instantly rejects and clears the field so a wrong photo can never be submitted.
     The server-side AttendanceProfilePhotoRule remains the final gate (background/margins/tilt). --}}
<script>
(function () {
    var MAX_BYTES = 1024 * 1024;
    var input = document.querySelector('input[name="student_main_image"]');
    if (!input) return;

    input.setAttribute('accept', 'image/jpeg,image/png');

    var hint = document.createElement('div');
    hint.style.cssText = 'font-size:12px;color:#777;margin-top:5px;';
    hint.innerHTML = 'Passport-size portrait only (JPG/PNG) &middot; Max 1 MB &middot; Minimum 240&times;320 px &middot; plain white background.';
    if (input.parentNode) { input.parentNode.appendChild(hint); }

    function reject(msg) {
        input.value = '';
        alert(msg);
        try { input.focus(); } catch (e) {}
    }

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) return;

        if (!/^image\/(jpeg|jpg|png)$/i.test(file.type)) {
            reject('Only a JPG or PNG passport-size photo is allowed.');
            return;
        }
        if (file.size > MAX_BYTES) {
            reject('Photo is too large (' + (file.size / 1048576).toFixed(2) + ' MB). Maximum allowed is 1 MB. Please upload a passport-size photo under 1 MB.');
            return;
        }

        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var w = img.naturalWidth, h = img.naturalHeight;
            URL.revokeObjectURL(url);
            if (w < 240 || h < 320) { reject('Photo resolution is too low. Minimum is 240×320 px (passport size).'); return; }
            if (h <= w) { reject('Photo must be a straight portrait (vertical), like a passport photo.'); return; }
            var ratio = w / h;
            if (ratio < 0.55 || ratio > 0.90) { reject('Photo framing is not passport-size. Use a standard vertical passport crop.'); return; }
        };
        img.onerror = function () { URL.revokeObjectURL(url); reject('Could not read the image. Please choose a valid passport-size photo.'); };
        img.src = url;
    });
})();
</script>