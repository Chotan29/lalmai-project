<div class="tabbable tabs-left">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab3">
        <li class="active">
            <a data-toggle="tab" href="#slider">
                <i class="ace-icon fa fa-tachometer bigger-110"></i>
                Slider
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#notice">
                <i class="ace-icon fa fa-sticky-note bigger-110"></i>
                Notice
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#blog">
                <i class="ace-icon fa fa-newspaper-o"></i>
                Blog
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#event">
                <i class="ace-icon fa fa-list"></i>
                Event
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#welcome">
                <i class="ace-icon fa fa-desktop"></i>
                Welcome Message
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#about">
                <i class="ace-icon fa fa-building-o"></i>
                About Area
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#service">
                <i class="ace-icon fa fa-tasks"></i>
                Services
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#staff">
                <i class="ace-icon fa fa-user-secret"></i>
                Staff/Team
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#counter">
                <i class="ace-icon fa fa-compass"></i>
                Counter
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#testimonial">
                <i class="ace-icon fa fa-comment"></i>
                Testimonial
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#client">
                <i class="ace-icon fa fa-certificate"></i>
                Client/Award
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#footer">
                <i class="ace-icon fa fa-th-list"></i>
                Footer Call to Action
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="slider" class="tab-pane in active">
            @include($view_path.'.includes.forms.slider')
        </div>

        <div id="notice" class="tab-pane">
            @include($view_path.'.includes.forms.notice')
        </div>

        <div id="blog" class="tab-pane">
            @include($view_path.'.includes.forms.blog')
        </div>

        <div id="event" class="tab-pane">
            @include($view_path.'.includes.forms.event')
        </div>

        <div id="welcome" class="tab-pane">
            @include($view_path.'.includes.forms.welcome')
        </div>

        <div id="about" class="tab-pane">
            @include($view_path.'.includes.forms.about')
        </div>

        <div id="service" class="tab-pane">
            @include($view_path.'.includes.forms.services')
        </div>

        <div id="staff" class="tab-pane">
            @include($view_path.'.includes.forms.staff')
        </div>

        <div id="counter" class="tab-pane">
            @include($view_path.'.includes.forms.counter')
        </div>

        <div id="testimonial" class="tab-pane">
            @include($view_path.'.includes.forms.testimonial')
        </div>

        <div id="client" class="tab-pane">
            @include($view_path.'.includes.forms.client')
        </div>

        <div id="footer" class="tab-pane">
            @include($view_path.'.includes.forms.footer')
        </div>

    </div>
</div>

