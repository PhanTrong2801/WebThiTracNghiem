<?php

namespace App\Http\Controllers;

use App\Models\NhomQuyen;
use App\Models\ChiTietQuyen;
use App\Models\DanhMucChucNang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Hiển thị trang danh sách nhóm quyền
     */
    public function index()
    {
        // Lấy danh sách nhóm quyền kèm số lượng user
        $roles = NhomQuyen::active()
            ->withCount('users')
            ->get();

        // Lấy danh mục chức năng
        $danhMucChucNang = DanhMucChucNang::all();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'danhMucChucNang' => $danhMucChucNang,
        ]);
    }

    /**
     * Tạo nhóm quyền mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'tennhomquyen' => 'required|string|max:50',
            'chitietquyen' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Tạo nhóm quyền
            $nhomQuyen = NhomQuyen::create([
                'tennhomquyen' => $request->tennhomquyen,
                'trangthai' => 1,
            ]);

            // Tạo chi tiết quyền
            if ($request->chitietquyen && count($request->chitietquyen) > 0) {
                $chiTietData = [];
                foreach ($request->chitietquyen as $ct) {
                    $chiTietData[] = [
                        'manhomquyen' => $nhomQuyen->manhomquyen,
                        'chucnang' => $ct['chucnang'],
                        'hanhdong' => $ct['hanhdong'],
                    ];
                }
                ChiTietQuyen::insert($chiTietData);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Thêm nhóm quyền thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Lấy chi tiết 1 nhóm quyền (JSON cho modal sửa)
     */
    public function show($id)
    {
        $nhomQuyen = NhomQuyen::with('chiTietQuyen')->findOrFail($id);

        return response()->json([
            'manhomquyen' => $nhomQuyen->manhomquyen,
            'tennhomquyen' => $nhomQuyen->tennhomquyen,
            'chitietquyen' => $nhomQuyen->chiTietQuyen->map(function ($ct) {
                return [
                    'chucnang' => $ct->chucnang,
                    'hanhdong' => $ct->hanhdong,
                ];
            }),
        ]);
    }

    /**
     * Cập nhật nhóm quyền
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tennhomquyen' => 'required|string|max:50',
            'chitietquyen' => 'array',
        ]);

        DB::beginTransaction();
        try {
            $nhomQuyen = NhomQuyen::findOrFail($id);
            $nhomQuyen->update(['tennhomquyen' => $request->tennhomquyen]);

            // Xóa chi tiết quyền cũ
            ChiTietQuyen::where('manhomquyen', $id)->delete();

            // Thêm chi tiết quyền mới
            if ($request->chitietquyen && count($request->chitietquyen) > 0) {
                $chiTietData = [];
                foreach ($request->chitietquyen as $ct) {
                    $chiTietData[] = [
                        'manhomquyen' => $id,
                        'chucnang' => $ct['chucnang'],
                        'hanhdong' => $ct['hanhdong'],
                    ];
                }
                ChiTietQuyen::insert($chiTietData);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'Cập nhật nhóm quyền thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa (soft delete) nhóm quyền
     */
    public function destroy($id)
    {
        $nhomQuyen = NhomQuyen::withCount('users')->findOrFail($id);

        if ($nhomQuyen->users_count > 0) {
            return redirect()->route('roles.index')->with('error', 'Không thể xóa nhóm quyền vì vẫn còn người dùng thuộc nhóm này!');
        }

        $nhomQuyen->update(['trangthai' => 0]);

        return redirect()->route('roles.index')->with('success', 'Xóa nhóm quyền thành công!');
    }
}
