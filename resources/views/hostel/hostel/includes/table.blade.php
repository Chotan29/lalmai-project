<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
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
                    <th>{{ __('common.s_n')}}</th>
                    <th>Name</th>
                    <th>Rooms</th>
                    <th>TotalBeds</th>
                    <th>Available Bed</th>
                    <th>Occupied Bed</th>
                    <th>{{ __('common.status')}}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['hostel']) && $data['hostel']->count() > 0)
                    @php($i=1)
                    @foreach($data['hostel'] as $hostel)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $hostel->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td><a href="{{ route($base_route.'.view', ['id' => $hostel->id]) }}">{{ $hostel->name }}</a> </td>
                            <td>{{ $hostel->rooms()->count() }}</td>
                            <td>{{ $hostel->beds()->count() }}</td>
                            <td>{{ $hostel->beds()->where('bed_status','=',1)->count() }}</td>
                            <td>{{ $hostel->beds()->where('bed_status','=',2)->count() }}</td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $hostel->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $hostel->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => $hostel->id]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => $hostel->id]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route($base_route.'.view', ['id' => $hostel->id]) }}" class="btn-success btn-sm">
                                        <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.edit', ['id' => $hostel->id]) }}" class="btn-success btn-sm">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => $hostel->id]) }}" class="btn-danger btn-sm bootbox-confirm" >
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
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>


