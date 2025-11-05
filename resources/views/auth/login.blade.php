@extends('layout.layout1')

@section('title', 'Login')

@section('content')
    <div class="max-w-md mx-auto mt-10 p-6 bg-white border border-slate-200 rounded-lg shadow-sm">
        <h2 class="text-2xl font-semibold mb-6 text-center">Login</h2>
        <form method="POST" id="login-form">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                <input id="email" type="email" name="email" required autofocus
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-sky-400">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-sky-400">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">Login</button>
            </div>
        </form>

        <div class="mt-4">
            <p class="text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-sky-600 hover:underline">Register here</a>
            </p>
        </div>
    </div>
@endsection

@section('content_headscript')
@endsection

@section('content_tailscript')
    <script>
        $('#login-form').on('submit', function (e) {
            e.preventDefault();
            const formData = {
                email: $('#email').val(),
                password: $('#password').val(),
                _token: $('input[name="_token"]').val()
            };

            $.ajax({
                type: 'POST',
                url: '{{ route("login") }}',
                data: formData,
                success: function (response) {
                    window.location.href = '{{ url("/dashboard") }}';
                    // console.log(response);
                },
                error: function (xhr) {
                    alert('Login failed. Please check your credentials and try again.');
                    console.log(xhr.responseText);
                }
            });
        });

    </script>
@endsection
