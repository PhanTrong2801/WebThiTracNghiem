<?php

namespace App\Http\Controllers;

use App\Models\MonHocModel;
use App\Models\KhoaModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Kiểm tra quyền
     */
    private function checkPermission($action)
    {
        $permissions = auth()->user()->getRolePermissions();
        if (!isset($permissions['monhoc']) || !in_array($action, $permissions['monhoc'])) {
            abort(403, 'Bạn không có quyền ' . $action . ' môn học.');
        }
    }

    /**
     * R - Danh sách môn học
     */
    public function index(Request $request)
    {
        $this->checkPermission('view');

        $query = MonHocModel::with('khoa')
            ->where('trangthai', 1);

        // Tìm kiếm theo mã hoặc tên
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('mamonhoc', 'like', '%' . $request->search . '%')
                  ->orWhere('tenmonhoc', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo khoa (null = chung, id khoa cụ thể)
        if ($request->filled('makhoa')) {
            if ($request->makhoa === 'chung') {
                $query->whereNull('makhoa');
            } else {
                $query->where('makhoa', $request->makhoa);
            }
        }

        $danhSachMonHoc = $query->orderBy('mamonhoc')->paginate(15)->withQueryString();

        // Danh sách khoa cho dropdown filter và form
        $danhSachKhoa = KhoaModel::orderBy('tenkhoa')->get(['id', 'tenkhoa']);

        return Inertia::render('MonHoc/Index', [
            'danhSachMonHoc' => $danhSachMonHoc,
            'danhSachKhoa'   => $danhSachKhoa,
            'filters'        => $request->only(['search', 'makhoa']),
        ]);
    }

    /**
     * C - Thêm môn học
     */
    public function store(Request $request)
    {
        $this->checkPermission('create');

        $request->validate([
            'mamonhoc'         => 'required|string|max:20|unique:monhoc,mamonhoc',
            'tenmonhoc'        => 'required|string|max:255',
            'makhoa'           => 'nullable|exists:khoa,id',
            'sotinchi'         => 'required|integer|min:1|max:10',
            'sotietlythuyet'   => 'required|integer|min:0',
            'sotietthuchanh'   => 'required|integer|min:0',
        ], [
            'mamonhoc.required'   => 'Mã môn học không được để trống.',
            'mamonhoc.unique'     => 'Mã môn học đã tồn tại.',
            'tenmonhoc.required'  => 'Tên môn học không được để trống.',
            'sotinchi.min'        => 'Số tín chỉ phải ít nhất là 1.',
        ]);

        MonHocModel::create([
            'mamonhoc'       => strtoupper(trim($request->mamonhoc)),
            'tenmonhoc'      => trim($request->tenmonhoc),
            'makhoa'         => $request->makhoa ?: null,
            'sotinchi'       => $request->sotinchi,
            'sotietlythuyet' => $request->sotietlythuyet,
            'sotietthuchanh' => $request->sotietthuchanh,
            'trangthai'      => 1,
        ]);

        return redirect()->back()->with('success', 'Thêm môn học thành công.');
    }

    /**
     * U - Cập nhật môn học
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission('update');

        $monhoc = MonHocModel::findOrFail($id);

        $request->validate([
            'mamonhoc'         => 'required|string|max:20|unique:monhoc,mamonhoc,' . $id,
            'tenmonhoc'        => 'required|string|max:255',
            'makhoa'           => 'nullable|exists:khoa,id',
            'sotinchi'         => 'required|integer|min:1|max:10',
            'sotietlythuyet'   => 'required|integer|min:0',
            'sotietthuchanh'   => 'required|integer|min:0',
        ]);

        $monhoc->update([
            'mamonhoc'       => strtoupper(trim($request->mamonhoc)),
            'tenmonhoc'      => trim($request->tenmonhoc),
            'makhoa'         => $request->makhoa ?: null,
            'sotinchi'       => $request->sotinchi,
            'sotietlythuyet' => $request->sotietlythuyet,
            'sotietthuchanh' => $request->sotietthuchanh,
        ]);

        return redirect()->back()->with('success', 'Cập nhật môn học thành công.');
    }

    /**
     * D - Xoá mềm môn học (set trangthai = 0)
     */
    public function destroy($id)
    {
        $this->checkPermission('delete');

        $monhoc = MonHocModel::findOrFail($id);
        $monhoc->update(['trangthai' => 0]);

        return redirect()->back()->with('success', 'Đã xoá môn học thành công.');
    }
}
