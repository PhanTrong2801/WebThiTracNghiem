<?php

namespace App\Http\Controllers;

use App\Models\NhomModel;
use App\Models\ChiTietNhomModel;
use App\Models\MonHocModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ModuleController extends Controller
{
    /**
     * Danh sách nhóm của giảng viên đang đăng nhập
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $hienthi = $request->query('hienthi', 1); // 1=đang giảng, 0=đã ẩn, all=tất cả

        $query = NhomModel::with(['monhoc'])->active()->where('giangvien', $userId);

        if ($hienthi !== 'all') {
            $query->where('hienthi', (int) $hienthi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tennhom', 'like', '%' . $search . '%')
                  ->orWhereHas('monhoc', function($mq) use ($search) {
                      $mq->where('tenmonhoc', 'like', '%' . $search . '%');
                  });
            });
        }

        $nhoms = $query->orderBy('namhoc', 'desc')->orderBy('hocky', 'desc')->orderBy('manhom', 'desc')->get();

        // Group theo môn học → năm học → học kỳ
        $grouped = [];
        foreach ($nhoms as $nhom) {
            $key = $nhom->mamonhoc . '_' . $nhom->namhoc . '_' . $nhom->hocky;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'mamonhoc'  => $nhom->mamonhoc,
                    'tenmonhoc' => $nhom->monhoc?->tenmonhoc ?? $nhom->mamonhoc,
                    'namhoc'    => $nhom->namhoc,
                    'hocky'     => $nhom->hocky,
                    'nhom'      => [],
                ];
            }
            $grouped[$key]['nhom'][] = [
                'manhom'   => $nhom->manhom,
                'tennhom'  => $nhom->tennhom,
                'ghichu'   => $nhom->ghichu,
                'siso'     => $nhom->siso,
                'hienthi'  => $nhom->hienthi,
                'mamoi'    => $nhom->mamoi,
            ];
        }

        // Chỉ lấy danh sách môn mà giảng viên này ĐƯỢC PHÂN CÔNG
        $monHocs = MonHocModel::active()
            ->whereIn('mamonhoc', function($q) use ($userId) {
                $q->select('mamonhoc')->from('phancong')->where('manguoidung', $userId);
            })
            ->get(['mamonhoc', 'tenmonhoc']);

        return Inertia::render('Module/Index', [
            'danhSachNhom' => array_values($grouped),
            'danhSachMonHoc' => $monHocs,
            'filters' => [
                'hienthi' => $hienthi,
                'search'  => $request->search,
            ],
        ]);
    }

    /**
     * Tạo nhóm mới (có mã mời ngẫu nhiên)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tennhom'  => 'required|string|max:255',
            'mamonhoc' => [
                'required', 
                'string', 
                'exists:monhoc,mamonhoc',
                function ($attribute, $value, $fail) {
                    $isAssigned = DB::table('phancong')
                        ->where('manguoidung', Auth::id())
                        ->where('mamonhoc', $value)
                        ->exists();
                    if (!$isAssigned) {
                        $fail('Bạn không được phân công dạy môn học này.');
                    }
                }
            ],
            'namhoc'   => 'required|integer',
            'hocky'    => 'required|integer|in:1,2,3',
            'ghichu'   => 'nullable|string|max:255',
        ]);

        // Tạo mã mời không trùng
        do {
            $mamoi = substr(md5(mt_rand()), 0, 7);
        } while (NhomModel::where('mamoi', $mamoi)->exists());

        NhomModel::create([
            'tennhom'   => $request->tennhom,
            'mamonhoc'  => $request->mamonhoc,
            'namhoc'    => $request->namhoc,
            'hocky'     => $request->hocky,
            'ghichu'    => $request->ghichu,
            'giangvien' => Auth::id(),
            'mamoi'     => $mamoi,
        ]);

        return redirect()->back()->with('success', 'Thêm nhóm thành công');
    }

    /**
     * Cập nhật thông tin nhóm
     */
    public function update(Request $request, $id)
    {
        $nhom = NhomModel::findOrFail($id);
        $this->authorize_owner($nhom);

        $request->validate([
            'tennhom'  => 'required|string|max:255',
            'mamonhoc' => [
                'required', 
                'string', 
                'exists:monhoc,mamonhoc',
                function ($attribute, $value, $fail) {
                    $isAssigned = DB::table('phancong')
                        ->where('manguoidung', Auth::id())
                        ->where('mamonhoc', $value)
                        ->exists();
                    if (!$isAssigned) {
                        $fail('Bạn không được phân công dạy môn học này.');
                    }
                }
            ],
            'namhoc'   => 'required|integer',
            'hocky'    => 'required|integer|in:1,2,3',
            'ghichu'   => 'nullable|string|max:255',
        ]);

        $nhom->update([
            'tennhom'  => $request->tennhom,
            'mamonhoc' => $request->mamonhoc,
            'namhoc'   => $request->namhoc,
            'hocky'    => $request->hocky,
            'ghichu'   => $request->ghichu,
        ]);

        return redirect()->back()->with('success', 'Cập nhật nhóm thành công');
    }

    /**
     * Xoá mềm nhóm
     */
    public function destroy($id)
    {
        $nhom = NhomModel::findOrFail($id);
        $this->authorize_owner($nhom);
        $nhom->update(['trangthai' => 0]);
        return redirect()->back()->with('success', 'Đã xóa nhóm thành công');
    }

    /**
     * Ẩn / Hiện nhóm (toggle hienthi)
     */
    public function toggleVisibility(Request $request, $id)
    {
        $nhom = NhomModel::findOrFail($id);
        $this->authorize_owner($nhom);
        $nhom->update(['hienthi' => $request->hienthi]);
        return response()->json(['ok' => true]);
    }

    /**
     * Làm mới mã mời
     */
    public function refreshInviteCode($id)
    {
        $nhom = NhomModel::findOrFail($id);
        $this->authorize_owner($nhom);

        do {
            $mamoi = substr(md5(mt_rand()), 0, 7);
        } while (NhomModel::where('mamoi', $mamoi)->exists());

        $nhom->update(['mamoi' => $mamoi]);
        return response()->json(['mamoi' => $mamoi]);
    }

    /**
     * Lấy danh sách sinh viên trong nhóm (JSON)
     */
    public function getStudents($id)
    {
        $nhom = NhomModel::with(['sinhvien'])->findOrFail($id);
        return response()->json($nhom->sinhvien);
    }

    /**
     * Thêm sinh viên vào nhóm
     */
    public function addStudent(Request $request, $id)
    {
        $request->validate(['manguoidung' => 'required|string']);

        $exists = ChiTietNhomModel::where('manhom', $id)
            ->where('manguoidung', $request->manguoidung)->exists();

        if ($exists) {
            return response()->json(['ok' => false, 'message' => 'Sinh viên đã tham gia nhóm này'], 409);
        }

        // Nếu user chưa có tài khoản có thể tạo mới (giữ đơn giản - chỉ thêm vào nhóm nếu đã có account)
        $user = UserModel::find($request->manguoidung);
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'Không tìm thấy người dùng'], 404);
        }

        ChiTietNhomModel::create([
            'manhom'      => $id,
            'manguoidung' => $request->manguoidung,
        ]);

        // Cập nhật sỉ số
        NhomModel::where('manhom', $id)->update([
            'siso' => ChiTietNhomModel::where('manhom', $id)->count()
        ]);

        return response()->json(['ok' => true, 'user' => $user]);
    }

    /**
     * Xoá sinh viên khỏi nhóm
     */
    public function removeStudent($id, $uid)
    {
        ChiTietNhomModel::where('manhom', $id)->where('manguoidung', $uid)->delete();

        NhomModel::where('manhom', $id)->update([
            'siso' => ChiTietNhomModel::where('manhom', $id)->count()
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Helper: Kiểm tra nhóm thuộc về giảng viên hiện tại
     */
    private function authorize_owner(NhomModel $nhom)
    {
        if ((string) $nhom->giangvien !== (string) Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác nhóm này.');
        }
    }
}
