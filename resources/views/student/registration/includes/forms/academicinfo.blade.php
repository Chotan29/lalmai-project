<h4 class="header large lighter blue"><i class="fa fa-university" aria-hidden="true"></i>Academic Info</h4>

<div class="form-group">
    <div class="table-responsive">
        <table id="responsive" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10%">Examination</th>
                    <th width="5%">Year</th>
                    <th>Board/University</th>
                    <th width="10%">ROLL NO</th>
                    <th width="25%">Subject</th>
                    <th width="5%">Marks Obtained</th>
                    <th width="5%">Marks Maximum</th>
                    <th width="5%">Percentage</th>
                    <th width="5%">Grade Point</th>
                    <th width="5%">Grade Letter</th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="academicInfo_wrapper">
                @if (isset($data['academicInfo-html']))
                    {!! $data['academicInfo-html'] !!}
                @endif
                @if (isset($data['academicInfoRows-html']))
                    {!! $data['academicInfoRows-html'] !!}
                @endif
            </tbody>
        </table>
    </div>
</div>

