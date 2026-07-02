<div class="clearfix hidden-print ">
    <div class="easy-link-menu align-center">
        <a class="{!! request()->is('dynamic/placement')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.placement') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.placement')}}</a>
        <a class="{!! request()->is('dynamic/scholarship')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.scholarship') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.scholarship')}}</a>
        <a class="{!! request()->is('dynamic/annexure')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.annexure') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.annexure')}}</a>
        <a class="{!! request()->is('dynamic/academic-info-level')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.academic-info-level') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.academic_info_level')}}</a>
        <a class="{!! request()->is('dynamic/degree')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.degree') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.degree')}}</a>
        <a class="{!! request()->is('dynamic/state')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.state') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.state')}}</a>
        <a class="{!! request()->is('dynamic/application-type')?'btn-success':'btn-primary' !!} btn-sm " href="{{ route('dynamic.application-type') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{__('academic.child.dynamic_gallery.child.application_type')}}</a>
    </div>
</div>
<hr class="hr-6">