<div id="sidebar" class="sidebar responsive ace-save-state">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">

            <a class="btn btn-success" href="{{ route('web.home') }}" target="_blank">
                <span class="menu-icon"><i class="fa fa-globe"></i></span>
            </a>

            <a class="btn btn-warning" href="{{ route('web.admin.notice.add') }}">
                <span class="menu-icon"><i class="fa fa-list"></i></span>
            </a>

            <a class="btn btn-info" href="{{ route('web.admin.blog.add') }}">
                <span class="menu-icon"><i class="fa fa-pencil"></i></span>
            </a>

            <a class="btn btn-danger" href="{{ route('web.admin.settings.general') }}">
                <span class="menu-icon"><i class="fa fa-cogs"></i></span>
            </a>
        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div><!-- /.sidebar-shortcuts -->

    <ul class="nav nav-list">
        @ability('super-admin','web-menu-dashboard')
        <li {!! request()->is('dashboard')?'class="active"':'' !!}>
            <a href="{{ route('dashboard') }}">
                <span class="menu-icon"><i class="fa fa-exchange"></i></span>
                <span class="menu-text">IMS Dashboard </span>
            </a>
        </li>

        <li {!! request()->is('webportal/admin/dashboard')?'class="active"':'' !!}>
            <a href="{{ route('web.admin.dashboard') }}">
                <span class="menu-icon"><i class="fa fa-dashboard"></i></span>
                <span class="menu-text"> CMS Dashboard </span>
            </a>
        </li>
        @endability


        @ability('super-admin','web-menu-analytics')
        <li {!! request()->is('webportal/admin/analytics')?'class="active"':'' !!}>
            <a href="{{ route('web.admin.analytics') }}">
                <span class="menu-icon"><i class="fa fa-bar-chart"></i></span>
                <span class="menu-text"> Analytic </span>
            </a>
        </li>
        @endability

        @ability('super-admin','web-menu-setting')
        <!-- Settings manager -->
        <li {!! request()->is('webportal/admin/settings*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-cogs"></i></span>
                <span class="menu-text">Settings</span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/settings/general')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.settings.general') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        General
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/settings/home')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.settings.home') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Home Page
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/settings/about-us')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.settings.about-us') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        About Us
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/settings/contact-us')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.settings.contact-us') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Contact Us
                    </a>
                </li>

               {{-- <li {!! request()->is('webportal/admin/settings/registration')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.settings.registration') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Registration
                    </a>
                </li>--}}

            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-menu-page')
        <!-- Page Menu -->
        <li {!! request()->is('webportal/admin/page*')||request()->is('webportal/admin/menu*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-copy"></i></span>
                <span class="menu-text"> Menu & Pages </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li {!! request()->is('webportal/admin/page')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.page') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Pages
                    </a>
                </li>

                <!-- Menu Manager -->
                <li {!! request()->is('webportal/admin/menu')?'class="active open"':"" !!}>
                    <a href="{{ route('web.admin.menu') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Menu
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-slider')
        <!-- Slider Menus -->
        <li {!! request()->is('webportal/admin/slider*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-picture-o"></i></span>
                <span class="menu-text"> Slider  </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li {!! request()->is('webportal/admin/slider')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.slider') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Sliders
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/slider/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.slider.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-notice')
        <!-- Notice  Menu -->
        <li {!! request()->is('webportal/admin/notice*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-th-list "></i></span>
                <span class="menu-text"> Notice </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/notice')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.notice') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Notice
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/notice/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.notice.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-blog')
        <!-- Blog  Menu -->
        <li {!! request()->is('webportal/admin/blog*') || request()->is('webportal/admin/category*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-list-alt"></i></span>
                <span class="menu-text"> Blog Post </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/blog')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.blog') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> All Posts
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/blog/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.blog.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i>  New Post
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/category')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.category') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> All Category
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/category/add*')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.category.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add Category
                    </a>
                </li>


            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-event')
        <!-- Events  Menu -->
        <li {!! request()->is('webportal/admin/events*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-calendar"></i></span>
                <span class="menu-text"> Events </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/events')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.events') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Events
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/events/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.events.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-services')
        <!-- Events  Menu -->
        <li {!! request()->is('webportal/admin/services*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-tasks"></i></span>
                <span class="menu-text"> Course/Services </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/services')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.services') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Course/Services
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/services/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.services.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-gallery')
        <!-- Gallery Menus -->
        <li {!! request()->is('webportal/admin/gallery*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-camera"></i></span>
                <span class="menu-text"> Gallery  </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li {!! request()->is('webportal/admin/gallery')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.gallery') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Galleries
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/gallery/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.gallery.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>


            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-download')
        <!-- Download  Menu -->
        <li {!! request()->is('webportal/admin/download*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-download"></i></span>
                <span class="menu-text"> Download </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/download')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.download') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Downloads
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/download/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.download.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-faq')
        <!-- Faq Menus -->
        <li {!! request()->is('webportal/admin/faq*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-question"></i></span>
                <span class="menu-text"> Faq  </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li {!! request()->is('webportal/admin/faq')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.faq') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Faqs
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/faq/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.faq.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>


            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-counter')
        <!-- Counters Menus -->
        <li {!! request()->is('webportal/admin/counter*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-compass"></i></span>
                <span class="menu-text"> Counters  </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li {!! request()->is('webportal/admin/counter')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.counter') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Counters
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/counter/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.counter.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>


            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-client')
        <!-- Slider Menus -->
        <li {!! request()->is('webportal/admin/client*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-certificate"></i></span>
                <span class="menu-text"> Client/Award  </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li {!! request()->is('webportal/admin/client')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.client') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Clients/Awards
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/client/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.client.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-testimonial')
        <!-- Testimonial manager -->
        <li {!! request()->is('webportal/admin/testimonial*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-comment"></i></span>
                <span class="menu-text">Testimonial </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/testimonial')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.testimonial') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Testimonials
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/testimonial/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.testimonial.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-staff')
        <!-- Staffs manager -->
        <li {!! request()->is('webportal/admin/staff*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-user-md"></i></span>
                <span class="menu-text">Staff </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/staff')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.staff') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Staffs
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/staff/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.staff.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-pricing')
        <!-- Staffs manager -->
        <li {!! request()->is('webportal/admin/pricing*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-dollar"></i></span>
                <span class="menu-text">Pricing </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/pricing')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.pricing') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-list"></i> Pricing
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/pricing/add')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.pricing.add') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <i class="ace-icon fa fa-plus"></i> Add New
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-registration')
        <!-- Notice  Menu -->
        <li {!! request()->is('webportal/admin/registration*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-certificate"></i></span>
                <span class="menu-text"> Registration </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/registration')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.registration') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Registration
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/registration/programme')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.registration.programme') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Application For
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/settings/registration')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.settings.registration') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Registration Setting
                    </a>
                </li>
            </ul>
        </li>
        @endability


        @ability('super-admin','web-menu-contact')
        <!-- Notice  Menu -->
        <li {!! request()->is('webportal/admin/contact*')||request()->is('webportal/admin/subscribers*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-credit-card"></i></span>
                <span class="menu-text"> Contact Manager </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>
            <ul class="submenu">
                <li {!! request()->is('webportal/admin/contact')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.contact') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Contact Detail
                    </a>
                </li>
                <li {!! request()->is('webportal/admin/contact/group')?'class="active"':"" !!}>
                    <a href="{{ route('web.admin.contact.group') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        Contact Group
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/subscribers*')?'class="active open"':"" !!}>
                    <a href="{{ route('web.admin.subscribers') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <span class="menu-text"> Subscribers </span>
                    </a>
                </li>

                <li {!! request()->is('webportal/admin/contact-us*')?'class="active open"':"" !!}>
                    <a href="{{ route('web.admin.contact-us') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        <span class="menu-text"> Contact Message </span>
                    </a>
                </li>
            </ul>
        </li>
        @endability

        @ability('super-admin','web-menu-alert')
        <li {!! request()->is('web/admin/info*')?'class="active open"':"" !!}>
            <a href="#" class="dropdown-toggle">
                <span class="menu-icon"><i class="fa fa-mobile-phone"></i></span>
                <span class="menu-text"> Alert/Notification</span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <ul class="submenu">
                <li class="{!! request()->is('web/admin/info/smsemail*')?'active':'' !!} hover">
                    <a href="{{ route('web.admin.info.smsemail') }}">
                        <i class="ace-icon fa fa-double-angle-right"></i>
                        SMS / E-mail
                    </a>

                    <b class="arrow"></b>
                </li>
            </ul>
        </li>
        @endability

    </ul><!-- /.nav-list -->

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>
