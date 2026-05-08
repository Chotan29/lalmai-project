<div class="row student-profile-container">
    <!-- Student Profile Picture Column -->
    <div class="col-xs-12 col-sm-3 text-center">
        <div class="profile-picture-container">
            <div class="profile-picture-wrapper">
                @if($student->student_image != '')
                    <img id="avatar" class="profile-picture" alt="Student Photo" 
                         src="{{ asset('images/studentProfile/'.$student->student_image) }}" />
                @else
                    <img id="avatar" class="profile-picture" alt="Student Photo" 
                         src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" />
                @endif
            </div>
            <div class="profile-status mt-3">
                <span class="badge badge-{{ $student->status == 'active' ? 'success' : 'warning' }}">
                    {{ strtoupper($student->status) }}
                </span>
                <div class="academic-status mt-1">
                    {{ ViewHelper::getAcademicStatus($student->academic_status) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Student Information Column -->
    <div class="col-xs-12 col-sm-9">
        <div class="student-details-card">
            <!-- Header Section -->
            <div class="student-header">
                <h3 class="student-name">
                    <a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">
                        {{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}
                    </a>
                </h3>
                <div class="student-meta">
                    <span class="reg-no">
                        <i class="fa fa-id-card"></i> {{ ViewHelper::getStudentById($student->id) }}
                    </span>
                    @if($student->university_reg)
                        <span class="univ-reg ml-2">
                            <i class="fa fa-university"></i> {{ $student->university_reg }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Information Grid -->
            <div class="student-info-grid">
                <div class="info-row">
                    <div class="info-label">Faculty/Program:</div>
                    <div class="info-value">
                        {{ ViewHelper::getFacultyTitle($student->faculty) }}
                    </div>
                    
                    <div class="info-label">Semester/Section:</div>
                    <div class="info-value">
                        {{ ViewHelper::getSemesterTitle($student->semester) }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Date of Birth:</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') }}
                    </div>
                    
                    <div class="info-label">Gender:</div>
                    <div class="info-value">
                        {{ $student->gender }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Blood Group:</div>
                    <div class="info-value">
                        {{ $student->blood_group ?: 'N/A' }}
                    </div>
                    
                    <div class="info-label">Nationality:</div>
                    <div class="info-value">
                        {{ $student->nationality ?: 'N/A' }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">
                        <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                    </div>
                    
                    <div class="info-label">Mobile:</div>
                    <div class="info-value">
                        {{ $student->mobile_1 ?: 'N/A' }}
                    </div>
                </div>

                <div class="info-row highlight-row">
                    <div class="info-label">Balance Fee:</div>
                    <div class="info-value">
                        <strong>{{ $student->balance }}</strong>
                        <small class="text-muted">({{ ViewHelper::convertNumberToWord($student->balance) }})</small>
                    </div>
                </div>
            </div>

            <!-- Certificate Links Section -->
            <div class="certificate-links-section">
                <hr class="divider">
                <div class="certificate-links">
                    @include('certificate.issue.includes.certificate-link')
                </div>
                <hr class="divider">
            </div>
        </div>
    </div>
</div>

<style>
.student-profile-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 20px;
    margin-bottom: 20px;
}

.profile-picture-container {
    position: relative;
    margin-bottom: 20px;
}

.profile-picture-wrapper {
    width: 150px;
    height: 150px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.profile-picture {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-status {
    text-align: center;
}

.student-details-card {
    padding: 15px;
}

.student-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.student-name {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.student-name a {
    color: inherit;
    text-decoration: none;
}

.student-name a:hover {
    color: #2962ff;
}

.student-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.reg-no, .univ-reg {
    font-size: 14px;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
}

.reg-no i, .univ-reg i {
    margin-right: 5px;
    font-size: 16px;
}

.student-info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.info-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.info-value {
    color: #2c3e50;
    font-size: 14px;
}

.highlight-row {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    margin-top: 10px;
}

.divider {
    border-top: 1px solid #eee;
    margin: 20px 0;
}

.certificate-links {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .info-row {
        grid-template-columns: 1fr;
        gap: 5px;
        margin-bottom: 10px;
    }
    
    .student-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .profile-picture-wrapper {
        width: 120px;
        height: 120px;
    }
}

/* Badge styles */
.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background-color: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.academic-status {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}
</style>