{!! Form::open(['route' => $base_route.'.store', 'id' => 'salary_add_form']) !!}
{!! Form::hidden('payroll_type', 'attendance_payroll',['id'=>'payroll_type']) !!}

    <div class="row">
        <div class="col-xs-12">
            <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Staffs List</h4>
            <div class="table-responsive">
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
                            <th>Join Date</th>
                            <th>Name of Staff</th>
                            <th>Designation</th>
                            <th>Contact Number</th>
                            <th>Basic Salary</th>
                            <th width="10%">Number Of Days / Months</th>
                            <th>TagLine</th>
                            <th>Due Date</th>

                        </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['staff']) && $data['staff']->count() > 0)
                        @php($i=1)
{{--                        {!! Form::hidden('id', $data['row']->id) !!}--}}
                        @foreach($data['staff'] as $staff)
                            <tr>
                                <td class="center first-child">
                                    <label>
                                        <input type="checkbox" name="chkIds[]" value="{{ $staff->id }}" class="ace" />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>{{ $i }}</td>
                                <td>{{ ViewHelper::getStaffById($staff->id)}}</td>
                                <td>{{ \Carbon\Carbon::parse($staff->join_date)->format('Y-m-d')}} </td>
                                <td>{{ $staff->first_name.' '.$staff->middle_name.' '.$staff->last_name }} </td>
                                <td>{{ ViewHelper::getDesignationId($staff->designation) }}</td>
                                <td><div class="label label-info arrowed">{{ $staff->mobile_1 }} </div></td>
                                <td>
                                    {!! Form::number('basic_salary[]',$staff->basic_salary, ['class' => 'form-control', 'required']) !!}
                                </td>
                                <td>
                                    {!! Form::number('time[]', null, ['class' => 'form-control', 'required']) !!}
                                </td>
                                <td>
                                    {!! Form::text('tag_line[]', null, ['class' => 'col-xs-10 col-sm-11', 'required']) !!}
                                </td>
                                <td>
                                    {!! Form::text('due_date[]', null, ["placeholder" => "YYYY-MM-DD", "class" => "col-xs-10 col-sm-11 input-mask-date date-picker", "required"]) !!}
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11">No Staffs data found. Please Filter Staffs to show. </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="clearfix form-actions">
        <div class="col-md-12 align-center">
            <button class="btn" type="reset">
                <i class="fa fa-undo bigger-110"></i>
                Reset
            </button>
            <button class="btn btn-success" type="submit" id="payroll-add-btn">
                <i class="fa fa-save bigger-110"></i>
                Add Payroll
            </button>
        </div>
    </div>
    <div class="hr-double hr-18"></div>


{!! Form::close() !!}

