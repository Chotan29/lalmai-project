@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
    }
    
    .transport-dashboard {
        background-color: #f5f7fb;
        min-height: 100vh;
    }
    
    .dashboard-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .transport-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
    }
    
    .timeline {
        position: relative;
        padding-left: 50px;
        margin-top: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .timeline-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .timeline-dot {
        position: absolute;
        left: -40px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.7rem;
    }
    
    .timeline-content {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .timeline-content:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .timeline-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .timeline-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin: 0;
    }
    
    .timeline-date {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .timeline-details {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
    }
    
    .detail-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .detail-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 3px;
    }
    
    .detail-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--dark-color);
    }
    
    .history-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-primary {
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }
    
    .badge-success {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
    }
    
    .badge-warning {
        background: rgba(251, 191, 36, 0.1);
        color: var(--warning-color);
    }
    
    .no-history {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .no-history i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-history h4 {
        margin-bottom: 10px;
    }
    
    @media (max-width: 768px) {
        .timeline {
            padding-left: 30px;
        }
        
        .timeline-dot {
            left: -25px;
            width: 15px;
            height: 15px;
        }
        
        .timeline-header {
            flex-direction: column;
        }
        
        .timeline-date {
            margin-top: 5px;
        }
        
        .timeline-details {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="transport-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Transport History
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        View your transportation records and routes
                    </small>
                </h1>
            </div>

            <div class="transport-container">
                @if (isset($data['transport_history']) && $data['transport_history']->count() > 0)
                <div class="timeline">
                    @php($i=1)
                    @foreach($data['transport_history'] as $history)
                    <div class="timeline-item">
                        <div class="timeline-dot">{{ $i }}</div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h3 class="timeline-title">Transport Record #{{ $i }}</h3>
                                <span class="timeline-date">
                                    {{ \Carbon\Carbon::parse($history->created_at)->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <span class="history-badge badge-{{ 
                                $history->history_type == 'assigned' ? 'success' : 
                                ($history->history_type == 'changed' ? 'warning' : 'primary') 
                            }}">
                                {{ ucfirst($history->history_type) }}
                            </span>
                            
                            <div class="timeline-details">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Academic Year</div>
                                        <div class="detail-value">{{ ViewHelper::getYearById($history->years_id) }}</div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-route"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Route</div>
                                        <div class="detail-value">{{ ViewHelper::getRouteNameById($history->routes_id) }}</div>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Vehicle</div>
                                        <div class="detail-value">{{ ViewHelper::getVehicleById($history->vehicles_id) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($i++)
                    @endforeach
                </div>
                @else
                <div class="no-history">
                    <i class="fas fa-bus"></i>
                    <h4>No Transport History Found</h4>
                    <p>You don't have any transport records yet.</p>
                </div>
                @endif
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to timeline items
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, index * 100);
            });
        });
    </script>
@endsection