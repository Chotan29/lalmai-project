<head>

    @yield('title_and_meta')

    <!--Favicon-->
    @if (isset($generalSetting->favicon))
        <link rel="shortcut icon" href="{{ asset('web/general_settings/'.$generalSetting->favicon) }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('web/general_settings/'.$generalSetting->favicon) }}" type="image/x-icon">
    @endif

    @yield('top-css')

    <!-- Stylesheets -->
    <link href="{{ asset('website/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('website/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('website/css/responsive.css') }}" rel="stylesheet">

    <!--Color Themes-->
    <link id="theme-color-file" href="{{ asset('website/css/color-themes/green-theme.css') }}" rel="stylesheet">
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!--[if lt IE 9]>

    <![endif]-->

    <link rel="stylesheet" type="text/css" href="{{ asset('website/css/flaticon.css') }}">
        <style>
            @import url("https://fonts.googleapis.com/css?family=Montserrat");
            body{
                font-family: 'Montserrat', sans-serif;
            }
        </style>

    @yield('css')
    <style>{{isset($generalSetting->custom_css)?$generalSetting->custom_css:"" }}</style>

    @yield('header_js')
    <script></script>
    {!! isset($generalSetting->header_codes)?$generalSetting->header_codes:"" !!}

</head>