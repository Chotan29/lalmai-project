<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-university" aria-hidden="true"></i>&nbsp;Academic Info</h4>
        <div class="table-responsive">
            @if (isset($data['academicInfos']))

                    <table class="table table-striped table-bordered table-hover text-uppercase">
                        <thead>
                        <tr>
                            <th>Examination</th>
                            <th>Year</th>
                            <th>Institution</th>
                            <th>ROLL NO</th>
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                            <th>Marks Maximum</th>
                            <th>Percentage</th>
                            <th>Grade Point</th>
                            <th>Grade Letter</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data['academicInfos'] as $academicInfo)
                            @php
                                $examBoard = $academicInfo->board ?? ($academicInfo->examination ?? ($academicInfo->board_university ?? '-'));
                                $passYear = $academicInfo->pass_year ?? ($academicInfo->year_of_pass ?? '-');
                                $institution = $academicInfo->institution ?? '-';
                                $rawPercentage = $academicInfo->percentage;
                                $hasValidPercentage = $rawPercentage !== null && $rawPercentage !== '' && (float)$rawPercentage > 0;
                                if ($hasValidPercentage) {
                                    $resultPercent = $rawPercentage;
                                } else {
                                    $resultPercent = $academicInfo->division_grade
                                        ?? ($academicInfo->grade_letter
                                        ?? ($academicInfo->percentage_grade ?? '-'));
                                }
                            @endphp
                            <tr>
                                <td>{{ $examBoard }}</td>
                                <td class="text-center">{{ $passYear }}</td>
                                <td>{{ $institution }}</td>
                                <td class="text-center">{{ $academicInfo->roll_no }}</td>
                                <td>{{ $academicInfo->major_subjects }}</td>
                                <td class="text-center">{{ $academicInfo->mark_obtained }}</td>
                                <td class="text-center">{{ $academicInfo->maximum_mark }}</td>
                                <td class="text-center">{{ $resultPercent }}</td>
                                <td class="text-center">{{ $academicInfo->grade_point }}</td>
                                <td class="text-center">{{ $academicInfo->grade_letter }}</td>
                                <td class="text-center">
                                    @ability('super-admin', 'student-delete-academic-info')
                                    <a href="{{ route('student.delete-academicInfo', ['id' => $academicInfo->id]) }}" class="btn-danger btn-sm bootbox-confirm align-right" >
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                    @endability
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
            @endif
        </div>
    </div>

</div><!-- /.row -->