<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="School & College Information Management System">

    <title>
        @if(isset($generalSetting->institute))
            {{$generalSetting->institute}}
        @else
            UNLIMITED Edu Firm
        @endif
    </title>

    <!-- Favicon -->
    @if(isset($generalSetting->favicon))
        <link rel="icon" href="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->favicon ) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            color: #1f2937;
        }
        
        .hero-title {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(90deg, #4f46e5 0%, #10b981 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3), 0 2px 4px -1px rgba(79, 70, 229, 0.1);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #0d9c6e;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3), 0 2px 4px -1px rgba(16, 185, 129, 0.1);
        }
        
        .progress-bar {
            transition: width 0.5s ease;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm py-4 px-6 sm:px-12">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center">
                @if(isset($generalSetting->logo))
                    <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" 
                         alt="{{$generalSetting->institute ?? 'UNLIMITED Edu Firm'}}" 
                         class="h-10">
                @else
                    <div class="text-2xl font-bold">
                        <span class="text-indigo-600">UNLIMITED</span>
                        <span class="text-gray-800"> Edu Firm</span>
                    </div>
                @endif
            </div>
            
            <div class="hidden md:flex space-x-8">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link text-gray-700 hover:text-indigo-600">
                            Dashboard
                        </a>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                           class="nav-link text-gray-700 hover:text-red-600">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-link text-gray-700 hover:text-indigo-600">
                            Login
                        </a>
                        @php
                            $registrationSetting = \App\Models\OnlineRegistrationSetting::where('status', 'active')
                                ->whereDate('start_date', '<=', now())
                                ->whereDate('end_date', '>=', now())
                                ->first();
                            $showRegistrationLink = isset($generalSetting->public_registration) && $generalSetting->public_registration == 1;
                            if (!$showRegistrationLink && $registrationSetting) {
                                $showRegistrationLink = true;
                            }
                        @endphp
                        @if($showRegistrationLink)
                            <a href="{{ route('online-registration.registration') }}" 
                               class="btn-secondary px-4 py-2 rounded-md text-white font-medium">
                                Register As Student
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="block py-2 text-gray-700 hover:text-indigo-600">
                        Dashboard
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="block py-2 text-gray-700 hover:text-red-600">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Logout
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block py-2 text-gray-700 hover:text-indigo-600">
                        Login
                    </a>
                    @php
                        $registrationSetting = \App\Models\OnlineRegistrationSetting::where('status', 'active')
                            ->whereDate('start_date', '<=', now())
                            ->whereDate('end_date', '>=', now())
                            ->first();
                        $showRegistrationLink = isset($generalSetting->public_registration) && $generalSetting->public_registration == 1;
                        if (!$showRegistrationLink && $registrationSetting) {
                            $showRegistrationLink = true;
                        }
                    @endphp
                    @if($showRegistrationLink)
                        <a href="{{ route('online-registration.registration') }}" 
                           class="btn-secondary block mt-2 px-4 py-2 rounded-md text-white font-medium text-center">
                            Register As Student
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow flex items-center">
        <div class="max-w-7xl mx-auto px-6 sm:px-12 py-12 w-full">
            <div class="text-center">
                @if(isset($generalSetting->logo) && !isset(auth()->user()->name))
                    <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" 
                         alt="{{$generalSetting->institute ?? 'UNLIMITED Edu Firm'}}" 
                         class="mx-auto h-32 mb-8 animate-float">
                @endif
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 hero-title animate__animated animate__fadeIn">
                    {{isset(auth()->user()->name)?'Welcome, '.auth()->user()->name:"Welcome to "}}
                </h1>
                
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-8 text-gray-800 animate__animated animate__fadeIn animate__delay-1s">
                    @if(isset($generalSetting->institute))
                        {{$generalSetting->institute}}
                    @else
                        UNLIMITED Edu Firm
                    @endif
                </h2>
                
                <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto mb-12 animate__animated animate__fadeIn animate__delay-2s">
                    {{$generalSetting->salogan ?? 'School & College Information Management System'}} 
                </p>
                
                <!-- Quick Links -->
                <div class="flex flex-wrap justify-center gap-4 mb-12 animate__animated animate__fadeIn animate__delay-3s">
                    @if (isset($data['welcome_menu']) && $data['welcome_menu']->count() > 0)
                        @foreach($data['welcome_menu'] as $Menu)
                            @if($Menu->page_type == 'content-page')
                                <a href="{{route('web.page').'/'.$Menu->slug}}" 
                                   class="btn-primary px-6 py-3 rounded-md text-white font-medium">
                                    {{ $Menu->title }}
                                </a>
                            @elseif($Menu->page_type =="predefine-link")
                                <a href="{{route('web.home').'/'.$Menu->slug}}" 
                                   class="btn-primary px-6 py-3 rounded-md text-white font-medium">
                                    {{ $Menu->title }}
                                </a>
                            @else
                                <a href="{{$Menu->link}}" target="_blank" 
                                   class="btn-primary px-6 py-3 rounded-md text-white font-medium">
                                    {{ $Menu->title }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        <a href="{{route('web.home')}}" 
                           class="btn-primary px-6 py-3 rounded-md text-white font-medium">
                            Web Page
                        </a>
                        <a href="{{route('login')}}" 
                           class="btn-secondary px-6 py-3 rounded-md text-white font-medium">
                            Login
                        </a>
                    @endif
                </div>
                
                <!-- Progress Bar -->
                <div class="max-w-xl mx-auto bg-gray-200 rounded-full h-4 mb-8 animate__animated animate__fadeIn animate__delay-4s">
                    <div id="myBar" class="bg-indigo-600 h-4 rounded-full flex items-center justify-center text-xs text-white" style="width: 0%">
                        Redirecting to Web Page...
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-6 px-6 sm:px-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto text-center text-gray-500 text-sm">
            @if(isset($generalSetting->copyright))
                {!! $generalSetting->copyright !!}
            @else
                <a href="http://businesswithtechnology.com" target="_blank" class="hover:text-indigo-600">
                    © BusinessWithTechnology
                </a>
            @endif
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        // Mobile menu toggle
        $('#mobile-menu-button').click(function() {
            $('#mobile-menu').slideToggle();
        });
        
        // Progress bar animation
        $(document).ready(function() {
            var i = 0;
            if (i == 0) {
                i = 1;
                var elem = document.getElementById("myBar");
                var width = 1;
                var id = setInterval(frame, 50);
                function frame() {
                    if (width >= 100) {
                        clearInterval(id);
                        i = 0;
                        window.location.href = "{{ route('web.home')}}";
                    } else {
                        width++;
                        elem.style.width = width + "%";
                    }
                }
            }
        });
    </script>
</body>
</html>