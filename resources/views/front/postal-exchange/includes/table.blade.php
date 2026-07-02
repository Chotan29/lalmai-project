<div class="form-horizontal">
    <div class="row">
        <div class="col-xs-12">
            @include('includes.data_table_header')
            <!-- div.table-responsive -->
                <div class="table-responsive">
                    {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                            <thead class="text-center">
                                <tr>
                                    <th class="center">
                                        <label class="pos-rel">
                                            <input type="checkbox" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </th>
                                    <th>{{ __('common.s_n')}}</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Ref</th>
                                    <th>From</th>
                                    <th>To </th>
                                    <th>Subject</th>
                                    <th>File</th>
                                    <th>{{ __('common.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($data['postal-exchange']) && $data['postal-exchange']->count() > 0)
                                    @php($i=1)
                                    @foreach($data['postal-exchange'] as $exchange)
                                        <tr>
                                            <td class="center first-child">
                                                <label>
                                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($exchange->id) }}" class="ace" />
                                                    <span class="lbl"></span>
                                                </label>
                                            </td>
                                            <td>{{ $i }}</td>
                                            <td class="text-uppercase"> {{$exchange->type}} </td>
                                            <td> {{\Carbon\Carbon::parse($exchange->date)->format('Y-M-d')}} </td>
                                            <td> {{$exchange->ref_no}} </td>
                                            <td> {{$exchange->from}} </td>
                                            <td> {{$exchange->to}} </td>
                                            <td> {{$exchange->subject}} </td>
                                            <td>
                                                @if(isset($exchange->attachment) && $exchange->attachment !=='')
                                                    <a href="{{ asset('postalExchange'.DIRECTORY_SEPARATOR.$exchange->attachment) }}" target="_blank">
                                                        <i class="ace-icon fa fa-download bigger-130"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => encrypt($exchange->id)]) }}">
                                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                                    </a>

                                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($exchange->id)]) }}" class="red bootbox-confirm">
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

