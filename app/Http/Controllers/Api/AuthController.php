<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => trim(strtoupper($credentials['name'])),
                'email' => trim($credentials['email']),
                'password' => bcrypt(trim($credentials['password'])),
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to register: ' . $e->getMessage(),
            ], 500);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return new UserResource($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah.',
                'errors' => ['email' => ['Email atau password salah.']],
            ], 422);
        }

        $request->session()->regenerate();

        return new UserResource(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
