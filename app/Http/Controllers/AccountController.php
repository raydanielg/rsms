<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function requestDeletion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required','string'],
            'reason' => ['nullable','string','max:1000'],
        ]);

        $user = $request->user();
        if (!Hash::check($data['password'], $user->password)) {
            return back()->with('error', 'Incorrect password');
        }

        DB::table('account_deletion_requests')->insert([
            'user_id' => $user->id,
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Your delete request has been sent to admin.');
    }
}
