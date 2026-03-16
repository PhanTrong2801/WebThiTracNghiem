import React, { useState, useEffect } from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, router } from '@inertiajs/react';
import axios from 'axios';

export default function RolesIndex() {
    const { auth, roles, crudChucNang = [], specialChucNang = [], actions = [], flash, filters } = usePage().props;
    const user = auth.user;
    
    // Extract pagination data
    const { data: rolesList = [], links = [], total = 0, from = 0, to = 0 } = roles || {};

    // State
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editId, setEditId] = useState(null);
    const [tennhomquyen, setTennhomquyen] = useState('');
    const [checkedPermissions, setCheckedPermissions] = useState({});
    const [specialPermissions, setSpecialPermissions] = useState({});
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(null);

    // Bắt sự kiện thay đổi searchTerm và gửi lên backend sau 300ms (debounce)
    useEffect(() => {
        const timer = setTimeout(() => {
            if (searchTerm !== (filters?.search || '')) {
                router.get('/roles', { search: searchTerm }, { preserveState: true, replace: true });
            }
        }, 300);
        return () => clearTimeout(timer);
    }, [searchTerm]);

    // Toggle checkbox CRUD
    const togglePermission = (chucnang, hanhdong) => {
        const key = `${chucnang}_${hanhdong}`;
        setCheckedPermissions(prev => ({ ...prev, [key]: !prev[key] }));
    };

    // Toggle special permission
    const toggleSpecial = (chucnang) => {
        setSpecialPermissions(prev => ({ ...prev, [chucnang]: !prev[chucnang] }));
    };

    // Mở modal thêm
    const openAddModal = () => {
        setIsEditing(false);
        setEditId(null);
        setTennhomquyen('');
        setCheckedPermissions({});
        setSpecialPermissions({});
        setShowModal(true);
    };

    // Mở modal sửa
    const openEditModal = async (id) => {
        try {
            const res = await axios.get(`/roles/${id}`);
            const data = res.data;
            setIsEditing(true);
            setEditId(id);
            setTennhomquyen(data.tennhomquyen);

            // Set checked permissions
            const perms = {};
            const specials = {};
            data.chitietquyen.forEach(ct => {
                if (['tgthi', 'tghocphan'].includes(ct.chucnang)) {
                    specials[ct.chucnang] = true;
                } else {
                    perms[`${ct.chucnang}_${ct.hanhdong}`] = true;
                }
            });
            setCheckedPermissions(perms);
            setSpecialPermissions(specials);
            setShowModal(true);
        } catch (err) {
            alert('Lỗi tải chi tiết nhóm quyền!');
        }
    };

    // Lưu (thêm hoặc cập nhật)
    const handleSave = () => {
        if (!tennhomquyen.trim()) {
            alert('Vui lòng nhập tên nhóm quyền!');
            return;
        }

        // Build chi tiết quyền array
        const chitietquyen = [];

        // CRUD permissions
        Object.entries(checkedPermissions).forEach(([key, checked]) => {
            if (checked) {
                const [chucnang, hanhdong] = key.split('_');
                chitietquyen.push({ chucnang, hanhdong });
            }
        });

        // Special permissions
        Object.entries(specialPermissions).forEach(([chucnang, checked]) => {
            if (checked) {
                chitietquyen.push({ chucnang, hanhdong: 'join' });
            }
        });

        if (isEditing) {
            router.put(`/roles/${editId}`, { tennhomquyen, chitietquyen }, {
                onSuccess: () => setShowModal(false),
            });
        } else {
            router.post('/roles', { tennhomquyen, chitietquyen }, {
                onSuccess: () => setShowModal(false),
            });
        }
    };

    // Xóa
    const handleDelete = (id) => {
        router.delete(`/roles/${id}`, {
            onSuccess: () => setShowDeleteConfirm(null),
        });
    };

    return (
        <MainLayout user={user}>
            <Head title="Nhóm quyền" />

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
                        <h3 className="block-title">Danh sách nhóm quyền</h3>
                        <div className="block-options">
                            <span className="badge bg-primary me-3">{from}-{to} / {total} nhóm quyền</span>
                            <button
                                type="button"
                                className="btn btn-hero btn-primary"
                                onClick={openAddModal}
                            >
                                <i className="fa-regular fa-plus me-1"></i> Thêm mới
                            </button>
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
                                            placeholder="Tìm kiếm nhóm quyền.."
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
                                                <th className="text-center">Mã nhóm quyền</th>
                                                <th>Tên nhóm</th>
                                                <th className="text-center">Số người dùng</th>
                                                <th className="text-center" style={{ width: '150px' }}>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {rolesList.length === 0 ? (
                                                <tr>
                                                    <td colSpan="4" className="text-center text-muted py-4">
                                                        Không tìm thấy nhóm quyền nào
                                                    </td>
                                                </tr>
                                            ) : (
                                                rolesList.map(role => (
                                                    <tr key={role.manhomquyen}>
                                                        <td className="text-center fw-semibold">{role.manhomquyen}</td>
                                                        <td>{role.tennhomquyen}</td>
                                                        <td className="text-center">
                                                            <span className="badge bg-primary">{role.users_count || 0}</span>
                                                        </td>
                                                        <td className="text-center">
                                                            <div className="btn-group">
                                                                <button
                                                                    className="btn btn-sm btn-alt-info"
                                                                    title="Sửa"
                                                                    onClick={() => openEditModal(role.manhomquyen)}
                                                                >
                                                                    <i className="fa fa-pencil-alt"></i>
                                                                </button>
                                                                <button
                                                                    className="btn btn-sm btn-alt-danger"
                                                                    title="Xóa"
                                                                    onClick={() => setShowDeleteConfirm(role.manhomquyen)}
                                                                >
                                                                    <i className="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
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
                                    <i className="fa fa-exclamation-triangle me-2"></i>Xác nhận xóa
                                </h5>
                            </div>
                            <div className="modal-body text-center py-4">
                                <i className="fa fa-trash fa-3x text-danger mb-3"></i>
                                <p className="fs-5">Bạn có chắc muốn xóa nhóm quyền này?</p>
                                <p className="text-muted">Hành động này không thể hoàn tác.</p>
                            </div>
                            <div className="modal-footer">
                                <button
                                    className="btn btn-alt-secondary"
                                    onClick={() => setShowDeleteConfirm(null)}
                                >
                                    Huỷ
                                </button>
                                <button
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
                    <div className="modal-dialog modal-dialog-scrollable modal-xl">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">
                                    {isEditing ? 'Sửa nhóm quyền' : 'Thêm nhóm quyền'}
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowModal(false)}
                                ></button>
                            </div>
                            <div className="modal-body pb-1">
                                {/* Tên nhóm quyền */}
                                <div className="mb-3">
                                    <label className="form-label" htmlFor="ten-nhom-quyen">Tên nhóm quyền</label>
                                    <input
                                        type="text"
                                        className="form-control form-control-alt"
                                        id="ten-nhom-quyen"
                                        placeholder="VD: Giảng viên"
                                        value={tennhomquyen}
                                        onChange={(e) => setTennhomquyen(e.target.value)}
                                    />
                                </div>

                                {/* Bảng quyền CRUD */}
                                <table className="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Tên quyền</th>
                                            {actions.map(a => (
                                                <th key={a.key} className="text-center">{a.label}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {crudChucNang.map(cn => (
                                            <tr key={cn.chucnang}>
                                                <td>{cn.tenchucnang}</td>
                                                {actions.map(a => (
                                                    <td key={a.key} className="text-center">
                                                        <input
                                                            className="form-check-input"
                                                            type="checkbox"
                                                            checked={!!checkedPermissions[`${cn.chucnang}_${a.key}`]}
                                                            onChange={() => togglePermission(cn.chucnang, a.key)}
                                                        />
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>

                                {/* Quyền đặc biệt */}
                                <div className="row justify-content-around mb-3">
                                    {specialChucNang.map(sp => (
                                        <div key={sp.chucnang} className="col-6 form-check form-switch d-flex justify-content-center gap-2">
                                            <input
                                                className="form-check-input"
                                                type="checkbox"
                                                id={`special_${sp.chucnang}`}
                                                checked={!!specialPermissions[sp.chucnang]}
                                                onChange={() => toggleSpecial(sp.chucnang)}
                                            />
                                            <label className="form-check-label" htmlFor={`special_${sp.chucnang}`}>
                                                {sp.label}
                                            </label>
                                        </div>
                                    ))}
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
