<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function show()
    {
        return view('pages.auth');
    }

    public function do_login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->with('error', 'Those details do not match our records.')
                ->withInput($request->only('email', 'form'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        $next = $request->input('next');

        $home = $user->role === 'admin'
            ? '/avenza/admin'
            : '/avenza';

        // A customer following an admin link should not land in the admin area
        if (
            $next &&
            str_starts_with($next, '/avenza/admin') &&
            $user->role !== 'admin'
        ) {
            $next = null;
        }

        // An admin following a customer link shouldn't land in the shop
        if (
            $next &&
            $user->role === 'admin' &&
            ! str_starts_with($next, '/avenza/admin')
        ) {
            $next = null;
        }

        return redirect(url($next ?: $home))
            ->with('success', 'Signed in as ' . $user->name . '.');
    }

    public function do_register(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'confirmed', 'min:8'],
            'contact_name'  => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
        ]);

        $userId = DB::table('users')->insertGetId([
            'name'       => $data['customer_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('customers')->insert([
            'user_id'       => $userId,
            'customer_name' => $data['customer_name'],
            'contact_name'  => $data['contact_name'] ?? null,
            'address'       => $data['address'] ?? null,
            'city'          => $data['city'] ?? null,
            'postal_code'   => $data['postal_code'] ?? null,
            'country'       => $data['country'] ?? null,
            'status'        => 'active',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        Auth::login(User::find($userId));

        $request->session()->regenerate();

        return redirect(url('/avenza'))
            ->with('success', 'Welcome to Avenza.');
    }

    public function do_logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(url('/auth'))
            ->with('success', 'You are signed out.');
    }
}
