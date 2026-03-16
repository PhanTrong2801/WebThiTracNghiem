<?php

namespace App\Http\Controllers;

use App\Models\KhoaModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KhoaController extends Controller
{
    /**
     * Kiểm tra quyền quản lý khoa
     */
    private function checkPermission($action)
    {
        $permissions = auth()->user()->getRolePermissions();
        if (!isset($permissions['khoa']) || !in_array($action, $permissions['khoa'])) {
            abort(403, 'Bạn không có quyền ' . $action . ' Ngành/Khoa.');
        }
    }

    /**
     * R - View danh sách khoa
     */
    public function index(Request $request)
    {
        $this->checkPermission('view');

        $query = KhoaModel::withCount([
            'users as sinh_vien_count' => function ($q) {
                $q->where('manhomquyen', 3);
            },
            'users as giang_vien_count' => function ($q) {
                $q->where('manhomquyen', '!=', 3);
            }
        ]);
        
        if ($request->has('search') && $request->search != '') {
            $query->where('tenkhoa', 'like', '%' . $request->search . '%');
        }

        $danhSachKhoaModel = $query->paginate(10)->withQueryString();

        return Inertia::render('Khoa/Index', [
            'danhSachKhoa' => $danhSachKhoaModel,
            'filters' => $request->only('search')
        ]);
    }

    /**
     * C - Thêm mới khoa
     */
    public function store(Request $request)
    {
        $this->checkPermission('create');

        $request->validate([
            'tenkhoa' => 'required|string|max:255',
        ]);

        KhoaModel::create([
            'tenkhoa' => $request->tenkhoa,
        ]);

        return redirect()->back()->with('success', 'Thêm mới Ngành/KhoaModel thành công.');
    }

    /**
     * U - Cập nhật khoa
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission('update');

        $request->validate([
            'tenkhoa' => 'required|string|max:255',
        ]);

        $khoa = KhoaModel::findOrFail($id);
        $khoa->update([
            'tenkhoa' => $request->tenkhoa,
        ]);

        return redirect()->back()->with('success', 'Cập nhật Ngành/Khoa thành công.');
    }

    /**
     * D - Xoá khoa
     */
    public function destroy($id)
    {
        $this->checkPermission('delete');

        $khoa = KhoaModel::withCount([
            'users as sinh_vien_count' => function ($q) {
                $q->where('manhomquyen', 3);
            },
            'users as giang_vien_count' => function ($q) {
                $q->where('manhomquyen', '!=', 3);
            }
        ])->findOrFail($id);
        
        // Cấm xoá nếu vẫn còn sinh viên
        if ($khoa->sinh_vien_count > 0) {
            return redirect()->back()->with('error', 'Không thể xoá Ngành/Khoa này vì vẫn còn Sinh viên trực thuộc.');
        }

        // Nếu chỉ còn giảng viên, tháo giảng viên ra khỏi khoa (set null)
        if ($khoa->giang_vien_count > 0) {
            $khoa->users()->update(['makhoa' => null]);
        }

        $khoa->delete();

        return redirect()->back()->with('success', 'Đã xoá Ngành/Khoa thành công.');
    }
}
