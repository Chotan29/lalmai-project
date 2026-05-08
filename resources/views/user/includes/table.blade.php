<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
    <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="7">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>User Detail</th>
                        <th>Image</th>
                        <th>User Type</th>
                        <th>{{ __('common.status')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['rows']) && $data['rows']->count() > 0)
                        {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                        @php($i = 1)
                        @foreach($data['rows'] as $row)
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ encrypt($row->id) }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>

                                <td>
                                    <b>{{ $row->name }}</b>
                                    <hr class="hr-2">
                                    {{ $row->contact_number }}
                                    <hr class="hr-2">
                                    {{ $row->email }}
                                    <hr class="hr-2">
                                    {{ $row->address }}
                                </td>
                                <td>
                                    @if ($row->profile_image)
                                        <img src="{{ asset('images/user/'.$row->profile_image) }}" width="80px">
                                    @else
                                        <p>No image</p>
                                    @endif
                                </td>
                                <td>
                                    @php($roles = $row->userRole)
                                    @if(isset($roles) && $roles->count() > 0)
                                        @foreach($roles as $role)
                                            <div class="label label-info arrowed-right arrowed-in">
                                                {{ $role->display_name }}
                                            </div>
                                            <hr class="hr-2">
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $row->status == 'active'?"btn-info":"btn-warning" }}" >
                                            {{ $row->status == 'active'?"Active":"In Active" }}
                                            <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route($base_route.'.active', ['id' => encrypt($row->id)]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            </li>

                                            <li>
                                                <a href="{{ route($base_route.'.in-active', ['id' => encrypt($row->id)]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </div>

                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-success btn-sm" href="{{ route($base_route.'.view', ['id' => encrypt($row->id)]) }}">
                                            <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                        </a>

                                        <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => encrypt($row->id)]) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                        </a>

                                        <a href="{{ route($base_route.'.delete', ['id' => encrypt($row->id)]) }}" class="btn-danger btn-sm bootbox-confirm">
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                        {!! Form::close() !!}

                    @else
                        <tr><td colspan="7">No data found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>