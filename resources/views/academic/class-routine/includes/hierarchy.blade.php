<div class="hierarchy-container">
    {{-- Department Heads --}}
    <div class="hierarchy-level">
        <h5 class="level-title"><i class="fas fa-university level-item-icon"></i> Head of Department</h5>
        <div class="level-items">
            @foreach($department_heads as $id => $department_head)
                <div class="level-item {{ request('department_head_id') == $id ? 'active' : '' }}" data-level="department_head" data-id="{{ $id }}">
                    <i class="fas fa-university level-item-icon"></i>{{ $department_head }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Departments --}}
    @isset($departments)
    <div class="hierarchy-level">
        <h5 class="level-title"><i class="fas fa-university level-item-icon"></i> Departments</h5>
        <div class="level-items">
            @foreach($departments as $id => $department)
                <div class="level-item {{ request('department_id') == $id ? 'active' : '' }}" data-level="department" data-id="{{ $id }}">
                    <i class="fas fa-university level-item-icon"></i>{{ $department }}
                </div>
            @endforeach
        </div>
    </div>
    @endisset

    {{-- Faculties --}}
    @isset($faculties)
    <div class="hierarchy-level">
        <h5 class="level-title"><i class="fas fa-graduation-cap level-item-icon"></i> Faculties/Programs</h5>
        <div class="level-items">
            @foreach($faculties as $id => $faculty)
                <div class="level-item {{ request('faculty_id') == $id ? 'active' : '' }}" data-level="faculty" data-id="{{ $id }}">
                    <i class="fas fa-graduation-cap level-item-icon"></i>{{ $faculty }}
                </div>
            @endforeach
        </div>
    </div>
    @endisset

    {{-- Semesters --}}
    @isset($semesters)
    <div class="hierarchy-level">
        <h5 class="level-title"><i class="fas fa-layer-group level-item-icon"></i> Semesters</h5>
        <div class="level-items">
            @foreach($semesters as $id => $semester)
                <div class="level-item {{ request('semester_id') == $id ? 'active' : '' }}" data-level="semester" data-id="{{ $id }}">
                    <i class="fas fa-layer-group level-item-icon"></i>{{ $semester }}
                </div>
            @endforeach
        </div>
    </div>
    @endisset

    

    {{-- Batches --}}
    @isset($batches)
    <div class="hierarchy-level">
        <h5 class="level-title"><i class="fas fa-users level-item-icon"></i> Batches</h5>
        <div class="level-items">
            @foreach($batches as $id => $batch)
                <div class="level-item {{ request('batch_id') == $id ? 'active' : '' }}" data-level="batch" data-id="{{ $id }}">
                    <i class="fas fa-users level-item-icon"></i>{{ $batch }}
                </div>
            @endforeach
        </div>
    </div>
    @endisset

    {{-- Subjects (NEW) --}}
    @isset($subjects)
    <div class="hierarchy-level">
        <h5 class="level-title"><i class="fas fa-book level-item-icon"></i> Subjects</h5>
        <div class="level-items">
            @foreach($subjects as $id => $title)
                <div class="level-item {{ request('subject_id') == $id ? 'active' : '' }}" data-level="subject" data-id="{{ $id }}">
                    <i class="fas fa-book level-item-icon"></i>{{ $title }}
                </div>
            @endforeach
        </div>
    </div>
    @endisset
</div>
