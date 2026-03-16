import React, { useState, useEffect, useRef } from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, router } from '@inertiajs/react';
import axios from 'axios';

export default function QuestionsIndex() {
    const { auth, danhSachCauHoi, danhSachMonHoc, danhSachKhoa, flash, filters } = usePage().props;
    const user = auth.user;
    const rolePermissions = auth.user_role?.cauhoi || [];

    const { data: questionList = [], links = [], total = 0, from = 0, to = 0 } = danhSachCauHoi || {};

    const canCreate = rolePermissions.includes('create');
    const canUpdate = rolePermissions.includes('update');
    const canDelete = rolePermissions.includes('delete');

    // ── States ────────────────────────────────────────────────────────────
    const [showModal, setShowModal]             = useState(false);
    const [isEditing, setIsEditing]             = useState(false);
    const [editId, setEditId]                   = useState(null);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(null);

    const [searchTerm, setSearchTerm]   = useState(filters?.search || '');
    const [filterKhoa, setFilterKhoa]     = useState(filters?.makhoa || '');
    const [filterMonHoc, setFilterMonHoc] = useState(filters?.mamonhoc || '');
    const [filterChuong, setFilterChuong] = useState(filters?.machuong || '');
    const [filterDoKho, setFilterDoKho]   = useState(filters?.dokho || '');

    // List of chapters for filter and form based on selected subject
    const [chaptersFilter, setChaptersFilter] = useState([]);
    const [chaptersForm, setChaptersForm]     = useState([]);

    // Form fields
    const [form, setForm] = useState({
        makhoa: '', mamonhoc: '', machuong: '', dokho: '1', noidung: '',
        cautraloi: [
            { noidungtl: '', ladapan: false },
            { noidungtl: '', ladapan: false },
        ]
    });
    
    // Manage answers state specifically to match form layout
    const [answers, setAnswers] = useState([
        { noidungtl: '', ladapan: false },
        { noidungtl: '', ladapan: false },
        { noidungtl: '', ladapan: false },
        { noidungtl: '', ladapan: false },
    ]);

    const [errors, setErrors] = useState({});

    // ── Debounce search & filter ──────────────────────────────────────────
    const isFirstRender = useRef(true);
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        const timer = setTimeout(() => {
            router.get('/questions', { 
                search: searchTerm, 
                makhoa: filterKhoa,
                mamonhoc: filterMonHoc, 
                machuong: filterChuong, 
                dokho: filterDoKho 
            }, {
                preserveState: true, replace: true,
            });
        }, 300);
        return () => clearTimeout(timer);
    }, [searchTerm, filterKhoa, filterMonHoc, filterChuong, filterDoKho]);

    // Fetch chapters when MonHoc filter changes
    useEffect(() => {
        if (filterMonHoc) {
            axios.get(`/chapters/subject/${filterMonHoc}`).then(res => setChaptersFilter(res.data));
            // Reset chuong filter if old selection doesn't exist
            setFilterChuong(''); 
        } else {
            setChaptersFilter([]);
            setFilterChuong('');
        }
    }, [filterMonHoc]);

    // Fetch chapters when MonHoc in form changes
    useEffect(() => {
        if (form.mamonhoc) {
            axios.get(`/chapters/subject/${form.mamonhoc}`).then(res => {
                setChaptersForm(res.data);
                if (isEditing) return; // if editing keep the mapped value
                if(res.data.length > 0) {
                    setForm(prev => ({...prev, machuong: res.data[0].machuong}));
                } else {
                    setForm(prev => ({...prev, machuong: ''}));
                }
            });
        } else {
            setChaptersForm([]);
        }
    }, [form.mamonhoc]);

    // Lọc danh sách Môn học theo Khoa
    const filteredMonHocSearch = filterKhoa 
        ? danhSachMonHoc.filter(mh => filterKhoa === 'chung' ? !mh.makhoa : mh.makhoa == filterKhoa) 
        : danhSachMonHoc;

    const filteredMonHocForm = form.makhoa 
        ? danhSachMonHoc.filter(mh => form.makhoa === 'chung' ? !mh.makhoa : mh.makhoa == form.makhoa) 
        : danhSachMonHoc;

    // ── Helpers ───────────────────────────────────────────────────────────
    
    // Strip HTML Tags to show in table
    const stripHtml = (html) => {
        if (!html) return '';
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || "";
    }

    const getLevelBadge = (level) => {
        switch (String(level)) {
            case '1': return <span className="badge bg-success">Cơ bản</span>;
            case '2': return <span className="badge bg-warning">Trung bình</span>;
            case '3': return <span className="badge bg-danger">Nâng cao</span>;
            default: return <span className="badge bg-secondary">Không rõ</span>;
        }
    }

    const resetForm = () => {
        setForm({ 
            makhoa: '',
            mamonhoc: '', 
            machuong: '', 
            dokho: '1', 
            noidung: '',
            cautraloi: [] 
        });
        setAnswers([
            { noidungtl: '', ladapan: true },
            { noidungtl: '', ladapan: false },
            { noidungtl: '', ladapan: false },
            { noidungtl: '', ladapan: false },
        ]);
        setErrors({});
    };

    const openAddModal = () => {
        resetForm();
        setIsEditing(false);
        setEditId(null);
        setShowModal(true);
    };

    const openEditModal = (q) => {
        setForm({
            makhoa: q.monhoc?.makhoa || 'chung',
            mamonhoc: q.mamonhoc,
            machuong: q.machuong,
            dokho: q.dokho,
            noidung: q.noidung,
        });
        
        if (q.cautraloi && q.cautraloi.length > 0) {
            setAnswers(q.cautraloi.map(ans => ({
                id: ans.macautl, // keep ID for potential update
                noidungtl: ans.noidungtl,
                ladapan: ans.ladapan === 1 || ans.ladapan === true,
            })));
        } else {
            setAnswers([
                { noidungtl: '', ladapan: true },
                { noidungtl: '', ladapan: false },
                { noidungtl: '', ladapan: false },
                { noidungtl: '', ladapan: false },
            ]);
        }

        setErrors({});
        setIsEditing(true);
        setEditId(q.macauhoi);
        setShowModal(true);
    };

    const handleChange = e => {
        const { name, value } = e.target;
        setForm(prev => ({ ...prev, [name]: value }));
    };

    const handleAnswerChange = (index, field, value) => {
        const newAnswers = [...answers];
        
        if (field === 'ladapan' && value === true) {
            // Radio-like behavior for answers (only one correct answer typically, but this allows multiple if modified to checkbox, currently single)
            newAnswers.forEach((ans, i) => { ans.ladapan = (i === index) });
        } else {
            newAnswers[index][field] = value;
        }
        
        setAnswers(newAnswers);
    };

    const addAnswerField = () => {
        setAnswers([...answers, { noidungtl: '', ladapan: false }]);
    };

    const removeAnswerField = (index) => {
        if (answers.length <= 2) {
            alert('Cần ít nhất 2 câu trả lời!');
            return;
        }
        const newAnswers = answers.filter((_, i) => i !== index);
        // Ensure at least one is correct if we removed the correct one
        if (!newAnswers.some(a => a.ladapan) && newAnswers.length > 0) {
            newAnswers[0].ladapan = true;
        }
        setAnswers(newAnswers);
    };

    // ── CRUD ──────────────────────────────────────────────────────────────
    const handleSave = () => {
        const payload = {
            ...form,
            cautraloi: answers.filter(a => a.noidungtl.trim() !== ''),
        };

        if (payload.cautraloi.length < 2) {
            alert('Vui lòng nhập ít nhất 2 câu trả lời');
            return;
        }

        if (!payload.cautraloi.some(a => a.ladapan)) {
            alert('Vui lòng chọn ít nhất 1 đáp án đúng');
            return;
        }

        if (isEditing) {
            router.put(`/questions/${editId}`, payload, {
                onSuccess: () => { setShowModal(false); resetForm(); },
                onError: (e) => setErrors(e),
            });
        } else {
            router.post('/questions', payload, {
                onSuccess: () => { setShowModal(false); resetForm(); },
                onError: (e) => setErrors(e),
            });
        }
    };

    const handleDelete = (id) => {
        router.delete(`/questions/${id}`, {
            onSuccess: () => setShowDeleteConfirm(null),
        });
    };

    // ── Render ────────────────────────────────────────────────────────────
    return (
        <MainLayout user={user}>
            <Head title="Câu hỏi - Ngân hàng đề" />

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
                    <div className="block-header block-header-default d-flex justify-content-between">
                        <h3 className="block-title">Tất cả câu hỏi</h3>
                        <div className="block-options">
                            <span className="badge bg-primary me-3">{from}–{to} / {total} câu</span>
                            {canCreate && (
                                <button type="button" className="btn btn-hero btn-primary" onClick={openAddModal}>
                                    <i className="fa-regular fa-plus me-1"></i> Thêm câu hỏi
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="block-content">
                        {/* Search & Filter */}
                        <div className="row mb-4 g-2">
                            <div className="col-md-2">
                                <select className="form-select form-select-alt" value={filterKhoa} onChange={e => { setFilterKhoa(e.target.value); setFilterMonHoc(''); }}>
                                    <option value="">-- Khoa --</option>
                                    <option value="chung">Khoa dùng chung</option>
                                    {danhSachKhoa.map(k => (
                                        <option key={k.id} value={k.id}>{k.tenkhoa}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-3">
                                <select className="form-select form-select-alt" value={filterMonHoc} onChange={e => setFilterMonHoc(e.target.value)}>
                                    <option value="">-- Môn học --</option>
                                    {filteredMonHocSearch.map(mh => (
                                        <option key={mh.mamonhoc} value={mh.mamonhoc}>{mh.tenmonhoc}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <select className="form-select form-select-alt" value={filterChuong} onChange={e => setFilterChuong(e.target.value)} disabled={!filterMonHoc}>
                                    <option value="">-- Chương --</option>
                                    {chaptersFilter.map(ch => (
                                        <option key={ch.machuong} value={ch.machuong}>{ch.tenchuong}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-2">
                                <select className="form-select form-select-alt" value={filterDoKho} onChange={e => setFilterDoKho(e.target.value)}>
                                    <option value="">-- Độ khó --</option>
                                    <option value="1">Cơ bản</option>
                                    <option value="2">Trung bình</option>
                                    <option value="3">Nâng cao</option>
                                </select>
                            </div>
                            <div className="col-md-3">
                                <div className="input-group">
                                    <input
                                        type="text"
                                        className="form-control form-control-alt"
                                        placeholder="Tìm nội dung câu hỏi..."
                                        value={searchTerm}
                                        onChange={e => setSearchTerm(e.target.value)}
                                    />
                                    <span className="input-group-text bg-body border-0">
                                        <i className="fa fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Table */}
                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th className="text-center" style={{ width: '80px' }}>ID</th>
                                        <th>Nội dung câu hỏi</th>
                                        <th className="d-none d-sm-table-cell" style={{ width: '200px' }}>Môn học</th>
                                        <th className="d-none d-xl-table-cell text-center" style={{ width: '120px' }}>Độ khó</th>
                                        {(canUpdate || canDelete) && (
                                            <th className="text-center" style={{ width: '120px' }}>Hành động</th>
                                        )}
                                    </tr>
                                </thead>
                                <tbody>
                                    {questionList.length === 0 ? (
                                        <tr>
                                            <td colSpan={(canUpdate || canDelete) ? 5 : 4} className="text-center text-muted py-4">
                                                Không tìm thấy câu hỏi nào
                                            </td>
                                        </tr>
                                    ) : questionList.map(q => (
                                        <tr key={q.macauhoi}>
                                            <td className="text-center fw-semibold text-muted">#{q.macauhoi}</td>
                                            <td className="text-wrap" style={{ maxWidth: '400px' }}>
                                                <div dangerouslySetInnerHTML={{ __html: q.noidung.length > 150 ? q.noidung.substring(0, 150) + '...' : q.noidung }} />
                                            </td>
                                            <td className="d-none d-sm-table-cell">
                                                <span className="fw-semibold">{q.monhoc?.tenmonhoc}</span>
                                                <br/>
                                                <span className="text-muted small">C{q.chuong?.machuong} </span>
                                            </td>
                                            <td className="text-center d-none d-xl-table-cell">
                                                {getLevelBadge(q.dokho)}
                                            </td>
                                            {(canUpdate || canDelete) && (
                                                <td className="text-center">
                                                    <div className="btn-group">
                                                        {canUpdate && (
                                                            <button type="button" className="btn btn-sm btn-alt-info" title="Sửa" onClick={() => openEditModal(q)}>
                                                                <i className="fa fa-pencil-alt"></i>
                                                            </button>
                                                        )}
                                                        {canDelete && (
                                                            <button type="button" className="btn btn-sm btn-alt-danger" title="Xóa" onClick={() => setShowDeleteConfirm(q.macauhoi)}>
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
                                <h5 className="modal-title text-white">Xác nhận xóa</h5>
                            </div>
                            <div className="modal-body text-center py-4">
                                <i className="fa fa-trash fa-3x text-danger mb-3"></i>
                                <p className="fs-5">Bạn có chắc muốn xóa câu hỏi này?</p>
                                <p className="text-muted">Dữ liệu sẽ được ẩn khỏi hệ thống.</p>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-alt-secondary" onClick={() => setShowDeleteConfirm(null)}>Huỷ</button>
                                <button type="button" className="btn btn-danger" onClick={() => handleDelete(showDeleteConfirm)}>Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* ── ADD / EDIT MODAL ── */}
            {showModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-scrollable modal-xl">
                        <div className="modal-content">
                            <div className="modal-header bg-body-light">
                                <h5 className="modal-title">
                                    {isEditing ? 'Chỉnh sửa câu hỏi' : 'Thêm câu hỏi mới'}
                                </h5>
                                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                <form onSubmit={e => { e.preventDefault(); handleSave(); }}>
                                    <div className="row g-4 mb-4">
                                        <div className="col-md-3">
                                            <label className="form-label">Khoa</label>
                                            <select className="form-select" name="makhoa" value={form.makhoa} onChange={e => setForm({...form, makhoa: e.target.value, mamonhoc: ''})}>
                                                <option value="">-- Tất cả Khoa --</option>
                                                <option value="chung">Khoa dùng chung</option>
                                                {danhSachKhoa.map(k => (
                                                    <option key={k.id} value={k.id}>{k.tenkhoa}</option>
                                                ))}
                                            </select>
                                        </div>

                                        <div className="col-md-3">
                                            <label className="form-label">Môn học <span className="text-danger">*</span></label>
                                            <select className={`form-select ${errors.mamonhoc ? 'is-invalid' : ''}`} name="mamonhoc" value={form.mamonhoc} onChange={handleChange}>
                                                <option value="">-- Chọn môn học --</option>
                                                {filteredMonHocForm.map(mh => (
                                                    <option key={mh.mamonhoc} value={mh.mamonhoc}>{mh.tenmonhoc}</option>
                                                ))}
                                            </select>
                                            {errors.mamonhoc && <div className="invalid-feedback">{errors.mamonhoc}</div>}
                                        </div>
                                        
                                        <div className="col-md-3">
                                            <label className="form-label">Chương <span className="text-danger">*</span></label>
                                            <select className={`form-select ${errors.machuong ? 'is-invalid' : ''}`} name="machuong" value={form.machuong} onChange={handleChange} disabled={!form.mamonhoc}>
                                                <option value="">-- Chọn chương --</option>
                                                {chaptersForm.map(ch => (
                                                    <option key={ch.machuong} value={ch.machuong}>{ch.tenchuong}</option>
                                                ))}
                                            </select>
                                            {errors.machuong && <div className="invalid-feedback">{errors.machuong}</div>}
                                        </div>

                                        <div className="col-md-3">
                                            <label className="form-label">Độ khó <span className="text-danger">*</span></label>
                                            <select className={`form-select ${errors.dokho ? 'is-invalid' : ''}`} name="dokho" value={form.dokho} onChange={handleChange}>
                                                <option value="1">Cơ bản</option>
                                                <option value="2">Trung bình</option>
                                                <option value="3">Nâng cao</option>
                                            </select>
                                            {errors.dokho && <div className="invalid-feedback">{errors.dokho}</div>}
                                        </div>
                                    </div>

                                    <div className="mb-4">
                                        <label className="form-label">Nội dung câu hỏi <span className="text-danger">*</span></label>
                                        <textarea 
                                            className={`form-control ${errors.noidung ? 'is-invalid' : ''}`} 
                                            rows="4"
                                            name="noidung" 
                                            value={form.noidung} 
                                            onChange={handleChange}
                                            placeholder="Bạn có thể nhập HTML hoặc plain text vào đây..."
                                        ></textarea>
                                        {errors.noidung && <div className="invalid-feedback">{errors.noidung}</div>}
                                    </div>

                                    <div className="mb-2">
                                        <h5 className="h6 border-bottom pb-2 mb-3">Danh sách câu trả lời</h5>
                                        {errors.cautraloi && <div className="text-danger small mb-2">{errors.cautraloi}</div>}
                                        
                                        <div className="table-responsive">
                                            <table className="table table-bordered table-vcenter">
                                                <thead className="bg-body-light">
                                                    <tr>
                                                        <th className="text-center" style={{ width: '60px' }}>Đúng</th>
                                                        <th>Nội dung câu trả lời</th>
                                                        <th className="text-center" style={{ width: '60px' }}>Xoá</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {answers.map((ans, idx) => (
                                                        <tr key={idx} className={ans.ladapan ? 'bg-success-light' : ''}>
                                                            <td className="text-center">
                                                                <div className="form-check d-flex justify-content-center">
                                                                    <input 
                                                                        className="form-check-input" 
                                                                        type="radio" 
                                                                        name="correct_answer"
                                                                        checked={ans.ladapan}
                                                                        onChange={() => handleAnswerChange(idx, 'ladapan', true)}
                                                                        id={`ans_correct_${idx}`}
                                                                    />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input 
                                                                    type="text" 
                                                                    className={`form-control border-0 bg-transparent ${ans.ladapan ? 'fw-bold' : ''}`}
                                                                    placeholder="Nhập câu trả lời..." 
                                                                    value={ans.noidungtl}
                                                                    onChange={(e) => handleAnswerChange(idx, 'noidungtl', e.target.value)}
                                                                />
                                                            </td>
                                                            <td className="text-center">
                                                                <button type="button" className="btn btn-sm btn-alt-danger" onClick={() => removeAnswerField(idx)}>
                                                                    <i className="fa fa-times"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                        <div className="mt-2">
                                            <button type="button" className="btn btn-sm btn-alt-secondary" onClick={addAnswerField}>
                                                <i className="fa fa-plus me-1"></i> Thêm đáp án
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div className="modal-footer bg-body-light">
                                <button type="button" className="btn btn-alt-secondary" onClick={() => setShowModal(false)}>Huỷ</button>
                                <button type="button" className="btn btn-primary" onClick={handleSave}>
                                    {isEditing ? 'Cập nhật' : 'Lưu câu hỏi'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
