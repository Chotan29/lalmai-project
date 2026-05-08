<div class="row">
    <div class="col-xs-12">
{{--        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>--}}
        <div class="clearfix table-head-menu ">
        <span class="easy-link-menu">
            <a class="btn-success btn-sm bulk-action-btn" attr-action-type="export-excel"><i class="fa fa-file-excel" aria-hidden="true"></i>&nbsp;Export</a>
            <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="active"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;{{__('common.active_button')}}</a>
            <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="in-active"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp{{__('common.in_active_button')}}</a>
            <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{__('common.delete_button')}}</a>
            <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="create-reset-login"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Create/Reset Login</a>
            <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="create-reset-library-member"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;Create Library Member</a>
        </span>
            <span class="pull-right tableTools-container"></span>
        </div>
{{--        <div class="table-header">--}}
{{--            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.--}}
{{--        </div>--}}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}

            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="11">{!! $data['staff']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        {{--<th>Image</th>--}}
                        <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                        <th>Staff Name</th>
                        <th>Phone</th>
                        <th>Designation</th>
                        <th>Qualification</th>
                        <th>{{ __('common.status')}}</th>
                        <th>{{ __('common.actions')}}</th>
                        <th>{{ __('common.service_activation')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['staff']) && $data['staff']->count() > 0)
                        @php($i=1)
                        @foreach($data['staff'] as $staff)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ encrypt($staff->id) }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                {{--<td>
                                    <img src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$staff->staff_image) }}"
                                         alt="{{ $staff->staff_image }}" class="img-responsive" width="80px">
                                </td>--}}
                                <td>
                                    @if($staff->reg_no != '')
                                        {{-- {!! QrCode::size(150)->generate($staff->reg_no) !!} --}}
                                    <a href="{{ route($base_route.'.view', ['id' => encrypt($staff->id)]) }}">{{ $staff->reg_no }}</a>
                                    @endif
                                    
                                </td>
                                <td><a href="{{ route($base_route.'.view', ['id' => encrypt($staff->id)]) }}"> {{ $staff->first_name.' '.$staff->middle_name.' '. $staff->last_name }}</a></td>
                                <td><a href="tel:{{ $staff->mobile_1 }}">{{ $staff->mobile_1 }}</a> </td>
                                <td>{{ $staff->designation }}</td>
                                <td>{{ $staff->qualification }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $staff->status == 'active'?"btn-info":"btn-warning" }}" >
                                            {{ $staff->status == 'active'?"Active":"In Active" }}
                                            <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route($base_route.'.active', ['id' => encrypt($staff->id)]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            </li>

                                            <li>
                                                <a href="{{ route($base_route.'.in-active', ['id' => encrypt($staff->id)]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </div>

                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route($base_route.'.view', ['id' => encrypt($staff->id)]) }}" class="btn-primary btn-sm">
                                            <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                        </a>

                                        <a href="{{ route($base_route.'.edit', ['id' => encrypt($staff->id)]) }}" class="btn-success btn-sm">
                                            <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                        </a>

                                        <a href="{{ route($base_route.'.delete', ['id' => encrypt($staff->id)]) }}" class="btn-danger btn-sm bootbox-confirm" >
                                            <i class="fa fa-trash bigger-130" title="Delete"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('library.member.quick-membership', ['reg_no' => $staff->reg_no,'user_type' => 2,'status' => 'active',]) }}" class="btn-primary btn-sm">
                                            <i class="ace-icon fa fa-book bigger-130"></i>
                                        </a>

                                        <a class="open-ActiveAgain btn-primary btn-sm" data-toggle="modal"
                                           data-target="#activeAgain"
                                           data-id="{{ encrypt($staff->id) }}"
                                           data-reg="{{ $staff->reg_no }}">
                                         <span>
                                             <i class="ace-icon fa fa-bed bigger-130"></i>
                                         </span>
                                        </a>

                                        <a class="open-TransportActiveAgain btn-primary btn-sm" data-toggle="modal"
                                           data-target="#TransportActiveAgain"
                                           data-id="{{ encrypt($staff->id) }}"
                                           data-reg="{{ $staff->reg_no }}">
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
                            <td colspan="11">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            {!! Form::close() !!}
        </div>
        </div>
    </div>
</div>