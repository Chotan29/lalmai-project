<div class="widget-box birthday-widget" id="birthday-widget">
    <div class="widget-header header-crimson">
        <div class="header-content">
            <div class="widget-title-container">
                <i class="ace-icon fa fa-birthday-cake pulse-animation"></i>
                <h4 class="widget-title">
                    Upcoming Birthdays
                    <small class="widget-subtitle">Next 30 days</small>
                </h4>
            </div>
            <div class="widget-actions">
                <button class="btn btn-xs btn-light refresh-btn" title="Refresh">
                    <i class="ace-icon fa fa-refresh"></i>
                </button>
                <button class="btn btn-xs btn-light filter-btn" title="Filter Options">
                    <i class="ace-icon fa fa-filter"></i>
                </button>
            </div>
        </div>

        <div class="widget-toolbar no-border">
            <ul class="nav nav-pills nav-birthday">
                <li class="nav-item active">
                    <a class="nav-link active" data-toggle="tab" href="#student-birthday-tab" data-load="student">
                        <i class="ace-icon fa fa-graduation-cap"></i>
                        Students 
                        <span class="badge badge-pill badge-light">{{ $data['student_birthday']->count() }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#staff-birthday-tab" data-load="staff">
                        <i class="ace-icon fa fa-user-tie"></i>
                        Staff 
                        <span class="badge badge-pill badge-light">{{ $data['staff_birthday']->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="widget-body">
        <div class="widget-main">
            <div class="tab-content">
                <!-- Student Birthdays Tab -->
                <div id="student-birthday-tab" class="tab-pane active">
                    <div class="loading-spinner">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading student birthdays...</p>
                    </div>
                    <div class="birthday-content">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Faculty</th>
                                        <th>Semester</th>
                                        <th>Reg No</th>
                                        <th>Student Name</th>
                                        <th>Date of Birth</th>
                                        <th>Days Until</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($data['student_birthday']) && $data['student_birthday']->count() > 0)
                                        @foreach($data['student_birthday'] as $i => $student)
                                            @php
                                                $birthday = \Carbon\Carbon::parse($student->date_of_birth);
                                                $today = \Carbon\Carbon::now();
                                                $nextBirthday = $birthday->copy()->year($today->year);
                                                
                                                if($nextBirthday->isPast()) {
                                                    $nextBirthday->addYear();
                                                }
                                                $daysUntil = $today->diffInDays($nextBirthday, false);
                                            @endphp
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td>{{ ViewHelper::getFacultyTitle($student->faculty) }}</td>
                                                <td>{{ ViewHelper::getSemesterTitle($student->semester) }}</td>
                                                <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">{{ $student->reg_no }}</a></td>
                                                <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}">{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</a></td>
                                                <td>{{ $birthday->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($nextBirthday->isToday())
                                                        <span class="label label-success">Today!</span>
                                                    @else
                                                        <span class="label label-primary">{{ $daysUntil }} days</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">No Birthday data found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Staff Birthdays Tab -->
                <div id="staff-birthday-tab" class="tab-pane">
                    <div class="loading-spinner">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading staff birthdays...</p>
                    </div>
                    <div class="birthday-content">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SN</th>
                                        <th>Reg No</th>
                                        <th>Staff Name</th>
                                        <th>Designation</th>
                                        <th>Date of Birth</th>
                                        <th>Days Until</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($data['staff_birthday']) && $data['staff_birthday']->count() > 0)
                                        @foreach($data['staff_birthday'] as $i => $staff)
                                            @php
                                                $birthday = \Carbon\Carbon::parse($staff->date_of_birth);
                                                $today = \Carbon\Carbon::now();
                                                $nextBirthday = $birthday->copy()->year($today->year);
                                                
                                                if($nextBirthday->isPast()) {
                                                    $nextBirthday->addYear();
                                                }
                                                $daysUntil = $today->diffInDays($nextBirthday, false);
                                            @endphp
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td><a href="{{ route('staff.view', ['id' => encrypt($staff->id)]) }}">{{ $staff->reg_no }}</a></td>
                                                <td><a href="{{ route('staff.view', ['id' => encrypt($staff->id)]) }}">{{ $staff->first_name.' '.$staff->middle_name.' '.$staff->last_name }}</a></td>
                                                <td>{{ ViewHelper::getDesignationId($staff->designation) }}</td>
                                                <td>{{ $birthday->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($nextBirthday->isToday())
                                                        <span class="label label-success">Today!</span>
                                                    @else
                                                        <span class="label label-primary">{{ $daysUntil }} days</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">No staff birthdays found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Widget Container */
.birthday-widget {
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
    background: #fff;
    margin-bottom: 20px;
    overflow: hidden;
}

/* Header Styles */
.header-crimson {
    background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
    color: white;
    padding: 12px 15px 0;
    border-bottom: none;
}

/* ... (other existing styles remain the same) ... */

/* Label Styles */
.label {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}

.label-success {
    color: #d4edda;
}

.label-primary {
    color: #d1ecf1;
}
</style>

<script>
$(document).ready(function() {
    // Initialize with active tab
    loadBirthdayTab('#student-birthday-tab', 'student');

    // Tab change handler
    $('.nav-birthday a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr("href");
        var tabType = $(e.target).data('load');
        loadBirthdayTab(target, tabType);
    });

    // Refresh button
    $('.refresh-btn').click(function() {
        var activeTab = $('.nav-birthday li.active a');
        var target = activeTab.attr("href");
        var tabType = activeTab.data('load');
        loadBirthdayTab(target, tabType, true);
        
        // Show refresh animation
        $(this).find('i').addClass('fa-spin');
        setTimeout(function() {
            $('.refresh-btn i').removeClass('fa-spin');
        }, 1000);
    });

    // Filter button
    $('.filter-btn').click(function() {
        alert('Filter options would appear here');
    });

    // Load tab content
    function loadBirthdayTab(target, type, forceRefresh = false) {
        var $target = $(target);
        var $spinner = $target.find('.loading-spinner');
        var $content = $target.find('.birthday-content');
        
        $spinner.hide();
        $content.show();
    }
});
</script>