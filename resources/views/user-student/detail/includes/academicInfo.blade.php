<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title">
                <i class="bi bi-book"></i>
                <h5 class="mb-0">Academic Information</h5>
            </div>
            @ability('super-admin', 'student-add-academic-info')
            <a href="{{ route('student.add-academicInfo', ['id' => $data['student']->id]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle mr-1"></i> Add Academic Info
            </a>
            @endability
        </div>
        
        @if (isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
            <div class="row">
                @foreach($data['academicInfos'] as $academicInfo)
                @php
                    $examBoard = $academicInfo->board ?? ($academicInfo->examination ?? ($academicInfo->board_university ?? '-'));
                    $passYear = $academicInfo->pass_year ?? ($academicInfo->year_of_pass ?? '-');
                    $rollNo = $academicInfo->roll_no ?? ($academicInfo->symbol_no ?? '-');
                    $rawPercentage = $academicInfo->percentage;
                    $hasValidPercentage = $rawPercentage !== null && $rawPercentage !== '' && (float) $rawPercentage > 0;
                    $resultValue = $hasValidPercentage
                        ? $rawPercentage
                        : ($academicInfo->division_grade ?? ($academicInfo->grade_letter ?? ($academicInfo->percentage_grade ?? '-')));
                @endphp
                <div class="col-md-6">
                    <div class="info-card animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="font-weight-bold text-primary mb-1">{{ $examBoard }}</h6>
                                <p class="text-muted small mb-0">{{ $passYear }}</p>
                            </div>
                            <div class="action-buttons">
                                @ability('super-admin', 'student-delete-academic-info')
                                <a href="{{ route('student.delete-academicInfo', ['id' => $academicInfo->id]) }}" 
                                   class="btn btn-sm btn-outline-danger bootbox-confirm"
                                   data-toggle="tooltip" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                                @endability
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Institution</div>
                                    <div class="info-value">{{ $academicInfo->institution }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Board/University</div>
                                    <div class="info-value">{{ $examBoard }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Symbol No</div>
                                    <div class="info-value">{{ $rollNo }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Percentage/Grade</div>
                                    <div class="info-value">{{ $resultValue }}</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($academicInfo->major_subjects)
                        <div class="info-item">
                            <div class="info-label">Major Subjects</div>
                            <div class="info-value">{{ $academicInfo->major_subjects }}</div>
                        </div>
                        @endif
                        
                        @if($academicInfo->remark)
                        <div class="alert alert-light mt-3 mb-0">
                            <div class="info-label">Remarks</div>
                            <div class="info-value">{{ $academicInfo->remark }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info animate__animated animate__fadeIn">
                <i class="bi bi-info-circle mr-2"></i> No academic information found.
            </div>
        @endif
        
        @if (isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
        <div class="info-card mt-4 animate__animated animate__fadeIn">
            <div class="section-title mb-4">
                <i class="bi bi-list-check"></i>
                <h5 class="mb-0">Academic Qualifications Summary</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">S.N.</th>
                            <th>Examination Passed</th>
                            <th>Institution</th>
                            <th>Board/University</th>
                            <th>Year</th>
                            <th>Percentage/Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($data['academicInfos'] as $academicInfo)
                            @php
                                $examBoard = $academicInfo->board ?? ($academicInfo->examination ?? ($academicInfo->board_university ?? '-'));
                                $passYear = $academicInfo->pass_year ?? ($academicInfo->year_of_pass ?? '-');
                                $rawPercentage = $academicInfo->percentage;
                                $hasValidPercentage = $rawPercentage !== null && $rawPercentage !== '' && (float) $rawPercentage > 0;
                                $resultValue = $hasValidPercentage
                                    ? $rawPercentage
                                    : ($academicInfo->division_grade ?? ($academicInfo->grade_letter ?? ($academicInfo->percentage_grade ?? '-')));
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $examBoard }}</td>
                                <td>{{ $academicInfo->institution }}</td>
                                <td>{{ $examBoard }}</td>
                                <td>{{ $passYear }}</td>
                                <td>{{ $resultValue }}</td>
                            </tr>
                            <?php $i++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>