<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="clearfix">
            <span class="easy-link-menu">
                <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="Approve"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;Approve</a>
                <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="Reject"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp;Reject</a>
                <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{__('common.delete_button')}}</a>
            </span>

            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
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
                            <th>Application For</th>
                            <th>RegNo.</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>
                                <i class="icon-time bigger-110 hidden-480"></i>
                                Reg.Date
                            </th>
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
                                        {{ WebsiteViewHelper::getProgrammeById($row->registration_programmes_id) }}
                                    </td>

                                    <td>
                                        {{ $row->reg_no }}<br>
                                    </td>
                                    <td>
                                        {{ $row->name }}<br>
                                    </td>
                                    <td>
                                        {{ $row->cell_1 }}<br>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($row->reg_date)->format('d M, Y') }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-minier dropdown-toggle {{ $row->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $row->status == 'active'?"Approved":"Not Approve" }}
                                                <i class="ace-icon fa fa-caret-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => encrypt($row->id)]) }}" title="Active"><i class="ace-icon fa fa-check"></i> Approve</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => encrypt($row->id)]) }}" title="In-Active"><i class="ace-icon fa fa-remove"></i> Reject/UnApprove</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="btn-group">
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
                                <td colspan="8">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                            </tr>

                        @else
                            <tr><td colspan="8">No data fount.</td></tr>
                        @endif
                        </tbody>
                    </table>
                    {!! Form::close() !!}

                </div>
            </div>
    </div>
</div>
