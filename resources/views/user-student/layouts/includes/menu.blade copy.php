<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state hidden-print">
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
                            <sapn class="menu-icon"><i class="fa fa-tachometer"></i></span>
                                <span class="menu-text"> Dashboard </span>
                        </a>
                    </li>
                @endpermission

                {{-- Profile --}}
                @permission('student-profile')
                    <li class="{!! request()->is('user-student/profile*') ? 'active' : '' !!}">
                        <a href="{{ route('user-student.profile') }}">
                            <sapn class="menu-icon"><i class="fa fa-user"></i></span>
                                <span class="menu-text"> Profile </span>
                        </a>
                    </li>
                @endpermission

                {{-- Account --}}
                @permission('student-fees')
                    <li class="{!! request()->is('user-student/fees*') ? 'active open' : '' !!}  hover">
                        <a href="{{ route('user-student.fees') }}">
                            <sapn class="menu-icon"><i class="fa fa-calculator" aria-hidden="true"></i></span>
                                <span class="menu-text">Fees</span>
                        </a>
                    </li>
                @endpermission



                {{-- Library --}}
                @permission('student-library')
                    <li class="{!! request()->is('user-student/library*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.library') }}">
                            <sapn class="menu-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
                                <span class="menu-text">Library</span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Attendance --}}
                @permission('student-attendance')
                    <li class="{!! request()->is('user-student/attendance*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.attendance') }}">
                            <sapn class="menu-icon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                <span class="menu-text"> Attendance</span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Hostel --}}
                @permission('student-hostel')
                    <li class="{!! request()->is('user-student/hostel*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.hostel') }}">
                            <sapn class="menu-icon"><i class=" fa fa-bed" aria-hidden="true"></i></span>
                                <span class="menu-text"> Hostels </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Transport --}}
                @permission('student-transport')
                    <li class="{!! request()->is('user-student/transport*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.transport') }}">
                            <sapn class="menu-icon"><i class=" fa fa-bus" aria-hidden="true"></i></span>
                                <span class="menu-text"> Transport </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Subject --}}
                @permission('student-course')
                    <li class="{!! request()->is('user-student/subject*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.subject') }}">
                            <sapn class="menu-icon"><i class=" fa fa-list-alt" aria-hidden="true"></i></span>
                                <span class="menu-text"> Course </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Notice --}}
                @permission('student-notice')
                    <li class="{!! request()->is('user-student/notice*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.notice') }}">
                            <sapn class="menu-icon"><i class=" fa fa-bullhorn" aria-hidden="true"></i></span>
                                <span class="menu-text"> Notice </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Examination --}}
                @permission('student-exam')
                    <li class="{!! request()->is('user-student/exams*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.exams') }}">
                            <sapn class="menu-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
                                <span class="menu-text"> Exam</span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Assignment --}}
                @permission('student-assignment')
                    <li class="{!! request()->is('user-student/assignment*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.assignment') }}">
                            <sapn class="menu-icon"><i class=" fa fa-tasks" aria-hidden="true"></i></span>
                                <span class="menu-text"> Assignment </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Application --}}
                @permission('student-application')
                    <li class="{!! request()->is('user-student/application*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.application') }}">
                            <sapn class="menu-icon"><i class="fa fa-file"></i></span>
                                Application
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Download --}}
                @permission('student-download')
                    <li class="{!! request()->is('user-student/download*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.download') }}">
                            <sapn class="menu-icon"><i class=" fa fa-download" aria-hidden="true"></i></span>
                                <span class="menu-text"> Download </span>
                        </a>
                        <b class="arrow"></b>
                    </li>
                @endpermission

                {{-- Examination --}}
                @permission('student-meeting')
                    <li class="{!! request()->is('user-student/meeting*') ? 'active' : '' !!} hover">
                        <a href="{{ route('user-student.meeting') }}">
                            <sapn class="menu-icon"><i class="fa fa-video-camera" aria-hidden="true"></i></span>
                                <span class="menu-text"> Meeting</span>

                                <b class="arrow fa fa-angle-down"></b>
                        </a>
                    </li>
                @endpermission

            </ul><!-- /.nav-list -->
        </div><!-- /.nav-wrap -->
    </div><!-- /.nav-wrap-up -->
</div>
