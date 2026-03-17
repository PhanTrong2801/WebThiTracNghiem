<?php

namespace App\Http\Controllers;

use App\Models\NhomModel;
use App\Models\ChiTietNhomModel;
use App\Models\DeThiModel;
use App\Models\KetQuaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StudentModuleController extends Controller
{
    /**
     * Danh sách các nhóm mà sinh viên đã tham gia
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $hienthi = $request->query('hienthi', 1);

        // Lấy danh sách nhóm thông qua bảng chitietnhom
        $joinedGroups = NhomModel::with(['monhoc', 'giangVienUser'])
            ->whereHas('chitietnhom', function ($query) use ($userId, $hienthi) {
                $query->where('manguoidung', $userId)
                      ->where('hienthi', $hienthi);
            })
            ->get();

        return Inertia::render('StudentModule/Index', [
            'joinedGroups' => $joinedGroups,
            'filters' => [
                'hienthi' => (int)$hienthi,
                'search' => $request->query('search', ''),
            ]
        ]);
    }

    /**
     * Tham gia nhóm bằng mã mời
     */
    public function join(Request $request)
    {
        $request->validate([
            'mamoi' => 'required|string|size:7',
        ]);

        $mamoi = $request->mamoi;
        $userId = Auth::id();

        $nhom = NhomModel::where('mamoi', $mamoi)->first();

        if (!$nhom) {
            return redirect()->back()->withErrors(['mamoi' => 'Mã mời không tồn tại.']);
        }

        // Kiểm tra xem đã tham gia chưa
        $exists = ChiTietNhomModel::where('manhom', $nhom->manhom)
            ->where('manguoidung', $userId)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['mamoi' => 'Bạn đã tham gia nhóm này rồi.']);
        }

        ChiTietNhomModel::create([
            'manhom' => $nhom->manhom,
            'manguoidung' => $userId,
            'hienthi' => 1,
        ]);

        return redirect()->back()->with('success', 'Tham gia nhóm thành công!');
    }

    /**
     * Rời khỏi nhóm hoặc xóa khỏi danh sách
     */
    public function leave(Request $request, $id)
    {
        $userId = Auth::id();
        
        ChiTietNhomModel::where('manhom', $id)
            ->where('manguoidung', $userId)
            ->delete();

        return redirect()->back()->with('success', 'Đã rời khỏi nhóm.');
    }

    /**
     * Ẩn/Hiện nhóm đối với sinh viên
     */
    public function toggleVisibility(Request $request, $id)
    {
        $userId = Auth::id();
        $hienthi = $request->input('hienthi');

        ChiTietNhomModel::where('manhom', $id)
            ->where('manguoidung', $userId)
            ->update(['hienthi' => $hienthi]);

        return redirect()->back()->with('success', $hienthi ? 'Đã hiện nhóm.' : 'Đã ẩn nhóm.');
    }

    /**
     * Lấy danh sách bạn cùng nhóm
     */
    public function getMembers($id)
    {
        $nhom = NhomModel::findOrFail($id);
        
        $members = $nhom->sinhvien()
            ->select('users.id', 'users.hoten', 'users.email', 'users.avatar')
            ->get();

        return response()->json($members);
    }

    /**
     * Lấy danh sách đề thi trong nhóm (JSON)
     * - Dựa vào bảng `giaodethi`
     * - Kèm trạng thái đã nộp/điểm nếu có (ketqua)
     */
    public function getTests($id)
    {
        $userId = Auth::id();

        // Chỉ cho phép xem nếu SV thuộc nhóm
        $isMember = ChiTietNhomModel::where('manhom', $id)
            ->where('manguoidung', $userId)
            ->exists();

        if (!$isMember) {
            abort(403, 'Bạn không thuộc nhóm này.');
        }

        $tests = DeThiModel::query()
            ->select([
                'dethi.made',
                'dethi.tende',
                'dethi.thoigianthi',
                'dethi.thoigianbatdau',
                'dethi.thoigianketthuc',
                'dethi.monthi',
                'monhoc.tenmonhoc',
            ])
            ->join('giaodethi', 'giaodethi.made', '=', 'dethi.made')
            ->join('monhoc', 'monhoc.mamonhoc', '=', 'dethi.monthi')
            ->where('giaodethi.manhom', $id)
            ->where('dethi.trangthai', 1)
            ->orderByDesc('dethi.made')
            ->get()
            ->map(function ($t) use ($userId) {
                $kq = KetQuaModel::query()
                    ->where('made', $t->made)
                    ->where('manguoidung', $userId)
                    ->first();

                return [
                    'made' => $t->made,
                    'tende' => $t->tende,
                    'thoigianthi' => $t->thoigianthi,
                    'thoigianbatdau' => $t->thoigianbatdau,
                    'thoigianketthuc' => $t->thoigianketthuc,
                    'monthi' => $t->monthi,
                    'tenmonhoc' => $t->tenmonhoc,
                    'diemthi' => $kq?->diemthi,
                ];
            });

        return response()->json($tests);
    }
}
