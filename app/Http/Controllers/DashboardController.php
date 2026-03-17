<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\NhomQuyenModel;
use App\Models\DeThiModel;
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
            // Số lượng đề thi đã tạo (Bảng dethi dùng cột nguoitao)
            $stats['totalExamsCreated'] = DB::table('dethi')->where('nguoitao', $user->id)->count();
        }

        // Thống kê Sinh viên
        if (isset($permissions['tghocphan']) || isset($permissions['tgthi'])) {
            $stats['studentStats'] = true;
            $stats['totalJoinedGroups'] = DB::table('chitietnhom')->where('manguoidung', $user->id)->count();
            
            // IDs các nhóm sinh viên tham gia
            $nhomIds = DB::table('chitietnhom')->where('manguoidung', $user->id)->pluck('manhom');

            // IDs các môn học sinh viên tham gia (qua bất kỳ nhóm nào)
            $mamonhocIds = DB::table('nhom')
                ->whereIn('manhom', $nhomIds)
                ->pluck('mamonhoc');

            // 1. Bài thi sắp tới: Thuộc môn tham gia, chưa làm, (chưa hết hạn hoặc không hạn)
            $stats['totalUpcomingTests'] = DeThiModel::query()
                ->whereIn('monthi', $mamonhocIds)
                ->where(function ($query) {
                    $query->where('thoigianketthuc', '>', now())
                        ->orWhereNull('thoigianketthuc');
                })
                ->whereNotExists(function ($query) use ($user) {
                    $query->select(DB::raw(1))
                        ->from('ketqua')
                        ->whereRaw('ketqua.made = dethi.made')
                        ->where('ketqua.manguoidung', $user->id);
                })
                ->count();

            // 2. Kết quả bài thi: Đã làm (có điểm) và thuộc các môn tham gia
            $stats['totalTestResults'] = DB::table('ketqua')
                ->where('manguoidung', $user->id)
                ->whereNotNull('diemthi')
                ->whereIn('made', function($q) use ($mamonhocIds) {
                    $q->select('made')->from('dethi')->whereIn('monthi', $mamonhocIds);
                })
                ->count();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'roleName' => $roleName,
            'roleId' => $manhomquyen,
            'tenKhoa' => $tenKhoa,
        ]);
    }
}
