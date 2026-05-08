
<div class="footer">
    <div class="footer-inner">
        <div class="footer-content">
            <span class="bigger-120">
                @if(isset($admin_info->copyright))
                    {!! $admin_info->copyright !!}
                @else
                    <a href="http://businesswithtechnology.com" target="_blank">© BusinessWithTechnology</a>
                @endif

            </span>

            &nbsp; &nbsp;
{{--<span class="action-buttons">--}}
{{--                <a href="#">--}}
{{--                    <i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>--}}
{{--                </a>--}}

{{--                <a href="#">--}}
{{--                    <i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>--}}
{{--                </a>--}}

{{--                <a href="#">--}}
{{--                    <i class="ace-icon fa fa-rss-square orange bigger-150"></i>--}}
{{--                </a>--}}
{{--            </span>--}}

{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="{{ asset('admin-panel/assets/js/jquery-2.1.4.min.js') }}"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="{{ asset('admin-panel/assets/js/jquery-1.11.3.min.js') }}"></script>
<![endif]-->
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src='{{ asset('admin-panel/assets/js/jquery.mobile.custom.min.js') }}'>"+"<"+"/script>");
</script>
<script src="{{ asset('admin-panel/assets/js/bootstrap.min.js') }}"></script>

<!-- page specific plugin scripts -->

<!-- ace scripts -->
<script src="{{ asset('admin-panel/assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('admin-panel/assets/js/ace.min.js') }}"></script>

<!-- inline scripts related to this page -->
<script src="{{ asset('admin-panel/assets/js/toastr.min.js') }}"></script>


@yield('js')


{{--@include('layouts.includes.footer')--}}