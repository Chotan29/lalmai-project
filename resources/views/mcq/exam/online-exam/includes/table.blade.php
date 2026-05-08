    <div class="col-xs-12">
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="center">
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>SN</th>
                    <th>{{__('form_fields.student.fields.faculty')}}</th>
                    <th>{{__('form_fields.student.fields.semester')}}</th>
                    <th>Subject</th>
                    <th>Exam</th>
                    <th>Date/Time</th>
                    <th>Question</th>
                    <th >MarkType</th>
                    <th >PassMark</th>
                    <th >Result</th>
                    <th>{{ __('common.status')}}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['exams']) && $data['exams']->count() > 0)
                    @php($i=1)
                    @foreach($data['exams'] as $exams)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($exams->id) }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{$i}}</td>
                            <td> {{  ViewHelper::getFacultyTitle( $exams->faculty ) }}</td>
                            <td> {{  ViewHelper::getSemesterTitle( $exams->semester ) }}</td>
                            <td>{{ViewHelper::getSubjectCodeById($exams->subjects_id).' - '.ViewHelper::getSubjectById($exams->subjects_id)}}</td>
                            <td>{{$exams->title}}</td>
                            <td class="text-uppercase text-center">
                                {{$exams->exam_status}} Time
                                <hr class="hr-4">
                                {{--''=>'Choose Exam Type...',
                                'duration'=>'Duration','date_duration'=>'Date & Duration','date_time_duration'=>'Date,Time & Duration'--}}
                                @if(isset($exams->exam_type) && $exams->exam_type=="date_duration")
                                    {{$exams->duration}} Minute
                                    <hr class="hr-2">
                                    {{$exams->start_date}}
                                    <hr class="hr-2">
                                    To
                                    <hr class="hr-2">
                                    {{$exams->end_date}}

                                @elseif(isset($exams->exam_type) && $exams->exam_type=="date_time_duration")
                                    {{$exams->duration}} Minute
                                    <hr class="hr-2">
                                    {{$exams->start_date_time}}
                                    <hr class="hr-2">
                                    To
                                    <hr class="hr-2">
                                    {{$exams->end_date_time}}
                                @else
                                    {{$exams->duration}} Minute
                                @endif
                            </td>
                            <td>{!! $exams->no_of_question !!}</td>
                            <td class="text-uppercase">{{$exams->mark_type}}</td>
                            <td>{{$exams->pass_mark}}</td>
                            <td>{{$exams->result_status}}</td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $exams->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $exams->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => encrypt($exams->id)]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => encrypt($exams->id)]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route($base_route.'.view', ['id' => encrypt($exams->id)]) }}" class="btn-primary btn-sm btn-primary">
                                        <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.edit', ['id' => encrypt($exams->id)]) }}" class="btn-primary btn-sm btn-success">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($exams->id)]) }}" class="btn-primary btn-sm btn-danger bootbox-confirm" >
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">No {{ $panel }} data found.</td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}

        </div>
    </div>
