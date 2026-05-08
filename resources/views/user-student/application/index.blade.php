@extends('user-student.layouts.master')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    .student-dashboard {
        min-height: 100vh;
        background: #f8fafc;
    }
    
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem;
    }
    
    .dashboard-header {
        margin-bottom: 2rem;
    }
    
    .header-content h1 {
        font-size: 1.75rem;
        color: #2d3748;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .header-content p {
        color: #718096;
        margin: 0;
    }
    
    .content-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .applications-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .applications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .applications-header h3 {
        margin: 0;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #2d3748;
    }
    
    .search-box {
        position: relative;
        width: 250px;
    }
    
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }
    
    .search-box input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .search-box input:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.1);
    }
    
    .applications-table-container {
        overflow-x: auto;
        padding: 0 1rem;
    }
    
    .applications-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .applications-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #4a5568;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .applications-table td {
        padding: 1rem;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }
    
    .applications-table tr:last-child td {
        border-bottom: none;
    }
    
    .applications-table tr:hover td {
        background: #f8fafc;
    }
    
    .table-checkbox {
        display: block;
        position: relative;
        cursor: pointer;
        height: 18px;
        width: 18px;
    }
    
    .table-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 18px;
        width: 18px;
        background-color: #fff;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
    }
    
    .table-checkbox:hover input ~ .checkmark {
        border-color: #3a7bd5;
    }
    
    .table-checkbox input:checked ~ .checkmark {
        background-color: #3a7bd5;
        border-color: #3a7bd5;
    }
    
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }
    
    .table-checkbox input:checked ~ .checkmark:after {
        display: block;
    }
    
    .table-checkbox .checkmark:after {
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.approved {
        background: #d4edda;
        color: #155724;
    }
    
    .table-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        color: #718096;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        background: #f0f5ff;
        color: #3a7bd5;
    }
    
    .action-btn.delete:hover {
        background: #fef2f2;
        color: #ef4444;
    }
    
    .no-data {
        padding: 3rem 1rem;
        text-align: center;
    }
    
    .no-data-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        color: #a0aec0;
    }
    
    .no-data-content i {
        font-size: 2.5rem;
    }
    
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid #edf2f7;
    }
    
    .table-info {
        color: #718096;
        font-size: 0.875rem;
    }
    
    .pagination {
        display: flex;
        gap: 0.5rem;
    }
    
    .page-item .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #4a5568;
        text-decoration: none;
    }
    
    .page-item.active .page-link {
        background: #3a7bd5;
        border-color: #3a7bd5;
        color: white;
    }
    
    .page-item.disabled .page-link {
        color: #cbd5e0;
    }
</style>
@endsection

@section('content')
    <div class="student-dashboard">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <div class="header-content">
                    <h1>
                        <i class="fas fa-file-alt"></i>
                        Application Management
                    </h1>
                    <p>View and manage your applications</p>
                </div>
            </div>
            
            <div class="dashboard-content">
                @include('user-student.application.includes.buttons')
                
                <div class="content-card">
                    @include('includes.flash_messages')
                    @include($view_path.'.application.includes.table')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    
    <script>
        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

         document.getElementById('application-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.applications-table tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>
@endsection

