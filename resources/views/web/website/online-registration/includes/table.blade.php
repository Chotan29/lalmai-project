<div class="row">
    <div class="col-xs-12">
        @include('weadmin.includes.data_table_header')
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
            <div class="table-responsive">
                <!-- div.table-responsive -->
                <div class="table-responsive">
                    {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                    <table {{--id="dynamic-table" --}}class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">
                                <label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </th>
                            <th>Programme</th>
                            <th>RegNo.</th>
                            <th>Name</th>
                            <th>
                                <i class="icon-time bigger-110 hidden-480"></i>
                                Reg.Date
                            </th>
                            <th class="hidden-480">Status</th>
                            <th></th>
                        </tr>
                        {{--<tr>
                            <th class="center">&nbsp;</th>
                            <th>
                                <select name="programme" class="form-control"  data-placeholder="Choose a Programme..." >
                                    <option value="">  </option>
                                    @foreach( $data['programmes'] as $key => $programme)
                                        <option value="{{ $key }}">{{ $programme }}</option>
                                    @endforeach
                                </select>
                            </th>

                            <th>{!! Form::text('title', request('title'), ['class' => 'form-control border-form', 'placeholder' => 'Title']) !!}</th>

                            <th>
                                <span class="form-inline">
                                    {!! Form::text('reg_date_start', request('reg_date_start'), ['class' => 'input-medium date-picker', 'placeholder' => 'From']) !!}
                                    &nbsp; To &nbsp;
                                    {!! Form::text('reg_date_end', request('reg_date_end'), ['class' => 'input-medium date-picker', 'placeholder' => 'To']) !!}
                                </span>
                            <th>{!! Form::select('status', ['all' => 'All', 'active' => 'Active', 'in-active' => 'Inactive'], request('status'), ['class' => 'from-control']) !!}</th>
                            <th>
                                <button class="btn btn-xs btn-info" id="filter-btn">
                                    <i class="icon-filter bigger-120">&nbsp;Filter</i>
                                </button>
                            </th>
                        </tr>--}}
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
                                        {{ ViewHelper::getProgrammeById($row->registration_programmes_id) }}
                                    </td>

                                    <td>
                                        {{ $row->reg_no }}<br>
                                    </td>
                                    <td>
                                        {{ $row->name }}<br>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($row->updated_at)->format('d M, Y') }}
                                    </td>
                                    <td class="hidden-480">
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $row->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $row->status == 'active'?"Active":"In Active" }}
                                                <span class="icon-caret-down icon-on-right"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => encrypt($row->id)]) }}" title="Active"><i class="icon-check bigger-120"></i> Active</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => encrypt($row->id)]) }}" title="In-Active"><i class="icon-remove bigger-120"></i> InActive</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td>
                                        <div class=" btn-group">

                                            <a href="{{ route($base_route.'.view', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-success" target="_blank">
                                                <i class="icon-eye-open bigger-120"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.edit', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-info">
                                                <i class="icon-edit bigger-120"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-danger bootbox-confirm">
                                                <i class="icon-trash bigger-120"></i>
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
