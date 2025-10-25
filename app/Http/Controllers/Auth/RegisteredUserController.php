<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalize fields before validation for consistent uniqueness checks
        if ($request->filled('school_name')) {
            $request->merge(['school_name' => strtoupper($request->string('school_name'))]);
        }

        $request->validate([
            'name' => ['required','string','max:255'],
            'phone' => ['required','string','max:20','unique:users,phone'],
            'region' => ['required','string','max:100'],
            'school_name' => ['required','string','max:255','unique:users,school_name'],
            'school_number' => ['required','string','max:50'],
            'username' => ['required','string','max:50','unique:users,username'],
            'email' => ['required','string','lowercase','email','max:255','unique:'.User::class],
            'password' => ['required','confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->string('name'),
            'username' => $request->string('username'),
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
            'region' => $request->string('region'),
            'school_name' => $request->string('school_name'),
            'school_number' => $request->string('school_number'),
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('register.congrats', absolute: false));
    }

    /**
     * Check availability for unique fields during registration.
     */
    public function availability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field' => ['required','in:email,phone,username,school_name'],
            'value' => ['required','string'],
        ]);

        $field = $validated['field'];
        $value = $validated['value'];

        if ($field === 'school_name') {
            $value = strtoupper($value);
        }

        $exists = DB::table('users')->where($field, $value)->exists();

        return response()->json(['available' => ! $exists]);
    }
}
