<title>{{ isset($data['row']->seo_title)?$data['row']->seo_title:$data['row']->title }} </title>
<meta name="description" content="{{substr((isset($data['row']->seo_description)?$data['row']->seo_description:strip_tags(htmlspecialchars_decode($data['row']->description))),0,200) }}">
<meta name="keywords" content="{{ isset($data['row']->seo_keyword)?$data['row']->seo_keyword:"" }}">
<meta property="og:image" content="{{ asset(isset($data['row']->image)?asset('web/events/'.$data['row']->image):'images/general_settings/'. $generalSetting->page_banner) }}">
<meta name="copyright" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}">
<meta name="author" content="{{$data['row']->created_by_name}}"/>
<meta name="application-name" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}">

<!--Facebook Tags-->
<meta property="og:site_name" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}">
<meta property="og:type" content="article"/>
<meta property="og:url" content="{{ request()->fullUrl() }}"/>
<meta property="og:title" content="{{ isset($data['row']->seo_title)?$data['row']->seo_title:$data['row']->title }}"/>
<meta property="og:description" content="{{substr((isset($data['row']->seo_description)?$data['row']->seo_description:strip_tags(htmlspecialchars_decode($data['row']->description))),0,200) }}"/>
<meta property="og:image" content="{{ asset(isset($data['row']->image)?asset('web/events/'.$data['row']->image):'images/general_settings/'. $generalSetting->page_banner) }}"/>
<meta property="article:author" content="{{$data['row']->created_by_name}}"/>
<meta property="og:locale" content="{{isset($contact_info->address)?$contact_info->address:''}}"/>

<!--Twitter Tags-->
<meta name="twitter:card" content="summary"/>
<meta name="twitter:site" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}"/>
<meta name="twitter:title" content="{{ isset($data['row']->seo_title)?$data['row']->seo_title:$data['row']->title }}"/>
<meta name="twitter:description" content="{{substr((isset($data['row']->seo_description)?$data['row']->seo_description:strip_tags(htmlspecialchars_decode($data['row']->description))),0,200) }}"/>
<meta name="twitter:image" content="{{ asset(isset($data['row']->image)?asset('web/events/'.$data['row']->image):'images/general_settings/'. $generalSetting->page_banner) }}"/>