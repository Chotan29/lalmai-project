<div id="navbar" class="navbar navbar-default ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a href="{{ route('web.admin.dashboard') }}" class="navbar-brand">
                <small>
                    <i class="ace-icon fa fa-globe"></i> &nbsp;
                    @if(isset($admin_info->admin_title))
                        {{$admin_info->admin_title}}
                        <strong class="text-capitalize orange2"> WEB CMS </strong>
                    @else
                        Edu Firm
                        <strong class="text-capitalize orange2"> WEB CMS </strong>
                    @endif
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue">
                    <a href="{{ route('web.home') }}" target="_blank">
                        <i class="ace-icon fa fa-globe"></i>
                    </a>
                </li>
                <li class="light-blue">
                    <a href="{{ route('web.admin.help') }}">
                        <i class="ace-icon fa fa-question"></i>
                    </a>
                </li>


                <li class="light-blue dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        @if(isset($profileImageSrc) && $profileImageSrc !== null)
                            <img id="avatar" class="nav-user-photo" alt="" src="{{ asset($profileImageSrc) }}" width="300px" />
                        @else
                            <img class="nav-user-photo" src="{{ asset('assets/images/avatars/user.jpg') }}" alt="" />
                        @endif
                        <span class="user-info">
                            <small>Welcome,</small>
                            {{ isset(Auth::user()->name)?Auth::user()->name:"User" }}
                        </span>
                            <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        @if(isset($profileImageSrc) && $profileImageSrc !== '')
                            <li>
                                <img id="avatar" class="img-responsive" alt="" src="{{ asset($profileImageSrc) }}" width="300px" />
                            </li>
                        @endif
                        <li>
                            @if(isset(auth()->user()->id))
                                <a href="{{ route('user.view', ['id' => encrypt(auth()->user()->id)]) }}">
                                    <i class="ace-icon fa fa-user"></i>
                                    Profile
                                </a>
                            @else
                                <a href="#">
                                    <i class="ace-icon fa fa-user"></i>
                                    Profile
                                </a>
                            @endif
                        </li>
                        <li class="divider"></li>

                        <li>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="icon-off"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>