<?php

namespace App\Http\Controllers;

use App\Models\PhanCongModel;
use App\Models\MonHocModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    /**
     * Phân quyền cơ bản - Inertia sẽ xử lý lỗi 403
     */
    private function checkPermission($action)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Chưa đăng nhập');
        }
        
        $permissions = $user->getRolePermissions();
        
        if (!isset($permissions['phancong']) || !in_array($action, $permissions['phancong'])) {
            abort(403, 'Bạn không có quyền: ' . $action);
        }
    }

    /**
     * Danh sách phân công
     */
    public function index(Request $request)
    {
        $this->checkPermission('view');

        $query = PhanCongModel::with(['monhoc', 'nguoidung']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                /** @var \Illuminate\Database\Eloquent\Builder $q */
                $q->whereHas('monhoc', function ($mh) use ($s) {
                    $mh->where('tenmonhoc', 'like', "%$s%")->orWhere('mamonhoc', 'like', "%$s%");
                })->orWhereHas('nguoidung', function ($nd) use ($s) {
                    $nd->where('hoten', 'like', "%$s%");
                });
            });
        }

        $danhSachPhanCong = $query->get()->map(fn($pc) => [
            'manguoidung' => $pc->manguoidung,
            'mamonhoc'    => $pc->mamonhoc,
            'hoten'       => $pc->nguoidung?->hoten,
            'tenmonhoc'   => $pc->monhoc?->tenmonhoc,
        ]);

        // Giảng viên (những user có quyền cauhoi/monhoc/hocphan)
        $giangViens = DB::table('users as nd')
            ->join('chitietquyen as ctq', 'nd.manhomquyen', '=', 'ctq.manhomquyen')
            ->leftJoin('khoa as k', 'nd.makhoa', '=', 'k.id')
            ->whereIn('ctq.chucnang', ['cauhoi', 'monhoc', 'hocphan', 'chuong'])
            ->groupBy('nd.id', 'nd.hoten', 'nd.makhoa', 'k.tenkhoa')
            ->select('nd.id', 'nd.hoten', 'nd.makhoa', 'k.tenkhoa')
            ->get();

        $danhSachMonHoc = MonHocModel::active()->get(['mamonhoc', 'tenmonhoc', 'makhoa', 'sotinchi', 'sotietlythuyet', 'sotietthuchanh']);
        
        $danhSachKhoa = DB::table('khoa')->get(['id', 'tenkhoa']);

        return Inertia::render('Assignment/Index', [
            'danhSachPhanCong' => $danhSachPhanCong,
            'danhSachGiangVien' => $giangViens,
            'danhSachMonHoc' => $danhSachMonHoc,
            'danhSachKhoa' => $danhSachKhoa,
            'filters' => ['search' => $request->search],
        ]);
    }

    /**
     * Thêm phân công (giang viên – danh sách môn)
     */
    public function store(Request $request)
    {
        $this->checkPermission('create');

        $request->validate([
            'manguoidung'  => 'required|string',
            'danhSachMon'  => 'required|array|min:1',
            'danhSachMon.*' => 'string|exists:monhoc,mamonhoc',
        ]);

        $user = UserModel::findOrFail($request->manguoidung);
        $userKhoa = $user->makhoa;

        foreach ($request->danhSachMon as $mamonhoc) {
            $monHoc = MonHocModel::where('mamonhoc', $mamonhoc)->first();
            
            // Ràng buộc: Giảng viên có khoa thì phải dạy môn của khoa đó hoặc môn chung
            if ($userKhoa !== null && $monHoc->makhoa !== null && $monHoc->makhoa != $userKhoa) {
                return redirect()->back()->with('error', "Môn {$monHoc->tenmonhoc} không thuộc khoa của giảng viên này.");
            }

            // Kiểm tra xem phân công đã tồn tại chưa
            $existing = PhanCongModel::where('mamonhoc', $mamonhoc)
                ->where('manguoidung', $request->manguoidung)
                ->first();

            if ($existing) {
                // Nếu đã có phân công trước đó (đã bị xóa), thì tạo lại
                PhanCongModel::firstOrCreate([
                    'mamonhoc'    => $mamonhoc,
                    'manguoidung' => $request->manguoidung,
                ]);
            } else {
                // Tạo mới phân công
                PhanCongModel::firstOrCreate([
                    'mamonhoc'    => $mamonhoc,
                    'manguoidung' => $request->manguoidung,
                ]);
            }

            // Cập nhật duocday = 1 để giáo viên có thể quản lý hiển thị nhóm
            DB::table('nhom')->where('mamonhoc', $mamonhoc)->update(['duocday' => 1]);
            DB::table('dethi')->where('monthi', $mamonhoc)->update(['duocday' => 1]);
        }

        return redirect()->back()->with('success', 'Phân công thành công');
    }

    /**
     * Xóa một phân công (môn cụ thể)
     */
    public function destroy($mamonhoc, $uid)
    {
        $this->checkPermission('delete');

        // Ẩn tất cả nhóm học phần và đề thi của môn này
        DB::table('nhom')->where('mamonhoc', $mamonhoc)->update(['duocday' => 0]);
        DB::table('dethi')->where('monthi', $mamonhoc)->update(['duocday' => 0]);

        // Xóa phân công
        PhanCongModel::where('mamonhoc', $mamonhoc)
            ->where('manguoidung', $uid)->delete();

        // Inertia cần redirect, không phải JSON
        if (request()->header('X-Inertia')) {
            return redirect()->back()->with('success', 'Đã xóa phân công.');
        }
        return response()->json(['message' => 'Đã xóa phân công']);
    }

    /**
     * Xoá toàn bộ phân công của một giảng viên
     */
    public function destroyAll($uid)
    {
        $this->checkPermission('delete');

        // Lấy danh sách môn của giảng viên này
        $monHocs = PhanCongModel::where('manguoidung', $uid)->pluck('mamonhoc');

        // Ẩn tất cả nhóm học phần và đề thi của các môn này
        DB::table('nhom')->whereIn('mamonhoc', $monHocs)->update(['duocday' => 0]);
        DB::table('dethi')->whereIn('monthi', $monHocs)->update(['duocday' => 0]);

        // Xóa phân công
        PhanCongModel::where('manguoidung', $uid)->delete();

        // Inertia cần redirect, không phải JSON
        if (request()->header('X-Inertia')) {
            return redirect()->back()->with('success', 'Đã xóa tất cả phân công.');
        }
        return response()->json(['message' => 'Đã xóa tất cả phân công']);
    }

    /**
     * JSON: lấy danh sách môn đã phân công cho 1 giảng viên
     */
    public function getByUser($uid)
    {
        $list = PhanCongModel::where('manguoidung', $uid)->pluck('mamonhoc');
        return response()->json($list);
    }
}
