@if (isset($data['home_setting']) && $data['home_setting']->slider_status == 1)
{{--
<link rel="stylesheet" href="{{ asset('website/css/plugins/revolution/css/settings.css') }}">
<!-- REVOLUTION SETTINGS STYLES -->
<link rel="stylesheet" href="{{ asset('website/css/plugins/revolution/css/layers.css') }}">
<!-- REVOLUTION LAYERS STYLES -->
<link rel="stylesheet" href="{{ asset('website/css/plugins/revolution/css/navigation.css') }}">
<!-- REVOLUTION NAVIGATION STYLES -->
--}}

{{--Full Width Slider--}}
<style>
    /* full width slider */
    .carousel {
        width: 100%;
        margin: 0 auto;
        padding-bottom: 50px;
        overflow: hidden;
        position: relative;
        z-index: 10;
        height: 600px;
        margin-top: 0px;
        margin-bottom: 0px;
    }

    .carousel-item{
        height: 600px;
    }


    .carousel-item{
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
    }

    .carousel-caption {
        bottom: 15%;
        padding: 20px;
        background-color: #043c8e;
        border-top: 5px #ff9a04 solid;
        height: fit-content;
        color: white;
    }

    .carousel-caption h5 {
        font-weight: 600;
        color:white;
    }

    .carousel-caption .btns-box a {
        text-decoration: none;
        outline: none;
        border-color: #ff9a04;
        color: white;
    }
    .carousel-caption .btns-box a:hover {
        text-decoration: none;
        outline: none;
        border-color: white;
        color: white;
    }

    /*.carousel-control-prev-icon,.carousel-control-prev-icon, .carousel-control-next-icon {
        width: 60px;
        height: 60px;
        line-height: 60px;
        background-color: rgba(255,255,255,0.80);
    }*/

</style>

@endif
