<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class EmasAuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLogin()
    {
        return Inertia::render('Emas/Auth/Login', [
            'canResetPassword' => false,
        ]);
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('email');
        $loginField = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('emas')->attempt([
            $loginField => $identifier,
            'password' => $request->password
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('emas.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Display the registration view.
     */
    public function showRegister()
    {
        return Inertia::render('Emas/Auth/Register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:emas_users',
            'email' => 'required|string|email|max:255|unique:emas_users',
            'phone' => 'required|string|max:255|unique:emas_users',
            'center_code' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = EmasUser::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'center_code' => $request->center_code,
            'district' => $request->district,
            'region' => $request->region,
            'password' => Hash::make($request->password),
            'role' => 'examiner', // Default role
        ]);

        Auth::guard('emas')->login($user);

        return redirect()->route('emas.dashboard');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('emas')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('emas.login');
    }
}
