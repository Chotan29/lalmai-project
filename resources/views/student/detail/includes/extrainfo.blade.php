<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <td colspan="2"><h4 class="header large lighter blue text-uppercase"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Fee Info</h4>
                    </td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Total Fee Per Year</td>
                    <td>{{ $data['student']->total_fee_per_year }}</td>
                </tr>
                <tr>
                    <td colspan="2"><h4 class="header large lighter blue text-uppercase"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Bank Detail</h4></td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Bank Name</td>
                    <td>{{ $data['student']->bank_name }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Bank Code</td>
                    <td>{{ $data['student']->bank_code }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Bank Account No.</td>
                    <td>{{ $data['student']->bank_account_number }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Admission Portal Login ID</td>
                    <td>{{ $data['student']->admission_portal_login_id }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Admission Portal Login Password</td>
                    <td>{{ $data['student']->admission_portal_login_password }}</td>
                </tr>
                <tr>
                    <td colspan="2"><h4 class="header large lighter blue text-uppercase"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Scholarship Info</h4></td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Scholarship</td>
                    <td>{{ ViewHelper::getScholarshipById($data['student']->scholarships_id) }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Scholarship Application Id</td>
                    <td>{{ $data['student']->scholarship_application_id }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Scholarship User Name</td>
                    <td>{{ $data['student']->scholarship_user_name }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Scholarship Password</td>
                    <td>{{ $data['student']->scholarship_password }}</td>
                </tr>
                <tr>
                    <td colspan="2"><h4 class="header large lighter blue text-uppercase"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Placement Detail</h4></td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Placement Company</td>
                    <td>{{ ViewHelper::getPlacementCompanyById($data['student']->placements_id) }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Placement Date</td>
                    <td>{{ $data['student']->placement_date }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Placement Salary</td>
                    <td>{{ $data['student']->placement_salary }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Placement Location</td>
                    <td>{{ $data['student']->placement_location }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase bold-text" width="25%">Placement Designation</td>
                    <td>{{ $data['student']->placement_designation }}</td>
                </tr>
            </table>

        </div>
    </div>

</div><!-- /.row -->


<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue text-uppercase"><i class="fa fa-university" aria-hidden="true"></i>&nbsp;Degrees Info</h4>
        <div class="table-responsive">
            @if($data['degrees'] && $data['degrees']->count() >0)
                <table class="table table-bordered">
                    <tr>
                        <th>Degree</th>
                        <th>Obtain Mark</th>
                        <th>Total Mark</th>
                        <th>Receive Date</th>
                        <th>Issue Date</th>
                    </tr>
                    @foreach($data['degrees'] as $degree)
                        <tr>
                            <td>{{ ViewHelper::getDegreeById($degree->degrees_id) }}</td>
                            <td>{{ $degree->obtained_mark }}</td>
                            <td>{{ $degree->total_marks }}</td>
                            <td>{{ isset($degree->receive_in_college_date)?Carbon\Carbon::parse($degree->receive_in_college_date)->format('Y-m-d'):'' }}</td>
                            <td>{{ isset($degree->issue_date)?Carbon\Carbon::parse($degree->issue_date)->format('Y-m-d'):'' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>

</div><!-- /.row -->