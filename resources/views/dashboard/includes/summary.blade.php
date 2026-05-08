<div class="dashboard-summary-widget">
    <div class="widget-header">
        <h4 class="widget-title">
            <i class="fa fa-chart-pie"></i>
            Overall Summary
        </h4>
        <div class="widget-controls">
            <button class="widget-toggle" data-action="collapse">
                <i class="fa fa-chevron-up"></i>
            </button>
        </div>
    </div>

    <div class="widget-body">
        <div class="summary-grid">
            <!-- Students Summary Card -->
            <div class="summary-card card-students">
                <div class="card-header">
                    <i class="fa fa-users"></i>
                    <h3>Students</h3>
                </div>
                <div class="card-body">
                    @if (isset($data['student_faculty_wise_active_status']) && $data['student_faculty_wise_active_status']->count() > 0)
                        <div class="summary-chart">
                            <canvas id="studentsFacultyChart" height="120"></canvas>
                        </div>
                        <div class="stats-overview">
                            <div class="stat-item">
                                <span class="stat-value">{{ $data['academic_status_count']->sum('total') }}</span>
                                <span class="stat-label">Total Students</span>
                            </div>
                            <div class="stat-item">
                                <span
                                    class="stat-value">{{ $data['student_active_status']->where('status', 'active')->first()->total ?? 0 }}</span>
                                <span class="stat-label">Active</span>
                            </div>
                        </div>
                    @else
                        <div class="no-data">No student data available</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('student') }}" class="view-details">View Details <i
                            class="fa fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Staff Summary Card -->
            <div class="summary-card card-staff">
                <div class="card-header">
                    <i class="fa fa-chalkboard-teacher"></i>
                    <h3>Staff</h3>
                </div>
                <div class="card-body">
                    @if (isset($data['staff_status']) && $data['staff_status']->count() > 0)
                        <div class="stats-overview">
                            <div class="stat-item">
                                <span class="stat-value">{{ $data['staff_status']->sum('total') }}</span>
                                <span class="stat-label">Total Staff</span>
                            </div>
                            <div class="stat-item">
                                <span
                                    class="stat-value">{{ $data['staff_status']->where('status', 'active')->first()->total ?? 0 }}</span>
                                <span class="stat-label">Active</span>
                            </div>
                        </div>
                        <div class="progress-container">
                            <div class="progress" style="height: 8px;">
                                @php
                                    $activeStaff =
                                        $data['staff_status']->where('status', 'active')->first()->total ?? 0;
                                    $totalStaff = $data['staff_status']->sum('total');
                                    $percentage = $totalStaff > 0 ? ($activeStaff / $totalStaff) * 100 : 0;
                                @endphp

                                <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="progress-label">Active Staff</div>
                        </div>
                    @else
                        <div class="no-data">No staff data available</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('staff') }}" class="view-details">View Details <i
                            class="fa fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Library Summary Card -->
            <div class="summary-card card-library">
                <div class="card-header">
                    <i class="fa fa-book"></i>
                    <h3>Library</h3>
                </div>
                <div class="card-body">
                    @if (isset($data['books_status']) && $data['books_status']->count() > 0)
                        <div class="stats-overview">
                            <div class="stat-item">
                                <span class="stat-value">{{ $data['books_status']->sum('total') }}</span>
                                <span class="stat-label">Total Books</span>
                            </div>
                            <div class="stat-item">
                                <span
                                    class="stat-value">{{ $data['books_status']->where('book_status', 1)->first()->total ?? 0 }}</span>
                                <span class="stat-label">Available</span>
                            </div>
                        </div>
                        <div class="mini-table">
                            <table>
                                @foreach ($data['books_status'] as $book_count)
                                    <tr>
                                        <td>{{ ViewHelper::getBookStatusById($book_count->book_status) }}</td>
                                        <td class="text-right">{{ $book_count->total }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        <div class="no-data">No library data available</div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('library.book') }}" class="view-details">View Details <i
                            class="fa fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Academic Status Card -->
            <div class="summary-card card-academic">
                <div class="card-header">
                    <i class="fa fa-graduation-cap"></i>
                    <h3>Academic Status</h3>
                </div>
                <div class="card-body">
                    @if (isset($data['academic_status_count']) && $data['academic_status_count']->count() > 0)
                        <div class="summary-chart">
                            <canvas id="academicStatusChart" height="120"></canvas>
                        </div>
                        <div class="status-list">
                            @foreach ($data['academic_status_count'] as $student_count)
                                <div class="status-item">
                                    <span
                                        class="status-label">{{ ViewHelper::getAcademicStatus($student_count->academic_status) }}</span>
                                    <span class="status-value">{{ $student_count->total }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">No academic data available</div>
                    @endif
                </div>
                <div class="card-footer">
                    {{-- <a href="{{ route('student.academic-status-list') }}" class="view-details">View Details <i
                            class="fa fa-arrow-right"></i></a> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern Dashboard Widget Styles */
    .dashboard-summary-widget {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .dashboard-summary-widget:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
    }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #eaeaea;
    }

    .widget-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
    }

    .widget-title i {
        margin-right: 10px;
        color: #6c757d;
    }

    .widget-controls {
        display: flex;
        align-items: center;
    }

    .widget-toggle {
        background: transparent;
        border: none;
        color: #6c757d;
        cursor: pointer;
        font-size: 14px;
        padding: 5px;
        transition: all 0.2s;
    }

    .widget-toggle:hover {
        color: #495057;
    }

    .widget-body {
        padding: 20px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .summary-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #495057;
    }

    .card-header i {
        margin-right: 10px;
        font-size: 18px;
    }

    .card-students .card-header i {
        color: #4e73df;
    }

    .card-staff .card-header i {
        color: #1cc88a;
    }

    .card-library .card-header i {
        color: #f6c23e;
    }

    .card-academic .card-header i {
        color: #e74a3b;
    }

    .card-body {
        padding: 15px;
        flex-grow: 1;
    }

    .stats-overview {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        display: block;
        font-size: 24px;
        font-weight: 700;
        color: #2e3a4d;
        line-height: 1.2;
    }

    .stat-label {
        display: block;
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-container {
        margin-top: 15px;
    }

    .progress-label {
        font-size: 11px;
        color: #6c757d;
        margin-top: 5px;
    }

    .summary-chart {
        height: 120px;
        margin-bottom: 15px;
    }

    .status-list {
        margin-top: 10px;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px dashed #eee;
    }

    .status-item:last-child {
        border-bottom: none;
    }

    .status-label {
        font-size: 13px;
        color: #6c757d;
    }

    .status-value {
        font-weight: 600;
        color: #2e3a4d;
    }

    .mini-table {
        margin-top: 10px;
    }

    .mini-table table {
        width: 100%;
    }

    .mini-table td {
        padding: 5px 0;
        font-size: 13px;
    }

    .mini-table td:last-child {
        text-align: right;
        font-weight: 600;
    }

    .no-data {
        text-align: center;
        padding: 20px 0;
        color: #6c757d;
        font-size: 14px;
    }

    .card-footer {
        padding: 12px 15px;
        background: #f8f9fa;
        border-top: 1px solid #f0f0f0;
        border-radius: 0 0 8px 8px;
    }

    .view-details {
        color: #4e73df;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .view-details i {
        margin-left: 5px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Students by Faculty Chart
        @if (isset($data['student_faculty_wise_active_status']) && $data['student_faculty_wise_active_status']->count() > 0)
            const facultyCtx = document.getElementById('studentsFacultyChart').getContext('2d');
            const facultyChart = new Chart(facultyCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach ($data['student_faculty_wise_active_status'] as $key => $faculties)
                            '{{ $key }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($data['student_faculty_wise_active_status'] as $key => $faculties)
                                {{ $faculties->sum('total') }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endif

        // Academic Status Chart
        @if (isset($data['academic_status_count']) && $data['academic_status_count']->count() > 0)
            const academicCtx = document.getElementById('academicStatusChart').getContext('2d');
            const academicChart = new Chart(academicCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($data['academic_status_count'] as $student_count)
                            '{{ ViewHelper::getAcademicStatus($student_count->academic_status) }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($data['academic_status_count'] as $student_count)
                                {{ $student_count->total }},
                            @endforeach
                        ],
                        backgroundColor: '#4e73df',
                        borderRadius: 4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endif
    });
</script>
