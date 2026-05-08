<div class="requested-books-container">
    <div class="table-responsive">
        <table id="" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book Details</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['book_request']) && $data['book_request']->count() > 0)
                    @foreach($data['book_request'] as $i => $requestedBook)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <div class="book-item">
                                    <div class="book-cover">
                                        @if($requestedBook->image)
                                            <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'book'.DIRECTORY_SEPARATOR.$requestedBook->image) }}" 
                                                 alt="{{ $requestedBook->title }}"
                                                 class="img-responsive">
                                        @else
                                            <div class="no-cover">
                                                <i class="fas fa-book"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="book-info">
                                        <div class="book-title">{{ $requestedBook->title }}</div>
                                        <div class="book-meta">
                                            <span class="isbn">
                                                <i class="fas fa-barcode"></i> 
                                                {{ $requestedBook->book_code ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('user-student.library.book-list') }}?categories={{$requestedBook->categories}}" 
                                   class="category-link">
                                    {{ ViewHelper::getBookCategoryById($requestedBook->categories) }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('user-student.library.book-list') }}?author={{$requestedBook->author}}" 
                                   class="author-link">
                                    {{ $requestedBook->author }}
                                </a>
                            </td>
                            <td>
                                <div class="request-date">
                                    {{ \Carbon\Carbon::parse($requestedBook->requested_date)->format('M d, Y') }}
                                    <div class="time-ago">
                                        {{ \Carbon\Carbon::parse($requestedBook->requested_date)->diffForHumans() }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('library.request-cancel', ['id' => encrypt($requestedBook->id),'member' => encrypt($data['lib_member']->id)]) }}" 
                                       class="btn-cancel"
                                       data-toggle="tooltip" 
                                       title="Cancel Request"
                                       onclick="return confirm('Are you sure you want to cancel this book request?')">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="no-requests">
                            <div class="empty-state">
                                <i class="fas fa-book-open"></i>
                                <h4>No Books Requested</h4>
                                <p>You haven't requested any books yet.</p>
                                <a href="{{ route('user-student.library.book-list') }}" class="btn-browse">
                                    <i class="fas fa-search"></i> Browse Books
                                </a>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<style>
    .requested-books-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 30px;
    }

    .book-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .book-cover {
        width: 50px;
        height: 70px;
        background: #f3f4f6;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-cover {
        color: #9ca3af;
        font-size: 20px;
    }

    .book-info {
        flex-grow: 1;
    }

    .book-title {
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 5px;
    }

    .book-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .isbn i {
        margin-right: 5px;
    }

    .category-link, .author-link {
        color: #4361ee;
        font-weight: 500;
        transition: color 0.2s;
    }

    .category-link:hover, .author-link:hover {
        color: #3a56d4;
        text-decoration: underline;
    }

    .request-date {
        font-weight: 500;
    }

    .time-ago {
        font-size: 12px;
        color: #6b7280;
        margin-top: 3px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-pending {
        background: rgba(251, 191, 36, 0.1);
        color: #d97706;
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        background: rgba(248, 113, 113, 0.1);
        color: #dc2626;
        border-radius: 4px;
        font-size: 13px;
        transition: all 0.2s;
        border: none;
    }

    .btn-cancel:hover {
        background: rgba(248, 113, 113, 0.2);
        color: #b91c1c;
        text-decoration: none;
    }

    .no-requests {
        text-align: center;
        padding: 40px 0;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 300px;
        margin: 0 auto;
    }

    .empty-state i {
        font-size: 50px;
        color: #d1d5db;
        margin-bottom: 15px;
    }

    .empty-state h4 {
        color: #6b7280;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #9ca3af;
        margin-bottom: 15px;
    }

    .btn-browse {
        padding: 8px 20px;
        background: #4361ee;
        color: white;
        border-radius: 4px;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-browse:hover {
        background: #3a56d4;
        color: white;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .book-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .book-cover {
            width: 40px;
            height: 60px;
        }
    }
</style>

<script>
    $(document).ready(function() {
        $('#requested-books-table').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search requested books...",
            },
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control');
            }
        });
        
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>