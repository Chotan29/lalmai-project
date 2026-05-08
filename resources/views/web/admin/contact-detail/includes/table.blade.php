<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
    <!-- div.table-responsive -->
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                        <tr>
                            <th class="center">
                                <label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </th>
                            <th>Group</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>MobileNo.</th>
                            <th>{{ __('common.status')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (isset($data['rows']) && $data['rows']->count() > 0)

                            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}

                            @foreach($data['rows'] as $row)

                                <tr>
                                    <td class="center">
                                        <label>
                                            <input type="checkbox" name="chkIds[]" value="{{ encrypt($row->id) }}" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td>
                                        {{ WebsiteViewHelper::getContactGroupById($row->contact_groups_id) }}
                                    </td>
                                    <td>
                                        {{ $row->name }}<br>
                                    </td>
                                    <td>
                                        {{ $row->address }}<br>
                                    </td>
                                    <td>
                                        {{ $row->cell_1 }}<br>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-minier dropdown-toggle {{ $row->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $row->status == 'active'?"Active":"In Active" }}
                                                <i class="ace-icon fa fa-caret-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => encrypt($row->id)]) }}" title="Active"><i class="ace-icon fa fa-check"></i> Active</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => encrypt($row->id)]) }}" title="In-Active"><i class="ace-icon fa fa-remove"></i> InActive</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td>
                                        <div class=" btn-group">
                                            <a href="{{ route($base_route.'.view', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-success">
                                                <i class="ace-icon fa fa-eye"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.edit', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-info">
                                                <i class="ace-icon fa fa-edit"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-danger bootbox-confirm">
                                                <i class="ace-icon fa fa-trash"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>

                            @endforeach

                            {!! Form::close() !!}

                            <tr>
                                <td colspan="7">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                            </tr>

                        @else
                            <tr><td colspan="7">No data fount.</td></tr>
                        @endif
                        </tbody>
                    </table>
                    {!! Form::close() !!}

                </div>
            </div>
    </div>
</div>
