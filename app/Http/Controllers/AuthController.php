<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Check if banned
            if ($user->is_banned) {
                Auth::logout();
                return back()->with('error', 'Your account has been suspended');
            }

            // Check organizer approval status
            if ($user->role === 'organizer') {
                if (!$user->is_approved) {
                    Auth::logout();
                    return back()->with('error', 'Your organizer account has been rejected or is pending approval');
                }
            }

            // Log successful login
            Log::info('User logged in:', ['email' => $user->email]);

            return match($user->role) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'organizer' => redirect()->intended(route('organizer.dashboard')),
                default => redirect()->intended(route('home')),
            };
        }

        // Log failed login attempt
        Log::warning('Failed login attempt:', ['email' => $request->email]);

        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Invalid credentials');
    }

    // Show register page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle register request
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
            'birthday' => 'required|date|before:' . Carbon::now()->subYears(13)->format('Y-m-d'),
            'role' => 'required|in:gamer,organizer',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'nickname' => $validated['nickname'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'birthday' => $validated['birthday'],
            'role' => $validated['role'],
            'is_approved' => null, // Set to null for approval
            'requested_organizer' => $validated['role'] === 'organizer' ? true : false,
        ]);

        // For organizer registration, just return success message without notifications
        if ($user->role === 'organizer') {
            return redirect()->route('login')->with([
                'success' => 'Organizer account created! Please wait for admin approval.',
                'is_organizer' => true
            ]);
        }

        // Auto-login for gamers
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Registration successful!');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}