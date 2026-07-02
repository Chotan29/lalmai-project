{{-- <div class="attendance-table-container">
    <div class="table-header-actions">
        <div class="table-title">
            <i class="ace-icon fa fa-calendar-check-o"></i>
            Attendance Booklet Records
        </div>
        <div class="table-controls">
            <div class="search-box">
                <input type="text" placeholder="Search records..." class="form-control input-sm search-input">
                <i class="ace-icon fa fa-search"></i>
            </div>
            <button class="btn btn-xs btn-info refresh-btn">
                <i class="ace-icon fa fa-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover attendance-booklet-table">
            <thead>
                <tr class="table-header-row">
                    <th class="serial-number">
                        <i class="ace-icon fa fa-hashtag"></i> #
                    </th>
                    <th class="year-column">
                        <i class="ace-icon fa fa-calendar"></i> Year
                    </th>
                    <th class="month-column">
                        <i class="ace-icon fa fa-calendar"></i> Month
                    </th>
                    <th class="days-column">
                        <i class="ace-icon fa fa-calendar-day"></i> Days
                    </th>
                    <th class="holiday-column">
                        <i class="ace-icon fa fa-umbrella-beach"></i> Holiday
                    </th>
                    <th class="status-column">
                        <i class="ace-icon fa fa-door-open"></i> Status
                    </th>
                </tr>
            </thead>

            <tbody>
                @if (isset($data['attendance_booklet']) && $data['attendance_booklet']->count() > 0)
                    @foreach($data['attendance_booklet'] as $index => $booklet)
                        <tr class="table-data-row" data-id="{{ $booklet->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ ViewHelper::getYearById($booklet->year) }}</td>
                            <td>{{ ViewHelper::getMonthById($booklet->month) }}</td>
                            <td>{{ $booklet->day_in_month }}</td>
                            <td>
                                <span class="holiday-indicator {{ $booklet->holiday ? 'holiday-yes' : 'holiday-no' }}">
                                    {{ $booklet->holiday ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $booklet->open ? 'status-open' : 'status-closed' }}">
                                    {{ $booklet->open ? 'Open' : 'Closed' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="no-data-row">
                        <td colspan="6">
                            <div class="no-data-message">
                                <i class="ace-icon fa fa-exclamation-circle"></i>
                                No attendance records found
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <a href="{{ route('attendance.master') }}" class="btn btn-sm btn-primary view-more-btn">
            <i class="ace-icon fa fa-chevron-right"></i> View All Records
        </a>
        
        @if (isset($data['attendance_booklet']) && $data['attendance_booklet']->count() > 0)
        <div class="table-summary">
            Showing {{ $data['attendance_booklet']->count() }} records
        </div>
        @endif
    </div>
</div>

<style>
/* Table Container */
.attendance-table-container {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

/* Header Styles */
.table-header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.table-title {
    font-weight: 600;
    color: #333;
    font-size: 15px;
}

.table-title .ace-icon {
    margin-right: 8px;
    color: #428bca;
}

.table-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-box {
    position: relative;
}

.search-input {
    padding-left: 30px;
    width: 200px;
    transition: width 0.3s;
}

.search-input:focus {
    width: 250px;
}

.search-box .ace-icon {
    position: absolute;
    left: 10px;
    top: 8px;
    color: #999;
}

.refresh-btn {
    padding: 3px 10px;
}

/* Table Styles */
.attendance-booklet-table {
    margin-bottom: 0;
}

.table-header-row th {
    background: #f1f5f9;
    color: #4a5568;
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: 2px solid #e2e8f0;
}

.table-header-row .ace-icon {
    margin-right: 5px;
    font-size: 14px;
}

.table-data-row td {
    padding: 12px 15px;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
    transition: background 0.2s;
}

.table-data-row:hover {
    background-color: #f8fafc;
}

/* Status Indicators */
.holiday-indicator {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.holiday-yes {
    background-color: #fee2e2;
    color: #dc2626;
}

.holiday-no {
    background-color: #dcfce7;
    color: #16a34a;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.status-open {
    background-color: #dbeafe;
    color: #1d4ed8;
}

.status-closed {
    background-color: #e5e7eb;
    color: #4b5563;
}

/* No Data Row */
.no-data-row td {
    padding: 30px;
    text-align: center;
    color: #6b7280;
}

.no-data-message {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Footer Styles */
.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
}

.view-more-btn {
    padding: 5px 15px;
}

.table-summary {
    font-size: 13px;
    color: #6b7280;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .table-header-actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .table-controls {
        width: 100%;
        justify-content: space-between;
    }
    
    .search-input {
        width: 150px;
    }
    
    .search-input:focus {
        width: 180px;
    }
}

@media (max-width: 576px) {
    .table-header-row th, 
    .table-data-row td {
        padding: 8px 10px;
        font-size: 13px;
    }
    
    .holiday-indicator,
    .status-badge {
        font-size: 11px;
        padding: 3px 6px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Initialize table interactions
    $('.refresh-btn').click(function() {
        // Add refresh functionality here
        location.reload();
    });
    
    // Search functionality
    $('.search-input').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.table-data-row').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Row click handler
    $('.table-data-row').click(function() {
        const recordId = $(this).data('id');
        // Implement what happens when a row is clicked
        console.log('Record clicked:', recordId);
    });
});
</script> --}}