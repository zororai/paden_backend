<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return back()->withErrors([
                'email' => 'DEBUG: User not found with this email.',
            ])->withInput($request->only('email'));
        }

        try {
            $passwordValid = Hash::check($request->password, $user->password);
        } catch (\RuntimeException $e) {
            // Password in database is not a valid hash - treat as invalid credentials
            return back()->withErrors([
                'email' => 'DEBUG: Password hash invalid. Hash starts with: ' . substr($user->password, 0, 10),
            ])->withInput($request->only('email'));
        }

        if (!$passwordValid) {
            return back()->withErrors([
                'email' => 'DEBUG: Password incorrect. Role: ' . $user->role . ', Verified: ' . ($user->email_verified_at ? 'Yes' : 'No'),
            ])->withInput($request->only('email'));
        }

        // Check if user has admin role
        if ($user->role !== 'admin') {
            return back()->withErrors([
                'email' => 'DEBUG: Role is "' . $user->role . '" not "admin".',
            ])->withInput($request->only('email'));
        }

        // Check if email is verified
        if (is_null($user->email_verified_at)) {
            return back()->withErrors([
                'email' => 'DEBUG: Email not verified.',
            ])->withInput($request->only('email'));
        }
    
        // Log in user for session-based access
        Auth::login($user, $request->filled('remember'));
    
        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
