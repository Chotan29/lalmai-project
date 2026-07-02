<div class="row">
    <div class="col-xs-12">
        @include('includes.data_table_header')
        <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="center" style="width: 50px;">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{__('academic.child.academic_level.child.semester')}}</th>
                        <th>{{ __('common.status')}}</th>
                        <th>Subjects</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['semester']) && $data['semester']->count() > 0)
                        @php($i=1)
                        @foreach($data['semester'] as $semester)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ encrypt($semester->id) }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i++ }}</td>
                                <td>
                                    <strong>{{ $semester->semester }} - [{{$semester->id}}]</strong>
                                    <div class="action-buttons" style="float:right;">
                                        <a class="green" href="{{ route($base_route.'.edit', ['id' => encrypt($semester->id)]) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                        </a>

                                        <a href="{{ route($base_route.'.delete', ['id' => encrypt($semester->id)]) }}" class="red bootbox-confirm">
                                            <i class="fa fa-trash bigger-130"></i>
                                        </a>
                                    </div>

                                </td>
                                <td>


                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $semester->status == 'active'?"btn-info":"btn-warning" }}" >
                                            {{ $semester->status == 'active'?"Active":"In Active" }}
                                            <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('semester.active', ['id' => encrypt($semester->id)]) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            </li>

                                            <li>
                                                <a href="{{ route('semester.in-active', ['id' => encrypt($semester->id)]) }}"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                            </li>
                                        </ul>
                                    </div>

                                </td>

                                <td>
                                    @if(isset($semester->subjects))
                                        <table>
                                            @php($j=1)
                                            @foreach($semester->subjects()->orderBy('code')->get() as $subject)
                                                <tr><td>

                                                        {{ $j.'. '.$subject->code.' - '. $subject->title  }}- [{{$subject->id}}]

                                                        <a class="green" href="{{ route('subject.edit', ['id' => encrypt($subject->id)]) }}" target="_blank" style="float:right;">
                                                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                                                        </a>
                                                        <hr class="hr-2">
                                                        @php($j++)
                                                    </td></tr>
                                            @endforeach
                                        </table>
                                    @endif
                                </td>


                            </tr>
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
