import React, { useEffect, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, router } from '@inertiajs/react';
import axios from 'axios';

export default function ModuleIndex() {
    const { auth, danhSachNhom, danhSachMonHoc, flash, filters } = usePage().props;
    const user = auth.user;
    const rolePermissions = auth.user_role?.hocphan || [];

    const canCreate = rolePermissions.includes('create');
    const canUpdate = rolePermissions.includes('update');
    const canDelete = rolePermissions.includes('delete');

    const [hienthi, setHienthi] = useState(filters?.hienthi ?? 1);
    const [search, setSearch] = useState(filters?.search || '');

    const getAcademicYear = () => {
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth() + 1; // getMonth is 0-indexed
        return month >= 9 ? year : year - 1;
    };

    const initialAcademicYear = getAcademicYear();
    const namHocOptions = [];
    // 1 năm trước, năm hiện tại và 4 năm sau = 6 năm
    for (let y = initialAcademicYear - 1; y <= initialAcademicYear + 4; y++) {
        namHocOptions.push(y);
    }

    const formatNamHoc = (y) => `${y}-${y + 1}`;

    // Modal Add/Edit
    const [showModal, setShowModal] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editId, setEditId] = useState(null);
    const [form, setForm] = useState({ 
        tennhom: '', 
        mamonhoc: '', 
        namhoc: initialAcademicYear, 
        hocky: 1, 
        ghichu: '' 
    });
    const [errors, setErrors] = useState({});

    // Students modal
    const [showStudents, setShowStudents] = useState(false);
    const [currentGroup, setCurrentGroup] = useState(null);
    const [students, setStudents] = useState([]);
    const [newSvId, setNewSvId] = useState('');

    // Delete confirm
    const [deleteId, setDeleteId] = useState(null);

    // Invite code display
    const [inviteCodes, setInviteCodes] = useState({});

    // Debounce search
    useEffect(() => {
        const t = setTimeout(() => {
            router.get('/modules', { hienthi, search }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(t);
    }, [search, hienthi]);

    const openAddModal = () => {
        setForm({ tennhom: '', mamonhoc: danhSachMonHoc[0]?.mamonhoc || '', namhoc: initialAcademicYear, hocky: 1, ghichu: '' });
        setIsEditing(false);
        setEditId(null);
        setErrors({});
        setShowModal(true);
    };

    const openEditModal = (nhom, mamonhoc, namhoc, hocky) => {
        setForm({
            tennhom: nhom.tennhom,
            mamonhoc,
            namhoc,
            hocky,
            ghichu: nhom.ghichu || '',
        });
        setIsEditing(true);
        setEditId(nhom.manhom);
        setErrors({});
        setShowModal(true);
    };

    const handleSave = () => {
        const url = isEditing ? `/modules/${editId}` : '/modules';
        const options = {
            onSuccess: () => setShowModal(false),
            onError: (e) => setErrors(e),
        };

        if (isEditing) {
            router.put(url, form, options);
        } else {
            router.post(url, form, options);
        }
    };

    const handleDelete = (id) => {
        router.delete(`/modules/${id}`, { onSuccess: () => setDeleteId(null) });
    };

    const toggleVisibility = (manhom, currentVal) => {
        axios.patch(`/modules/${manhom}/visibility`, { hienthi: currentVal === 1 ? 0 : 1 })
            .then(() => router.reload());
    };

    const refreshInviteCode = (manhom) => {
        axios.patch(`/modules/${manhom}/invite-code`).then(res => {
            setInviteCodes(prev => ({ ...prev, [manhom]: res.data.mamoi }));
        });
    };

    const openStudents = (nhom) => {
        setCurrentGroup(nhom);
        setNewSvId('');
        axios.get(`/modules/${nhom.manhom}/students`).then(res => setStudents(res.data));
        setShowStudents(true);
    };

    const addStudent = () => {
        if (!newSvId.trim()) return;
        axios.post(`/modules/${currentGroup.manhom}/students`, { manguoidung: newSvId })
            .then(res => {
                if (res.data.ok) {
                    setStudents(prev => [...prev, res.data.user]);
                    setNewSvId('');
                } else {
                    alert(res.data.message || 'Không thể thêm sinh viên');
                }
            });
    };

    const removeStudent = (uid) => {
        if (!confirm('Xóa sinh viên này khỏi nhóm?')) return;
        axios.delete(`/modules/${currentGroup.manhom}/students/${uid}`)
            .then(() => setStudents(prev => prev.filter(s => s.id !== uid)));
    };

    return (
        <MainLayout user={user}>
            <Head title="Nhóm học phần" />

            <div className="content">
                {flash?.success && (
                    <div className="alert alert-success alert-dismissible" role="alert">
                        {flash.success}<button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                )}

                {/* Header */}
                <div className="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
                    <div className="d-flex align-items-center gap-2 flex-grow-1">
                        <div className="btn-group">
                            <button className={`btn btn-sm ${hienthi == 1 ? 'btn-primary' : 'btn-alt-primary'}`}
                                onClick={() => setHienthi(1)}>
                                Đang giảng dạy
                            </button>
                            <button className={`btn btn-sm ${hienthi == 0 ? 'btn-primary' : 'btn-alt-primary'}`}
                                onClick={() => setHienthi(0)}>
                                Đã ẩn
                            </button>
                            <button className={`btn btn-sm ${hienthi === 'all' ? 'btn-primary' : 'btn-alt-primary'}`}
                                onClick={() => setHienthi('all')}>
                                Tất cả
                            </button>
                        </div>
                        <input type="text" className="form-control form-control-sm" style={{ maxWidth: '260px' }}
                            placeholder="Tìm tên nhóm hoặc môn học..." value={search}
                            onChange={e => setSearch(e.target.value)} />
                    </div>
                    {canCreate && (
                        <button className="btn btn-hero btn-primary btn-sm" onClick={openAddModal}>
                            <i className="fa fa-plus me-1"></i> Thêm nhóm
                        </button>
                    )}
                </div>

                {/* Groups list */}
                {danhSachNhom.length === 0 ? (
                    <div className="block block-rounded">
                        <div className="block-content text-center py-5 text-muted">
                            <i className="fa fa-layer-group fa-3x mb-3 d-block"></i>
                            Không có nhóm nào. Hãy tạo nhóm mới!
                        </div>
                    </div>
                ) : danhSachNhom.map((group, idx) => (
                    <div key={idx} className="block block-rounded mb-3">
                        <div className="block-header block-header-default">
                            <h5 className="block-title mb-0">
                                <span className="badge bg-primary me-2">{group.mamonhoc}</span>
                                {group.tenmonhoc}
                                <span className="text-muted ms-2 fw-normal small">
                                    NH {formatNamHoc(group.namhoc)} – HK {group.hocky}
                                </span>
                            </h5>
                        </div>
                        <div className="block-content">
                            <div className="row g-3">
                                {group.nhom.map(nhom => (
                                    <div key={nhom.manhom} className="col-md-4">
                                        <div className={`card h-100 ${nhom.hienthi === 0 ? 'border-secondary opacity-75' : 'border-primary'}`}>
                                            <div className="card-body">
                                                <div className="d-flex justify-content-between align-items-start">
                                                    <h6 className="card-title mb-1 fw-bold">{nhom.tennhom}</h6>
                                                    {nhom.hienthi === 0 && (
                                                        <span className="badge bg-secondary">Đã ẩn</span>
                                                    )}
                                                </div>
                                                {nhom.ghichu && <p className="text-muted small mb-2">{nhom.ghichu}</p>}
                                                <div className="d-flex align-items-center gap-2 mt-2 mb-2">
                                                    <i className="fa fa-users text-muted"></i>
                                                    <span className="small">Sỉ số: <strong>{nhom.siso}</strong></span>
                                                </div>
                                                {/* Invite code */}
                                                <div className="d-flex align-items-center gap-1 mb-3">
                                                    <code className="text-primary small">
                                                        {inviteCodes[nhom.manhom] || nhom.mamoi}
                                                    </code>
                                                    <button className="btn btn-xs btn-alt-secondary" title="Làm mới mã mời"
                                                        onClick={() => refreshInviteCode(nhom.manhom)}>
                                                        <i className="fa fa-arrows-rotate fa-xs"></i>
                                                    </button>
                                                    <button className="btn btn-xs btn-alt-secondary" title="Sao chép"
                                                        onClick={() => navigator.clipboard.writeText(inviteCodes[nhom.manhom] || nhom.mamoi)}>
                                                        <i className="fa fa-clipboard fa-xs"></i>
                                                    </button>
                                                </div>
                                                {/* Actions */}
                                                <div className="d-flex gap-1 flex-wrap">
                                                    <button className="btn btn-sm btn-alt-info" onClick={() => openStudents(nhom)}>
                                                        <i className="fa fa-users me-1"></i>Sinh viên
                                                    </button>
                                                    {canUpdate && (
                                                        <button className="btn btn-sm btn-alt-warning"
                                                            onClick={() => openEditModal(nhom, group.mamonhoc, group.namhoc, group.hocky)}>
                                                            <i className="fa fa-pencil"></i>
                                                        </button>
                                                    )}
                                                    {canCreate && (
                                                        <button className="btn btn-sm btn-alt-secondary"
                                                            title={nhom.hienthi === 1 ? 'Ẩn nhóm' : 'Hiện nhóm'}
                                                            onClick={() => toggleVisibility(nhom.manhom, nhom.hienthi)}>
                                                            <i className={`fa fa-eye${nhom.hienthi === 0 ? '' : '-slash'}`}></i>
                                                        </button>
                                                    )}
                                                    {canDelete && (
                                                        <button className="btn btn-sm btn-alt-danger"
                                                            onClick={() => setDeleteId(nhom.manhom)}>
                                                            <i className="fa fa-trash"></i>
                                                        </button>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Delete Confirm */}
            {deleteId && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header bg-danger">
                                <h5 className="modal-title text-white">Xác nhận xóa nhóm</h5>
                            </div>
                            <div className="modal-body text-center py-4">
                                <i className="fa fa-triangle-exclamation fa-3x text-danger mb-3 d-block"></i>
                                <p>Bạn có chắc muốn xóa nhóm này? Toàn bộ dữ liệu sinh viên trong nhóm cũng sẽ bị xóa.</p>
                            </div>
                            <div className="modal-footer">
                                <button className="btn btn-alt-secondary" onClick={() => setDeleteId(null)}>Huỷ</button>
                                <button className="btn btn-danger" onClick={() => handleDelete(deleteId)}>Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Add/Edit Modal */}
            {showModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header bg-body-light">
                                <h5 className="modal-title">{isEditing ? 'Cập nhật nhóm' : 'Thêm nhóm mới'}</h5>
                                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                <div className="mb-3">
                                    <label className="form-label">Tên nhóm <span className="text-danger">*</span></label>
                                    <input type="text" className={`form-control ${errors.tennhom ? 'is-invalid' : ''}`}
                                        value={form.tennhom} onChange={e => setForm({ ...form, tennhom: e.target.value })}
                                        placeholder="Nhập tên nhóm..." />
                                    {errors.tennhom && <div className="invalid-feedback">{errors.tennhom}</div>}
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Môn học <span className="text-danger">*</span></label>
                                    <select className={`form-select ${errors.mamonhoc ? 'is-invalid' : ''}`}
                                        value={form.mamonhoc} onChange={e => setForm({ ...form, mamonhoc: e.target.value })}>
                                        <option value="">-- Chọn môn học --</option>
                                        {danhSachMonHoc.map(mh => (
                                            <option key={mh.mamonhoc} value={mh.mamonhoc}>{mh.tenmonhoc}</option>
                                        ))}
                                    </select>
                                    {errors.mamonhoc && <div className="invalid-feedback">{errors.mamonhoc}</div>}
                                </div>
                                <div className="row g-3 mb-3">
                                    <div className="col-6">
                                        <label className="form-label">Năm học <span className="text-danger">*</span></label>
                                        <select className="form-select" value={form.namhoc}
                                            onChange={e => setForm({ ...form, namhoc: parseInt(e.target.value) })}>
                                            {namHocOptions.map(y => <option key={y} value={y}>{formatNamHoc(y)}</option>)}
                                        </select>
                                    </div>
                                    <div className="col-6">
                                        <label className="form-label">Học kỳ <span className="text-danger">*</span></label>
                                        <select className="form-select" value={form.hocky}
                                            onChange={e => setForm({ ...form, hocky: e.target.value })}>
                                            <option value={1}>1</option>
                                            <option value={2}>2</option>
                                            <option value={3}>3</option>
                                        </select>
                                    </div>
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Ghi chú</label>
                                    <input type="text" className="form-control"
                                        value={form.ghichu} onChange={e => setForm({ ...form, ghichu: e.target.value })}
                                        placeholder="Nhập ghi chú (tuỳ chọn)..." />
                                </div>
                            </div>
                            <div className="modal-footer bg-body-light">
                                <button className="btn btn-alt-secondary" onClick={() => setShowModal(false)}>Huỷ</button>
                                <button className="btn btn-primary" onClick={handleSave}>
                                    {isEditing ? 'Cập nhật' : 'Tạo nhóm'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Students Modal */}
            {showStudents && currentGroup && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-scrollable modal-lg">
                        <div className="modal-content">
                            <div className="modal-header bg-body-light">
                                <h5 className="modal-title">
                                    Sinh viên – {currentGroup.tennhom}
                                    <span className="badge bg-primary ms-2">{students.length}</span>
                                </h5>
                                <button type="button" className="btn-close" onClick={() => setShowStudents(false)}></button>
                            </div>
                            <div className="modal-body">
                                {/* Add student */}
                                <div className="input-group mb-3">
                                    <input type="text" className="form-control"
                                        placeholder="Nhập mã sinh viên (ID) để thêm..."
                                        value={newSvId} onChange={e => setNewSvId(e.target.value)}
                                        onKeyDown={e => e.key === 'Enter' && addStudent()} />
                                    <button className="btn btn-primary" onClick={addStudent}>
                                        <i className="fa fa-plus me-1"></i>Thêm
                                    </button>
                                </div>
                                <table className="table table-vcenter table-sm">
                                    <thead>
                                        <tr>
                                            <th>Mã SV</th>
                                            <th>Họ và tên</th>
                                            <th>Email</th>
                                            <th className="text-center" style={{ width: '80px' }}>Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {students.length === 0 ? (
                                            <tr><td colSpan={4} className="text-center text-muted py-3">Chưa có sinh viên</td></tr>
                                        ) : students.map(sv => (
                                            <tr key={sv.id}>
                                                <td><code>{sv.id}</code></td>
                                                <td>{sv.hoten}</td>
                                                <td className="text-muted small">{sv.email}</td>
                                                <td className="text-center">
                                                    <button className="btn btn-sm btn-alt-danger"
                                                        onClick={() => removeStudent(sv.id)}>
                                                        <i className="fa fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="modal-footer bg-body-light">
                                <button className="btn btn-alt-secondary" onClick={() => setShowStudents(false)}>Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
