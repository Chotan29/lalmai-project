<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state hidden-print">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>

    <ul class="nav nav-list">
        {{-- Dashboard --}}
        @permission('student-dashboard')
        <li class="{!! request()->is('user-student')?'active':'' !!}">
            <a href="{{ route('user-student') }}" >
                <i class="menu-icon fa fa-tachometer"></i>
                <span class="menu-text"> Dashboard </span>
            </a>
        </li>
        @endpermission

        {{-- Profile --}}
        @permission('student-profile')
        <li class="{!! request()->is('user-student/profile*')?'active':'' !!}">
            <a href="{{ route('user-student.profile') }}" >
                <i class="menu-icon fa fa-user"></i>
                <span class="menu-text"> Profile </span>
            </a>
        </li>
        @endpermission

        {{-- Account --}}
        @permission('student-fees')
        <li class="{!! request()->is('user-student/fees*')?'active open':'' !!}  hover">
            <a href="{{ route('user-student.fees') }}" >
                <i class="menu-icon fa fa-calculator" aria-hidden="true"></i>
                <span class="menu-text">Fees</span>
            </a>
        </li>
        @endpermission
    </ul><!-- /.nav-list -->
</div>
