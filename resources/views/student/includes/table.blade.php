{!! Form::open(['route' => $base_route . '.bulk-action', 'id' => 'bulk_action_form']) !!}

<div class="row">
    <div class="col-xs-12">

        <div class="clearfix table-head-menu" style="text-align: left !important;">
            <span class="easy-link-menu">
                <a class="btn-success btn-sm bulk-action-btn" attr-action-type="print-certificate"><i class="fa fa-print"
                        aria-hidden="true"></i>&nbsp;Print Certificate</a>
                <a class="btn-success btn-sm bulk-action-btn" attr-action-type="export-excel"><i class="fa fa-file-excel"
                        aria-hidden="true"></i>&nbsp;Export</a>
                {{--                <a class="btn-info btn-sm bulk-action-btn" attr-action-type="generate-pdf"><i class="fa fa-file-pdf" aria-hidden="true"></i>&nbsp;PDF</a> --}}
                <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="active"><i class="fa fa-check"
                        aria-hidden="true"></i>&nbsp;{{ __('common.active_button') }}</a>
                <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="in-active"><i class="fa fa-remove"
                        aria-hidden="true"></i>&nbsp{{ __('common.in_active_button') }}</a>
                <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash"
                        aria-hidden="true"></i>&nbsp;{{ __('common.delete_button') }}</a> |
                <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete-all"><i class="fa fa-trash-o"
                    aria-hidden="true"></i>&nbsp;Delete All Students</a> |
                <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="create-reset-login"><i
                        class="fa fa-user" aria-hidden="true"></i>&nbsp;Create/Reset Login</a>
                <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="create-reset-library-member"><i
                        class="fa fa-book" aria-hidden="true"></i>&nbsp;Create Library Member</a>

            </span>

            <div class="col-sm-2 float-right">
                {!! Form::select('certificate-template', $data['certificate-template'], null, ['class' => 'form-control']) !!}
            </div>

            <span class="pull-right tableTools-container"></span>
        </div>

        <!-- div.table-responsive -->
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="10">{!! $data['student']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n') }}</th>
                        <th>{{ __('form_fields.student.fields.faculty') }}</th>
                        <th>{{ __('form_fields.student.fields.semester') }}</th>
                        <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                        {{-- <th>Reg.Date</th> --}}
                        <th>{{ __('form_fields.student.fields.name_of_student') }}</th>
                        <th>{{ __('form_fields.student.fields.academic_status') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('common.actions') }}</th>
                        <th>{{ __('common.service_activation') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['student']) && $data['student']->count() > 0)
                        @php($i = 1)
                        @foreach ($data['student'] as $student)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ encrypt($student->id) }}"
                                            class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                <td> {{ $student->faculty }}</td>
                                <td> {{ $student->semester }}</td>
                                {{--                            <td> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td> --}}
                                {{--                            <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td> --}}
                                {{-- <td>{{ \Carbon\Carbon::parse($student->reg_date)->format('Y-m-d')}} </td> --}}
                                <td>
                                    @if($student->reg_no != '')
                                        {{-- {!! QrCode::size(150)->generate($student->reg_no) !!} --}}
                                    <a
                                        href="{{ route($base_route . '.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a>
                                    @endif
                                    
                                </td>
                                <td><a href="{{ route($base_route . '.view', ['id' => encrypt($student->id)]) }}">
                                        {{ $student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name }}</a>
                                </td>
                                <td>
                                    {{ $student->academic_status }}
                                </td>
                                <td>
                                    <div class="dropdown status-dropdown">
                                        <button
                                            class="btn btn-xs dropdown-toggle status-btn {{ $student->status == 'active' ? 'btn-success' : 'btn-warning' }}"
                                            type="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i
                                                class="fa {{ $student->status == 'active' ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                            {{ $student->status == 'active' ? 'Active' : 'In Active' }}
                                            <i class="fa fa-caret-down ml-1"></i>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-status">
                                            <a class="dropdown-item text-success"
                                                href="{{ route($base_route . '.active', ['id' => encrypt($student->id)]) }}">
                                                <i class="fa fa-check-circle"></i> Set as Active
                                            </a>
                                            <a class="dropdown-item text-warning"
                                                href="{{ route($base_route . '.in-active', ['id' => encrypt($student->id)]) }}">
                                                <i class="fa fa-ban"></i> Set as In-Active
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-primary btn-sm"
                                            href="{{ route('account.fees.collection.view', ['id' => encrypt($student->id)]) }}">
                                            <i class="fa fa-calculator" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route($base_route . '.generate-pdf', ['id' => encrypt($student->id)]) }}"
                                            class="btn-success btn-sm">
                                            <i class="fa fa-file-pdf bigger-130"></i>
                                        </a>

                                        <a href="{{ route('online-registration.print', ['id' => encrypt($student->id)]) }}"
                                            class="btn-primary btn-sm">
                                            <i class="ace-icon fa fa-print bigger-130"></i>
                                        </a>
                                        <a href="{{ route($base_route . '.view', ['id' => encrypt($student->id)]) }}"
                                            class="btn-success btn-sm">
                                            <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                        </a>

                                        <a href="{{ route($base_route . '.edit', ['id' => encrypt($student->id)]) }}"
                                            class="btn-success btn-sm">
                                            <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                        </a>

                                        <a href="{{ route($base_route . '.delete', ['id' => encrypt($student->id)]) }}"
                                            class="btn-danger btn-sm bootbox-confirm">
                                            <i class="fa fa-trash bigger-130" title="Delete"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        {{-- Library --}}
                                        <a href="{{ route('library.member.quick-membership', ['reg_no' => $student->reg_no, 'user_type' => 1, 'status' => 'active']) }}"
                                            class="btn-primary btn-sm">
                                            <i class="ace-icon fa fa-book bigger-130"></i>
                                        </a>

                                        {{-- Hostel --}}
                                        <a class="open-ActiveAgain btn-primary btn-sm" data-toggle="modal"
                                            data-target="#activeAgain" data-id="{{ $student->id }}"
                                            data-reg="{{ $student->reg_no }}">
                                            <span>
                                                <i class="ace-icon fa fa-bed bigger-130"></i>
                                            </span>
                                        </a>

                                        {{-- Transport --}}
                                        <a class="open-TransportActiveAgain btn-primary btn-sm" data-toggle="modal"
                                            data-target="#TransportActiveAgain" data-id="{{ $student->id }}"
                                            data-reg="{{ $student->reg_no }}">
                                            <span>
                                                <i class="ace-icon fa fa-bus bigger-130"></i>
                                            </span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9">No {{ $panel }} data found. Please Filter {{ $panel }}
                                to show. </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
{!! Form::close() !!}
