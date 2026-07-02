<div class="row">
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
                        <th>Reg. Num.</th>
                        <th>Name of Student</th>
                        <th>{{__('form_fields.student.fields.semester')}}</th>
                        <th>Fee Head</th>
                        <th>Due Date</th>
                        <th>Fee Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['fee_master']) && $data['fee_master']->count() > 0)
                        @php($i=1)
                        @foreach($data['fee_master'] as $fee_master)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $fee_master->id }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                <td>{{ ViewHelper::getStudentById($fee_master->students_id)}}</td>
                                <td>{{ ViewHelper::getStudentNameById($fee_master->students_id)}}</td>
                                <td> {{ ViewHelper::getSemesterById($fee_master->semester)  }}</td>
                                <td>{{ ViewHelper::getFeeHeadById($fee_master->fee_head) }} </td>
                                <td>{{ \Carbon\Carbon::parse($fee_master->fee_due_date)->format('Y-m-d')}} </td>
                                <td>{{ $fee_master->fee_amount }} </td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-success btn-sm" href="{{ route($base_route.'.edit', ['id' => $fee_master->id]) }}">
                                            <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                        </a>

                                        <a href="{{ route($base_route.'.delete', ['id' => $fee_master->id]) }}" class="btn-danger btn-sm bootbox-confirm">
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
        </div>
        {!! Form::close() !!}
    </div>
</div>


