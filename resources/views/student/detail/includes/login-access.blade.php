{{--student user--}}
<div class="row">

    @if( !$data['student_login'])
    {{--create--}}
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;Create Student Login Access</h4>

        {!! Form::open(['route' => 'student.user.create', 'method' => 'POST', 'class' => 'form-horizontal',
                       /*'id' => 'validation-form',*/ "enctype" => "multipart/form-data"]) !!}
            {!! Form::hidden('role_id', 6) !!}
            {!! Form::hidden('hook_id', encrypt($data['student']->id)) !!}

            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::text('name', $data['student']->first_name.' '.
                        $data['student']->middle_name.' '.$data['student']->last_name, ["placeholder" => "", "class" => "form-control border-form","id" => "student_name", "required"]) !!}
                </div>

                {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::email('email', $data['student']->email, ["placeholder" => "", "class" => "form-control border-form","id" => "student_email",  "required"]) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::password('password',  ["placeholder" => "", "class" => "form-control border-form", "id" => "student_password","required"]) !!}
                </div>

                {!! Form::label('confirmPassword', 'Confirm Password', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::password('confirmPassword',  ["placeholder" => "", "class" => "form-control border-form"/*,"onkeyup"=>"passCheck()"*/, "id" => "student_password_confirmation","required"]) !!}
                </div>
            </div>
            <div class="space-4"></div>

            <div class="clearfix form-actions">
                <div class="col-md-12 align-right">
                    <button class="btn" type="reset">
                        <i class="fa fa-undo bigger-110"></i>
                        Reset
                    </button>

                    <button class="btn btn-info" type="submit">
                        <i class="fa fa-save bigger-110"></i>
                        Create Login Access
                    </button>
                </div>
            </div>

            <div class="hr hr-24"></div>
        {!! Form::close() !!}
    </div>
    @else
    {{--edit--}}
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;Edit Student Login Access</h4>
        <a href="{{ route('student.user.active', ['id' => $data['student_login']->id]) }}" title="Active" class="btn-success btn-sm"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Un-Lock User</a>
        <a href="{{ route('student.user.in-active', ['id' => $data['student_login']->id]) }}" title="In-Active" class="btn-warning btn-sm"><i class="fa fa-lock" aria-hidden="true"></i> Lock User</a>
        <a href="{{ route('student.user.delete', ['id' => $data['student_login']->id]) }}" title="Delete" class="btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i> Delete User</a>
        <div class="hr hr-24"></div>

        {!! Form::model($data['student_login'], ['route' => ['student.user.update', encrypt($data['student_login']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                  'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
        {!! Form::hidden('id', encrypt($data['student_login']->id)) !!}
        {!! Form::hidden('role_id', 6) !!}
        {!! Form::hidden('hook_id', encrypt($data['student']->id)) !!}

        <div class="form-group">
            {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::text('name', null, ["placeholder" => "", "class" => "form-control border-form","id" => "guardian_name", "required"]) !!}
            </div>

            {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::email('email', null, ["placeholder" => "", "class" => "form-control border-form","id" => "guardian_email", "required"]) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::password('password',  ["placeholder" => "", "class" => "form-control border-form", "id" => "guardian_password","required"]) !!}
            </div>

            {!! Form::label('confirmPassword', 'Confirm Password', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::password('confirmPassword',  ["placeholder" => "", "class" => "form-control border-form"/*,"onkeyup"=>"passCheck()"*/,"id" => "guardian_password_confirmation", "required"]) !!}
            </div>
        </div>
        <div class="col-sm-4">
            <label data-toggle="dropdown" class="label {{ $data['student_login']->status == 'active'?"label-success":"label-danger" }}" >
                {{ $data['student_login']->status == 'active'?"Active User":"User Locked" }}
            </label>
        </div>
        <div class="clearfix hr-8"></div>

        <div class="clearfix form-actions">
            <div class="col-md-12 align-right">
                <button class="btn" type="reset">
                    <i class="fa fa-undo bigger-110"></i>
                    Reset
                </button>

                <button class="btn btn-info" type="submit">
                    <i class="fa fa-save bigger-110"></i>
                Update Detail
                </button>
            </div>
        </div>


        <div class="hr hr-24"></div>
        {!! Form::close() !!}
    </div>
    @endif

</div>

{{--guardian user--}}
<div class="row">

    @if( !$data['guardian_login'])
        {{--create--}}
        <div class="col-xs-12">
            <h4 class="header large lighter blue"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;Create Guardian Login Access</h4>

            {!! Form::open(['route' => 'student.guardian.user.create', 'method' => 'POST', 'class' => 'form-horizontal',
                           'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
            {!! Form::hidden('role_id', 7) !!}
            {!! Form::hidden('hook_id', encrypt($data['student']->guardian_id)) !!}


            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::text('name', $data['student']->guardian_first_name.' '.$data['student']->guardian_middle_name.' '.$data['student']->guardian_last_name, ["class" => "form-control border-form", "required"]) !!}
                </div>

                {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::email('email', $data['student']->guardian_email, ["class" => "form-control border-form", "required"]) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::password('password',  ["class" => "form-control border-form", "required"]) !!}
                </div>

                {!! Form::label('confirmPassword', 'Confirm Password', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::password('confirmPassword',  ["class" => "form-control border-form"/*,"onkeyup"=>"passCheck()"*/,"required"]) !!}
                </div>
            </div>
            <div class="space-4"></div>

            <div class="clearfix form-actions">
                <div class="col-md-12 align-right">
                    <button class="btn" type="reset">
                        <i class="fa fa-undo bigger-110"></i>
                        Reset
                    </button>

                    <button class="btn btn-info" type="submit">
                        <i class="fa fa-save bigger-110"></i>
                        Create Login Access
                    </button>
                </div>
            </div>

            <div class="hr hr-24"></div>
            {!! Form::close() !!}
        </div>
    @else
        {{--edit--}}
        <div class="col-xs-12">
            <h4 class="header large lighter blue"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;Edit Guardian Login Access</h4>
            <a href="{{ route('student.guardian.user.active', ['id' => $data['guardian_login']->id]) }}" title="Active" class="btn-success btn-sm"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Un-Lock User</a>
            <a href="{{ route('student.guardian.user.in-active', ['id' => $data['guardian_login']->id]) }}" title="In-Active" class="btn-warning btn-sm"><i class="fa fa-lock" aria-hidden="true"></i> Lock User</a>
            <a href="{{ route('student.guardian.user.delete', ['id' => $data['guardian_login']->id]) }}" title="Delete" class="btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i> Delete User</a>
            <div class="hr hr-24"></div>

            {!! Form::model($data['guardian_login'], ['route' => ['student.guardian.user.update', encrypt($data['guardian_login']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                      'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
            {!! Form::hidden('id', encrypt($data['guardian_login']->id)) !!}
            {!! Form::hidden('role_id', 7) !!}
            {!! Form::hidden('hook_id', encrypt($data['student']->guardian_id)) !!}



            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::text('name', null, ["class" => "form-control border-form", "required"]) !!}
                </div>

                {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::email('email', null, ["class" => "form-control border-form", "required"]) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::password('password',  ["placeholder" => "", "class" => "form-control border-form", "required"]) !!}
                </div>

                {!! Form::label('confirmPassword', 'Confirm Password', ['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::password('confirmPassword',  ["placeholder" => "", "class" => "form-control border-form"/*,"onkeyup"=>"passCheck()"*/, "required"]) !!}
                </div>
            </div>
            <div class="col-sm-4">
                <label data-toggle="dropdown" class="label {{ $data['guardian_login']->status == 'active'?"label-success":"label-danger" }}" >
                    {{ $data['guardian_login']->status == 'active'?"Active User":"User Locked" }}
                </label>
            </div>
            <div class="clearfix hr-8"></div>

            <div class="clearfix form-actions">
                <div class="col-md-12 align-right">
                    <button class="btn" type="reset">
                        <i class="fa fa-undo bigger-110"></i>
                        Reset
                    </button>

                    <button class="btn btn-info" type="submit">
                        <i class="fa fa-save bigger-110"></i>
                Update Detail
                    </button>
                </div>
            </div>


            <div class="hr hr-24"></div>
            {!! Form::close() !!}
        </div>
    @endif

</div>
