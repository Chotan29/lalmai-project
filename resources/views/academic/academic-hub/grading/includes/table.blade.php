<div class="row">
    <div class="col-md-12 col-xs-12">
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
                    <th>Group</th>
                    <th>Grade Scale Detail</th>
                    <th>{{ __('common.status')}}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['grading']) && $data['grading']->count() > 0)
                    @php($i=1)
                    @foreach($data['grading'] as $grading)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($grading->id )}}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>
                                {{ $grading->title }}
                            </td>
                            <td>
                                <table class="table table-striped table-bordered table-hover">
                                @if(isset($grading->gradingScale))
                                        <tr>
                                            <th>Name</th>
                                            <th>From(%)</th>
                                            <th>To(%)</th>
                                            <th>Point</th>
                                            <th>Remark</th>
                                        </tr>
                                    @foreach($grading->gradingScale as $scale)
                                        <tr>
                                            <td> {{ $scale->name }}</td>
                                            <td> {{ $scale->percentage_from }}</td>
                                            <td> {{ $scale->percentage_to }}</td>
                                            <td> {{ $scale->grade_point }}</td>
                                            <td> {{ $scale->description }}</td>
                                        </tr>
                                    @endforeach
                                @endif


                                </table>
                            </td>
                            {{--<td>{{ $grading->status }}</td>--}}
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $grading->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $grading->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('grading.active', ['id' => encrypt($grading->id)]) }}"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>
                                        </li>

                                        <li>
                                            <a href="{{ route('grading.in-active', ['id' => encrypt($grading->id)]) }}"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                        </li>
                                    </ul>
                                </div>

                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => encrypt($grading->id)]) }}">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($grading->id)]) }}"  class="btn-danger btn-sm bootbox-confirm">
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}
        </div>
    </div>