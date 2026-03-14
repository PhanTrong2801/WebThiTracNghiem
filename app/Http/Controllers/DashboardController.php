<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NhomQuyen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $manhomquyen = $user->manhomquyen;

        // Lấy tên nhóm quyền
        $nhomQuyen = NhomQuyen::find($manhomquyen);
        $roleName = $nhomQuyen ? $nhomQuyen->tennhomquyen : 'Người dùng';

        // Lấy danh sách quyền của user
        $permissions = $user->getRolePermissions();

        // Thống kê chung
        $stats = [];

        // Nếu có quyền quản lý người dùng hoặc nhóm quyền (tương đương Admin)
        if (isset($permissions['nguoidung']) || isset($permissions['nhomquyen'])) {
            $stats = [
                'totalKhoa' => DB::table('khoa')->count(),
                'totalUsers' => DB::table('users')->count(),
                'totalAdmins' => DB::table('users')->where('manhomquyen', 1)->count(),
                'totalTeachers' => DB::table('users')->where('manhomquyen', 2)->count(),
                'totalSubjects' => Schema::hasTable('monhoc') ? DB::table('monhoc')->count() : 0,
                'totalChapters' => Schema::hasTable('chuong') ? DB::table('chuong')->count() : 0,
                'totalStudents' => DB::table('users')->where('manhomquyen', 3)->count(),
                'totalRoles' => DB::table('nhomquyen')->where('trangthai', 1)->count(),
            ];

        } 
        // Nếu có quyền quản lý giảng dạy (tương đương Giảng viên)
        elseif (isset($permissions['dethi']) || isset($permissions['hocphan']) || isset($permissions['cauhoi'])) {
            $stats = [
                'totalStudents' => DB::table('users')->where('manhomquyen', 3)->count(),
            ];
        } 
        // Còn lại (tương đương Sinh viên)
        else {
            $stats = [];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'roleName' => $roleName,
            'roleId' => $manhomquyen,
        ]);
    }
}
