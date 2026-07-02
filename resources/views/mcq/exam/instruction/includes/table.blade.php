
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
                            <th width="70%">{{ $panel }}</th>
                            <th>{{ __('common.status')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data['exam_instructions']) && $data['exam_instructions']->count() > 0)
                            @php($i=1)
                            @foreach($data['exam_instructions'] as $instruction)
                                <tr>
                                    <td class="center first-child">
                                        <label>
                                            <input type="checkbox" name="chkIds[]" value="{{ $instruction->id }}" class="ace" />
                                            <span class="lbl"></span>
                                        </label>
                                    </td>
                                    <td>{{ $i }}</td>
                                    <td>
                                        <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <a href="#{{$instruction->id}}" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                        <i class="ace-icon fa fa-arrow-down"></i>                                                        &nbsp;
                                                        {{ $instruction->title }} - [{{ $instruction->id }}]
                                                    </a>
                                                </div>

                                                <div class="panel-collapse collapse" id="{{$instruction->id}}" style="height: 0px;">
                                                    <div class="panel-body">
                                                        {!! $instruction->description !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $instruction->status == 'active'?"btn-info":"btn-warning" }}" >
                                                {{ $instruction->status == 'active'?"Active":"In Active" }}
                                                <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                            </button>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route($base_route.'.active', ['id' => $instruction->id]) }}"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route($base_route.'.in-active', ['id' => $instruction->id]) }}"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => $instruction->id]) }}">
                                                <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                            </a>

                                            <a href="{{ route($base_route.'.delete', ['id' => $instruction->id]) }}" class="btn-danger btn-sm bootbox-confirm">
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