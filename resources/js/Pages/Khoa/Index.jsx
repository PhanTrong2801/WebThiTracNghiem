import React, { useState, useEffect } from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, router } from '@inertiajs/react';

export default function KhoaIndex() {
    // Nhận data từ controller
    const { auth, danhSachKhoa, flash, filters } = usePage().props;
    const user = auth.user;
    const rolePermissions = auth.user_role?.khoa || [];

    // Extract pagination data
    const { data: khoaList = [], links = [], total = 0, from = 0, to = 0 } = danhSachKhoa || {};

    // Lọc quyền frontend
    const canCreate = rolePermissions.includes('create');
    const canUpdate = rolePermissions.includes('update');
    const canDelete = rolePermissions.includes('delete');

    // States
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editId, setEditId] = useState(null);
    const [tenkhoa, setTenkhoa] = useState('');
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(null);

    // Bắt sự kiện thay đổi searchTerm và gửi lên backend sau 300ms (debounce)
    useEffect(() => {
        const timer = setTimeout(() => {
            if (searchTerm !== (filters?.search || '')) {
                router.get('/khoa', { search: searchTerm }, { preserveState: true, replace: true });
            }
        }, 300);
        return () => clearTimeout(timer);
    }, [searchTerm]);

    // Mở modal thêm (chỉ gọi được nút này nếu canCreate = true do render điều kiện)
    const openAddModal = () => {
        setIsEditing(false);
        setEditId(null);
        setTenkhoa('');
        setShowModal(true);
    };

    // Mở modal sửa
    const openEditModal = (khoa) => {
        setIsEditing(true);
        setEditId(khoa.id);
        setTenkhoa(khoa.tenkhoa);
        setShowModal(true);
    };

    // Xử lý Lưu / Cập nhật
    const handleSave = () => {
        if (!tenkhoa.trim()) {
            alert('Vui lòng nhập tên Ngành/Khoa!');
            return;
        }

        if (isEditing) {
            router.put(`/khoa/${editId}`, { tenkhoa }, {
                onSuccess: () => setShowModal(false),
            });
        } else {
            router.post('/khoa', { tenkhoa }, {
                onSuccess: () => setShowModal(false),
            });
        }
    };

    // Xử lý Xóa
    const handleDelete = (id) => {
        router.delete(`/khoa/${id}`, {
            onSuccess: () => setShowDeleteConfirm(null),
        });
    };

    return (
        <MainLayout user={user}>
            <Head title="Ngành/Khoa" />

            <div className="content">
                {/* Flash messages */}
                {flash?.success && (
                    <div className="alert alert-success alert-dismissible" role="alert">
                        {flash.success}
                        <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                )}
                {flash?.error && (
                    <div className="alert alert-danger alert-dismissible" role="alert">
                        {flash.error}
                        <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                )}

                <div className="block block-rounded">
                    <div className="block-header block-header-default">
                        <h3 className="block-title">Danh sách Ngành/Khoa</h3>
                        <div className="block-options">
                            <span className="badge bg-primary me-3">{from}-{to} / {total} ngành</span>
                            {/* Chỉ hiển thị nút Thêm nếu có quyền Thêm */}
                            {canCreate && (
                                <button
                                    type="button"
                                    className="btn btn-hero btn-primary"
                                    onClick={openAddModal}
                                >
                                    <i className="fa-regular fa-plus me-1"></i> Thêm mới
                                </button>
                            )}
                        </div>
                    </div>
                    
                    <div className="block-content">
                        <div className="row justify-content-center">
                            <div className="col-md-11">
                                {/* Search */}
                                <div className="mb-4">
                                    <div className="input-group">
                                        <input
                                            type="text"
                                            className="form-control form-control-alt"
                                            placeholder="Tìm kiếm khoa.."
                                            value={searchTerm}
                                            onChange={(e) => setSearchTerm(e.target.value)}
                                        />
                                        <span className="input-group-text bg-body border-0">
                                            <i className="fa fa-search"></i>
                                        </span>
                                    </div>
                                </div>

                                {/* Table */}
                                <div className="table-responsive">
                                    <table className="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th className="text-center" style={{ width: '80px' }}>ID</th>
                                                <th>Tên Ngành/Khoa</th>
                                                <th className="text-center" style={{ width: '120px' }}>Sinh viên</th>
                                                <th className="text-center" style={{ width: '120px' }}>Giảng viên</th>
                                                {/* Chỉ hiển thị cột Hành động nếu có quyền Sửa HOẶC quyền Xóa */}
                                                {(canUpdate || canDelete) && (
                                                    <th className="text-center" style={{ width: '150px' }}>Hành động</th>
                                                )}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {khoaList.length === 0 ? (
                                                <tr>
                                                    <td colSpan={(canUpdate || canDelete) ? 5 : 4} className="text-center text-muted py-4">
                                                        Không tìm thấy dữ liệu nào
                                                    </td>
                                                </tr>
                                            ) : (
                                                khoaList.map(khoa => (
                                                    <tr key={khoa.id}>
                                                        <td className="text-center fw-semibold">{khoa.id}</td>
                                                        <td>{khoa.tenkhoa}</td>
                                                        <td className="text-center">
                                                            <span className="badge bg-success">{khoa.sinh_vien_count || 0}</span>
                                                        </td>
                                                        <td className="text-center">
                                                            <span className="badge bg-info">{khoa.giang_vien_count || 0}</span>
                                                        </td>
                                                        {(canUpdate || canDelete) && (
                                                            <td className="text-center">
                                                                <div className="btn-group">
                                                                    {canUpdate && (
                                                                        <button
                                                                            type="button"
                                                                            className="btn btn-sm btn-alt-info"
                                                                            title="Sửa"
                                                                            onClick={() => openEditModal(khoa)}
                                                                        >
                                                                            <i className="fa fa-pencil-alt"></i>
                                                                        </button>
                                                                    )}
                                                                    {canDelete && (
                                                                        <button
                                                                            type="button"
                                                                            className="btn btn-sm btn-alt-danger"
                                                                            title="Xóa"
                                                                            onClick={() => setShowDeleteConfirm(khoa.id)}
                                                                        >
                                                                            <i className="fa fa-trash"></i>
                                                                        </button>
                                                                    )}
                                                                </div>
                                                            </td>
                                                        )}
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    {/* Pagination */}
                    {links.length > 3 && (
                        <div className="block-content block-content-full block-content-sm bg-body-light">
                            <Pagination links={links} />
                        </div>
                    )}
                </div>
            </div>

            {/* DELETE CONFIRM MODAL */}
            {showDeleteConfirm && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header bg-danger">
                                <h5 className="modal-title text-white">
                                    <i className="fa fa-exclamation-triangle me-2"></i> Xác nhận xóa
                                </h5>
                            </div>
                            <div className="modal-body text-center py-4">
                                <i className="fa fa-trash fa-3x text-danger mb-3"></i>
                                <p className="fs-5">Bạn có chắc muốn xóa dữ liệu này?</p>
                                <p className="text-muted">Hành động này không thể hoàn tác.</p>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-alt-secondary"
                                    onClick={() => setShowDeleteConfirm(null)}
                                >
                                    Huỷ
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={() => handleDelete(showDeleteConfirm)}
                                >
                                    <i className="fa fa-trash me-1"></i> Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* ADD/EDIT MODAL */}
            {showModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">
                                    {isEditing ? 'Sửa Ngành/Khoa' : 'Thêm Ngành/Khoa'}
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowModal(false)}
                                ></button>
                            </div>
                            <div className="modal-body pb-4">
                                <div className="mb-3">
                                    <label className="form-label" htmlFor="ten-khoa">Tên Ngành/Khoa</label>
                                    <input
                                        type="text"
                                        className="form-control form-control-alt"
                                        id="ten-khoa"
                                        placeholder="VD: Khoa Công nghệ Thông tin"
                                        value={tenkhoa}
                                        onChange={(e) => setTenkhoa(e.target.value)}
                                        onKeyDown={(e) => e.key === 'Enter' && handleSave()}
                                    />
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-sm btn-alt-secondary"
                                    onClick={() => setShowModal(false)}
                                >
                                    Huỷ
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-sm btn-primary"
                                    onClick={handleSave}
                                >
                                    {isEditing ? 'Cập nhật' : 'Lưu'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
