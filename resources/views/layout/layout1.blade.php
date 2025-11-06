<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" />
    <!-- Optional app CSS (if you compile Tailwind locally) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

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
                    <a class="text-sm hover:text-sky-600" href="{{
    Auth::check() ? url('/dashboard') : url('/')
                        }}">Home</a>
                    <a class="text-sm hover:text-sky-600" href="{{ url('/about') }}">About</a>
                    {{-- <a class="text-sm hover:text-sky-600" href="{{ url('/products') }}">Products</a> --}}
                    <details class="relative">
                        <summary class="list-none cursor-pointer text-sm flex items-center gap-2 hover:text-sky-600">
                            Products
                            <svg class="w-3 h-3 ml-1 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>

                        <div class="absolute left-0 mt-2 w-48 bg-white border rounded shadow-sm z-20">
                            <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ url('/products') }}">Master Barang</a>
                            <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ url('/stock-product') }}">Stok Barang</a>
                            <a class="block px-3 py-2 text-sm hover:bg-slate-50" href="{{ url('/transaction-history') }}">Riwayat Transaksi</a>
                        </div>
                    </details>
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

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
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
            {{-- button back --}}
            <a href="javascript:history.back()" class="mb-4 inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
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

    <!-- jQuery + Bootstrap + DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/autofill/2.7.1/js/dataTables.autoFill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>


    <script src="{{ asset('js/app.js') }}"></script>
    @yield('content_tailscript')
</body>

</html>
