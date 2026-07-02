<div class="widget-box widget-attendance" id="attendance-widget">
    <div class="widget-header widget-header-flat">
        <h4 class="widget-title">
            <i class="ace-icon fa fa-calendar"></i>
            Attendance
            <small class="widget-subtitle">Manage attendance records</small>
        </h4>

        <div class="widget-toolbar no-border">
            <ul class="nav nav-tabs nav-attendance">
                <li class="active">
                    <a data-toggle="tab" href="#booklet-tab" class="btn btn-sm btn-light">
                        <i class="ace-icon fa fa-book"></i> Booklet
                    </a>
                </li>
                {{--
                <li>
                    <a data-toggle="tab" href="#studentAttenaence-tab" class="btn btn-sm btn-light">
                        <i class="ace-icon fa fa-graduation-cap"></i> Students
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#staffAttendance-tab" class="btn btn-sm btn-light">
                        <i class="ace-icon fa fa-users"></i> Staff
                    </a>
                </li>
                --}}
            </ul>
        </div>
    </div>

    <div class="widget-body">
        <div class="widget-main no-padding">
            <div class="tab-content tab-content-attendance">
                <div id="booklet-tab" class="tab-pane active">
                    <div class="table-container">
                        @include('dashboard.includes.attendance.booklet')
                    </div>
                </div>
                {{--
                <div id="studentAttendnce-tab" class="tab-pane">
                    <div class="table-container">
                        @include('dashboard.includes.account.payroll')
                    </div>
                </div>
                <div id="staffAttendance-tab" class="tab-pane">
                    <div class="table-container">
                        @include('dashboard.includes.account.transaction')
                    </div>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>

<style>
/* Widget Container */
.widget-attendance {
    border: 1px solid #d8d8d8;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    background: #fff;
    margin-bottom: 20px;
}

/* Header Styles */
.widget-header-flat {
    padding: 12px 15px;
    background: #f9f9f9;
    border-bottom: 1px solid #eee;
}

.widget-title {
    font-size: 16px;
    font-weight: 600;
    color: #555;
    display: inline-block;
    margin: 0;
    padding: 0;
}

.widget-title .ace-icon {
    margin-right: 8px;
    color: #428bca;
}

.widget-subtitle {
    display: block;
    font-size: 11px;
    color: #888;
    font-weight: normal;
    margin-top: 2px;
}

/* Tab Navigation */
.nav-attendance {
    border-bottom: none;
    margin: 0;
    padding: 0;
    display: inline-block;
}

.nav-attendance > li {
    margin-left: 5px;
    display: inline-block;
}

.nav-attendance > li > a {
    padding: 5px 12px;
    border-radius: 3px;
    color: #555;
    border: 1px solid transparent;
    transition: all 0.2s;
}

.nav-attendance > li.active > a,
.nav-attendance > li > a:hover {
    background: #fff;
    border-color: #ddd;
    color: #428bca;
}

/* Content Area */
.widget-main.no-padding {
    padding: 0;
}

.tab-content-attendance {
    border-radius: 0 0 4px 4px;
}

.table-container {
    padding: 15px;
    overflow-x: auto;
}

/* Responsive Adjustments */
@media (max-width: 767px) {
    .widget-header-flat {
        padding: 10px;
    }
    
    .widget-title {
        display: block;
        margin-bottom: 10px;
    }
    
    .nav-attendance {
        display: block;
        white-space: nowrap;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    
    .nav-attendance > li {
        display: inline-block;
        float: none;
    }
}
</style>