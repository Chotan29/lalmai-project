<div class="row student-profile-container">
    <!-- Student Profile Picture Column -->
    <div class="col-xs-12 col-sm-3 text-center">
        <div class="profile-picture-container">
            <div class="profile-picture-wrapper">
                @if($data['student']->student_image != '')
                    <img class="profile-picture" alt="Student Photo" 
                         src="{{ asset('images/studentProfile/'.$data['student']->student_image) }}" />
                @else
                    <img class="profile-picture" alt="Student Photo" 
                         src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" />
                @endif
            </div>
            <div class="profile-status">
                <span class="badge badge-success">ACTIVE</span>
                <div class="academic-status">REGULAR</div>
            </div>
        </div>
    </div>

    <!-- Student Information Column -->
    <div class="col-xs-12 col-sm-9">
        <div class="student-details-card">
            <!-- Header Section -->
            <div class="student-header">
                <h3 class="student-name">
                    {{ $data['student']->first_name.' '.$data['student']->middle_name.' '.$data['student']->last_name }}
                </h3>
                <div class="student-meta">
                    <span class="reg-no"><i class="fa fa-id-card"></i> {{ $data['student']->reg_no }}</span>
                    @if($data['student']->university_reg)
                        <span class="univ-reg"><i class="fa fa-university"></i> {{ $data['student']->university_reg }}</span>
                    @endif
                </div>
            </div>

            <!-- Compact Information Grid -->
            <div class="student-info-grid">
                <!-- Row 1 -->
                <div class="info-row">
                    <div><span class="info-label">Faculty:</span> {{ ViewHelper::getFacultyTitle($data['student']->faculty) }}</div>
                    <div><span class="info-label">Semester:</span> {{ ViewHelper::getSemesterTitle($data['student']->semester) }}</div>
                </div>
            
                <!-- Row 5 -->
                <div class="info-row">
                    <div><span class="info-label">Batch:</span> {{ $data['student']->batch }}</div>
                    <div><span class="info-label">Mobile:</span> {{ $data['student']->mobile_1 }}{{ $data['student']->mobile_2 ? ', '.$data['student']->mobile_2 : '' }}</div>
                </div>
                
                <!-- Row 6 (Financial) -->
                @if($data['student']->balance > 0 || isset($data['current_unpaid_installment']))
                <div class="info-row financial-info">
                    <div>
                        <span class="info-label">Current Due:</span>
                        @if(isset($data['current_unpaid_installment']['installmentAmount']) && $data['current_unpaid_installment']['installmentAmount'] > 0)
                            {{ number_format($data['current_unpaid_installment']['installmentAmount'], 2) }}
                        @else
                            0
                        @endif
                    </div>
                    <div>
                        <span class="info-label">Total Balance:</span>
                        @if($data['student']->balance > 0)
                            {{ number_format($data['student']->balance, 2) }}
                        @else
                            0
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.student-profile-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 15px;
    margin-bottom: 15px;
    height: auto; /* Maintains compact height */
    max-height: 400px; /* Adjust as needed */
    overflow-y: auto; /* Adds scroll if content exceeds height */
}

.profile-picture-container {
    position: relative;
    margin-bottom: 10px;
}

.profile-picture-wrapper {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.profile-picture {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-status {
    text-align: center;
    margin-top: 8px;
}

.student-details-card {
    padding: 10px 0 0 0;
    height: 100%;
}

.student-header {
    margin-bottom: 12px;
}

.student-name {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    line-height: 1.2;
}

.student-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    font-size: 13px;
}

.reg-no, .univ-reg {
    color: #6c757d;
    display: inline-flex;
    align-items: center;
}

.reg-no i, .univ-reg i {
    margin-right: 4px;
    font-size: 14px;
}

.student-info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.info-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    font-size: 13px;
    line-height: 1.4;
    padding: 6px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row.financial-info {
    background-color: #fff8f0;
    padding: 8px;
    border-radius: 6px;
    margin-top: 5px;
}

.info-label {
    font-weight: 600;
    color: #495057;
    display: inline-block;
    min-width: 90px;
}

.info-value {
    color: #2c3e50;
    display: inline;
}

.badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success {
    background-color: #d1fae5;
    color: #065f46;
}

.academic-status {
    font-size: 11px;
    color: #6c757d;
    margin-top: 3px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-picture-wrapper {
        width: 100px;
        height: 100px;
    }
    
    .info-row {
        grid-template-columns: 1fr;
        gap: 5px;
    }
    
    .student-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .student-name {
        font-size: 18px;
    }
    
    .student-profile-container {
        max-height: none;
        overflow-y: visible;
    }
}
</style>