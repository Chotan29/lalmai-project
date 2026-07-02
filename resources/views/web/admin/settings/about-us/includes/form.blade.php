<div class="tabbable tabs-left">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab3">
        <li class="active">
            <a data-toggle="tab" href="#slider">
                <i class="ace-icon fa fa-tachometer bigger-110"></i>
                About Us
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
    </ul>

    <div class="tab-content">
        <div id="slider" class="tab-pane in active">
            @include($view_path.'.includes.forms.about')
        </div>

        <div id="staff" class="tab-pane">
            @include($view_path.'.includes.forms.staff')
        </div>

        <div id="counter" class="tab-pane">
            @include($view_path.'.includes.forms.counter')
        </div>
    </div>
</div>

