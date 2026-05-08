@if(isset($current_department_head) || isset($current_department) || isset($current_faculty) || isset($current_semester) || isset($current_batch))
<div class="breadcrumb-nav">
    <div class="breadcrumb-item">
        <a href="{{ route('routine.index') }}">Department Heads</a>
    </div>
    
    @if(isset($current_department_head))
    <div class="breadcrumb-divider">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="breadcrumb-item {{ !isset($current_department) ? 'active' : '' }}">
        <a href="{{ route('routine.index', ['department_head_id' => $current_department_head->id]) }}">
            {{ $current_department_head->department_head }}
        </a>
    </div>
    @endif
    
    @if(isset($current_department))
    <div class="breadcrumb-divider">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="breadcrumb-item {{ !isset($current_faculty) ? 'active' : '' }}">
        <a href="{{ route('routine.index', [
            'department_head_id' => $current_department_head->id,
            'department_id' => $current_department->id
        ]) }}">
            {{ $current_department->department }}
        </a>
    </div>
    @endif
    
    @if(isset($current_faculty))
    <div class="breadcrumb-divider">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="breadcrumb-item {{ !isset($current_semester) ? 'active' : '' }}">
        <a href="{{ route('routine.index', [
            'department_head_id' => $current_department_head->id,
            'department_id' => $current_department->id,
            'faculty_id' => $current_faculty->id
        ]) }}">
            {{ $current_faculty->faculty }}
        </a>
    </div>
    @endif
    
    @if(isset($current_semester))
    <div class="breadcrumb-divider">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="breadcrumb-item {{ !isset($current_batch) ? 'active' : '' }}">
        <a href="{{ route('routine.index', [
            'department_head_id' => $current_department_head->id,
            'department_id' => $current_department->id,
            'faculty_id' => $current_faculty->id,
            'semester_id' => $current_semester->id
        ]) }}">
            {{ $current_semester->semester }}
        </a>
    </div>
    @endif
    
    @if(isset($current_batch))
    <div class="breadcrumb-divider">
        <i class="fas fa-chevron-right"></i>
    </div>
    <div class="breadcrumb-item active">
        {{ $current_batch->title }}
    </div>
    @endif
</div>
@endif