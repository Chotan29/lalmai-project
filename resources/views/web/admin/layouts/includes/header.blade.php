{{--
<head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        @if(isset($admin_info->admin_title))
                <title>{{$admin_info->admin_title}} | CMS </title>
        @else
                <title> Time Saver | CMS</title>
        @endif

        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        @if (isset($admin_info->favicon))
                <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/general_settings/'.$admin_info->favicon) }}">
        @endif

        <!-- bootstrap & fontawesome -->
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" />



        <!-- text fonts -->
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/fonts.googleapis.com.css') }}" />

        <!-- ace styles -->
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />

        <!--[if lte IE 9]>
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/ace-part2.min.css') }}" class="ace-main-stylesheet" />
        <![endif]-->
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/ace-skins.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/ace-rtl.min.css') }}" />

        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/datepicker.css') }}" />

        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/custom.css') }}" />

        <!--[if lte IE 9]>
        <link rel="stylesheet" href="{{ asset('admin-panel/assets/css/ace-ie.min.css') }}" />
        <![endif]-->

        <!-- inline styles related to this page -->

        <!-- ace settings handler -->
        <script src="{{ asset('admin-panel/assets/js/ace-extra.min.js') }}"></script>

        <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

        <!--[if lte IE 8]>
        <script src="{{ asset('admin-panel/assets/js/html5shiv.min.js') }}"></script>
        <script src="{{ asset('admin-panel/assets/js/respond.min.js') }}"></script>
        <![endif]-->

        <link href="{{ asset('admin-panel/assets/css/toastr.min.css') }}" rel="stylesheet">

        <style>
                .ace-settings-box.open {
                        max-width: 320px;
                        max-height: 1000px;
                        padding: 0 14px;
                        border-width: 2px;
                        -webkit-transition-delay: 0s;
                        -moz-transition-delay: 0s;
                        -o-transition-delay: 0s;
                        transition-delay: 0s;
                }
        </style>
        @yield('css')
</head>
--}}

@include('layouts.includes.header')