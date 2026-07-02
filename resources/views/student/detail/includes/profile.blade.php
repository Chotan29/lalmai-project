<div class="row">
    <div class="col-sm-12 align-right hidden-print">
        <div class="btn-group">
            @if($data['student']->status == 'active')
                <a href="javascript:void(0)"
                   class="btn btn-success btn-sm"
                   data-url="{{ route($base_route.'.in-active', ['id' => encrypt($data['student']->id)]) }}"
                   data-action="in-active"
                   onclick="studentStatusConfirm(this)">
                    <i class="ace-icon fa fa-toggle-on"></i> Active
                </a>
            @else
                <a href="javascript:void(0)"
                   class="btn btn-danger btn-sm"
                   data-url="{{ route($base_route.'.active', ['id' => encrypt($data['student']->id)]) }}"
                   data-action="active"
                   onclick="studentStatusConfirm(this)">
                    <i class="ace-icon fa fa-toggle-off"></i> Inactive
                </a>
            @endif
            @php
                $hasStudentPassword = \DB::table('users')
                    ->where('role_id', 6)
                    ->where('hook_id', $data['student']->id)
                    ->whereNotNull('password')
                    ->where('password', '!=', '')
                    ->exists();
            @endphp
            @if($hasStudentPassword)
                <span class="btn btn-success btn-sm" style="cursor:default;">
                    <i class="fa fa-lock"></i> Password Set
                </span>
            @else
                <span class="btn btn-danger btn-sm" style="cursor:default;">
                    <i class="fa fa-unlock"></i> No Password
                </span>
            @endif
            <a href="{{ route($base_route.'.edit', ['id' => encrypt($data['student']->id)]) }}" class="btn btn-primary btn-sm">
                <i class="ace-icon fa fa-pencil"></i> Edit Profile
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="ace-icon fa fa-print"></i> Print Profile
            </button>
            <button class="btn btn-primary btn-sm" id="download-profile">
                <i class="ace-icon fa fa-download"></i> Download PDF
            </button>
        </div>
    </div>
</div>

