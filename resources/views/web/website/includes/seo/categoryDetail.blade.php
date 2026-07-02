<title>{{ isset($data['row']->title)?$data['row']->title:$generalSetting->site_title}} </title>
<meta name="description" content="{!!  substr((isset($data['row']->description)?$data['row']->description:$generalSetting->site_desc),0,200) !!}">
<meta name="keywords" content="{{ isset($generalSetting->site_keyword)?$generalSetting->site_keyword:"" }}">
<meta property="og:image" content="{{ asset(isset($data['row']->image_name)?asset('web/category/'.$data['row']->image_name):'images/general_settings/'. $generalSetting->page_banner) }}">
<meta name="copyright" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}">
<meta name="author" content="{{$data['row']->created_by_name}}"/>
<meta name="application-name" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}">

<!--Facebook Tags-->
<meta property="og:site_name" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}">
<meta property="og:type" content="article"/>
<meta property="og:url" content="{{ request()->fullUrl() }}"/>
<meta property="og:title" content="{{ isset($data['row']->title)?$data['row']->title:$generalSetting->site_title }}"/>
<meta property="og:description" content="{!! $data['row']->description !!}"/>
<meta property="og:image" content="{{ asset(isset($data['row']->image_name)?asset('web/category/'.$data['row']->image_name):'images/general_settings/'. $generalSetting->page_banner) }}"/>
<meta property="article:author" content="{{$data['row']->created_by_name}}"/>
<meta property="og:locale" content="{{isset($contact_info->address)?$contact_info->address:''}}"/>

<!--Twitter Tags-->
<meta name="twitter:card" content="summary"/>
<meta name="twitter:site" content="{{isset($generalSetting->site_title)?$generalSetting->site_title:''}}"/>
<meta name="twitter:title" content="{{ isset($data['row']->title)?$data['row']->title:$generalSetting->site_title }}"/>
<meta name="twitter:description" content="{!!  substr((isset($data['row']->description)?$data['row']->description:$generalSetting->site_desc),0,200) !!}"/>
<meta name="twitter:image" content="{{ asset(isset($data['row']->image_name)?asset('web/category/'.$data['row']->image_name):'images/general_settings/'. $generalSetting->page_banner) }}"/>