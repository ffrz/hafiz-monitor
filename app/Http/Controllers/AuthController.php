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
        ]);

        // basic validations
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // extra validations
        $data = $request->only(['email', 'password']);

        if (!Auth::attempt($data, $request->has('remember'))) {
            $validator->errors()->add('email', __('messages.login-failed_invalid-email-or-password'));
        } else if (!Auth::user()->active) {
            $validator->errors()->add('email', __('messages.login-failed_inactive-account'));
            $this->_logout($request);
        } else {
            $request->session()->regenerate();
            return redirect(route('memorization.index'));
        }

        return redirect()->back()->withInput()->withErrors($validator);
    }

    public function logout(Request $request)
    {
        $this->_logout($request);
        return redirect('/')->with('success', __('messages.logout-success'));
    }

    public function register(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return inertia('auth/Register');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'email' => 'required|unique:users,email|min:2|max:255',
            'password' => 'required|min:5|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->fill($request->only(['name', 'email']));
        $user->password = Hash::make($request->post('password'));
        $user->active = true;
        $user->save();

        return redirect(route('login'))->with('success', 'messages.registration-success');
    }

    public function forgotPassword(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return inertia('auth/ForgotPassword');
        }
    }
}
