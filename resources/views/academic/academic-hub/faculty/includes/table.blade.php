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
                    <th>CODE - {{ $panel }}</th>
                    <th>{{ __('common.status')}}</th>
                    <th>{{__('form_fields.student.fields.semester')}}</th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['faculty']) && $data['faculty']->count() > 0)
                    @php($i=1)
                    @foreach($data['faculty'] as $faculty)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($faculty->id )}}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>
                                <b>{{ $faculty->faculty }}-[{{$faculty->id}}]</b>
                                <div class="action-buttons" style="float: right;">
                                    <a class="green" href="{{ route($base_route.'.edit', ['id' => encrypt($faculty->id)]) }}">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($faculty->id)]) }}" class="red bootbox-confirm">
                                        <i class="fa fa-trash bigger-130"></i>
                                    </a>
                                </div>
                                <hr class="hr-2">
                                ID - {{$faculty->id}} , CODE - {{$faculty->faculty_code}}
                                <hr class="hr-2">
                                Major Subjects - {{$faculty->subject_count}}
                                <hr class="hr-2">
                                Grading System - {{ ViewHelper::getGradingTitle($faculty->gradingType_id) }}
                                <hr class="hr-2">
                                Scale - {{ $faculty->scale }}
                                <hr class="hr-2">
                                Duration - {{$faculty->duration}}
                                <hr class="hr-2">
                                Credit Required - {{$faculty->credit_required}}
                                <hr class="hr-2">
                                Registration Valid  - {{$faculty->registration_validate}}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $faculty->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $faculty->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('faculty.active', ['id' => encrypt($faculty->id)]) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route('faculty.in-active', ['id' => encrypt($faculty->id)]) }}"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>

                            </td>
                            <td >
                                @if(isset($faculty->semesters))
                                    @php($j=1)
                                    @foreach($faculty->semesters as $semester)
                                        {{ $j.'. '.$semester->semester.' - '.$semester->slug }} - [{{$semester->id}}]
                                        <a class="green" href="{{ route('semester.edit', ['id' => encrypt($semester->id)]) }}" target="_blank" style="float:right;">
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
