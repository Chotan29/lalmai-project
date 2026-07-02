    <div class="col-xs-12">
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="11">{!! $data['customer']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>RegNo</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th >Mobile</th>
                        <th>{{ __('common.status')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['customer']) && $data['customer']->count() > 0)
                    @php($i=1)
                    @foreach($data['customer'] as $customer)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($customer->id) }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td><a href="{{ route($base_route.'.view', ['id' => encrypt($customer->id)]) }}">{{ $customer->reg_no }}</a></td>
                            <td><a href="{{ route($base_route.'.view', ['id' => encrypt($customer->id)]) }}"> {{ $customer->name }}</a></td>
                            <td>{{ $customer->address }}</td>
                            <td>{{ $customer->mobile_1 }} </td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $customer->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $customer->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => encrypt($customer->id)]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => encrypt($customer->id)]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route($base_route.'.view', ['id' => encrypt($customer->id)]) }}" class="btn-primary btn-sm btn-primary">
                                        <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.edit', ['id' => encrypt($customer->id)]) }}" class="btn-primary btn-sm btn-success">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($customer->id)]) }}" class="btn-primary btn-sm btn-danger bootbox-confirm" >
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
