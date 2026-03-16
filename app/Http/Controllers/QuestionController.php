<?php

namespace App\Http\Controllers;

use App\Models\CauHoiModel;
use App\Models\CauTraLoiModel;
use App\Models\ChuongModel;
use App\Models\MonHocModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Phân quyền (giả định dùng mảng quyền trong user meta như các controller khác)
        // Nếu dùng RBAC của ứng dụng, hãy kiểm tra tại đây. Hiện tôi pass theo auth thông thường
        
        $search = $request->query('search');
        $mamonhoc = $request->query('mamonhoc');
        $machuong = $request->query('machuong');
        $dokho = $request->query('dokho');
        $makhoa = $request->query('makhoa');

        $query = CauHoiModel::with(['monhoc', 'chuong', 'cautraloi' => function($q) {
            // Lấy id, nội dung và đáp án (có đánh dấu 1=đúng)
            $q->select('macautl', 'macauhoi', 'noidungtl', 'ladapan');
        }])->active();

        if (!empty($search)) {
            $query->where('noidung', 'like', '%' . $search . '%');
        }

        if (!empty($mamonhoc)) {
            $query->where('mamonhoc', $mamonhoc);
        }

        if (!empty($machuong)) {
            $query->where('machuong', $machuong);
        }

        if (!empty($dokho)) {
            $query->where('dokho', $dokho);
        }

        if (!empty($makhoa)) {
            $query->whereHas('monhoc', function($q) use ($makhoa) {
                if ($makhoa === 'chung') {
                    $q->whereNull('makhoa');
                } else {
                    $q->where('makhoa', $makhoa);
                }
            });
        }

        // Lấy 10 câu hỏi / trang
        $questions = $query->orderBy('macauhoi', 'desc')->paginate(10)->withQueryString();

        // Load dropdown dữ liệu cho Môn học
        $monHocs = MonHocModel::active()->get(['mamonhoc', 'tenmonhoc', 'makhoa']);
        
        // Load danh sách Khoa
        $khoas = \App\Models\KhoaModel::all(['id', 'tenkhoa']);

        return Inertia::render('Questions/Index', [
            'danhSachCauHoi' => $questions,
            'danhSachMonHoc' => $monHocs,
            'danhSachKhoa' => $khoas,
            'filters' => [
                'search' => $search,
                'makhoa' => $makhoa,
                'mamonhoc' => $mamonhoc,
                'machuong' => $machuong,
                'dokho' => $dokho,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mamonhoc' => 'required|string|max:20',
            'machuong' => 'required|integer',
            'dokho' => 'required|integer',
            'noidung' => 'required|string|max:500',
            'cautraloi' => 'required|array|min:2', // Ít nhất 2 câu trả lời
            'cautraloi.*.noidungtl' => 'required|string|max:500',
            'cautraloi.*.ladapan' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Tạo câu hỏi
            $cauHoi = CauHoiModel::create([
                'noidung' => $validated['noidung'],
                'dokho' => $validated['dokho'],
                'mamonhoc' => $validated['mamonhoc'],
                'machuong' => $validated['machuong'],
                'nguoitao' => Auth::id() ?? $request->user()->name, // Cần lưu ý nguoitao trong DB là string
            ]);

            // Thêm các câu trả lời
            foreach ($validated['cautraloi'] as $answer) {
                CauTraLoiModel::create([
                    'macauhoi' => $cauHoi->macauhoi,
                    'noidungtl' => $answer['noidungtl'],
                    'ladapan' => $answer['ladapan'] ? 1 : 0,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Thêm câu hỏi thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'mamonhoc' => 'required|string|max:20',
            'machuong' => 'required|integer',
            'dokho' => 'required|integer',
            'noidung' => 'required|string|max:500',
            'cautraloi' => 'required|array|min:2',
            'cautraloi.*.noidungtl' => 'required|string|max:500',
            'cautraloi.*.ladapan' => 'required|boolean',
        ]);

        $cauHoi = CauHoiModel::findOrFail($id);

        DB::beginTransaction();
        try {
            // Cập nhật câu hỏi
            $cauHoi->update([
                'noidung' => $validated['noidung'],
                'dokho' => $validated['dokho'],
                'mamonhoc' => $validated['mamonhoc'],
                'machuong' => $validated['machuong'],
                'nguoitao' => Auth::id() ?? $request->user()->name,
            ]);

            // Cập nhật câu trả lời: Cách an toàn nhất là xoá cũ thêm mới
            // Hoặc có thể cập nhật thông qua ID
            CauTraLoiModel::where('macauhoi', $id)->delete();
            
            foreach ($validated['cautraloi'] as $answer) {
                CauTraLoiModel::create([
                    'macauhoi' => $cauHoi->macauhoi,
                    'noidungtl' => $answer['noidungtl'],
                    'ladapan' => $answer['ladapan'] ? 1 : 0,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Cập nhật câu hỏi thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cauHoi = CauHoiModel::findOrFail($id);
        
        // Soft delete bằng cách set trangthai = 0 như code legacy
        $cauHoi->trangthai = 0;
        $cauHoi->save();

        return redirect()->back()->with('success', 'Xoá câu hỏi thành công');
    }
}
