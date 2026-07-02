<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Student List</h4>
        <div class="clearfix">
            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{__('form_fields.student.fields.faculty')}}</th>
                        <th>{{__('form_fields.student.fields.semester')}}</th>
                        <th>Reg. No.</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>{{ __('common.status')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['student']) && $data['student']->count() > 0)
                    @php($i=1)
                    @foreach($data['student'] as $student)
                        <tr>
                            <td>{{ $i }}</td>
                            <td> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td>
                            <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                            <td><a href="{{ route($base_route.'.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a></td>
                            <td><a href="{{ route($base_route.'.view', ['id' => encrypt($student->id)]) }}"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                            <td><div class="label label-info arrowed">{{ $student->mobile_1 }}</div></td>
                            <td>
                                {{ ViewHelper::getAcademicStatus($student->academic_status)}}
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $student->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $student->status == 'active'?"Active":"In Active" }}
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn-primary btn-sm action-buttons ">
                                    <a class="white" href="{{ route($base_route.'.view', ['id' => encrypt($student->id)]) }}">
                                        <i class="ace-icon fa fa-calculator bigger-130"></i>&nbsp;
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>