<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách users (Frontend - Inertia)
     */
    public function index()
    {
        $users = User::select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen')
            ->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * API: Trả về tất cả users (JSON)
     * GET /api/users
     */
    public function apiIndex()
    {
        $users = User::select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen')
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
        $user = User::select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen')
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
