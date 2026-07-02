<span class="label label-info arrowed-in arrowed-right arrowed responsive">Red mark input field are required. </span>
<hr class="hr-16">
    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('form_fields.student.fields.faculty')}}</label>
        <div class="col-sm-5">
            {!! Form::select('faculty', $data['faculties'], null, ['class' => 'form-control chosen-select', 'onChange' => 'loadSemesters(this);']) !!}
        </div>

        <label class="col-sm-2 control-label">{{__('form_fields.student.fields.semester')}}</label>
        <div class="col-sm-3">
            <select name="semesters_id" class="form-control semesters_id" onChange="loadSubject(this)" >
                <option value=""> Select {{__('form_fields.student.fields.semester')}} </option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Subject</label>
        <div class="col-sm-3">
            <select name="subjects_id" class="form-control semester_subject" >
                <option> Select Subject </option>
            </select>
        </div>
        <label class="col-sm-1 control-label">Exam</label>
        <div class="col-sm-6">
            {!! Form::text('title', null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'title'])
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Description</label>
        <div class="col-sm-10">
            {!! Form::textarea('description', null, ["class" => "form-control border-form summernote", "rows"=>"2"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'description'])
        </div>

    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Instruction</label>
        <div class="col-sm-3">
            {!! Form::select('mcq_instructions_id', $data['instruction'], null, ['class' => 'form-control subcategory']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'mcq_instructions_id'])
        </div>

        <label class="col-sm-1 control-label">Exam</label>
        <div class="col-sm-2">
            {!! Form::select('exam_status', [''=>'Choose Exam Status...','one'=>'One Time','multi'=>'Multiple Time'],null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'exam_status'])
        </div>

        <label class="col-sm-1 control-label">Mark Type</label>
        <div class="col-sm-1">
            {!! Form::select('mark_type', [''=>'Choose Mark Type...','percent'=>'Percent','fixed'=>'Fixed'],null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'mark_type'])
        </div>

        <label class="col-sm-1 control-label">PassMark</label>
        <div class="col-sm-1">
            {!! Form::text('pass_mark', null, ["class" => "form-control border-form"]) !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Question Type</label>
        <div class="col-sm-3">
            {!! Form::select('question_type', [''=>'Choose Question Type...','random'=>'Random','fixed'=>'Fixed'],null, ['class' => 'form-control subcategory']) !!}
            @include('includes.form_fields_validation_message', ['name' => 'question_type'])
        </div>

        <label class="col-sm-2 control-label">Total Question</label>
        <div class="col-sm-2">
            {!! Form::number('no_of_question', null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'no_of_question'])
        </div>

        <label class="col-sm-1 control-label">Result Publish</label>
        <div class="col-sm-2">
            {{--{!! Form::select('result_status', [''=>'Choose Result Type...','auto'=>'Automatic/Immediate','manual'=>'Manually'],null, ["class" => "form-control border-form"]) !!}--}}
            {!! Form::select('result_status', [''=>'Choose Result Type...','1'=>'Automatic/Immediate','0'=>'Manually'],null, ["class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'result_status'])
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Exam Type</label>
        <div class="col-sm-2">
            {!! Form::select('exam_type', [''=>'Choose Exam Type...','duration'=>'Duration','date_duration'=>'Date & Duration','date_time_duration'=>'Date,Time & Duration'],null, ["id" => "exam_type","class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'exam_type'])
        </div>

        <div id="durationDiv">
            <label for="duration" class="col-sm-1 control-label">
                Duration(M)
            </label>
            <div class="col-sm-1">
                {!! Form::text('duration', null, ["id" => "duration","class" => "form-control border-form"]) !!}
            </div>
        </div>

        <div id="startdateDiv">
            <label for="startdate" class="col-sm-1 control-label">
                Start<span class="text-red">*</span>
            </label>
            <div class="col-sm-2">
                {!! Form::text('start_date', null, ["id" => "startdate","class" => "form-control border-form"]) !!}
            </div>
        </div>

        <div id="enddateDiv">
            <label for="enddate" class="col-sm-1 control-label">
                End <span class="text-red">*</span>
            </label>
            <div class="col-sm-2">
                {!! Form::text('end_date', null, ["id" => "enddate","class" => "form-control border-form"]) !!}
            </div>
        </div>

        <div id="startdatetimeDiv">
            <label for="startdatetime" class="col-sm-1 control-label">
                Start <span class="text-red">*</span>
            </label>
            <div class="col-sm-2">
                {!! Form::datetime('start_date_time', null, ["id" => "startdatetime","class" => "form-control border-form"]) !!}
                {{--"id" => "date-timepicker1",--}}
                {{--{!! Form::datetime('start_date_time', null, ["id" => "startdatetime","class" => "form-control border-form"]) !!}--}}
            </div>
        </div>

        <div id="enddatetimeDiv" >
            <label for="enddatetime" class="col-sm-1 control-label">
                End <span class="text-red">*</span>
            </label>
            <div class="col-sm-2">
                {!! Form::datetime('end_date_time', null, ["id" => "enddatetime","class" => "form-control border-form"]) !!}

            </div>
        </div>

    </div>
