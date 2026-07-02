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
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>{{ __('common.status')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data['route']) && $data['route']->count() > 0)
                            @php($i=1)
                            @foreach($data['route'] as $route)
                                <tr>
                                    <td class="center first-child">
                                        <label>
                                            <input type="checkbox" name="chkIds[]" value="{{ $route->id }}" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td>{{ $i }}</td>
                                    <td>
                                        <table class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td colspan="2">
                                                    <div class="width-80 label label-info label-xlg arrowed-in arrowed-right">
                                                        <div class="inline position-relative">
                                                            <a href="#" class="user-title-label dropdown-toggle" data-toggle="dropdown">
                                                                <span class="white">{{ $route->title }}</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Rent</td>
                                                <td>{{ $route->rent }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">{{ $route->description }}</td>
                                            </tr>

                                        </table>
                                    </td>

                                    <td>
                                        <table class="table table-striped table-bordered table-hover">
                                            @if(isset($route->vehicle))
                                                @foreach($route->vehicle as $vehicle)
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="width-80 label label-info label-xlg">
                                                                <div class="inline position-relative">
                                                                    <a href="#" class="user-title-label dropdown-toggle" data-toggle="dropdown">
                                                                        <span class="white">{{ $vehicle->number }}</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Model</td>
                                                        <td>{{ $vehicle->model }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Type</td>
                                                        <td>{{ $vehicle->type }}</td>
                                                    </tr>

                                                @endforeach
                                            @endif
                                        </table>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $route->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $route->status == 'active'?"Active":"In Active" }}
                                                <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => $route->id]) }}"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => $route->id]) }}"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => $route->id]) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => $route->id]) }}" class="btn-danger btn-sm bootbox-confirm">
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