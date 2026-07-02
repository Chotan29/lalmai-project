<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
    <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="11">{!! $data['templates']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th class="center">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>Name of Certificate</th>
                        <th>Template</th>
                        <th>{{ __('common.status')}}</th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['templates']) && $data['templates']->count() > 0)
                    @php($i=1)
                    @foreach($data['templates'] as $template)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($template->id) }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td s>
                                {{$template->certificate}}
                                <hr>
                                <img id="avatar"  src="{{ asset('images'.DIRECTORY_SEPARATOR.'certificateBackground'.DIRECTORY_SEPARATOR.$template->background_image) }}" class="img-responsive" width="100px">
                            </td>
                            <td>
                                {!! $template->template !!}
                            </td>
                            <td class="">
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-minier dropdown-toggle {{ $template->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $template->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => encrypt($template->id)]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => encrypt($template->id)]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                              

                                    <a href="{{ route($base_route.'.edit', ['id' => encrypt($template->id)]) }}" class="btn btn-success btn-minier">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-130" title="Edit"></i> Edit
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($template->id)]) }}" class="btn btn-danger btn-minier bootbox-confirm" >
                                        <i class="ace-icon fa fa-trash-o bigger-130" title="Delete"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="12">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}
        </div>
    </div>
</div>


