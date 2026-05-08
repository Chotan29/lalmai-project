<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
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
                        <th>Reg. Num.</th>
                        <th>Reg. Date</th>
                        <th>University Reg.</th>
                        <th>{{__('form_fields.student.fields.name_of_student')}}</th>

                        <th>Date Of Birth</th>
                        <th>Gender</th>
                        <th>Blood Group</th>

                        <th>Nationality</th>
                        <th>ID 1.</th>
                        <th>PAN</th>
                        <th>Ration Card / Voter Card</th>
                        <th>ID 4</th>

                        <th>Religion</th>
                        <th>Caste</th>
                        <th>Mother Tongueue</th>

                        <th>Email</th>
                        <th>Address</th>
                        <th>State</th>
                        <th>Country</th>

                        <th>Temp. Address</th>
                        <th>Temp. State</th>
                        <th>Temp. Country</th>

                        <th>Home Phone</th>
                        <th>Mobile Number</th>

                        <th>Academic Status</th>
                        <th>{{ __('common.status')}}</th>

                        <th>Grand Father Name</th>
                        <th>Father Name</th>
                        <th>Father Eligibility</th>
                        <th>Father Occupation</th>
                        <th>Father Office</th>
                        <th>Father Office Number</th>
                        <th>Father Resident Number</th>
                        <th>Father Mobile</th>
                        <th>Father Email</th>

                        <th>Mother Name</th>
                        <th>Mother Eligibility</th>
                        <th>Mother Occupation</th>
                        <th>Mother Office</th>
                        <th>Mother Office Number</th>
                        <th>Mother Resident Number</th>
                        <th>Mother Mobile</th>
                        <th>Mother Email</th>

                        <th>Guardian Name</th>
                        <th>Guardian Eligibility</th>
                        <th>Guardian Occupation</th>
                        <th>Guardian Office</th>
                        <th>Guardian Office Number</th>
                        <th>Guardian Resident Number</th>
                        <th>Guardian Mobile</th>
                        <th>Guardian Email</th>
                        <th>Guardian Relation</th>
                        <th>Guardian Address</th>

                        <th>ExtraInfo</th>

                        <th>Total Fee Per Year</th>
                        <th>Bank Name</th>
                        <th>Bank Code</th>
                        <th>Bank Account Number</th>
                        <th>Admission Portal Login ID</th>
                        <th>Admission Portal Login Password</th>

                        <th>Scholarship</th>
                        <th>Scholarship Application ID</th>
                        <th>Scholarship User Name</th>
                        <th>Scholarship Password</th>

                        <th>Placement Company</th>
                        <th>Placement Date</th>
                        <th>Placement Salary</th>
                        <th>Placement Location</th>
                        <th>Placement Designation</th>


                        <th>Degree Info</th>

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
                            <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a></td>
                            <td>{{ \Carbon\Carbon::parse($student->reg_date)->format('Y-m-d')}} </td>
                            <td>{{ $student->university_reg }}</td>
                            <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                            <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d')}}</td>

                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->blood_group }}</td>

                            <td>{{ $student->nationality }}</td>
                            <td>{{ $student->national_id_1 }}</td>
                            <td>{{ $student->national_id_2 }}</td>
                            <td>{{ $student->national_id_3 }}</td>
                            <td>{{ $student->national_id_4 }}</td>

                            <td>{{ $student->religion }}</td>
                            <td>{{ $student->caste }}</td>
                            <td>{{ $student->mother_tongue }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->address }}</td>
                            <td>{{ $student->state }}</td>
                            <td>{{ $student->country }}</td>
                            <td>{{ $student->temp_address }}</td>
                            <td>{{ $student->temp_state }}</td>
                            <td>{{ $student->temp_country }}</td>
                            <td>{{ $student->home_phone }}</td>
                            <td>
                                @if(isset($student->mobile_2))
                                    {{ $student->mobile_1.', '.$student->mobile_2}}
                                @else
                                    {{ $student->mobile_1}}
                                @endif
                            </td>
                            <td>{{ ViewHelper::getStudentAcademicStatusId($student->academic_status) }}</td>
                            <td>{{ $student->status=="active"?"Active":"In-Active" }}</td>

                            <td>{{ $student->grandfather_first_name.' '.$student->grandfather_middle_name.' '. $student->grandfather_last_name }}</td>
                            <td>{{ $student->father_first_name.' '.$student->father_middle_name.' '. $student->father_last_name }}</td>
                            <td>{{ $student->father_eligibility }}</td>
                            <td>{{ $student->father_occupation }}</td>
                            <td>{{ $student->father_office }}</td>
                            <td>{{ $student->father_office_number }}</td>
                            <td>{{ $student->father_residence_number }}</td>
                            <td>{{ $student->father_mobile_1.', '.$student->father_mobile_1 }}</td>
                            <td>{{ $student->father_email }}</td>

                            <td>{{ $student->mother_first_name.' '.$student->mother_middle_name.' '. $student->mother_last_name }}</td>
                            <td>{{ $student->mother_eligibility }}</td>
                            <td>{{ $student->mother_occupation }}</td>
                            <td>{{ $student->mother_office }}</td>
                            <td>{{ $student->mother_office_number }}</td>
                            <td>{{ $student->mother_residence_number }}</td>
                            <td>{{ $student->mother_mobile_1.', '.$student->mother_mobile_1 }}</td>
                            <td>{{ $student->mother_email }}</td>

                            <td>{{ $student->guardian_first_name.' '.$student->guardian_middle_name.' '. $student->guardian_last_name }}</td>
                            <td>{{ $student->guardian_eligibility }}</td>
                            <td>{{ $student->guardian_occupation }}</td>
                            <td>{{ $student->guardian_office }}</td>
                            <td>{{ $student->guardian_office_number }}</td>
                            <td>{{ $student->guardian_residence_number }}</td>
                            <td>{{ $student->guardian_mobile_1.', '.$student->guardian_mobile_1 }}</td>
                            <td>{{ $student->guardian_email }}</td>
                            <td>{{ $student->guardian_relation }}</td>
                            <td>{{ $student->guardian_address }}</td>

                            <td>{{ $student->extra_info }}</td>

                            <td>{{ $student->total_fee_per_year }}</td>
                            <td>{{ $student->bank_name }}</td>
                            <td>{{ $student->bank_code }}</td>
                            <td>{{ $student->bank_account_number }}</td>
                            <td>{{ $student->admission_portal_login_id }}</td>
                            <td>{{ $student->admission_portal_login_password }}</td>

                            <td>{{ $student->scholarships_id }}</td>
                            <td>{{ $student->scholarship_application_id }}</td>
                            <td>{{ $student->scholarship_user_name }}</td>
                            <td>{{ $student->scholarship_password }}</td>

                            <td>{{ ViewHelper::getPlacementCompanyById($student->placements_id) }}</td>
                            <td>{{ $student->placement_date }}</td>
                            <td>{{ $student->placement_salary }}</td>
                            <td>{{ $student->placement_location }}</td>
                            <td>{{ $student->placement_designation }}</td>

                            <td>
                                @php($degrees = $student->studentDegrees()->get())
                                @if($degrees && $degrees->count()>0)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Degree</th>
                                            <th>Obtain Mark</th>
                                            <th>Total Mark</th>
                                            <th>Receive Date</th>
                                            <th>Issue Date</th>
                                        </tr>
                                        @foreach($degrees as $degree)
                                            <tr>
                                                <td>{{ ViewHelper::getDegreeById($degree->degrees_id) }}</td>
                                                <td>{{ $degree->obtained_mark }}</td>
                                                <td>{{ $degree->total_marks }}</td>
                                                <td>{{ Carbon\Carbon::parse($degree->receive_in_college_date)->format('Y-m-d') }}</td>
                                                <td>{{ Carbon\Carbon::parse($degree->issue_date)->format('Y-m-d') }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="51">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}

        </div>
    </div>
</div>