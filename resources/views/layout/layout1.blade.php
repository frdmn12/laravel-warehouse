<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Tailwind via CDN (for simple setup) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional app CSS (if you compile Tailwind locally) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @yield('content_headscript')
</head>

<body class="bg-white text-slate-800 antialiased">
    <header class="sticky top-0 bg-white shadow-sm z-20">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center space-x-3">
                    <a class="flex items-center gap-2" href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto" onerror="this.style.display='none'">
                        <span class="font-semibold">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>

                <div class="hidden md:flex md:items-center md:space-x-6">
                    <a class="text-sm hover:text-sky-600" href="{{ url('/') }}">Home</a>
                    <a class="text-sm hover:text-sky-600" href="{{ url('/about') }}">About</a>
                    <a class="text-sm hover:text-sky-600" href="{{ url('/products') }}">Products</a>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:block">
                        @guest
                            <a class="text-sm mr-3 hover:text-sky-600" href="{{ route('login') }}">Login</a>
                            @if (Route::has('register'))
                                <a class="text-sm hover:text-sky-600" href="{{ route('register') }}">Register</a>
                            @endif
                        @else
                            <details class="relative">
                                <summary class="list-none cursor-pointer text-sm flex items-center gap-2">
                                    {{ Auth::user()->name }}
                                </summary>
                                <div class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-sm">
                                    {{-- <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ route('profile') }}">Profile</a>
                                    <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ route('settings') }}">Settings</a> --}}
                                    <div class="border-t"></div>
                                    <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                                </li>
                        @endguest
                            </ul>
                    </div>
                </div>
        </nav>
    </header>

    <main class="py-4">
        <div class="container mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- <footer class="bg-light text-muted py-3">
        <div class="container text-center small">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </div>
    </footer> --}}

    <!-- Bootstrap 5 JS (bundle includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('content_tailscript')
</body>

</html>
