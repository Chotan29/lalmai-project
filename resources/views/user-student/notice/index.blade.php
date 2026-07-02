@extends('user-student.layouts.master')

@section('css')
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --dark-color: #1a1a2e;
        --light-color: #f8f9fa;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --danger-color: #f87171;
    }
    
    .notice-dashboard {
        background-color: #f5f7fb;
        min-height: 100vh;
    }
    
    .dashboard-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .notice-filter {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    
    .filter-btn {
        background: white;
        border: 1px solid #dee2e6;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .filter-btn.active, .filter-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .notice-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 20px;
    }
    
    .notice-card {
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 10px;
        margin-bottom: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .notice-card:last-child {
        margin-bottom: 0;
    }
    
    .notice-card-header {
        padding: 18px 20px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .notice-card-header:hover {
        background: #f1f3f5;
    }
    
    .notice-card-header.active {
        background: var(--primary-color);
        color: white;
    }
    
    .notice-title {
        font-weight: 600;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        margin: 0;
    }
    
    .notice-badge {
        background: rgba(0,0,0,0.1);
        color: var(--dark-color);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-right: 15px;
    }
    
    .notice-card-header.active .notice-badge {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .notice-arrow {
        transition: transform 0.3s ease;
    }
    
    .notice-card-header.active .notice-arrow {
        transform: rotate(90deg);
        color: white;
    }
    
    .notice-card-body {
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }
    
    .notice-card-body.active {
        padding: 20px;
        max-height: 1000px;
    }
    
    .notice-content {
        line-height: 1.6;
        color: #495057;
    }
    
    .notice-meta {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
        font-size: 0.9rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        color: #6c757d;
    }
    
    .meta-item i {
        margin-right: 8px;
    }
    
    .publish-date {
        color: var(--success-color);
    }
    
    .end-date {
        color: var(--warning-color);
    }
    
    .no-notices {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .no-notices i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-notices h4 {
        margin-bottom: 10px;
    }
    
    .priority-high {
        border-left: 4px solid var(--danger-color);
    }
    
    .priority-medium {
        border-left: 4px solid var(--warning-color);
    }
    
    .priority-low {
        border-left: 4px solid var(--success-color);
    }
    
    @media (max-width: 768px) {
        .notice-filter {
            flex-wrap: wrap;
        }
        
        .notice-meta {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="notice-dashboard">
    <div class="main-content-inner">
        <div class="page-content">
            @include('layouts.includes.template_setting')
            
            <div class="dashboard-header">
                <h1 style="margin: 0; color: var(--dark-color);">
                    Notice Board
                    <small style="display: block; margin-top: 5px; font-size: 0.9rem; color: #6c757d;">
                        <i class="fas fa-angle-double-right"></i>
                        Stay updated with the latest announcements
                    </small>
                </h1>
                
                <div class="notice-filter">
                    <button class="filter-btn active">All Notices</button>
                    <button class="filter-btn">Academic</button>
                    <button class="filter-btn">Events</button>
                    <button class="filter-btn">Urgent</button>
                </div>
            </div>

            <div class="notice-container">
                @if (isset($data['rows']) && $data['rows']->count() > 0)
                    @php($i = 1)
                    @foreach($data['rows'] as $row)
                    <div class="notice-card priority-{{ $row->priority ?? 'medium' }}">
                        <div class="notice-card-header" onclick="toggleNotice(this)">
                            <h3 class="notice-title">
                                <span class="notice-badge">#{{ $i }}</span>
                                {{ $row->title }}
                            </h3>
                            <i class="fas fa-chevron-right notice-arrow"></i>
                        </div>
                        
                        <div class="notice-card-body">
                            <div class="notice-content">
                                {!! $row->message !!}
                            </div>
                            
                            <div class="notice-meta">
                                <div class="meta-item publish-date">
                                    <i class="far fa-calendar-check"></i>
                                    Published: {{ \Carbon\Carbon::parse($row->publish_date)->format('M d, Y') }}
                                </div>
                                <div class="meta-item end-date">
                                    <i class="far fa-calendar-times"></i>
                                    Expires: {{ \Carbon\Carbon::parse($row->end_date)->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($i++)
                    @endforeach
                @else
                    <div class="no-notices">
                        <i class="far fa-bell-slash"></i>
                        <h4>No Notices Available</h4>
                        <p>There are currently no notices to display.</p>
                    </div>
                @endif
            </div>
        </div><!-- /.page-content -->
    </div>
</div><!-- /.main-content -->
@endsection

@section('js')
    <script>
        function toggleNotice(element) {
            const card = element.closest('.notice-card');
            const body = card.querySelector('.notice-card-body');
            const header = card.querySelector('.notice-card-header');
            
            // Toggle active class
            header.classList.toggle('active');
            body.classList.toggle('active');
            
            // Close other open notices
            if (header.classList.contains('active')) {
                document.querySelectorAll('.notice-card-header.active').forEach(otherHeader => {
                    if (otherHeader !== header) {
                        otherHeader.classList.remove('active');
                        otherHeader.nextElementSibling.classList.remove('active');
                    }
                });
            }
        }
        
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Here you would add filtering logic
            });
        });
        
        // Open first notice by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstNotice = document.querySelector('.notice-card-header');
            if (firstNotice) firstNotice.click();
        });
    </script>
@endsection