<div class="table-responsive">
    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>{{ __('common.s_n')}}</th>
                <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                <th>Staff Name</th>
                <th>Designation</th>
                <th>DateofBirth</th>
                {{--<th>Age</th>--}}
            </tr>
        </thead>
        <tbody>
        @if (isset($data['staff_birthday']) && $data['staff_birthday']->count() > 0)
            @php($i=1)
            @foreach($data['staff_birthday'] as $staff)
                <tr>
                    <td>{{ $i }}</td>
                    <td><a href="{{ route('staff.view', ['id' => encrypt($staff->id)]) }}">{{ $staff->reg_no }}</a></td>
                    <td><a href="{{ route('staff.view', ['id' => encrypt($staff->id)]) }}"> {{ $staff->first_name.' '.$staff->middle_name.' '. $staff->last_name }}</a></td>
                    <td>{{ ViewHelper::getDesignationId($staff->designation) }}</td>
                    <td>{{\Carbon\Carbon::parse($staff->date_of_birth)->format('Y-m-d')}}</td>
                    {{--<td>{{\Carbon\Carbon::parse($staff->date_of_birth)->age}}</td>--}}
                </tr>
                @php($i++)
            @endforeach
        @else
            <tr>
                <td colspan="11">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
            </tr>
        @endif
        </tbody>
        <tfoot>
            <tr>
                <td class="text-center" colspan="5">{!! $data['staff_birthday']->appends($data['filter_query'])->links() !!}</td>
            </tr>
        </tfoot>
    </table>
</div>
