<div class="overdue-books-container">
    <div class="table-header-actions">
        <div class="table-title">
            <i class="ace-icon fa fa-exclamation-triangle text-danger"></i> Overdue Books
            <small class="text-muted">{{ $data['book_return_over']->count() }} overdue items found</small>
        </div>
        <div class="table-controls">
            <div class="search-box">
                <input type="text" placeholder="Search overdue books..." class="form-control input-sm search-input">
                <i class="ace-icon fa fa-search"></i>
            </div>
            <div class="btn-group">
                <button class="btn btn-xs btn-warning send-reminders-btn">
                    <i class="ace-icon fa fa-envelope"></i> Send Reminders
                </button>
                <button class="btn btn-xs btn-info refresh-btn">
                    <i class="ace-icon fa fa-refresh"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover overdue-books-table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Borrower</th>
                    <th>Book Title</th>
                    <th width="120">Issued On</th>
                    <th width="120">Due Date</th>
                    <th width="150">Days Overdue</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['book_return_over']) && $data['book_return_over']->count() > 0)
                    @foreach($data['book_return_over'] as $index => $return_over)
                    <tr class="overdue-row" data-id="{{ $return_over->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="borrower-info">
                                <a href="{{ $return_over->user_type == 1 ? route('library.student.view', ['id' => $return_over->member_id]) : route('library.staff.view', ['id' => $return_over->member_id]) }}"
                                   class="borrower-link">
                                    {{ $return_over->reg_no }}
                                </a>
                                <span class="borrower-type {{ $return_over->user_type == 1 ? 'type-student' : 'type-staff' }}">
                                    {{ $return_over->user_type == 1 ? 'Student' : 'Staff' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('library.book.view', ['id' => $return_over->bookmaster_id]) }}" 
                               class="book-title">
                                <i class="ace-icon fa fa-book"></i> {{ $return_over->title }}
                            </a>
                        </td>
                        <td>
                            <div class="date-cell">
                                {{ \Carbon\Carbon::parse($return_over->issued_on)->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="date-cell overdue-date">
                                {{ \Carbon\Carbon::parse($return_over->due_date)->format('M d, Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="overdue-status">
                                <i class="ace-icon fa fa-clock"></i>
                                <span class="overdue-days">
                                    {{ \Carbon\Carbon::parse($return_over->due_date)->diffForHumans(\Carbon\Carbon::now()) }}
                                </span>
                                <span class="overdue-badge">
                                    {{ \Carbon\Carbon::parse($return_over->due_date)->diffInDays(\Carbon\Carbon::now()) }} days
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr class="no-overdue-row">
                        <td colspan="6">
                            <div class="no-overdue-message">
                                <i class="ace-icon fa fa-check-circle text-success"></i>
                                No overdue books currently
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if (isset($data['book_return_over']) && $data['book_return_over']->count() > 0)
    <div class="table-footer">
        <a href="{{ route('library.return-over') }}" class="btn btn-sm btn-danger view-all-btn">
            <i class="ace-icon fa fa-list"></i> View All Overdue Books
        </a>
        <div class="overdue-summary">
            <span class="text-danger">
                <i class="ace-icon fa fa-exclamation-triangle"></i>
                {{ $data['book_return_over']->count() }} items overdue
            </span>
            <span class="oldest-overdue">
                Oldest: {{ \Carbon\Carbon::parse($data['book_return_over']->max('due_date'))->diffForHumans() }}
            </span>
        </div>
    </div>
    @endif
</div>

<style>
/* Container Styles */
.overdue-books-container {
    border: 1px solid #f5c6cb;
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
    background: #fff5f5;
    border-bottom: 1px solid #f5c6cb;
}

.table-title {
    font-weight: 600;
    color: #dc3545;
    font-size: 15px;
    display: flex;
    align-items: center;
}

.table-title .ace-icon {
    margin-right: 8px;
    font-size: 16px;
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
    border: 1px solid #f5c6cb;
    font-size: 13px;
}

.search-input:focus {
    width: 250px;
    border-color: #dc3545;
}

.search-box .ace-icon {
    position: absolute;
    left: 10px;
    top: 8px;
    color: #dc3545;
}

.btn-group .btn {
    border-radius: 20px !important;
}

.send-reminders-btn {
    padding: 3px 12px;
}

.refresh-btn {
    padding: 3px 10px;
}

/* Table Styles */
.overdue-books-table {
    margin-bottom: 0;
    font-size: 14px;
}

.overdue-books-table th {
    background: #fff5f5;
    color: #dc3545;
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: 2px solid #f5c6cb;
}

.overdue-books-table td {
    padding: 12px 15px;
    vertical-align: middle;
    border-top: 1px solid #f8d7da;
}

.overdue-row:hover {
    background-color: #fff5f5 !important;
}

/* Borrower Info */
.borrower-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.borrower-link {
    font-weight: 500;
    color: #4b6cb7;
}

.borrower-type {
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 500;
}

.type-student {
    background-color: #d0f2ff;
    color: #0c8599;
}

.type-staff {
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
    color: #dc3545;
    text-decoration: none;
}

.book-title .ace-icon {
    margin-right: 5px;
    color: #868e96;
}

/* Date Cells */
.date-cell {
    white-space: nowrap;
}

.overdue-date {
    color: #dc3545;
    font-weight: 500;
}

/* Overdue Status */
.overdue-status {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #dc3545;
    font-weight: 500;
}

.overdue-status .ace-icon {
    font-size: 16px;
}

.overdue-days {
    flex-grow: 1;
}

.overdue-badge {
    background-color: #dc3545;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: bold;
}

/* No Data Row */
.no-overdue-row td {
    padding: 30px;
    text-align: center;
    color: #6b7280;
}

.no-overdue-message {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.no-overdue-message .ace-icon {
    font-size: 16px;
}

/* Footer Styles */
.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #fff5f5;
    border-top: 1px solid #f5c6cb;
}

.view-all-btn {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 13px;
}

.overdue-summary {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 13px;
}

.overdue-summary .text-danger {
    font-weight: 500;
}

.oldest-overdue {
    color: #6c757d;
    font-size: 12px;
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
    
    .table-footer {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}

@media (max-width: 576px) {
    .overdue-books-table th, 
    .overdue-books-table td {
        padding: 8px 10px;
        font-size: 13px;
    }
    
    .borrower-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }
    
    .overdue-status {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }
    
    .overdue-badge {
        align-self: flex-start;
    }
}
</style>

<script>
$(document).ready(function() {
    // Search functionality
    $('.search-input').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.overdue-row').filter(function() {
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
    
    // Send reminders button
    $('.send-reminders-btn').click(function() {
        // Implement reminder sending functionality
        alert('Send reminders to all overdue borrowers');
    });
    
    // Row click handler
    $('.overdue-row').click(function() {
        const issueId = $(this).data('id');
        // Implement what happens when an overdue row is clicked
        console.log('Overdue book clicked:', issueId);
    });
});
</script>