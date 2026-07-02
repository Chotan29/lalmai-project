<div class="row">
    <div class="col-xs-12">
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
        <div>
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
                            <th>{{ $panel }}</th>
                            <th>{{ __('common.status')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data['bed-status']) && $data['bed-status']->count() > 0)
                            @php($i=1)
                            @foreach($data['bed-status'] as $bed_status)
                                <tr>
                                    <td class="center first-child">
                                        <label>
                                            <input type="checkbox" name="chkIds[]" value="{{ $bed_status->id }}" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td>{{ $i }}</td>
                                    <td>{{ $bed_status->title }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $bed_status->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $bed_status->status == 'active'?"Active":"In Active" }}
                                                <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => encrypt($bed_status->id)]) }}"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => encrypt($bed_status->id)]) }}"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => encrypt($bed_status->id)]) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => encrypt($bed_status->id)]) }}" class="btn-danger btn-sm bootbox-confirm">
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                        </div>
                                    </td>
                                </tr>
                                @php($i++)
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            {!! Form::close() !!}
        </div>
    </div>
</div>