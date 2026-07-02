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
                    <th>{{ $panel }}</th>
                    <th>{{ __('common.status')}}</th>
                    <th>{{__('form_fields.student.fields.faculty')}}</th>
                </tr>
                </thead>
                <tbody>
                    @if (isset($data['department']) && $data['department']->count() > 0)
                        @php($i=1)
                        @foreach($data['department'] as $department)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ encrypt($department->id )}}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                <td>
                                    <b>{{ $department->department }}</b>
                                    <div class="action-buttons" style="float: right;">
                                        <a class="green" href="{{ route($base_route.'.edit', ['id' => encrypt($department->id)]) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                        </a>

                                        <a href="{{ route($base_route.'.delete', ['id' => encrypt($department->id)]) }}" class="red bootbox-confirm">
                                            <i class="fa fa-trash bigger-130"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $department->status == 'active'?"btn-info":"btn-warning" }}" >
                                            {{ $department->status == 'active'?"Active":"In Active" }}
                                            <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route($base_route.'.active', ['id' => encrypt($department->id)]) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            </li>

                                            <li>
                                                <a href="{{ route($base_route.'.in-active', ['id' => encrypt($department->id)]) }}"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </div>

                                </td>
                                <td >
                                    @if(isset($department->faculties))
                                        @php($j=1)
                                        @foreach($department->faculties as $facultyHead)
                                            {{$j.'. '.$facultyHead->faculty}} - [{{$facultyHead->id}}]
                                            <a class="green" href="{{ route('faculty.edit', ['id' => encrypt($facultyHead->id)]) }}" target="_blank" style="float:right;">
                                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                                            </a>
                                            <hr class="hr-2">
                                            @php($j++)
                                        @endforeach
                                    @endif
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
