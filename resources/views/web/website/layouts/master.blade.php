<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
     @include('web.website.layouts.includes.head')
    <body>
        <div class="body">
            <div class="page-wrapper">
                @include('web.website.layouts.includes.header')

                @yield('content')

                <!--Main Footer-->
                @include('web.website.layouts.includes.footer')

                @include('web.website.includes.script')
            </div>
        </div>
                @yield('js')

    </body>
</html>
