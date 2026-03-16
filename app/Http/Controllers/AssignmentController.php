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
     * Phân quyền cơ bản
     */
    private function checkPermission($action)
    {
        $permissions = auth()->user()->getRolePermissions();
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

            PhanCongModel::firstOrCreate([
                'mamonhoc'    => $mamonhoc,
                'manguoidung' => $request->manguoidung,
            ]);
        }

        return redirect()->back()->with('success', 'Phân công thành công');
    }

    /**
     * Xóa một phân công (môn cụ thể)
     */
    public function destroy($mamonhoc, $uid)
    {
        $this->checkPermission('delete');

        PhanCongModel::where('mamonhoc', $mamonhoc)
            ->where('manguoidung', $uid)->delete();

        return redirect()->back()->with('success', 'Đã xóa phân công');
    }

    /**
     * Xoá toàn bộ phân công của một giảng viên
     */
    public function destroyAll($uid)
    {
        $this->checkPermission('delete');
        PhanCongModel::where('manguoidung', $uid)->delete();
        return redirect()->back()->with('success', 'Đã xóa tất cả phân công');
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
