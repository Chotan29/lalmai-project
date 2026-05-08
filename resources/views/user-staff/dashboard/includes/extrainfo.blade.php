<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <td colspan="2"><h4 class="header large lighter blue text-uppercase"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Fee Info</h4>
                    </td>
                </tr>
                <tr>
                    <td>Qualification</td>
                    <td>{{ $data['staff']->qualification }}</td>
                </tr>
                <tr>
                    <td>Basic Salary</td>
                    <td>{{ $data['staff']->basic_salary }}</td>
                </tr>
                <tr>
                    <td>Date of Releaving</td>
                    <td>{{ $data['staff']->date_of_relieving }}</td>
                </tr>
                <tr>
                    <td>Qualification</td>
                    <td>{{ $data['staff']->date_of_rejoin }}</td>
                </tr>
                <tr>
                    <td>Experience</td>
                    <td>{{ $data['staff']->experience }}</td>
                </tr>
                <tr>
                    <td>Experience Info</td>
                    <td>{{ $data['staff']->experience_info }}</td>
                </tr>
                <tr>
                    <td>OtherInfo</td>
                    <td>{{ $data['staff']->other_info }}</td>
                </tr>
                <tr>
                    <td colspan="2"><h4 class="header large lighter blue text-uppercase"><i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;Bank Detail</h4></td>
                </tr>
                <tr>
                    <td>Bank Name</td>
                    <td>{{ $data['staff']->bank_name }}</td>
                </tr>
                <tr>
                    <td>Bank Code</td>
                    <td>{{ $data['staff']->bank_code }}</td>
                </tr>
                <tr>
                    <td>Bank Account No.</td>
                    <td>{{ $data['staff']->bank_account_number }}</td>
                </tr>

            </table>

        </div>
    </div>

</div><!-- /.row -->

