<div class="dashboard-metrics">
    <!-- Student Card -->
    <div class="metric-card student-card">
        <a href="{{ route('student') }}" class="metric-link">
            <div class="metric-content">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-info">
                    <h3 class="metric-value">{{ $data['studentIndicator'] ?? 0 }}</h3>
                    <p class="metric-label">Students</p>
                </div>
                <div class="metric-badge">
                    <span
                        class="badge-active">{{ $data['student_active_status']->where('status', 'active')->first()->total ?? 0 }}
                        Active</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Staff Card -->
    <div class="metric-card staff-card">
        <a href="{{ route('staff') }}" class="metric-link">
            <div class="metric-content">
                <div class="metric-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="metric-info">
                    <h3 class="metric-value">{{ $data['staff_status']->sum('total') }}</h3>
                    <p class="metric-label">Staff</p>
                </div>
                <div class="metric-badge">
                    <span
                        class="badge-active">{{ $data['staff_status']->where('status', 'active')->first()->total ?? 0 }}
                        Active</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Library Card -->
    <div class="metric-card library-card">
        <a href="{{ route('library.book') }}" class="metric-link">
            <div class="metric-content">
                <div class="metric-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="metric-info">
                    <h3 class="metric-value">{{ $data['books_status']->sum('total') }}</h3>
                    <p class="metric-label">Books</p>
                </div>
                <div class="metric-badge">
                    <span
                        class="badge-available">{{ $data['books_status']->where('book_status', 'available')->first()->total ?? 0 }}
                        Available</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Exams Card -->
    <div class="metric-card exam-card">
        <a href="{{ route('exam.schedule') }}" class="metric-link">
            <div class="metric-content">
                <div class="metric-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="metric-info">
                    <h3 class="metric-value">{{ $data['exams_status'] }}</h3>
                    <p class="metric-label">Exams</p>
                </div>
                <div class="metric-badge">
                    <span class="badge-upcoming">Upcoming</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Hostel Card -->
    <div class="metric-card hostel-card">
        <a href="{{ route('hostel') }}" class="metric-link">
            <div class="metric-content">
                <div class="metric-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="metric-info">
                    <h3 class="metric-value">{{ $data['bed_status']->sum('total') }}</h3>
                    <p class="metric-label">Hostel Beds</p>
                </div>
                <div class="metric-badge">
                    <span
                        class="badge-occupied">{{ $data['bed_status']->where('status', 'occupied')->first()->total ?? 0 }}
                        Occupied</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Transport Card -->
    {{-- <div class="metric-card transport-card">
        <a href="{{ route('transport.vehicle') }}" class="metric-link">
            <div class="metric-content">
                <div class="metric-icon">
                    <i class="fas fa-bus"></i>
                </div>
                <div class="metric-info">
                    <h3 class="metric-value">{{ $data['transport_status']->sum('total') }}</h3>
                    <p class="metric-label">Vehicles</p>
                </div>
                <div class="metric-badge">
                    <span
                        class="badge-active">{{ $data['transport_status']->where('status', 'active')->first()->total ?? 0 }}
                        Active</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
            <i class="fas fa-users"></i>
            <div class="stat-info">
                <h3>{{ $data['studentIndicator'] }}</h3>
                <p>Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4cc9f0, #4895ef);">
            <i class="fas fa-chalkboard-teacher"></i>
            <div class="stat-info">
                <h3>{{ $data['staffIndicator'] }}</h3>
                <p>Total Staff</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f8961e, #f3722c);">
            <i class="fas fa-money-bill-wave"></i>
            <div class="stat-info">
                <h3>${{ number_format($data['feeCollectionIndicator'], 2) }}</h3>
                <p>Total Fees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f72585, #b5179e);">
            <i class="fas fa-file-invoice-dollar"></i>
            <div class="stat-info">
                <h3>${{ number_format($data['salaryPayIndicator'], 2) }}</h3>
                <p>Total Salaries</p>
            </div>
        </div>
    </div>
</div> --}}

<style>
    /* Dashboard Metrics Grid */
    .dashboard-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 30px;
        margin-bottom: 10px;
    }

    /* Metric Card Styles */
    .metric-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .metric-link {
        display: block;
        text-decoration: none;
        color: inherit;
        padding: 20px;
        height: 100%;
    }

    .metric-content {
        position: relative;
        height: 100%;
    }

    .metric-icon {
        font-size: 28px;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    .metric-info {
        margin-bottom: 15px;
    }

    .metric-value {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 5px 0;
        line-height: 1;
    }

    .metric-label {
        font-size: 14px;
        color: #6c757d;
        margin: 0;
    }

    .metric-badge {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
    }

    .badge-active,
    .badge-available,
    .badge-upcoming,
    .badge-occupied {
        display: inline-block;
        /* padding: 4px 8px; */
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Card Specific Colors */
    .student-card {
        border-left: 4px solid #4e73df;
    }

    .student-card .metric-icon {
        color: #4e73df;
    }

    .student-card .badge-active {
        background-color: rgba(78, 115, 223, 0.1);
        color: #4e73df;
    }

    .staff-card {
        border-left: 4px solid #1cc88a;
    }

    .staff-card .metric-icon {
        color: #1cc88a;
    }

    .staff-card .badge-active {
        background-color: rgba(28, 200, 138, 0.1);
        color: #1cc88a;
    }

    .library-card {
        border-left: 4px solid #f6c23e;
    }

    .library-card .metric-icon {
        color: #f6c23e;
    }

    .library-card .badge-available {
        background-color: rgba(246, 194, 62, 0.1);
        color: #f6c23e;
    }

    .exam-card {
        border-left: 4px solid #e74a3b;
    }

    .exam-card .metric-icon {
        color: #e74a3b;
    }

    .exam-card .badge-upcoming {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
    }

    .hostel-card {
        border-left: 4px solid #36b9cc;
    }

    .hostel-card .metric-icon {
        color: #36b9cc;
    }

    .hostel-card .badge-occupied {
        background-color: rgba(54, 185, 204, 0.1);
        color: #36b9cc;
    }

    .transport-card {
        border-left: 4px solid #858796;
    }

    .transport-card .metric-icon {
        color: #858796;
    }

    .transport-card .badge-active {
        background-color: rgba(133, 135, 150, 0.1);
        color: #858796;
    }

    @media (max-width: 1200px) {
        .dashboard-metrics {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .dashboard-metrics {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .dashboard-metrics {
            grid-template-columns: 1fr;
        }
    }
</style>
