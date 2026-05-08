<table>
    <thead>
     <tr>
        <th>{{ __('common.s_n')}}</th>
        <th>Reg. Num.</th>
        <th>Join Date</th>
        <th>Staff Name</th>
        <th>Designation</th>
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>Date of Birth</th>
        <th>Gender</th>
        <th>BloodGroup</th>
        <th>Nationality</th>
        <th>Mother Tongueue</th>
        <th>Address</th>
        <th>State</th>
        <th>Country</th>
        <th>Temp. Address</th>
        <th>Temp. State</th>
        <th>Temp. Country</th>
        <th>Home Phone</th>
        <th>Mobile 1</th>
        <th>Mobile 2</th>
        <th>Email</th>
        <th>Qualification</th>
        <th>Experience</th>
        <th>Experience Info</th>
        <th>Other Info</th>
        <th>{{ __('common.status')}}</th>
    </tr>
    </thead>
    <tbody>
        @if (isset($staffs) && $staffs->count() > 0)
            @php($i=1)
            @foreach($staffs as $staff)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $staff->reg_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($staff->join_date)->format('Y-m-d')}} </td>
                    <td>{{ $staff->first_name.' '.$staff->middle_name.' '. $staff->last_name }}</td>
                    <td>{{ ViewHelper::getDesignationId($staff->designation) }}</td>
                    <td>{{ $staff->father_name }}</td>
                    <td>{{ $staff->mother_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($staff->date_of_birth)->format('Y-m-d')}} </td>
                    <td>{{ $staff->gender }}</td>
                    <td>{{ $staff->blood_group }}</td>
                    <td>{{ $staff->nationality }}</td>
                    <td>{{ $staff->mother_tongue }}</td>
                    <td>{{ $staff->address }}</td>
                    <td>{{ $staff->state }}</td>
                    <td>{{ $staff->country }}</td>
                    <td>{{ $staff->temp_address }}</td>
                    <td>{{ $staff->temp_state }}</td>
                    <td>{{ $staff->temp_country }}</td>
                    <td>{{ $staff->home_phone }}</td>
                    <td>{{ $staff->mobile_1}} </td>
                    <td>{{ $staff->mobile_2}} </td>
                    <td>{{ $staff->email }}</td>
                    <td>{{ $staff->qualification }}</td>
                    <td>{{ $staff->experience }}</td>
                    <td>{{ $staff->experience_info }}</td>
                    <td>{{ $staff->other_info }}</td>
                    <td>{{ $staff->status=="active"?"Active":"In-Active" }}</td>
                </tr>
                @php($i++)
            @endforeach
        @endif
    </tbody>
</table>