<div class="row student-profile-header">
    <div class="col-xs-12 col-sm-3 col-print-3">
        <div class="profile-card text-center">
            <div class="profile-image-container">
                @if($data['student']->student_image != '')
                    <img class="profile-image" src="{{ asset('images/' . $folder_name . '/' . $data['student']->student_image) }}" alt="Student Photo">
                @else
                    <img class="profile-image" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" alt="Default Profile">
                @endif
            </div>
            
            @if($data['student']->student_signature != '')
                <div class="signature-container">
                    <div class="signature-label">Signature</div>
                    <img class="signature-image" src="{{ asset('images/' . $folder_name . '/' . $data['student']->student_signature) }}" alt="Student Signature">
                </div>
            @endif
            
            <div class="qr-code-container hidden-print">
                @if($data['student']->reg_no != '')
                    {!! QrCode::size(150)->generate($data['student']->reg_no) !!}
                    <div class="qr-code-label">Student ID QR Code</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-9 col-print-9">
        <div class="student-basic-info">
            <h2 class="student-name">
                {{ $data['student']->first_name.' '.$data['student']->middle_name.' '.$data['student']->last_name }}
                <small class="text-muted">({{ $data['student']->reg_no }})</small>
            </h2>
            
            <div class="student-meta">
                @if($data['student']->faculty != "")
                    <span class="meta-item">
                        <i class="ace-icon fa fa-university"></i> 
                        {{ ViewHelper::getFacultyTitle($data['student']->faculty) }}
                    </span>
                @endif
                
                @if($data['student']->semester != "")
                    <span class="meta-item">
                        <i class="ace-icon fa fa-calendar"></i> 
                        {{ ViewHelper::getSemesterTitle($data['student']->semester) }}
                    </span>
                @endif
                
                @if($data['student']->batch != "")
                    <span class="meta-item">
                        <i class="ace-icon fa fa-users"></i> 
                        Batch: {{ ViewHelper::getStudentBatchId($data['student']->batch) }}
                    </span>
                @endif
            </div>
        </div>
        
        <div class="student-details-container">
            <!-- Personal Information -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="ace-icon fa fa-user"></i> Personal Information
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                @if($data['student']->reg_date != "")
                                    <dt>Registration Date:</dt>
                                    <dd>{{ \Carbon\Carbon::parse($data['student']->reg_date)->format('d/m/Y') }}</dd>
                                @endif
                                
                                @if($data['student']->date_of_birth != "")
                                    <dt>Date of Birth:</dt>
                                    <dd>{{ \Carbon\Carbon::parse($data['student']->date_of_birth)->format('d/m/Y') }} 
                                        (Age: {{ \Carbon\Carbon::parse($data['student']->date_of_birth)->age }} years)
                                    </dd>
                                @endif
                                
                                @if($data['student']->gender != "")
                                    <dt>Gender:</dt>
                                    <dd>{{ $data['student']->gender }}</dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                @if($data['student']->blood_group != "")
                                    <dt>Blood Group:</dt>
                                    <dd>{{ $data['student']->blood_group }}</dd>
                                @endif
                                
                                @if($data['student']->religion != "")
                                    <dt>Religion:</dt>
                                    <dd>{{ $data['student']->religion }}</dd>
                                @endif
                                
                                @if($data['student']->nationality != "")
                                    <dt>Nationality:</dt>
                                    <dd>{{ $data['student']->nationality }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="ace-icon fa fa-phone"></i> Contact Information
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                @if($data['student']->email != "")
                                    <dt>Email:</dt>
                                    <dd><a href="mailto:{{ $data['student']->email }}">{{ $data['student']->email }}</a></dd>
                                @endif
                                
                                @if($data['student']->mobile_1 != "")
                                    <dt>Mobile:</dt>
                                    <dd>
                                        {{ $data['student']->mobile_1 }}
                                        @if($data['student']->mobile_2 != "")
                                            , {{ $data['student']->mobile_2 }}
                                        @endif
                                    </dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                @if($data['student']->national_id_1 && $data['student']->national_id_2)
                                    <dt>{{ $data['student']->national_id_1 }}:</dt>
                                    <dd>{{ $data['student']->national_id_2 }}</dd>
                                @endif
                                
                                @if($data['student']->national_id_3 && $data['student']->national_id_4)
                                    <dt>{{ $data['student']->national_id_3 }}:</dt>
                                    <dd>{{ $data['student']->national_id_4 }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="ace-icon fa fa-home"></i> Address Information
                    </h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Permanent Address</h5>
                            @if($data['student']->address != "")
                                <address>
                                    {{ $data['student']->address }}<br>
                                    @if($data['student']->state != "")
                                        {{ $data['student']->state }}, 
                                    @endif
                                    @if($data['student']->country != "")
                                        {{ $data['student']->country }}
                                    @endif
                                </address>
                            @else
                                <p class="text-muted">Not provided</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>Temporary Address</h5>
                            @if($data['student']->temp_address != "")
                                <address>
                                    {{ $data['student']->temp_address }}<br>
                                    @if($data['student']->temp_state != "")
                                        {{ $data['student']->temp_state }}, 
                                    @endif
                                    @if($data['student']->temp_country != "")
                                        {{ $data['student']->temp_country }}
                                    @endif
                                </address>
                            @else
                                <p class="text-muted">Same as permanent address</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Family Information -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="ace-icon fa fa-users"></i> Family Information
                    </h4>
                </div>
                <div class="panel-body">
                    <!-- Father Information -->
                    @if($data['student']->father_first_name != "")
                        <div class="family-member">
                            <h5>Father's Details</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Name:</strong> {{ $data['student']->father_first_name.' '.$data['student']->father_middle_name.' '.$data['student']->father_last_name }}</p>
                                    @if($data['student']->father_eligibility != "")
                                        <p><strong>Education:</strong> {{ $data['student']->father_eligibility }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($data['student']->father_occupation != "")
                                        <p><strong>Occupation:</strong> {{ $data['student']->father_occupation }}</p>
                                    @endif
                                    @if($data['student']->father_office != "")
                                        <p><strong>Office:</strong> {{ $data['student']->father_office }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($data['student']->father_mobile_1 != "")
                                        <p><strong>Contact:</strong> {{ $data['student']->father_mobile_1 }}
                                            @if($data['student']->father_mobile_2 != "")
                                                , {{ $data['student']->father_mobile_2 }}
                                            @endif
                                        </p>
                                    @endif
                                    @if($data['student']->father_email != "")
                                        <p><strong>Email:</strong> {{ $data['student']->father_email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Mother Information -->
                    @if($data['student']->mother_first_name != "")
                        <div class="family-member">
                            <h5>Mother's Details</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Name:</strong> {{ $data['student']->mother_first_name.' '.$data['student']->mother_middle_name.' '.$data['student']->mother_last_name }}</p>
                                    @if($data['student']->mother_eligibility != "")
                                        <p><strong>Education:</strong> {{ $data['student']->mother_eligibility }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($data['student']->mother_occupation != "")
                                        <p><strong>Occupation:</strong> {{ $data['student']->mother_occupation }}</p>
                                    @endif
                                    @if($data['student']->mother_office != "")
                                        <p><strong>Office:</strong> {{ $data['student']->mother_office }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($data['student']->mother_mobile_1 != "")
                                        <p><strong>Contact:</strong> {{ $data['student']->mother_mobile_1 }}
                                            @if($data['student']->mother_mobile_2 != "")
                                                , {{ $data['student']->mother_mobile_2 }}
                                            @endif
                                        </p>
                                    @endif
                                    @if($data['student']->mother_email != "")
                                        <p><strong>Email:</strong> {{ $data['student']->mother_email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Guardian Information -->
                    @if($data['student']->guardian_first_name != "")
                        <div class="family-member">
                            <h5>Guardian's Details</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Name:</strong> {{ $data['student']->guardian_first_name.' '.$data['student']->guardian_middle_name.' '.$data['student']->guardian_last_name }}</p>
                                    @if($data['student']->guardian_relation != "")
                                        <p><strong>Relation:</strong> {{ $data['student']->guardian_relation }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($data['student']->guardian_occupation != "")
                                        <p><strong>Occupation:</strong> {{ $data['student']->guardian_occupation }}</p>
                                    @endif
                                    @if($data['student']->guardian_office != "")
                                        <p><strong>Office:</strong> {{ $data['student']->guardian_office }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    @if($data['student']->guardian_mobile_1 != "")
                                        <p><strong>Contact:</strong> {{ $data['student']->guardian_mobile_1 }}
                                            @if($data['student']->guardian_mobile_2 != "")
                                                , {{ $data['student']->guardian_mobile_2 }}
                                            @endif
                                        </p>
                                    @endif
                                    @if($data['student']->guardian_email != "")
                                        <p><strong>Email:</strong> {{ $data['student']->guardian_email }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($data['student']->guardian_address != "")
                                <p><strong>Address:</strong> {{ $data['student']->guardian_address }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Academic Qualifications -->
            @if(isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="ace-icon fa fa-graduation-cap"></i> Academic Qualifications
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">S.N.</th>
                                        <th>Board/University</th>
                                        <th>Institution</th>
                                        <th>Year of Passing</th>
                                        <th>Percentage/Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($data['academicInfos'] as $academicInfo)
                                        <?php
                                            $examBoard = $academicInfo->board ?? ($academicInfo->examination ?? ($academicInfo->board_university ?? '-'));
                                            $institution = $academicInfo->institution ?? '-';
                                            $passYear = $academicInfo->pass_year ?? ($academicInfo->year_of_pass ?? '-');
                                            $rawPercentage = $academicInfo->percentage;
                                            $hasValidPercentage = $rawPercentage !== null && $rawPercentage !== '' && (float)$rawPercentage > 0;
                                            if ($hasValidPercentage) {
                                                $result = $rawPercentage;
                                            } else {
                                                $result = $academicInfo->division_grade
                                                    ?? ($academicInfo->grade_letter
                                                    ?? ($academicInfo->percentage_grade ?? '-'));
                                            }
                                        ?>
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $examBoard }}</td>
                                            <td>{{ $institution }}</td>
                                            <td>{{ $passYear }}</td>
                                            <td>{{ $result }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Annexures -->
            @if(isset($data['annexure']) && $data['annexure']->count() > 0)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="ace-icon fa fa-paperclip"></i> Attached Documents
                        </h4>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($data['annexure'] as $annexure)
                                <li class="list-group-item">
                                    <i class="ace-icon fa fa-check-circle text-success"></i> 
                                    {{ ViewHelper::getAnnextureById($annexure->annexures_id) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Family Photos (Hidden in Print) -->
<div class="row hidden-print">
    <div class="col-xs-12">
        <h4 class="header smaller lighter blue">
            <i class="ace-icon fa fa-camera"></i>
            Family Photos
        </h4>
    </div>
    
    <div class="col-xs-12 col-sm-4 col-md-3 text-center">
        <div class="thumbnail">
            <div class="caption">
                <h5>Father</h5>
            </div>
            @if(isset($data['student']->father_image) && $data['student']->father_image != '')
                <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['student']->father_image) }}" class="img-responsive">
            @else
                <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" class="img-responsive">
            @endif
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-4 col-md-3 text-center">
        <div class="thumbnail">
            <div class="caption">
                <h5>Mother</h5>
            </div>
            @if(isset($data['student']->mother_image) && $data['student']->mother_image != '')
                <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['student']->mother_image) }}" class="img-responsive">
            @else
                <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" class="img-responsive">
            @endif
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-4 col-md-3 text-center">
        <div class="thumbnail">
            <div class="caption">
                <h5>Guardian</h5>
            </div>
            @if(isset($data['student']->guardian_image) && $data['student']->guardian_image != '')
                <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR.$data['student']->guardian_image) }}" class="img-responsive">
            @else
                <img src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" class="img-responsive">
            @endif
        </div>
    </div>
</div>

<style>
    /* Profile Image Styling */
    .profile-image-container {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 4px;
        background: #fff;
    }
    
    .profile-image {
        max-width: 100%;
        height: auto;
        border-radius: 3px;
    }
    
    .signature-container {
        margin: 15px 0;
        padding: 10px;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
    
    .signature-image {
        max-width: 150px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .signature-label {
        text-align: center;
        font-size: 12px;
        color: #777;
        margin-bottom: 5px;
    }
    
    .qr-code-container {
        margin-top: 15px;
        padding: 10px;
    }
    
    .qr-code-label {
        text-align: center;
        font-size: 12px;
        margin-top: 5px;
        color: #555;
    }
    
    /* Student Basic Info */
    .student-name {
        margin-top: 0;
        color: #2a6496;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .student-meta {
        margin-bottom: 15px;
    }
    
    .meta-item {
        display: inline-block;
        margin-right: 15px;
        color: #555;
    }
    
    /* Panel Styling */
    .panel {
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .panel-heading {
        padding: 10px 15px;
        border-bottom: 1px solid transparent;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px;
    }
    
    .panel-title {
        margin-top: 0;
        margin-bottom: 0;
        font-size: 16px;
    }
    
    /* Family Member Styling */
    .family-member {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #eee;
    }
    
    .family-member:last-child {
        border-bottom: none;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .dl-horizontal dt {
            float: left;
            width: 100px;
            clear: left;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .dl-horizontal dd {
            margin-left: 110px;
        }
    }
    
    /* Print Specific Styles */
    @media print {
        .hidden-print {
            display: none !important;
        }
        
        .panel {
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }
        
        .student-name {
            font-size: 18pt;
        }
        
        .profile-image {
            max-width: 200px;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // PDF Download button
        $('#download-profile').click(function() {
            // Implement PDF generation logic here
            alert('PDF download functionality will be implemented here');
        });
        
        // Responsive adjustments
        function adjustLayout() {
            if ($(window).width() < 768) {
                $('.dl-horizontal dt').addClass('col-xs-4');
                $('.dl-horizontal dd').addClass('col-xs-8');
            } else {
                $('.dl-horizontal dt').removeClass('col-xs-4');
                $('.dl-horizontal dd').removeClass('col-xs-8');
            }
        }
        
        $(window).resize(adjustLayout);
        adjustLayout();
    });
</script>
<script>
function studentStatusConfirm(el) {
    var url    = el.getAttribute('data-url');
    var action = el.getAttribute('data-action');
    var isActive = action === 'active';

    Swal.fire({
        title: '<i class="fa ' + (isActive ? 'fa-check-circle' : 'fa-ban') + ' mr-2"></i>Confirm ' + (isActive ? 'Active' : 'In-Active'),
        html: '<div class="swal-custom-alert alert-' + (isActive ? 'success' : 'warning') + '">' +
              '<div class="d-flex align-items-center">' +
              '<i class="fa ' + (isActive ? 'fa-check-circle' : 'fa-ban') + ' mr-2"></i>' +
              '<div><strong>1 record</strong> selected' +
              '<div class="small">' + (isActive ? 'Selected records will be activated.' : 'Selected records will be deactivated.') + '</div>' +
              '</div></div></div>' +
              '<div class="text-center text-muted small mt-2"><i class="fa fa-info-circle mr-1"></i>Are you sure you want to continue?</div>',
        icon: isActive ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa ' + (isActive ? 'fa-check-circle' : 'fa-ban') + ' mr-1"></i> Confirm',
        cancelButtonText: '<i class="fa fa-times mr-1"></i> Cancel',
        confirmButtonColor: isActive ? '#28a745' : '#ffc107',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        customClass: { popup: 'swal-custom-popup' }
    }).then(function(result) {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>