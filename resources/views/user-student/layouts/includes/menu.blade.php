{{-- Student top nav --}}
<style>
    #sidebar.student-top-nav {
        background: #fff;
        border-top: none;
        border-bottom: 1px solid #e8eef6;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }

    #sidebar.student-top-nav .nav-wrap {
        padding: 0 8px;
    }

    #sidebar.student-top-nav .nav-list {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    #sidebar.student-top-nav .nav-list > li {
        border: 0;
    }

    #sidebar.student-top-nav .nav-list > li > a {
        border: 0 !important;
        border-radius: 10px;
        margin: 5px 2px;
        padding: 9px 13px;
        color: #143457 !important;
        font-weight: 600;
        line-height: 1.1;
        transition: all 0.22s ease;
        position: relative;
        overflow: hidden;
    }

    #sidebar.student-top-nav .nav-list > li > a::after {
        content: '';
        position: absolute;
        left: 12px;
        right: 12px;
        bottom: 4px;
        height: 2px;
        background: currentColor;
        opacity: 0;
        transform: scaleX(0.25);
        transition: all 0.22s ease;
    }

    #sidebar.student-top-nav .nav-list > li > a:hover {
        background: #eef5ff !important;
        color: #0f3f77 !important;
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(15, 63, 119, 0.14);
    }

    #sidebar.student-top-nav .nav-list > li > a:hover::after {
        opacity: 0.55;
        transform: scaleX(1);
    }

    #sidebar.student-top-nav .nav-list > li.active > a,
    #sidebar.student-top-nav .nav-list > li.open > a {
        background: linear-gradient(135deg, #1f5aa6, #184a87) !important;
        color: #ffffff !important;
        box-shadow: 0 7px 14px rgba(21, 70, 132, 0.25);
    }

    #sidebar.student-top-nav .nav-list > li.active > a::after,
    #sidebar.student-top-nav .nav-list > li.open > a::after {
        opacity: 0.8;
        transform: scaleX(1);
    }

    #sidebar.student-top-nav .nav-list > li > a:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(31, 90, 166, 0.18);
    }

    #sidebar.student-top-nav .nav-list > li > a .menu-icon {
        margin-right: 6px;
        font-size: 14px;
        width: auto;
        min-width: 0;
        vertical-align: middle;
        transition: transform 0.22s ease;
    }

    #sidebar.student-top-nav .nav-list > li > a:hover .menu-icon {
        transform: translateY(-1px) scale(1.08);
    }

    #sidebar.student-top-nav .nav-list > li > a .menu-text {
        font-size: 14px;
        letter-spacing: 0.1px;
    }

    @media (max-width: 991px) {
        #sidebar.student-top-nav .nav-list > li > a {
            padding: 7px 10px;
            margin: 4px 1px;
        }

        #sidebar.student-top-nav .nav-list > li > a .menu-text {
            font-size: 13px;
        }
    }
</style>

<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state hidden-print student-top-nav">
    <script type="text/javascript">
        try {
            ace.settings.loadState('sidebar')
        } catch (e) {}
    </script>
    <div class="nav-wrap-up pos-rel">
        <div class="nav-wrap">
            <ul class="nav nav-list hidden-print">
                {{-- Dashboard --}}
                @permission('student-dashboard')
                    <li class="{!! request()->is('user-student') ? 'active' : '' !!}">
                        <a href="{{ route('user-student') }}">
                            <span class="menu-icon"><i class="fa fa-tachometer"></i></span>
                                <span class="menu-text"> Dashboard </span>
                        </a>
                    </li>
                @endpermission

                {{-- Profile --}}
                @permission('student-profile')
                    <li class="{!! request()->is('user-student/profile*') ? 'active' : '' !!}">
                        <a href="{{ route('user-student.profile') }}">
                            <span class="menu-icon"><i class="fa fa-user"></i></span>
                                <span class="menu-text"> Profile </span>
                        </a>
                    </li>
                @endpermission

                {{-- Account --}}
                @permission('student-fees')
                    <li class="{!! request()->is('user-student/fees*') ? 'active open' : '' !!}  hover">
                        <a href="{{ route('user-student.fees') }}">
                            <span class="menu-icon"><i class="fa fa-calculator" aria-hidden="true"></i></span>
                                <span class="menu-text">Fees</span>
                        </a>
                    </li>
                @endpermission



                {{-- Library --}}
                @permission('student-library')
                    <li class="{!! request()->is('user-student/library*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.library') }}">
                            <span class="menu-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
                                <span class="menu-text">Library</span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Attendance --}}
                @permission('student-attendance')
                    <li class="{!! request()->is('user-student/attendance*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.attendance') }}">
                            <span class="menu-icon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                <span class="menu-text"> Attendance</span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Hostel --}}
                @permission('student-hostel')
                    <li class="{!! request()->is('user-student/hostel*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.hostel') }}">
                            <span class="menu-icon"><i class=" fa fa-bed" aria-hidden="true"></i></span>
                                <span class="menu-text"> Hostels </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Transport --}}
                @permission('student-transport')
                    <li class="{!! request()->is('user-student/transport*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.transport') }}">
                            <span class="menu-icon"><i class=" fa fa-bus" aria-hidden="true"></i></span>
                                <span class="menu-text"> Transport </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Subject --}}
                @permission('student-course')
                    <li class="{!! request()->is('user-student/subject*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.subject') }}">
                            <span class="menu-icon"><i class=" fa fa-list-alt" aria-hidden="true"></i></span>
                                <span class="menu-text"> Course </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission
                
                {{-- @permission('student-course') --}}
                    <li class="{!! request()->is('user-student/routine*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.routine') }}">
                            <span class="menu-icon"><i class=" fa fa-list-alt" aria-hidden="true"></i></span>
                                <span class="menu-text"> Routine </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                {{-- @endpermission --}}

                {{-- Notice --}}
                @permission('student-notice')
                    <li class="{!! request()->is('user-student/notice*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.notice') }}">
                            <span class="menu-icon"><i class=" fa fa-bullhorn" aria-hidden="true"></i></span>
                                <span class="menu-text"> Notice </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Examination --}}
                @permission('student-exam')
                    <li class="{!! request()->is('user-student/exams*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.exams') }}">
                            <span class="menu-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
                                <span class="menu-text"> Exam</span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Assignment --}}
                @permission('student-assignment')
                    <li class="{!! request()->is('user-student/assignment*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.assignment') }}">
                            <span class="menu-icon"><i class=" fa fa-tasks" aria-hidden="true"></i></span>
                                <span class="menu-text"> Assignment </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Application --}}
                @permission('student-application')
                    <li class="{!! request()->is('user-student/application*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.application') }}">
                            <span class="menu-icon"><i class="fa fa-file"></i></span>
                                Application
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Download --}}
                @permission('student-download')
                    <li class="{!! request()->is('user-student/download*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.download') }}">
                            <span class="menu-icon"><i class=" fa fa-download" aria-hidden="true"></i></span>
                                <span class="menu-text"> Download </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Examination --}}
                @permission('student-meeting')
                    <li class="{!! request()->is('user-student/meeting*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.meeting') }}">
                            <span class="menu-icon"><i class="fa fa-video-camera" aria-hidden="true"></i></span>
                                <span class="menu-text"> Meeting</span>

                                <b class="arrow fa fa-angle-down"></b>
                        </a>
                    </li>
                @endpermission

            </ul><!-- /.nav-list -->
        </div><!-- /.nav-wrap -->
    </div><!-- /.nav-wrap-up -->
</div>
