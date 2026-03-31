<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\NhomQuyenModel;
use App\Models\KhoaModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách users (Frontend - Inertia)
     */
    public function index(Request $request)
    {
        $query = UserModel::with(['khoa', 'nhomquyen'])
            ->select('id', 'hoten', 'email', 'gioitinh', 'ngaysinh', 'trangthai', 'manhomquyen', 'makhoa');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('hoten', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('id', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != 0) {
            $query->where('manhomquyen', $request->role);
        }

        // Filter by khoa
        if ($request->has('khoa') && $request->khoa != 0) {
            $query->where('makhoa', $request->khoa);
        }

        $users = $query->paginate(10)->withQueryString();
        
        $roles = NhomQuyenModel::where('trangthai', 1)->get();
        $khoas = KhoaModel::all(); // Lấy tất cả khoa

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'khoa']),
            'roles' => $roles,
            'khoas' => $khoas
        ]);
    }

    /**
     * API: Lấy tất cả users (JSON)
     */
    public function apiIndex()
    {
        $users = UserModel::with(['khoa', 'nhomquyen'])->get();
        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * API: Lấy 1 user theo id (JSON)
     */
    public function apiShow(string $id)
    {
        $user = UserModel::with(['khoa', 'nhomquyen'])->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * API: Tạo người dùng mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|max:20',
            'email' => 'required|email',
            'hoten' => 'required|string|max:255',
            'ngaysinh' => 'nullable|string',
            'gioitinh' => 'nullable|integer',
            'password' => 'required|string|min:6',
            'manhomquyen' => 'required|integer',
            'trangthai' => 'required|integer',
            'makhoa' => 'nullable|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $userModel = new UserModel();
            $userModel->createUser(
                $request->id,
                $request->email,
                $request->hoten,
                $request->password,
                $request->ngaysinh,
                $request->gioitinh ?? 1,
                $request->manhomquyen,
                $request->trangthai,
                $request->makhoa
            );

            return back()->with('success', 'Thêm người dùng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể tạo người dùng: ' . $e->getMessage());
        }
    }

    /**
     * API: Cập nhật người dùng
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'hoten' => 'required|string|max:255',
            'ngaysinh' => 'nullable|string',
            'gioitinh' => 'nullable|integer',
            'password' => 'nullable|string|min:6',
            'manhomquyen' => 'required|integer',
            'trangthai' => 'required|integer',
            'makhoa' => 'nullable|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $userModel = new UserModel();
            $userModel->updateUser(
                $id,
                $request->email,
                $request->hoten,
                $request->password ?? '',
                $request->ngaysinh,
                $request->gioitinh ?? 1,
                $request->manhomquyen,
                $request->trangthai,
                $request->makhoa
            );

            return back()->with('success', 'Cập nhật người dùng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * API: Xóa người dùng
     */
    public function destroy(string $id)
    {
        try {
            $userModel = new UserModel();
            $result = $userModel->deleteUser($id);
            if (!$result) {
                return back()->with('error', 'Không thể xóa người dùng.');
            }

            return back()->with('success', 'Đã xóa người dùng.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * API: Kiểm tra user tồn tại (MSSV hoặc Email)
     */
    public function checkUser(Request $request)
    {
        $mssv = $request->mssv;
        $email = $request->email;

        $userModel = new UserModel();
        $users = $userModel->checkUser($mssv, $email);

        return response()->json($users);
    }

    /**
     * API: Import Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'fileToUpload' => 'required|mimes:xlsx,xls,csv'
        ]);

        // Logic xử lý file Excel ở đây (Cần thư viện như PhpSpreadsheet)
        // Tạm thời trả về thành công
        return response()->json(['message' => 'Import logic needs implementation with PhpSpreadsheet', 'valid' => false]);
    }

    /**
     * Lấy danh sách roles cho dropdown
     */
    public function getRoles()
    {
        $roles = NhomQuyenModel::where('trangthai', 1)->get();
        return response()->json($roles);
    }
}
