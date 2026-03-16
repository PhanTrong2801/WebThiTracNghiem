<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách users (Frontend - Inertia)
     */
    public function index(Request $request)
    {
        $query = UserModel::with(['khoa', 'nhomquyen'])
            ->select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen', 'makhoa');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('hoten', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('id', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only('search')
        ]);
    }

    /**
     * API: Trả về tất cả users (JSON)
     * GET /api/users
     */
    public function apiIndex()
    {
        $users = UserModel::select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $users,
            'total' => $users->count(),
        ]);
    }

    /**
     * API: Trả về 1 user theo id (JSON)
     * GET /api/users/{id}
     */
    public function apiShow(string $id)
    {
        $user = UserModel::select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen')
            ->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found with id: ' . $id,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}
