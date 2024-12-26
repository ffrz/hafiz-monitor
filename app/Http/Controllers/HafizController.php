<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hafiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HafizController extends Controller
{
    public function index()
    {
        return inertia('hafiz/Index');
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = Hafiz::query();
        $q->where('user_id', Auth::user()->id);
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['status']) && ($filter['status'] == 'active' || $filter['status'] == 'inactive')) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('phone', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('address', 'like', '%' . $filter['search'] . '%');
            });
        }

        $hafizes = $q->paginate($request->get('per_page', 10))->withQueryString();
        return response()->json($hafizes);
    }

    public function editor($id = 0)
    {
        $hafiz = $id ? Hafiz::findOrFail($id) : new Hafiz(['active' => true]);
        return inertia('hafiz/Editor', [
            'data' => $hafiz,
        ]);
    }

    public function save(Request $request)
    {
        $userId = Auth::user()->id;
        $rules = [
            'name' => 'required|max:255',
        ];
        $messages = [
            'name.required' => 'Nama hafidz harus diisi.',
            'name.max' => 'Nama hafidz maksimal 255 karakter.',
        ];
        $hafiz = null;
        $message = '';
        $fields = ['name', 'gender', 'birth_date', 'phone', 'address', 'active', 'notes'];
        if (!$request->id) {
            $request->validate($rules, $messages);
            $hafiz = new Hafiz();
            $message = 'Hafiz telah ditambahkan.';
        } else {
            $request->validate($rules, $messages);
            $hafiz = Hafiz::findOrFail($request->id);
            $message = "Hafiz {$hafiz->name} telah diperbarui.";
        }
        $data = $request->only($fields);
        $hafiz->fill($data);
        $hafiz->user_id = $userId;
        $hafiz->save();

        return redirect(route('hafiz.index'))->with('success', $message);
    }

    public function delete($id)
    {
        $hafiz = Hafiz::findOrFail($id);
        $hafiz->delete();

        return response()->json([
            'message' => "Hafiz {$hafiz->name} telah dihapus!"
        ]);
    }
}
