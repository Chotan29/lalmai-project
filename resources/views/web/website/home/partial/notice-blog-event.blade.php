@if (isset($data['notice']) || isset($data['blog']) || isset($data['event']))
<section class="services-section">
    <div class="auto-container">
        <div class="row clearfix">
            <!--Content Side / Our Blog-->
           @include($view_path.'.partial.notice')
           @include($view_path.'.partial.blog')
           @include($view_path.'.partial.event')
        </div>
    </div>
</section>
<!--Full Width Section-->
@endif