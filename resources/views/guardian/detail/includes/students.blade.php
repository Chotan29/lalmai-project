<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Linked Students List</h4>
        <div class="clearfix">
            {!! Form::open(['route' => $base_route.'.link','method' => 'POST', 'class' => 'form-horizontal',
                        "enctype" => "multipart/form-data"]) !!}

                <div class="form-group">
                    {!! Form::hidden('guardian_id', $data['guardian']->id, ["placeholder" => "", "class" => "col-xs-5 col-sm-5"]) !!}
                    {!! Form::label('guardian_info', 'Find Student Using Name | Mobile Number | Email & Click on Link Now ', ['class' => 'col-sm-12 control-label align-center']) !!}
                    <div class="col-sm-12">
                        {!! Form::select('student_link_id', [], null, ["placeholder" => "Type Student Reg.No. or Contact Number or Name...", "class" => "col-xs-12 col-sm-12", "style" => "width: 100%;"]) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'student_link_id'])

                        <hr>
                        <div class="align-right">
                            <button type="submit" class="btn btn-sm btn-primary" id="link-student-html-btn">
                                <i class="ace-icon fa fa-link bigger-130"></i> Link Student
                            </button>
                        </div>
                    </div>
                </div>

            {!! Form::close() !!}

            <span class="pull-right tableTools-container"></span>
        </div>
        <div class="table-header">
            Students List Which is Links with Guardian.
        </div>
    <!-- div.table-responsive -->
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th>{{ __('common.s_n')}}</th>
                    <th>{{__('form_fields.student.fields.faculty')}}</th>
                    <th>{{__('form_fields.student.fields.semester')}}</th>
                    <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                    <th>{{__('form_fields.student.fields.name_of_student')}}</th>
                    <th>{{ __('common.status')}}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['guardian']) && $data['guardian']->students()->count() > 0)
                    @php($i=1)
                    @foreach($data['guardian']->students()->get() as $student)
                        <tr>
                            <td>{{ $i }}</td>
                            <td> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td>
                            <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                            <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a></td>
                            <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $student->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $student->status == 'active'?"Active":"In Active" }}
                                    </button>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route($base_route.'.unlink', ['student' => $student->id,'guardian' => encrypt($data['guardian']->id)]) }}" class="btn-danger btn-sm bootbox-confirm" >
                                    <i class="ace-icon fa fa-unlink bigger-130"></i> Unlink
                                </a>
                            </td>

                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}

        </div>
    </div>
</div>