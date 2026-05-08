<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>
        @if(isset($generalSetting->institute))
            {{ $panel }} | {{$generalSetting->institute}}
        @else
            {{ isset($panel)?$panel:'' }} | {{ env('APP_NAME') }}
        @endif
    </title>
    @if (isset($generalSetting->favicon))
        <link rel="shortcut icon" href="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->favicon) }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->favicon) }}" type="image/x-icon">
    @endif

    <meta name="description" content="top menu &amp; navigation" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    {{-- <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" /> --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/css/fonts.googleapis.com.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/ace.min.css') }}" class="ace-main-stylesheet" id="main-ace-style" />

    <link rel="stylesheet" href="{{ asset('assets/css/ace-skins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/ace-rtl.min.css') }}" />

    {{-- Try CDN first, then fallback to local --}}
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ asset('assets/js/jquery-2.2.4.min.js') }}"><\/script>')</script>

    <script src="{{ asset('assets/js/ace-extra.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />

    <link href="https://fonts.googleapis.com/css?family=Fugaz+One|Lobster|Merienda|Righteous|Black+Ops+One|Gilda+Display" rel="stylesheet">
    
    <style>
        .chosen-container, [class*=chosen-container]{
            width: 400px !important;
            width: 100% !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    @yield('css')

    @yield('top-script')

    {{--Preloader Css--}}
    <style>
        #overlay {
            background: #E4E6E9;
            color: #666666;
            position: fixed;
            height: 100%;
            width: 100%;
            z-index: 1000;
            top: 0;
            left: 0;
            float: left;
            text-align: center;
            padding-top: 25%;
            font-size: 4em;
        }
    </style>

    {{-- for menus buttons --}}
    <style>
    /* ===== Common Menu System Styles ===== */
    .menu-navigation-container {
        background: #fff;
        padding: 10px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .menu-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .menu-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .menu-item {
        margin: 0;
    }
    
    .menu-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        background-color: #3498db;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .menu-link:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .menu-link.active {
        background-color: #27ae60;
    }
    
    .menu-highlight .menu-link {
        background-color: #f39c12;
    }
    
    .menu-highlight .menu-link:hover {
        background-color: #e67e22;
    }
    
    .menu-highlight .menu-link.active {
        background-color: #d35400;
    }
    
    .menu-link i {
        margin-right: 8px;
        font-size: 16px;
    }
    
    .menu-text {
        white-space: nowrap;
    }
    
    .menu-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #ddd, transparent);
        margin: 10px 0;
    }
    
    /* === Legacy Menu Compatibility === */
    .easy-link-menu {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        /* gap: 8px;
            display: flex */
;
    gap: 5px;
    align-items: center;
    }
    
    .easy-link-menu a.btn-sm {
        display: flex;
        align-items: center;
        /* padding: 10px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500; */
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);

        /* custom */
        border: none;
        padding: 0px 18px;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        /* gap: 8px; */
    }
    
    .easy-link-menu a.btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .easy-link-menu a.btn-sm i {
        margin-right: 3px;
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
        .menu-list, .easy-link-menu {
            gap: 6px;
        }
        
        .menu-link, .easy-link-menu a.btn-sm {
            padding: 8px 12px;
            font-size: 13px;
        }
    }
    
    @media (max-width: 768px) {
        .menu-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .easy-link-menu {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
    }

    /* filter form behaviour */
    .filter-card,.filter-form {
    /* margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    
    top: 16%;
    display: flow;
    position: fixed;
    z-index: 999; */

    /* Breadcrumbs */
.breadcrumb-container {
    background: white;
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 20px;
    box-shadow: var(--card-shadow);
    border: 1px solid var(--border);
}

.breadcrumb-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
}

.breadcrumb-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: var(--transition);
}

.breadcrumb-link:hover {
    color: #4338ca;
    text-decoration: underline;
}

.breadcrumb-divider {
    color: #cbd5e1;
    font-size: 14px;
}
}
    
