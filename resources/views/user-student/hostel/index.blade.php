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
    
    .hostel-dashboard {
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
    
    .hostel-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 25px;
    }
    
    .current-status {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
    }
    
    .status-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        color: var(--primary-color);
        font-size: 1.5rem;
    }
    
    .status-content h3 {
        margin: 0 0 5px 0;
        color: var(--dark-color);
    }
    
    .status-content p {
        margin: 0;
        color: #6c757d;
    }
    
    .history-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .history-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .history-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .history-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .history-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin: 0;
    }
    
    .history-date {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .history-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .badge-assigned {
        background: rgba(74, 222, 128, 0.1);
        color: var(--success-color);
    }
    
    .badge-changed {
        background: rgba(251, 191, 36, 0.1);
        color: var(--warning-color);
    }
    
    .badge-left {
        background: rgba(248, 113, 113, 0.1);
        color: var(--danger-color);
    }
    
    .history-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    .detail-item {
        margin-bottom: 10px;
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
    
    .no-history {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        grid-column: 1 / -1;
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
        .current-status {
            flex-direction: column;
            text-align: center;
        }
        
        .status-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .history-cards {
            grid-template-columns: 1fr;
        }
        
        .history-details {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="hostel-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Hostel History
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        View your accommodation records and current status
                    </small>
                </h1>
            </div>

            <div class="hostel-container">
                @if (isset($data['history']) && $data['history']->count() > 0)
                    @php($current = $data['history']->first())
                    <div class="current-status">
                        <div class="status-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="status-content">
                            <h3>Current Accommodation</h3>
                            <p>
                                {{ ViewHelper::getHostelNameById($current->hostels_id) }} • 
                                Room {{ ViewHelper::getRoomNumberById($current->rooms_id) }} • 
                                Bed {{ $current->beds_id }}
                            </p>
                        </div>
                    </div>
                    
                    <h3 style="margin-bottom: 20px; color: var(--dark-color);">
                        <i class="fas fa-history"></i> Hostel History
                    </h3>
                    
                    <div class="history-cards">
                        @php($i=1)
                        @foreach($data['history'] as $history)
                        <div class="history-card">
                            <div class="history-header">
                                <h4 class="history-title">Record #{{ $i }}</h4>
                                <span class="history-date">
                                    {{ \Carbon\Carbon::parse($history->created_at)->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <span class="history-badge badge-{{ 
                                $history->history_type == 'assigned' ? 'assigned' : 
                                ($history->history_type == 'changed' ? 'changed' : 'left') 
                            }}">
                                {{ ucfirst($history->history_type) }}
                            </span>
                            
                            <div class="history-details">
                                <div class="detail-item">
                                    <div class="detail-label">Academic Year</div>
                                    <div class="detail-value">{{ ViewHelper::getYearById($history->years_id) }}</div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Hostel</div>
                                    <div class="detail-value">{{ ViewHelper::getHostelNameById($history->hostels_id) }}</div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Room</div>
                                    <div class="detail-value">{{ ViewHelper::getRoomNumberById($history->rooms_id) }}</div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Bed</div>
                                    <div class="detail-value">{{ $history->beds_id }}</div>
                                </div>
                            </div>
                        </div>
                        @php($i++)
                        @endforeach
                    </div>
                @else
                <div class="no-history">
                    <i class="fas fa-home"></i>
                    <h4>No Hostel History Found</h4>
                    <p>You don't have any hostel accommodation records yet.</p>
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
            // Add animation to history cards
            const historyCards = document.querySelectorAll('.history-card');
            historyCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endsection