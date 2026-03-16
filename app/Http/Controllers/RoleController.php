<?php

namespace App\Http\Controllers;

use App\Models\NhomQuyenModel;
use App\Models\ChiTietQuyenModel;
use App\Models\DanhMucChucNangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Kiểm tra quyền
     */
    private function checkPermission($action)
    {
        $permissions = auth()->user()->getRolePermissions();
        if (!isset($permissions['nhomquyen']) || !in_array($action, $permissions['nhomquyen'])) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }
    }

    /**
     * Hiển thị trang danh sách nhóm quyền
     */
    public function index(Request $request)
    {
        $this->checkPermission('view');

        $query = NhomQuyenModel::active()->withCount('users');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('tennhomquyen', 'like', '%' . $request->search . '%');
        }

        $roles = $query->paginate(10)->withQueryString();

        // Lấy danh mục chức năng
        $danhMucChucNang = DanhMucChucNangModel::all();

        // Tách chức năng thành CRUD và Đặc biệt để xử lý trên giao diện
        $crudChucNang = $danhMucChucNang->reject(function ($cn) {
            return in_array($cn->chucnang, ['tgthi', 'tghocphan', 'caidat', 'sinhvien']);
        })->values();

        $specialChucNang = $danhMucChucNang->filter(function ($cn) {
            return in_array($cn->chucnang, ['tgthi', 'tghocphan']);
        })->map(function ($cn) {
            return ['chucnang' => $cn->chucnang, 'label' => $cn->tenchucnang];
        })->values();

        // Danh sách quyền CRUD chuẩn
        $actions = [
            ['key' => 'view', 'label' => 'Xem'],
            ['key' => 'create', 'label' => 'Thêm mới'],
            ['key' => 'update', 'label' => 'Cập nhật'],
            ['key' => 'delete', 'label' => 'Xóa'],
        ];

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'crudChucNang' => $crudChucNang,
            'specialChucNang' => $specialChucNang,
            'actions' => $actions,
            'filters' => $request->only('search')
        ]);
    }

    /**
     * Tạo nhóm quyền mới
     */
    public function store(Request $request)
    {
        $this->checkPermission('create');

        $request->validate([
            'tennhomquyen' => 'required|string|max:50',
            'chitietquyen' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Tạo nhóm quyền
            $nhomQuyen = NhomQuyenModel::create([
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
                ChiTietQuyenModel::insert($chiTietData);
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
        // Có quyền edit hoặc view thì mới xem được JSON này
        $permissions = auth()->user()->getRolePermissions();
        if (!isset($permissions['nhomquyen']) || (!in_array('view', $permissions['nhomquyen']) && !in_array('update', $permissions['nhomquyen']))) {
            return response()->json(['error' => 'Bạn không có quyền xem chi tiết.'], 403);
        }

        $nhomQuyen = NhomQuyenModel::with('chiTietQuyen')->findOrFail($id);

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
        $this->checkPermission('update');

        // Không ai được sửa nhóm quyền Admin (id 1) TRỪ KHI người đó CHÍNH LÀ Admin (có manhomquyen = 1)
        if ($id == 1 && auth()->user()->manhomquyen != 1) {
            return redirect()->route('roles.index')->with('error', 'Bạn không có quyền chỉnh sửa Nhóm quyền Quản trị viên cao nhất!');
        }

        $request->validate([
            'tennhomquyen' => 'required|string|max:50',
            'chitietquyen' => 'array',
        ]);

        DB::beginTransaction();
        try {
            $nhomQuyen = NhomQuyenModel::findOrFail($id);
            $nhomQuyen->update(['tennhomquyen' => $request->tennhomquyen]);

            // Xóa chi tiết quyền cũ
            ChiTietQuyenModel::where('manhomquyen', $id)->delete();

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
                ChiTietQuyenModel::insert($chiTietData);
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
        $this->checkPermission('delete');

        // KHÔNG BAO GIỜ CHO PHÉP XÓA NHÓM QUYỀN ADMIN
        if ($id == 1) {
            return redirect()->route('roles.index')->with('error', 'Tuyệt đối không thể xóa Nhóm quyền Quản trị viên hệ thống!');
        }

        $nhomQuyen = NhomQuyenModel::withCount('users')->findOrFail($id);

        if ($nhomQuyen->users_count > 0) {
            return redirect()->route('roles.index')->with('error', 'Không thể xóa nhóm quyền vì vẫn còn người dùng thuộc nhóm này!');
        }

        $nhomQuyen->update(['trangthai' => 0]);

        return redirect()->route('roles.index')->with('success', 'Xóa nhóm quyền thành công!');
    }
}
