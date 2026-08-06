{{-- Version stamped from the file's own timestamp: without it a browser keeps serving the
     paper.css it cached, and a design change looks like it simply did not happen. --}}
@php($paperCss = public_path('assets/css/paper.css'))
<link rel="stylesheet" href="{{ asset('assets/css/paper.css') }}?v={{ is_file($paperCss) ? filemtime($paperCss) : 1 }}">