</style>
{{-- /* table header buttons */ --}}
<style>
    /* Target ONLY the easy-link-menu section */
    .table-head-menu > .easy-link-menu {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        margin-right: 10px; /* Maintain space before tableTools */
    }

    /* Style ONLY buttons within easy-link-menu */
    .table-head-menu > .easy-link-menu > .bulk-action-btn {
        padding: 2px 6px !important;
        font-size: 13px !important;
        line-height: 1.3 !important;
        border-radius: 2px !important;
        min-width: auto !important;
    }

    /* Style ONLY icons within easy-link-menu buttons */
    .table-head-menu > .easy-link-menu > .bulk-action-btn > i {
        font-size: 10px !important;
        margin-right: 3px !important;
    }

    /* Add separator after delete button ONLY in easy-link-menu */
    .table-head-menu > .easy-link-menu > .btn-danger[attr-action-type="delete"] {
        position: relative;
        margin-right: 8px !important;
    }
    
    .table-head-menu > .easy-link-menu > .btn-danger[attr-action-type="delete"]::after {
        content: "|";
        color: #ddd;
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Style ONLY the certificate template dropdown */
    .table-head-menu > .col-sm-2:not(.tableTools-container) {
        display: inline-block;
        width: auto !important;
        margin-left: 0 !important;
        margin-right: 10px !important;
        vertical-align: middle;
    }

    .table-head-menu > .col-sm-2:not(.tableTools-container) > .form-control {
        height: 26px !important;
        padding: 2px 6px !important;
        font-size: 11px !important;
        width: 180px !important;
    }

    /* Responsive adjustments ONLY for easy-link-menu */
    @media (max-width: 768px) {
        .table-head-menu > .easy-link-menu {
            width: 100%;
            margin-bottom: 6px;
        }
        
        .table-head-menu > .col-sm-2:not(.tableTools-container) {
            width: 100% !important;
            margin-bottom: 6px !important;
        }
        
        .table-head-menu > .col-sm-2:not(.tableTools-container) > .form-control {
            width: 100% !important;
        }
    }
</style>

<style>
/* ========== Menu Icon Styles ========== */
.menu-icon-new {
  display: inline-block;
  width: 24px;
  text-align: center;
  margin-right: 8px;
  font-size: 16px;
  color: #6c757d;
  transition: all 0.3s ease;
}

.nav-list > li > a > .menu-icon-new {
  /* Specific styling for top-level menu icons */
  font-size: 18px;
  vertical-align: middle;
}

.nav-list > li.active > a > .menu-icon-new,
.nav-list > li:hover > a > .menu-icon-new {
  color: var(--primary-color);
}

/* Submenu icon styles */
.submenu .menu-icon-new {
  font-size: 14px;
  width: 20px;
}

/* Arrow icon styling */
.arrow {
  display: inline-block;
  font-family: 'FontAwesome';
  font-size: 12px;
  margin-left: auto;
  transition: transform 0.3s ease;
}

.nav-list > li.open > a > .arrow {
  transform: rotate(90deg);
}

/* Ensure proper alignment of menu items */
.nav-list > li > a {
  display: flex;
  align-items: center;
  padding: 10px 15px;
}

.nav-list > li > a > .menu-text {
  flex-grow: 1;
}



 .nav-container {
        display: flex;
        gap: 0.5rem;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        color: #5a6169;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .nav-item i {
        font-size: 1.1rem;
    }
    
    .nav-item:hover {
        background: #f5f7fa;
        color: #3a7bd5;
    }
    
    .nav-item.active {
        background: #3a7bd5;
        color: white;
    }
    
    .nav-item.active:hover {
        background: #2c65c4;
    }
</style>

<style>
/* Keep user-profile dropdown fully visible in top navbar */
#navbar,
#navbar .navbar-container,
#navbar .navbar-header,
#navbar .navbar-buttons,
#navbar .navbar-collapse,
#navbar .ace-nav,
#navbar .ace-nav > li {
    overflow: visible !important;
}

#navbar .ace-nav > li {
    position: relative;
}

#navbar .user-menu.dropdown-menu {
    z-index: 10050;
    right: 0;
    left: auto;
    min-width: 180px;
}
</style>


</head>