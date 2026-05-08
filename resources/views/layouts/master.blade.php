<!DOCTYPE html>
<html lang="en">
    @include('layouts.includes.header')
    @section('top-script')
    @endsection
    @yield('css')
    <body class="no-skin">
{{--        <div id="overlay">--}}
{{--            <i class="ace-icon fa fa-spinner fa-spin blue bigger-125"></i>--}}
{{--        </div>--}}
        @include('layouts.includes.nav')

        <div class="main-container ace-save-state" id="main-container">
            <script type="text/javascript">
                try{ace.settings.loadState('main-container')}catch(e){}
            </script>
            @if(Auth::check())
             @include('layouts.includes.menu')
                {{-- @if (isset($generalSetting->nav_layout) && $generalSetting->nav_layout ==0)
                    {{-- @include('layouts.includes.menu') --}}
                @else
                    {{-- @include('layouts.includes.menu-side') 

                @endif --}}
            @endif
            @yield('content')
            @include('layouts.includes.footer')
            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
            </a>
        </div><!-- /.main-container -->

        @include('layouts.includes.footer-script')

        @yield('js')
       

        {{-- @if (session()->has('message_warning')).

            <script>
                toastr.warning("{{ session()->get('message_warning')  }}", "Warning:");
            </script>
        @endif

        @if (session()->has('message_success'))
            <script>
                $(document).ready(function () {
                    toastr.success("{{ session()->get('message_success') }}", "Success:");
                });
            </script>
        @endif

        @if (session()->has('message_info'))
            <script>
                $(document).ready(function () {
                    toastr.info("{{ session()->get('message_info') }}", "Info:");
                });
            </script>
        @endif

        @if (session()->has('message_danger'))
            <script>
                toastr.success("{{ session()->get('message_danger') }}", "Danger:");
            </script>
        @endif --}}
    </body>
</html>

