@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
        --info-color: #60a5fa;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
    }

    .library-request-dashboard {
        background-color: #f5f7fb;
        min-height: 100vh;
    }

    /* Header Styles */
    .library-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .library-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .library-title i {
        color: var(--primary-color);
    }

    /* Tabs */
    .library-tabs {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .nav-tabs {
        background: #f8f9fa;
        padding: 0 1.5rem;
        border-bottom: none;
        display: flex;
    }

    .nav-tabs > li > a {
        border: none;
        color: #6b7280;
        font-weight: 500;
        padding: 1rem 1.5rem;
        margin-right: 0.5rem;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-tabs > li > a:hover {
        background: rgba(67, 97, 238, 0.05);
        color: var(--primary-color);
    }

    .nav-tabs > li.active > a {
        background: white;
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
    }

    .tab-content {
        padding: 1.5rem;
    }

    /* Book Table */
    .book-table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        padding: 1.5rem;
    }

    .book-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .book-table th {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .book-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .book-table tr:last-child td {
        border-bottom: none;
    }

    .book-table tr:hover td {
        background: #f8fafc;
    }

    /* Book Card */
    .book-card {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .book-cover {
        width: 60px;
        height: 80px;
        background: #e5e7eb;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 0.75rem;
        overflow: hidden;
        flex-shrink: 0;
    }

    .book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .book-info {
        flex-grow: 1;
    }

    .book-title {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }

    .book-meta {
        font-size: 0.8rem;
        color: #6b7280;
    }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .status-available {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
    }

    .status-unavailable {
        background: rgba(248, 113, 113, 0.1);
        color: var(--danger-color);
    }

    /* Action Buttons */
    .action-btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-request {
        background: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }

    .btn-request:hover {
        background: #3a56d4;
        transform: translateY(-2px);
    }

    .btn-requested {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(74, 222, 128, 0.2);
    }

    .btn-cancel {
        background: rgba(248, 113, 113, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(248, 113, 113, 0.2);
        margin-left: 0.5rem;
    }

    .btn-cancel:hover {
        background: rgba(248, 113, 113, 0.2);
    }

    /* Filter Form */
    .filter-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .filter-header {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-title {
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-body {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .filter-group {
        margin-bottom: 1rem;
    }

    .filter-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #6b7280;
    }

    .filter-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .filter-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .filter-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1rem;
    }

    .btn-filter {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .library-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .nav-tabs {
            flex-direction: column;
        }
        
        .nav-tabs > li > a {
            border-radius: 0;
            margin-right: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .book-card {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="library-request-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="library-header">
                <h1 class="library-title">
                    <i class="fas fa-book"></i>
                    Library Book Requests
                </h1>
                @include($view_path.'.library.includes.buttons')
            </div>

            @include('includes.flash_messages')

            @if($data['lib_member'])
            <div class="library-tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#book-list">
                            <i class="fas fa-list"></i>
                            Book List ({{$data['books']->count()}})
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#requested-books">
                            <i class="fas fa-clock"></i>
                            My Requests ({{$data['book_request']->count()}})
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Book List Tab -->
                    <div id="book-list" class="tab-pane in active">
                        <!-- Filter Form -->
                        <div class="filter-container">
                            <div class="filter-header" data-toggle="collapse" data-target="#filterCollapse">
                                <div class="filter-title">
                                    <i class="fas fa-filter"></i>
                                    Filter Books
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="filter-body collapse" id="filterCollapse">
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label class="filter-label">ISBN Number</label>
                                        <input type="text" name="isbn_number" class="filter-input" placeholder="Enter ISBN">
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label">Book Code</label>
                                        <input type="text" name="code" class="filter-input" placeholder="Enter code">
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label">Category</label>
                                        <select name="categories" class="filter-input">
                                            <option value="">Select Category</option>
                                            @foreach($data['categories'] as $key => $category)
                                                <option value="{{ $key }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label class="filter-label">Book Title</label>
                                        <input type="text" name="title" class="filter-input" placeholder="Enter title">
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label">Author</label>
                                        <input type="text" name="author" class="filter-input" placeholder="Enter author">
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label">Rack Location</label>
                                        <input type="text" name="rack_location" class="filter-input" placeholder="Enter location">
                                    </div>
                                </div>
                                
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label class="filter-label">Language</label>
                                        <input type="text" name="language" class="filter-input" placeholder="Enter language">
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label">Publisher</label>
                                        <input type="text" name="publisher" class="filter-input" placeholder="Enter publisher">
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label">Publish Year</label>
                                        <input type="text" name="publish_year" class="filter-input" placeholder="Enter year">
                                    </div>
                                </div>
                                
                                <div class="filter-actions">
                                    <button class="btn-filter" id="filter-btn">
                                        <i class="fas fa-filter"></i>
                                        Apply Filters
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Book Table -->
                        <div class="book-table-container">
                            <div class="table-responsive">
                                <table class="book-table" id="dynamic-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Book</th>
                                            <th>Category</th>
                                            <th>Author</th>
                                            <th>Publisher</th>
                                            <th>Availability</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($data['books']) && $data['books']->count() > 0)
                                            @foreach($data['books'] as $i => $books)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>
                                                        <div class="book-card">
                                                            <div class="book-cover">
                                                                @if($books->image)
                                                                    <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'book'.DIRECTORY_SEPARATOR.$books->image) }}" alt="{{ $books->title }}">
                                                                @else
                                                                    <i class="fas fa-book"></i>
                                                                @endif
                                                            </div>
                                                            <div class="book-info">
                                                                <div class="book-title">{{ $books->title }}</div>
                                                                <div class="book-meta">ISBN: {{ $books->isbn_number ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('user-student.library.book-list') }}?categories={{$books->categories}}" style="color: var(--primary-color);">
                                                            {{ ViewHelper::getBookCategoryById($books->categories) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('user-student.library.book-list') }}?author={{$books->author}}" style="color: var(--primary-color);">
                                                            {{ $books->author }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('user-student.library.book-list') }}?publisher={{$books->publisher}}" style="color: var(--primary-color);">
                                                            {{ $books->publisher }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @php($availableCount = $books->bookCollection()->where('book_status','=',1)->count())
                                                        @if($availableCount > 0)
                                                            <span class="status-badge status-available">
                                                                <i class="fas fa-check-circle"></i> Available ({{ $availableCount }})
                                                            </span>
                                                        @else
                                                            <span class="status-badge status-unavailable">
                                                                <i class="fas fa-times-circle"></i> Not Available
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!in_array($books->id,$data['book_request_ids']))
                                                            <a href="{{ route('user-student.library.request-book', ['id' => encrypt($books->id)]) }}" class="action-btn btn-request">
                                                                <i class="fas fa-arrow-left"></i> Request
                                                            </a>
                                                        @else
                                                            <span class="action-btn btn-requested">
                                                                <i class="fas fa-check"></i> Requested
                                                            </span>
                                                            <a href="{{ route('library.request-cancel', ['id' => encrypt($books->id),'member' => encrypt($data['lib_member']->id)]) }}" class="action-btn btn-cancel">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" style="text-align: center; padding: 2rem;">
                                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                                        <i class="fas fa-book-open" style="font-size: 3rem; color: #d1d5db;"></i>
                                                        <h4 style="color: #6b7280;">No Books Found</h4>
                                                        <p>Try adjusting your search filters</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Requested Books Tab -->
                    <div id="requested-books" class="tab-pane">
                        @include($view_path.'.library.book-list.includes.book-requested-table')
                    </div>
                </div>
            </div>
            @else
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 0.75rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #fbbf24;"></i>
                    <h3 style="color: #6b7280; margin-top: 1rem;">Library Membership Required</h3>
                    <p>You need to register as a library member to request books.</p>
                    <a href="#" class="btn btn-primary" style="margin-top: 1rem;">Become a Member</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with enhanced features
           
            
            // Filter form toggle
            $('.filter-header').click(function() {
                $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
            });
            
            // Filter button functionality (same as your existing code)
            $('#filter-btn').click(function() {
                var url = '{{ $data['url'] }}';
                var flag = false;
                var params = [
                    'isbn_number', 'code', 'categories', 'title', 'author', 
                    'language', 'publisher', 'publish_year', 'edition', 
                    'edition_year', 'series', 'rack_location'
                ];
                
                params.forEach(function(param) {
                    var value = $('input[name="'+param+'"], select[name="'+param+'"]').val();
                    if (value !== '' && value !== null) {
                        url += (flag ? '&' : '?') + param + '=' + encodeURIComponent(value);
                        flag = true;
                    }
                });
                
                if (flag) {
                    location.href = url;
                }
            });
            
            // Make filter inputs uppercase
            $('.upper').keyup(function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@endsection