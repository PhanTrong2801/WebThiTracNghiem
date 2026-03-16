import React, { useState, useEffect, useRef } from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, router } from '@inertiajs/react';
import axios from 'axios';

export default function MonHocIndex() {
    const { auth, danhSachMonHoc, danhSachKhoa, flash, filters } = usePage().props;
    const user = auth.user;
    const rolePermissions = auth.user_role?.monhoc || [];

    const { data: monHocList = [], links = [], total = 0, from = 0, to = 0 } = danhSachMonHoc || {};

    const canCreate = rolePermissions.includes('create');
    const canUpdate = rolePermissions.includes('update');
    const canDelete = rolePermissions.includes('delete');

    // ── States ────────────────────────────────────────────────────────────
    const [showModal, setShowModal]             = useState(false);
    const [isEditing, setIsEditing]             = useState(false);
    const [editId, setEditId]                   = useState(null);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(null);

    const [searchTerm, setSearchTerm]   = useState(filters?.search || '');
    const [filterKhoa, setFilterKhoa]   = useState(filters?.makhoa || '');

    // Chapter Management States
    const [showChapterModal, setShowChapterModal] = useState(false);
    const [selectedSubject, setSelectedSubject]   = useState(null);
    const [chapters, setChapters]                 = useState([]);
    const [loadingChapters, setLoadingChapters]   = useState(false);
    const [isEditingChapter, setIsEditingChapter] = useState(false);
    const [chapterForm, setChapterForm]           = useState({ machuong: null, tenchuong: '' });


    // Form fields
    const [form, setForm] = useState({
        mamonhoc: '', tenmonhoc: '', makhoa: '', sotinchi: 1,
        sotietlythuyet: 0, sotietthuchanh: 0,
    });

    const [errors, setErrors] = useState({});

    // ── Debounce search & filter ──────────────────────────────────────────
    // isFirstRender: bỏ qua lần mount đầu để không reset pagination khi user click sang trang khác
    const isFirstRender = useRef(true);
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        const timer = setTimeout(() => {
            router.get('/subject', { search: searchTerm, makhoa: filterKhoa }, {
                preserveState: true, replace: true,
            });
        }, 300);
        return () => clearTimeout(timer);
    }, [searchTerm, filterKhoa]);

    // ── Helpers ───────────────────────────────────────────────────────────
    const getKhoaName = (makhoa) => {
        if (!makhoa) return <span className="badge bg-secondary">Chung</span>;
        const k = danhSachKhoa.find(k => k.id === makhoa);
        return k ? <span className="badge bg-primary">{k.tenkhoa}</span>
                 : <span className="badge bg-warning">Không rõ</span>;
    };

    const resetForm = () => {
        setForm({ mamonhoc: '', tenmonhoc: '', makhoa: '', sotinchi: 1,
                  sotietlythuyet: 0, sotietthuchanh: 0 });
        setErrors({});
    };

    const openAddModal = () => {
        resetForm();
        setIsEditing(false);
        setEditId(null);
        setShowModal(true);
    };

    const openEditModal = (mh) => {
        setForm({
            mamonhoc:       mh.mamonhoc,
            tenmonhoc:      mh.tenmonhoc,
            makhoa:         mh.makhoa || '',
            sotinchi:       mh.sotinchi,
            sotietlythuyet: mh.sotietlythuyet,
            sotietthuchanh: mh.sotietthuchanh,
        });
        setErrors({});
        setIsEditing(true);
        setEditId(mh.id);
        setShowModal(true);
    };

    const handleChange = e => {
        const { name, value } = e.target;
        setForm(prev => ({ ...prev, [name]: value }));
    };

    // ── CRUD ──────────────────────────────────────────────────────────────
    const handleSave = () => {
        const payload = {
            ...form,
            makhoa: form.makhoa || null,
        };

        if (isEditing) {
            router.put(`/subject/${editId}`, payload, {
                onSuccess: () => { setShowModal(false); resetForm(); },
                onError: (e) => setErrors(e),
            });
        } else {
            router.post('/subject', payload, {
                onSuccess: () => { setShowModal(false); resetForm(); },
                onError: (e) => setErrors(e),
            });
        }
    };

    const handleDelete = (id) => {
        router.delete(`/subject/${id}`, {
            onSuccess: () => setShowDeleteConfirm(null),
        });
    };

    // ── CHAPTER CRUD ──────────────────────────────────────────────────────────
    const fetchChapters = (mamonhoc) => {
        setLoadingChapters(true);
        axios.get(`/chapters/subject/${mamonhoc}`)
            .then(res => {
                setChapters(res.data);
                setLoadingChapters(false);
            })
            .catch(err => {
                console.error(err);
                setLoadingChapters(false);
            });
    };

    const openChapterModal = (mh) => {
        setSelectedSubject(mh);
        setChapterForm({ machuong: null, tenchuong: '' });
        setIsEditingChapter(false);
        fetchChapters(mh.mamonhoc);
        setShowChapterModal(true);
    };

    const handleSaveChapter = (e) => {
        e.preventDefault();
        if (!chapterForm.tenchuong.trim()) return;

        if (isEditingChapter) {
            axios.put(`/chapters/${chapterForm.machuong}`, { tenchuong: chapterForm.tenchuong })
                .then(() => {
                    fetchChapters(selectedSubject.mamonhoc);
                    setChapterForm({ machuong: null, tenchuong: '' });
                    setIsEditingChapter(false);
                });
        } else {
            axios.post('/chapters', { 
                tenchuong: chapterForm.tenchuong, 
                mamonhoc: selectedSubject.mamonhoc 
            }).then(() => {
                fetchChapters(selectedSubject.mamonhoc);
                setChapterForm({ machuong: null, tenchuong: '' });
            });
        }
    };

    const startEditChapter = (ch) => {
        setChapterForm({ machuong: ch.machuong, tenchuong: ch.tenchuong });
        setIsEditingChapter(true);
    };

    const cancelEditChapter = () => {
        setChapterForm({ machuong: null, tenchuong: '' });
        setIsEditingChapter(false);
    };

    const handleDeleteChapter = (id) => {
        if (confirm('Bạn có chắc muốn xoá chương này?')) {
            axios.delete(`/chapters/${id}`)
                .then(() => fetchChapters(selectedSubject.mamonhoc));
        }
    };

    // ── Render ────────────────────────────────────────────────────────────
    return (
        <MainLayout user={user}>
            <Head title="Môn học" />

            <div className="content">
                {/* Flash */}
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
                        <h3 className="block-title">Danh sách môn học</h3>
                        <div className="block-options">
                            <span className="badge bg-primary me-3">{from}–{to} / {total} môn</span>
                            {canCreate && (
                                <button type="button" className="btn btn-hero btn-primary" onClick={openAddModal}>
                                    <i className="fa-regular fa-plus me-1"></i> Thêm môn học
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="block-content">
                        {/* Search & Filter */}
                        <div className="row mb-4 g-2">
                            <div className="col-md-6">
                                <div className="input-group">
                                    <input
                                        type="text"
                                        className="form-control form-control-alt"
                                        placeholder="Tìm kiếm theo mã hoặc tên môn học..."
                                        value={searchTerm}
                                        onChange={e => setSearchTerm(e.target.value)}
                                    />
                                    <span className="input-group-text bg-body border-0">
                                        <i className="fa fa-search"></i>
                                    </span>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <select
                                    className="form-select form-select-alt"
                                    value={filterKhoa}
                                    onChange={e => setFilterKhoa(e.target.value)}
                                >
                                    <option value="">-- Tất cả khoa --</option>
                                    <option value="chung">Chung (không thuộc khoa)</option>
                                    {danhSachKhoa.map(k => (
                                        <option key={k.id} value={k.id}>{k.tenkhoa}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Table */}
                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th className="text-center" style={{ width: '100px' }}>Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th className="text-center" style={{ width: '150px' }}>Khoa</th>
                                        <th className="text-center d-none d-sm-table-cell" style={{ width: '90px' }}>Tín chỉ</th>
                                        <th className="text-center d-none d-md-table-cell" style={{ width: '80px' }}>LT</th>
                                        <th className="text-center d-none d-md-table-cell" style={{ width: '80px' }}>TH</th>
                                        <th className="text-center" style={{ width: '100px' }}>Chương</th>
                                        {(canUpdate || canDelete) && (
                                            <th className="text-center" style={{ width: '130px' }}>Hành động</th>
                                        )}
                                    </tr>
                                </thead>
                                <tbody>
                                    {monHocList.length === 0 ? (
                                        <tr>
                                            <td colSpan={(canUpdate || canDelete) ? 8 : 7} className="text-center text-muted py-4">
                                                Không tìm thấy môn học nào
                                            </td>
                                        </tr>
                                    ) : monHocList.map(mh => (
                                        <tr key={mh.id}>
                                            <td className="text-center fw-semibold">{mh.mamonhoc}</td>
                                            <td>{mh.tenmonhoc}</td>
                                            <td className="text-center">{getKhoaName(mh.makhoa)}</td>
                                            <td className="text-center d-none d-sm-table-cell">{mh.sotinchi}</td>
                                            <td className="text-center d-none d-md-table-cell">{mh.sotietlythuyet}</td>
                                            <td className="text-center d-none d-md-table-cell">{mh.sotietthuchanh}</td>
                                            <td className="text-center">
                                                <button 
                                                    type="button" 
                                                    className="btn btn-sm btn-alt-primary"
                                                    onClick={() => openChapterModal(mh)}
                                                >
                                                    <i className="fa fa-list-ol"></i>
                                                </button>
                                            </td>
                                            {(canUpdate || canDelete) && (
                                                <td className="text-center">
                                                    <div className="btn-group">
                                                        {canUpdate && (
                                                            <button
                                                                type="button"
                                                                className="btn btn-sm btn-alt-info"
                                                                title="Sửa"
                                                                onClick={() => openEditModal(mh)}
                                                            >
                                                                <i className="fa fa-pencil-alt"></i>
                                                            </button>
                                                        )}
                                                        {canDelete && (
                                                            <button
                                                                type="button"
                                                                className="btn btn-sm btn-alt-danger"
                                                                title="Xóa"
                                                                onClick={() => setShowDeleteConfirm(mh.id)}
                                                            >
                                                                <i className="fa fa-trash"></i>
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            )}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
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

            {/* ── DELETE CONFIRM MODAL ── */}
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
                                <p className="fs-5">Bạn có chắc muốn xóa môn học này?</p>
                                <p className="text-muted">Môn học sẽ bị ẩn và không còn hiển thị trong hệ thống.</p>
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

            {/* ── ADD / EDIT MODAL ── */}
            {showModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">
                                    {isEditing ? 'Chỉnh sửa môn học' : 'Thêm môn học mới'}
                                </h5>
                                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                <div className="row g-3">
                                    {/* Mã môn */}
                                    <div className="col-md-4">
                                        <label className="form-label">Mã môn học <span className="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            className={`form-control form-control-alt ${errors.mamonhoc ? 'is-invalid' : ''}`}
                                            name="mamonhoc"
                                            placeholder="VD: CS03001"
                                            value={form.mamonhoc}
                                            onChange={handleChange}
                                            disabled={isEditing}
                                        />
                                        {errors.mamonhoc && <div className="invalid-feedback">{errors.mamonhoc}</div>}
                                    </div>

                                    {/* Tên môn */}
                                    <div className="col-md-8">
                                        <label className="form-label">Tên môn học <span className="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            className={`form-control form-control-alt ${errors.tenmonhoc ? 'is-invalid' : ''}`}
                                            name="tenmonhoc"
                                            placeholder="VD: Lập trình hướng đối tượng"
                                            value={form.tenmonhoc}
                                            onChange={handleChange}
                                        />
                                        {errors.tenmonhoc && <div className="invalid-feedback">{errors.tenmonhoc}</div>}
                                    </div>

                                    {/* Khoa */}
                                    <div className="col-md-6">
                                        <label className="form-label">Thuộc Khoa</label>
                                        <select
                                            className="form-select form-select-alt"
                                            name="makhoa"
                                            value={form.makhoa}
                                            onChange={handleChange}
                                        >
                                            <option value="">-- Chung (không thuộc khoa) --</option>
                                            {danhSachKhoa.map(k => (
                                                <option key={k.id} value={k.id}>{k.tenkhoa}</option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* Tín chỉ */}
                                    <div className="col-md-6">
                                        <label className="form-label">Số tín chỉ <span className="text-danger">*</span></label>
                                        <input
                                            type="number"
                                            className={`form-control form-control-alt ${errors.sotinchi ? 'is-invalid' : ''}`}
                                            name="sotinchi"
                                            min="1"
                                            max="10"
                                            value={form.sotinchi}
                                            onChange={handleChange}
                                        />
                                        {errors.sotinchi && <div className="invalid-feedback">{errors.sotinchi}</div>}
                                    </div>

                                    {/* Tiết LT & TH */}
                                    <div className="col-md-6">
                                        <label className="form-label">Số tiết lý thuyết</label>
                                        <input
                                            type="number"
                                            className="form-control form-control-alt"
                                            name="sotietlythuyet"
                                            min="0"
                                            value={form.sotietlythuyet}
                                            onChange={handleChange}
                                        />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label">Số tiết thực hành</label>
                                        <input
                                            type="number"
                                            className="form-control form-control-alt"
                                            name="sotietthuchanh"
                                            min="0"
                                            value={form.sotietthuchanh}
                                            onChange={handleChange}
                                        />
                                    </div>
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

            {/* ── CHAPTER MODAL ── */}
            {showChapterModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">
                                    Quản lý chương: <span className="text-primary">{selectedSubject?.tenmonhoc}</span>
                                </h5>
                                <button type="button" className="btn-close" onClick={() => setShowChapterModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                {/* Form thêm/sửa chương */}
                                <form className="mb-4 p-3 bg-light rounded" onSubmit={handleSaveChapter}>
                                    <div className="row g-2 align-items-end">
                                        <div className="col-grow">
                                            <label className="form-label mb-1 small fw-bold">
                                                {isEditingChapter ? 'Đổi tên chương' : 'Thêm chương mới'}
                                            </label>
                                            <input
                                                type="text"
                                                className="form-control form-control-sm"
                                                placeholder="Nhập tên chương..."
                                                value={chapterForm.tenchuong}
                                                onChange={e => setChapterForm({ ...chapterForm, tenchuong: e.target.value })}
                                                autoFocus
                                            />
                                        </div>
                                        <div className="col-auto">
                                            <div className="btn-group">
                                                <button type="submit" className="btn btn-sm btn-primary">
                                                    {isEditingChapter ? 'Cập nhật' : 'Thêm'}
                                                </button>
                                                {isEditingChapter && (
                                                    <button type="button" className="btn btn-sm btn-alt-secondary" onClick={cancelEditChapter}>
                                                        Huỷ
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                {/* Danh sách chương */}
                                {loadingChapters ? (
                                    <div className="text-center py-4">
                                        <div className="spinner-border text-primary" role="status"></div>
                                    </div>
                                ) : (
                                    <div className="table-responsive">
                                        <table className="table table-vcenter table-sm">
                                            <thead>
                                                <tr>
                                                    <th className="text-center" style={{ width: '50px' }}>#</th>
                                                    <th>Tên chương</th>
                                                    <th className="text-center" style={{ width: '100px' }}>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {chapters.length === 0 ? (
                                                    <tr>
                                                        <td colSpan="3" className="text-center text-muted py-3 small">
                                                            Chưa có chương nào cho môn này
                                                        </td>
                                                    </tr>
                                                ) : chapters.map((ch, idx) => (
                                                    <tr key={ch.machuong}>
                                                        <td className="text-center small text-muted">{idx + 1}</td>
                                                        <td className="fw-medium">{ch.tenchuong}</td>
                                                        <td className="text-center">
                                                            <div className="btn-group">
                                                                <button
                                                                    type="button"
                                                                    className="btn btn-sm btn-alt-info"
                                                                    onClick={() => startEditChapter(ch)}
                                                                >
                                                                    <i className="fa fa-pencil-alt"></i>
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    className="btn btn-sm btn-alt-danger"
                                                                    onClick={() => handleDeleteChapter(ch.machuong)}
                                                                >
                                                                    <i className="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-sm btn-primary" onClick={() => setShowChapterModal(false)}>
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
