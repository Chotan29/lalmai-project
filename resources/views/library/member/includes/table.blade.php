<div class="form-horizontal">
<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
    <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="10">{!! $data['member']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>User Type</th>
                        <th>Reg. No. </th>
                        <th>{{ __('common.status')}}</th>
                        <th>{{ __('common.actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['member']) && $data['member']->count() > 0)
                    @php($i=1)
                    @foreach($data['member'] as $member)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $member->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>{{ ViewHelper::getLibUserTypeId($member->user_type) }} </td>
                            <td>
                                @if($member->user_type == 1)
                                    <a href="{{ route('library.student.view', ['id' => $member->member_id]) }}">
                                        {{ ViewHelper::getStudentById($member->member_id) }}
                                    </a>
                                @else
                                    <a href="{{ route('library.staff.view', ['id' => $member->member_id]) }}">
                                        {{ ViewHelper::getStaffById($member->member_id) }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $member->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $member->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => $member->id]) }}"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>

                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => $member->id]) }}"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                        </li>
                                    </ul>
                                </div>

                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => $member->id]) }}">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => $member->id]) }}"  class="btn-danger btn-sm bootbox-confirm">
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="10">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

