<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function _logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // TODO:: log activity
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function login(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return inertia('auth/Login');
        }

        // kode dibawah ini untuk handle post

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi harus diisi.',
        ]);

        // basic validations
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // extra validations
        $data = $request->only(['email', 'password']);

        if (!Auth::attempt($data, $request->has('remember'))) {
            $validator->errors()->add('email', 'Email atau password salah!');
        } else if (!Auth::user()->active) {
            $validator->errors()->add('email', 'Akun anda tidak aktif. Silahkan hubungi tim administrator!');
            $this->_logout($request);
        } else {
            $request->session()->regenerate();
            return redirect(route('dashboard'));
        }

        return redirect()->back()->withInput()->withErrors($validator);
    }

    public function logout(Request $request)
    {
        $this->_logout($request);
        return redirect('/')->with('success', 'Anda telah logout.');
    }

    public function register(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return inertia('auth/Register');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|min:2|max:255',
            'password' => 'required|min:5|confirmed',
        ], [
            'name.min' => 'Nama terlalu pendek, minimal 2 karakter.',
            'name.max' => 'Nama terlalu panjang, maksimal 100 karakter.',

            'email.required' => 'Email harus harus diisi.',
            'email.email' => 'Email tidak valid.',
            'email.min' => 'Email terlalu pendek, minimal 2 karakter.',
            'email.max' => 'Email terlalu panjang, maksimal 255 karakter.',

            'password.required' => 'Kata sandi harus diisi.',
            'password.min' => 'Kata sandi terlalu pendek, minimal 5 karakter.',
            'password.confirmed' => 'Kata sandi yang anda konfirmasi salah.',
        ]);

        // basic validations
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->fill($request->only(['name', 'email']));
        $user->password = Hash::make($request->post('password'));
        $user->active = true;
        $user->save();

        return redirect(route('login'))->with('success', 'Pendaftaran berhasil, silahkan login.');
    }

    public function forgotPassword(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return inertia('auth/ForgotPassword');
        }
    }
}
