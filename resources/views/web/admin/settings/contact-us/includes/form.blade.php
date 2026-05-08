<div class="tabbable tabs-left">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
        <li class="active">
            <a data-toggle="tab" href="#about"><i class="ace-icon fa fa-list-alt bigger-110"></i> About</a>
        </li>
        <li>
            <a data-toggle="tab" href="#social"><i class="ace-icon fa fa-facebook bigger-110"></i> Social</a>
        </li>
        <li>
            <a data-toggle="tab" href="#map"><i class="ace-icon fa fa-location-arrow bigger-110"></i> Map</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="about" class="tab-pane active">
            @include($view_path.'.includes.forms.about')
        </div>
        <div id="social" class="tab-pane">
            @include($view_path.'.includes.forms.social')
        </div>
        <div id="map" class="tab-pane">
            @include($view_path.'.includes.forms.map')
        </div>
    </div>

    <div class="hr hr-24"></div>
</div>
