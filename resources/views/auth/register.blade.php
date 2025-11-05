@extends('layout.layout1')

@section('title', 'Register')
@section('content')
    <div class="max-w-md mx-auto mt-10 p-6 bg-white border border-slate-200 rounded-lg shadow-sm">
        <h2 class="text-2xl font-semibold mb-6 text-center">Register</h2>
        <form method="POST" id="register-form">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-1">Name</label>
                <input id="name" type="text" name="name" required autofocus
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-sky-400">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                <input id="email" type="email" name="email" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-sky-400">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-sky-400">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-sky-400">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">Register</button>
            </div>
        </form>
    </div>
@endsection

@section('content_tailscript')
    <script>
        $('#register-form').on('submit', function (e) {
            e.preventDefault();
            const formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                _token: $('input[name="_token"]').val()
            };

            $.ajax({
                type: 'POST',
                url: '/register',
                data: formData,
                success: function (response) {
                    // Handle successful registration
                    console.log(response);
                    window.location.href = '{{ url("/dashboard") }}';
                },
                error: function (xhr) {
                    // alert('Registration failed. Please check your input and try again.');
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endsection
