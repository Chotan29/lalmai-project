<div class="row">
    <div class="col-xs-12">
        <div class="clearfix">
            <span class="easy-link-menu">
                <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="print"><i class="fa fa-print" aria-hidden="true"></i>&nbsp;Print</a>
                <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="active"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;{{__('common.active_button')}}</a>
                <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="in-active"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp{{__('common.in_active_button')}}</a>
                <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{__('common.delete_button')}}</a>
            </span>

            <span class="pull-right tableTools-container"></span>
        </div>

        <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="15">{!! $data['student']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{__('form_fields.student.fields.faculty')}}</th>
                        <th>{{__('form_fields.student.fields.semester')}}</th>
                        <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                        <th>{{__('form_fields.student.fields.name_of_student')}}</th>
                        <th>PC.NO.</th>
                        <th>Issue Date</th>
                        <th>Ref.No.</th>
                        <th>Duration</th>
                        <th>Year</th>
                        <th>GPA</th>
                        <th>Scale</th>
                        <th>AVG</th>
                        <th>{{ __('common.actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['student']) && $data['student']->count() > 0)
                    @php($i=1)
                    @foreach($data['student'] as $student)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="stuIds[]" value="{{ $student->id }}" class="ace" />
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($student->certificate_id) }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td>
                            <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                            <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}" target="_blank">{{ $student->reg_no }}</a></td>
                            <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}" target="_blank"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                            <td>{{$student->tmc_num}}</td>
                            <td>{{ \Carbon\Carbon::parse($student->date_of_issue)->format('d-M-Y')}} </td>
                            <td>{{$student->ref_num}}</td>
                            <td>{{$student->program_duration}}</td>
                            <td>{{$student->year}}</td>
                            <td>{{$student->gpa}}</td>
                            <td>{{$student->scale}}</td>
                            <td>{{$student->average_grade}}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route($base_route.'.print', ['id' => encrypt($student->id)]) }}" class="btn-primary btn-sm" target="_blank">
                                        <i class="ace-icon fa fa-print bigger-130"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.view', ['id' => encrypt($student->id)]) }}" class="btn-success btn-sm">
                                        <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.edit', ['id' => encrypt($student->id)]) }}" class="btn-success btn-sm">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($student->id)]) }}" class="btn-danger btn-sm bootbox-confirm" target="_blank" >
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                </div>
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