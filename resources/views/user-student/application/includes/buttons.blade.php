{{-- <div class="clearfix hidden-print " >
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('user-student/application')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('user-student.application') }}"><i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;Application Detail</a>
        <a class="{!! request()->is('user-student/application/add')?'btn-success':'btn-primary' !!} btn-sm" href="{{ route('user-student.application.add') }}"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New Application</a>
    </div>
</div>
<hr class="hr-4"> --}}

<div class="application-navigation">
    <div class="nav-container">
        <a class="nav-item {{ request()->is('user-student/application') ? 'active' : '' }}" href="{{ route('user-student.application') }}">
            <i class="fas fa-list-alt"></i>
            <span>Applications</span>
        </a>
        <a class="nav-item {{ request()->is('user-student/application/add') ? 'active' : '' }}" href="{{ route('user-student.application.add') }}">
            <i class="fas fa-plus-circle"></i>
            <span>New Application</span>
        </a>
    </div>
</div>

<style>
    .application-navigation {
        margin-bottom: 2rem;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        padding: 0.5rem;
    }
    
    .nav-container {
        display: flex;
        gap: 0.5rem;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        color: #5a6169;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .nav-item i {
        font-size: 1.1rem;
    }
    
    .nav-item:hover {
        background: #f5f7fa;
        color: #3a7bd5;
    }
    
    .nav-item.active {
        background: #3a7bd5;
        color: white;
    }
    
    .nav-item.active:hover {
        background: #2c65c4;
    }
</style>