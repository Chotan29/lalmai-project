<div class="library-table-container">
    <div class="table-header-actions">
        <div class="table-title">
            <i class="ace-icon fa fa-book-open"></i> Issued Books
            <small class="text-muted">Showing {{ $data['book_issued']->count() }} active issues</small>
        </div>
        <div class="table-controls">
            <div class="search-box">
                <input type="text" placeholder="Search issued books..." class="form-control input-sm search-input">
                <i class="ace-icon fa fa-search"></i>
            </div>
            <button class="btn btn-xs btn-info refresh-btn">
                <i class="ace-icon fa fa-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover book-issue-table">
            <thead>
                <tr>
                    <th class="serial-col">#</th>
                    <th class="user-col">Member</th>
                    <th class="book-col">Book Title</th>
                    <th class="date-col">Issued On</th>
                    <th class="date-col">Due Date</th>
                    <th class="status-col">Status</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['book_issued']) && $data['book_issued']->count() > 0)
                    @foreach($data['book_issued'] as $index => $bookIssue)
                    <tr class="book-issue-row" data-id="{{ $bookIssue->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="member-info">
                                <a href="{{ $bookIssue->user_type == 1 ? route('library.student.view', ['id' => $bookIssue->member_id]) : route('library.staff.view', ['id' => $bookIssue->member_id]) }}"
                                   class="member-link">
                                    {{ $bookIssue->reg_no }}
                                </a>
                                <span class="member-badge {{ $bookIssue->user_type == 1 ? 'badge-student' : 'badge-staff' }}">
                                    {{ $bookIssue->user_type == 1 ? 'Student' : 'Staff' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('library.book.view', ['id' => $bookIssue->bookmaster_id]) }}"
                               class="book-title">
                                <i class="ace-icon fa fa-book"></i> {{ $bookIssue->title }}
                            </a>
                        </td>
                        <td>
                            <div class="date-cell">
                                <i class="ace-icon fa fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($bookIssue->issued_on)->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="date-cell {{ $bookIssue->due_date > Carbon\Carbon::now() ? 'text-success' : 'text-danger' }}">
                                <i class="ace-icon fa fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($bookIssue->due_date)->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="status-label {{ $bookIssue->due_date > Carbon\Carbon::now() ? 'status-active' : 'status-overdue' }}">
                                <i class="ace-icon fa {{ $bookIssue->due_date > Carbon\Carbon::now() ? 'fa-clock' : 'fa-exclamation-circle' }}"></i>
                                {{ \Carbon\Carbon::parse($bookIssue->due_date)->diffForHumans(\Carbon\Carbon::now()) }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr class="no-data-row">
                        <td colspan="6">
                            <div class="no-data-message">
                                <i class="ace-icon fa fa-info-circle"></i>
                                No books are currently issued
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <a href="{{ route('library.return-over') }}" class="btn btn-sm btn-primary view-more-btn">
            <i class="ace-icon fa fa-chevron-right"></i> View All Issued Books
        </a>
        
        @if (isset($data['book_issued']) && $data['book_issued']->count() > 0)
        <div class="table-summary">
            Showing {{ min($data['book_issued']->count(), 10) }} of {{ $data['book_issued']->count() }} records
        </div>
        @endif
    </div>
</div>

<style>
/* Table Container */
.library-table-container {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 20px;
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
    display: flex;
    align-items: center;
}

.table-title .ace-icon {
    margin-right: 8px;
    color: #4b6cb7;
}

.table-title small {
    font-size: 12px;
    margin-left: 10px;
    font-weight: normal;
    color: #6c757d;
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
    border-radius: 20px;
    border: 1px solid #ddd;
    font-size: 13px;
}

.search-input:focus {
    width: 250px;
    border-color: #4b6cb7;
}

.search-box .ace-icon {
    position: absolute;
    left: 10px;
    top: 8px;
    color: #999;
}

.refresh-btn {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 13px;
}

/* Table Styles */
.book-issue-table {
    margin-bottom: 0;
    font-size: 14px;
}

.book-issue-table th {
    background: #f1f5f9;
    color: #4a5568;
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}

.book-issue-table td {
    padding: 12px 15px;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

.book-issue-row:hover {
    background-color: #f8fafc !important;
}

/* Member Info */
.member-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.member-link {
    font-weight: 500;
    color: #4b6cb7;
}

.member-badge {
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 500;
}

.badge-student {
    background-color: #d0f2ff;
    color: #0c8599;
}

.badge-staff {
    background-color: #d3f9d8;
    color: #2b8a3e;
}

/* Book Title */
.book-title {
    color: #495057;
    font-weight: 500;
    transition: color 0.2s;
}

.book-title:hover {
    color: #4b6cb7;
    text-decoration: none;
}

.book-title .ace-icon {
    margin-right: 5px;
    color: #868e96;
}

/* Date Cells */
.date-cell {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}

.date-cell .ace-icon {
    color: #868e96;
    font-size: 13px;
}

.text-success {
    color: #2b8a3e !important;
}

.text-danger {
    color: #fa5252 !important;
}

/* Status Labels */
.status-label {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}

.status-active {
    background-color: #e7f5ff;
    color: #1c7ed6;
}

.status-overdue {
    background-color: #fff5f5;
    color: #fa5252;
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
    border-radius: 20px;
    font-size: 13px;
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
    .book-issue-table th, 
    .book-issue-table td {
        padding: 8px 10px;
        font-size: 13px;
    }
    
    .member-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }
    
    .status-label {
        font-size: 11px;
        padding: 3px 6px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Search functionality
    $('.search-input').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.book-issue-row').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Refresh button
    $('.refresh-btn').click(function() {
        $(this).find('i').addClass('fa-spin');
        setTimeout(function() {
            location.reload();
        }, 500);
    });
    
    // Row click handler
    $('.book-issue-row').click(function() {
        const issueId = $(this).data('id');
        // Implement what happens when a row is clicked
        console.log('Book issue clicked:', issueId);
    });
});
</script>