<div class="widget-box library-widget" id="library-widget">
    <div class="widget-header header-slate">
        <div class="widget-header-content">
            <div class="widget-title-container">
                <i class="ace-icon fa fa-book"></i>
                <h4 class="widget-title">
                    Library Management
                    <small class="widget-subtitle">Track books and due dates</small>
                </h4>
            </div>
            <div class="widget-actions">
                <button class="btn btn-xs btn-light refresh-btn" title="Refresh">
                    <i class="ace-icon fa fa-refresh"></i>
                </button>
                <div class="library-alert">
                    <i class="ace-icon fa fa-exclamation-triangle"></i>
                    <span class="overdue-count">{{ $data['book_return_over']->count() }}</span> overdue
                </div>
            </div>
        </div>

        <div class="widget-toolbar no-border">
            <ul class="nav nav-pills nav-library">
                <li class="nav-item active">
                    <a class="nav-link active" data-toggle="tab" href="#issue-tab">
                        <i class="ace-icon fa fa-book-open"></i>
                        Issued Books
                        <span class="badge badge-pill badge-light">{{ $data['book_issued']->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#returnOver-tab">
                        <i class="ace-icon fa fa-exclamation-circle"></i>
                        Overdue Books
                        <span class="badge badge-pill badge-danger">{{ $data['book_return_over']->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="widget-body">
        <div class="widget-main no-padding">
            <div class="tab-content">
                <div id="issue-tab" class="tab-pane active">
                    <div class="loading-spinner">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading issued books...</p>
                    </div>
                    <div class="table-container">
                        @include('dashboard.includes.library.bookissue')
                    </div>
                </div>
                <div id="returnOver-tab" class="tab-pane">
                    <div class="loading-spinner">
                        <i class="ace-icon fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading overdue books...</p>
                    </div>
                    <div class="table-container">
                        @include('dashboard.includes.library.returnover')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Widget Container */
.library-widget {
    border-radius: 6px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
    background: #fff;
    margin-bottom: 20px;
    overflow: hidden;
}

/* Header Styles */
.header-slate {
    background: linear-gradient(135deg, #4b6cb7, #182848);
    color: white;
    padding: 12px 15px 0;
    border-bottom: none;
}

.widget-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.widget-title-container {
    display: flex;
    align-items: center;
}

.widget-title {
    font-size: 16px;
    font-weight: 600;
    color: white;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
}

.widget-subtitle {
    font-size: 11px;
    font-weight: 400;
    opacity: 0.9;
    margin-top: 2px;
}

.widget-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.library-alert {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.15);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
}

.library-alert .ace-icon {
    margin-right: 5px;
    color: #ffcc5c;
}

.overdue-count {
    font-weight: bold;
    margin-left: 3px;
}

.refresh-btn {
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    transition: all 0.3s;
}

.refresh-btn:hover {
    background: rgba(255,255,255,0.3);
}

/* Tab Navigation */
.nav-library {
    border-bottom: none;
    margin: 0 -5px;
    padding: 0;
}

.nav-library .nav-item {
    margin: 0 5px;
}

.nav-library .nav-link {
    color: white;
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 13px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
}

.nav-library .nav-link.active {
    background: white;
    color: #4b6cb7;
    font-weight: 500;
}

.nav-library .nav-link .ace-icon {
    margin-right: 5px;
    font-size: 14px;
}

.nav-library .badge {
    margin-left: 5px;
    font-weight: 500;
}

.nav-library .nav-link.active .badge {
    background: #4b6cb7;
    color: white;
}

.nav-library .nav-link .badge-danger {
    background: #ff6b6b;
}

/* Content Area */
.widget-main.no-padding {
    padding: 0;
}

.tab-content {
    border-radius: 0 0 6px 6px;
}

.loading-spinner {
    display: none;
    padding: 30px;
    text-align: center;
    color: #888;
}

.loading-spinner .ace-icon {
    margin-bottom: 10px;
}

.table-container {
    display: block;
    padding: 15px;
    overflow-x: auto;
}

/* Enhanced Table Styles */
.table-bordered {
    border: 1px solid #f0f0f0;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(75, 108, 183, 0.03);
}

.table th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

/* Status Labels */
.label {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}

.label-primary {
    background-color: #d0ebff;
    color: #1c7ed6;
}

.label-danger {
    background-color: #ffe3e3;
    color: #fa5252;
}

.label-info {
    background-color: #d0f2ff;
    color: #0c8599;
}

.label-success {
    background-color: #d3f9d8;
    color: #2b8a3e;
}

/* Responsive Adjustments */
@media (max-width: 767px) {
    .widget-header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .widget-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .nav-library {
        overflow-x: auto;
        white-space: nowrap;
        display: block;
        padding-bottom: 5px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Initialize visibility states
    $('.loading-spinner').hide();
    $('.table-container').show();
    
    // Refresh button functionality
    $('.refresh-btn').click(function() {
        var activeTab = $('.nav-library li.active a');
        var target = activeTab.attr("href");
        
        // Show loading state
        $(this).find('i').addClass('fa-spin');
        $(target).find('.loading-spinner').show();
        $(target).find('.table-container').hide();
        
        // Load content
        $(target).find('.table-container').load(location.href + ' ' + target + ' .table-container', function() {
            // Hide loading state when complete
            $('.refresh-btn i').removeClass('fa-spin');
            $(target).find('.loading-spinner').hide();
            $(target).find('.table-container').show();
            initLibraryTableInteractions();
        });
    });

    // Handle tab changes
    $('.nav-library a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr("href");
        // Ensure proper visibility when switching tabs
        $(target).find('.loading-spinner').hide();
        $(target).find('.table-container').show();
    });

    // Initialize table interactions
    function initLibraryTableInteractions() {
        // Add hover effects to table rows
        $('.table-container tr').hover(
            function() {
                $(this).css('background-color', '#f8f9fa');
            },
            function() {
                $(this).css('background-color', '');
            }
        );
        
        // Add click handler for book links
        $('.table-container a[href*="library.book.view"]').click(function(e) {
            e.preventDefault();
            // Implement modal view or page navigation
            console.log('View book:', $(this).attr('href'));
        });
    }
    
    // Initialize on page load
    initLibraryTableInteractions();
    
    // Hide any visible spinners after initial load (fallback)
    setTimeout(function() {
        $('.loading-spinner').hide();
        $('.table-container').show();
    }, 1000);
});
</script>