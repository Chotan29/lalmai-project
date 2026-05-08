<div class="table-responsive">
    {{--<h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>--}}
    @include('web.admin.includes.bulk_action_button_link')
    {{--<div class="table-header">
        {{ $panel }}  Record list on table. Filter list using search box as your Wish.
    </div>--}}
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="center">
                    <label>
                        <input type="checkbox" class="ace" />
                        <span class="lbl"></span>
                    </label>
                </th>
                <th>SN</th>
                <th>Name</th>
                <th>Email</th>
                <th>{{ __('common.status')}}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if (isset($data['rows']) && $data['rows']->count() > 0)
                @php
                    $i=1;
                @endphp
                {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                @foreach($data['rows'] as $row)
                    <tr>
                        <td class="center">
                            <label>
                                <input type="checkbox" name="chkIds[]" value="{{ $row->id }}" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </td>
                        <td>
                            @php
                                echo $i;
                            @endphp
                        </td>
                        <td>
                            {{ $row->name }}
                        </td>

                        <td>
                            {{ $row->email }}
                        </td>
                        <td>
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn btn-minier dropdown-toggle {{ $row->status == 'active'?"btn-info":"btn-warning" }}" >
                                    {{ $row->status == 'active'?"Active":"In Active" }}
                                    <i class="ace-icon fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route($base_route.'.active', ['id' => $row->id]) }}" title="Active"><i class="ace-icon fa fa-check"></i> Active</a>
                                    </li>

                                    <li>
                                        <a href="{{ route($base_route.'.in-active', ['id' => $row->id]) }}" title="In-Active"><i class="ace-icon fa fa-remove"></i> InActive</a>
                                    </li>
                                </ul>
                            </div>
                        </td>

                        <td>
                            <div class=" btn-group">
                                <a href="{{ route('web.admin.subscribers.delete', ['id' => $row->id]) }}" class="btn btn-xs btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-trash"></i>
                                </a>

                            </div>

                        </td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
                {!! Form::close() !!}
                    <tr>
                        <td colspan="6">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
            @else
                <tr><td colspan="6">No data found.</td></tr>
            @endif
        </tbody>
    </table>
</div><!-- /.table-responsive -->