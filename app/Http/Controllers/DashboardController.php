<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\NhomQuyenModel;
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
        $nhomQuyen = NhomQuyenModel::find($manhomquyen);
        $roleName = $nhomQuyen ? $nhomQuyen->tennhomquyen : 'Người dùng';

        // Lấy tên khoa
        $tenKhoa = $user->khoa ? $user->khoa->tenkhoa : 'Không có';

        // Lấy danh sách quyền của user
        $permissions = $user->getRolePermissions();

        // Thống kê chung (tuỳ theo quyền)
        $stats = [];

        // Thống kê Admin / Quản trị
        if (isset($permissions['khoa'])) {
            $stats['totalKhoa'] = DB::table('khoa')->count();
        }
        if (isset($permissions['nguoidung'])) {
            $stats['totalUsers'] = DB::table('users')->count();
            $stats['totalAdmins'] = DB::table('users')->where('manhomquyen', 1)->count();
            // Thêm tổng số giảng viên - sinh viên nếu có quyền quản trị người dùng
            if (!isset($stats['totalTeachers'])) {
                $stats['totalTeachers'] = DB::table('users')->where('manhomquyen', 2)->count();
            }
            if (!isset($stats['totalStudents'])) {
                $stats['totalStudents'] = DB::table('users')->where('manhomquyen', 3)->count();
            }
        }
        if (isset($permissions['monhoc'])) {
            $stats['totalSubjects'] = Schema::hasTable('monhoc') ? DB::table('monhoc')->count() : 0;
        }
        if (isset($permissions['chuong'])) {
            $stats['totalChapters'] = Schema::hasTable('chuong') ? DB::table('chuong')->count() : 0;
        }
        if (isset($permissions['nhomquyen'])) {
            $stats['totalRoles'] = DB::table('nhomquyen')->where('trangthai', 1)->count();
        }

        // Thống kê Giảng viên (nếu chưa có từ quyền người dùng)
        if (isset($permissions['dethi']) || isset($permissions['hocphan']) || isset($permissions['cauhoi'])) {
            if (!isset($stats['totalStudents'])) {
                $stats['totalStudents'] = DB::table('users')->where('manhomquyen', 3)->count();
            }
            $stats['teacherStats'] = true;
            // Số lượng nhóm học phần do giảng viên này quản lý (Bảng nhom dùng cột giangvien)
            $stats['totalManagedGroups'] = DB::table('nhom')->where('giangvien', $user->id)->count();
            // Số lượng câu hỏi (Bảng cauhoi dùng cột nguoitao)
            $stats['totalQuestionsCreated'] = DB::table('cauhoi')->where('nguoitao', $user->id)->count();
        }

        // Thống kê Sinh viên
        if (isset($permissions['tghocphan']) || isset($permissions['tgthi'])) {
            $stats['studentStats'] = true;
            $stats['totalJoinedGroups'] = DB::table('chitietnhom')->where('manguoidung', $user->id)->count();
            // Bạn có thể thêm thống kê bài thi sắp tới nếu có bảng dethi/lichthi
            $stats['totalUpcomingTests'] = 0; // Tạm thời để 0 nếu chưa có logic thi
            $stats['totalTestResults'] = 0;   // Tạm thời để 0
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'roleName' => $roleName,
            'roleId' => $manhomquyen,
            'tenKhoa' => $tenKhoa,
        ]);
    }
}
