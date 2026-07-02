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
                            <th>Reg.No.</th>
                            <th>{{ $panel }}</th>
                            <th>{{ __('common.status')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data['document']) && $data['document']->count() > 0)
                            @php($i=1)
                            @foreach($data['document'] as $document)
                                <tr>
                                    <td class="center first-child">
                                        <label>
                                            <input type="checkbox" name="chkIds[]" value="{{ encrypt($document->id) }}" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td>{{ $i }}</td>
                                    <td><a href="{{ route('inventory.customer.view', ['id' => encrypt($document->member_id)]) }}"> {{  ViewHelper::getCustomerById( $document->member_id ) }}</a></td>
                                    <td><a href="{{ asset('documents'.DIRECTORY_SEPARATOR.'customer'.DIRECTORY_SEPARATOR.ViewHelper::getCustomerById( $document->member_id ).'/'.$document->file) }}" target="_blank">
                                            <i class="ace-icon fa fa-download bigger-120"></i> &nbsp;{{ $document->title }}
                                           </a></td>
                                    {{--<td>{{ $document->status }}</td>--}}
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $document->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $document->status == 'active'?"Active":"In Active" }}
                                                <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => encrypt(encrypt($document->id))]) }}"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => encrypt($document->id)]) }}"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                    <td>
                                        <div class="action-buttons">

                                            <a href="{{ route($base_route.'.edit', ['id' => encrypt($document->id)]) }}" class="btn-primary btn-sm btn-success">
                                                <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => encrypt($document->id)]) }}" class="btn-primary btn-sm btn-danger bootbox-confirm" >
                                                <i class="fa fa-trash bigger-130" title="Delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @php($i++)
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">No {{ $panel }} data found.</td>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            {!! Form::close() !!}
        </div>
    </div>
</div>