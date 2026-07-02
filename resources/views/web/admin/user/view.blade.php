@extends('web.admin.layouts.master')

@section('css')

    <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/datepicker.css') }}" />

@endsection

@section('content')

    <div class="main-content">

        <div class="breadcrumbs" id="breadcrumbs">
            <script type="text/javascript">
                try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
            </script>

            <ul class="breadcrumb">
                @include($view_path.'.includes.breadcrumb-primary')
                <li class="active">View</li>
            </ul><!-- .breadcrumb -->


        </div>

        <div class="page-content">
            <div class="page-header">
                <h1>
                    {{ $panel }} Manager
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        View
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @include('web.admin.includes.buttons.view-page-button')

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="20%">Column</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr>
                                        <td>Profile Image</td>
                                        <td>
                                            @if ($data['row']->profile_image)
                                                <img src="{{ asset('images/user/'.$data['row']->profile_image) }}" width="150px">
                                            @else
                                                <p>No image</p>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Name</td>
                                        <td>{{ $data['row']->name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $data['row']->email }}</td>
                                    </tr>

                                    <tr>
                                        <td>Address</td>
                                        <td>{{ $data['row']->address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Contact Number</td>
                                        <td>{{ $data['row']->contact_number }}</td>
                                    </tr>

                                    <tr>
                                        <td>Password</td>
                                        <td>
                                            <div id="show-password-change"><button class="btn btn-sm btn-primary">Change Password</button></div>
                                            <div id="change-password">
                                                <div id="cancel-password-change"><button class="btn btn-sm btn-warning">Cancel</button></div>
                                                <hr>
                                                {!! Form::model($data['row'], ['route' => ['admin.user.password-change', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                                                    'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                                                {!! Form::hidden('id', encrypt($data['row']->id)) !!}

                                                <div class="form-group">
                                                    {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
                                                    <div class="col-sm-4">
                                                        {!! Form::password('password',  ["placeholder" => "", "class" => "form-control border-form","autofocus","id"=>"pass", "required"]) !!}
                                                        @include('includes.form_fields_validation_message', ['name' => 'password'])
                                                    </div>

                                                    {!! Form::label('confirmPassword', 'Confirm Password', ['class' => 'col-sm-2 control-label']) !!}
                                                    <div class="col-sm-4">
                                                        {!! Form::password('confirmPassword',  ["placeholder" => "", "class" => "form-control border-form"/*,"onkeyup"=>"passCheck()"*/,"id"=>"repatpass", "required"]) !!}
                                                        @include('includes.form_fields_validation_message', ['name' => 'confirmPassword'])
                                                    </div>
                                                </div>

                                                <div class="clearfix form-actions">
                                                    <div class="align-right">
                                                        <button class="btn btn-info" type="submit">
                                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                                            Change
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="hr hr-24"></div>

                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $data['row']->status?'Active':'Inactive' }}</td>
                                    </tr>


                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div><!-- /span -->
                    </div><!-- /row -->

                    <div class="hr hr-18 dotted hr-double"></div>


                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->
    </div><!-- /.main-content -->

@endsection


@section('js')


@endsection