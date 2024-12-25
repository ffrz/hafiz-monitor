<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return inertia('user/Index');
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = User::query();
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['role'] && $filter['role'] != 'all')) {
            $q->where('role', '=', $filter['role']);
        }

        if (!empty($filter['status']) && ($filter['status'] == 'active' || $filter['status'] == 'inactive')) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('email', 'like', '%' . $filter['search'] . '%');
            });
        }

        $users = $q->paginate($request->get('per_page', 10))->withQueryString();

        return response()->json($users);
    }

    public function editor($id = 0)
    {
        $user = $id ? User::findOrFail($id) : new User();

        if (!$id) {
            $user->active = true;
            $user->admin = true;
        } else if ($user == Auth::user()) {
            return redirect(route('user.index'))->with('warning', 'TIdak dapat mengubah akun anda sendiri.');
        }

        return inertia('user/Editor', [
            'data' => $user,
        ]);
    }

    public function save(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'email' => 'email|min:3|max:100',
            'password' => 'required|min:5|max:40',
        ];
        $messages = [
            'name.required' => 'Nama pengguna harus diisi',
            'name.max' => 'Nama pengguna maksimal 255 karakter',
            'password.required' => 'Kata sandi harus diisi',
            'password.min' => 'Kata sandi minimal 5 karakter',
            'password.max' => 'Kata sandi maksimal 40 karakter',
        ];
        $user = null;
        $message = '';
        $fields = ['name', 'email', 'active'];
        $password = $request->get('password');
        if (!$request->id) {
            $rules['email'] = "required|email|max:255|unique:users,email,NULL,id";
            $request->validate($rules, $messages);
            $user = new User();
            $message = 'Pengguna baru telah dibuat.';
        } else {
            // username harus unik untuk masing-masing company_id, exclude id
            $rules['email'] = "required|email|max:255|unique:users,email,{$request->id},id";
            if (empty($request->get('password'))) {
                // kalau password tidak diisi, skip validation dan jangan update password
                unset($rules['password']);
                unset($fields['password']);
            }
            $request->validate($rules, $messages);
            $user = User::findOrFail($request->id);
            $message = "Pengguna {$user->username} telah diperbarui.";
        }

        if (!empty($password)) {
            $user->password = Hash::make($password);
        }
        $user->fill($request->only($fields));
        $user->save();

        return redirect(route('user.index'))->with('success', $message);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == Auth::user()->id) {
            return response()->json([
                'message' => 'Tidak dapat menghapus akun sendiri!'
            ], 409);
        }

        $user->delete();

        return response()->json([
            'message' => "Pengguna {$user->email} telah dihapus!"
        ]);
    }
}
