<?php

namespace App\Http\Controllers;

use App\Models\ChuongModel;
use App\Models\MonHocModel;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * Kiểm tra quyền
     */
    private function checkPermission($action)
    {
        $permissions = auth()->user()->getRolePermissions();
        if (!isset($permissions['chuong']) || !in_array($action, $permissions['chuong'])) {
            abort(403, 'Bạn không có quyền ' . $action . ' chương.');
        }
    }

    /**
     * Lấy danh sách chương của một môn học (Dùng cho AJAX/API)
     */
    public function getBySubject($mamonhoc)
    {
        $this->checkPermission('view');

        $chapters = ChuongModel::where('mamonhoc', $mamonhoc)
            ->active()
            ->orderBy('machuong')
            ->get();

        return response()->json($chapters);
    }

    /**
     * C - Thêm chương mới
     */
    public function store(Request $request)
    {
        $this->checkPermission('create');

        $request->validate([
            'tenchuong' => 'required|string|max:255',
            'mamonhoc'  => 'required|exists:monhoc,mamonhoc',
        ]);

        $chapter = ChuongModel::create([
            'tenchuong' => trim($request->tenchuong),
            'mamonhoc'  => $request->mamonhoc,
            'trangthai' => 1,
        ]);

        return response()->json([
            'message' => 'Thêm chương thành công.',
            'chapter' => $chapter
        ]);
    }

    /**
     * U - Cập nhật chương
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission('update');

        $request->validate([
            'tenchuong' => 'required|string|max:255',
        ]);

        $chapter = ChuongModel::findOrFail($id);
        $chapter->update([
            'tenchuong' => trim($request->tenchuong),
        ]);

        return response()->json([
            'message' => 'Cập nhật chương thành công.',
            'chapter' => $chapter
        ]);
    }

    /**
     * D - Xoá mềm chương
     */
    public function destroy($id)
    {
        $this->checkPermission('delete');

        $chapter = ChuongModel::findOrFail($id);
        $chapter->update(['trangthai' => 0]);

        return response()->json([
            'message' => 'Đã xoá chương thành công.'
        ]);
    }
}
