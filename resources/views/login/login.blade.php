<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @if(isset($data['general_setting']->institute))
            {{$data['general_setting']->institute}} - Login
        @else
            Unlimited Edu Firm - Login
        @endif
    </title>
    <meta name="description" content="School & College Information Management System">

    @if(isset($data['general_setting']->favicon))
        <link rel="icon" href="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$data['general_setting']->favicon ) }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
             --primary-color: #0e4c8b;
            --primary-hover: #346da5;
            /* --primary-color: #4f46e5;
            --primary-hover: #4338ca; */
            --secondary-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .login-container {
            max-width: 28rem;
            width: 100%;
        }
        
        .form-input {
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .floating-menu {
            background: var(--primary-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .floating-menu:hover {
            transform: translateX(-5px);
        }
        
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }
        
        .password-toggle {
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .login-container {
                padding: 0 1rem;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="login-container">
        <div class="text-center mb-8">
            @if(isset($data['general_setting']->logo))
                <a href="{{isset($data['general_setting']->website)?$data['general_setting']->website:'#'}}" class="inline-block">
                    <img id="avatar" src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$data['general_setting']->logo) }}" 
                         alt="{{$data['general_setting']->institute ?? 'Unlimited Edu Firm'}}" 
                         class="mx-auto h-24 w-auto">
                </a>
            @else
                <div class="mx-auto h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                    <i class="fas fa-graduation-cap text-indigo-600 text-3xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">
                    @if(isset($data['general_setting']->institute))
                        {{$data['general_setting']->institute}}
                    @else
                        <span class="text-red-600">UNLIMITED</span>
                        <span class="text-indigo-600"> Edu Firm</span>
                    @endif
                </h2>
            @endif
            {{-- <p class="mt-2 text-sm text-gray-600">
                {{$data['general_setting']->salogan}}
            </p> --}}
        </div>

        @include('includes.flash_messages')

        <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10">
            <div class="mb-6 text-center">
                <h3 class="text-lg font-medium text-gray-900">Sign in to your account</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Or <a href="{{ route('online-registration.registration') }}" class="font-medium text-indigo-600 hover:text-indigo-500">register as a new student</a>
                </p>
            </div>

            <form class="mb-0 space-y-6" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                
                @if(session()->has('login_error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ session()->get('login_error') }}</h3>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if (session('status'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">{{ session('status') }}</h3>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1 relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="form-input appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               value="{{ old('email') }}"
                               placeholder="you@example.com">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="form-input appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="••••••••">
                        <button type="button" class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-500 focus:outline-none" onclick="togglePassword()">
                            <i class="far fa-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot your password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn-primary w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            @if(isset($data['general_setting']->copyright))
                {!! $data['general_setting']->copyright !!}
            @else
                <a href="http://businesswithtechnology.com" target="_blank" class="hover:text-indigo-500">© BusinessWithTechnology</a>
            @endif
        </div>
    </div>

    @if(isset($data['general_setting']->quick_menu) && $data['general_setting']->quick_menu == 1)
        <div class="fixed bottom-8 right-8 z-50">
            <div class="floating-menu rounded-lg p-4 text-white shadow-lg transform transition-all duration-300 hover:shadow-xl">
                <h3 class="font-bold text-lg mb-2 flex items-center">
                    <i class="fas fa-bolt mr-2 animate-bounce"></i> Quick Menu
                </h3>
                @if(isset($data['general_setting']->public_registration) && $data['general_setting']->public_registration == 1)
                    <a href="{{ route('online-registration.registration') }}" class="block py-1 hover:text-yellow-200 transition-colors duration-200">
                        <i class="fas fa-user-plus mr-2"></i> Student Registration
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
        
        // Add focus styles dynamically
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-indigo-200');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-indigo-200');
            });
        });
    </script>
    @include('includes.scripts.tracking')
</body>
</html>