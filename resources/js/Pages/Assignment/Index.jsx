import React, { useState, useEffect } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, router } from '@inertiajs/react';
import axios from 'axios';

export default function AssignmentIndex() {
    const { auth, danhSachPhanCong, danhSachGiangVien, danhSachMonHoc, danhSachKhoa, flash, filters } = usePage().props;
    const user = auth.user;

    const [search, setSearch] = useState(filters?.search || '');

    // Modal add
    const [showModal, setShowModal] = useState(false);
    const [selectedKhoa, setSelectedKhoa] = useState('');
    const [gvSearch, setGvSearch] = useState('');
    const [selectedGV, setSelectedGV] = useState('');
    const [selectedMons, setSelectedMons] = useState([]); // array of mamonhoc
    const [assignedMons, setAssignedMons] = useState([]); // already assigned
    const [monSearch, setMonSearch] = useState('');

    // Fetch assigned subjects when lecturer changes
    useEffect(() => {
        if (!selectedGV) { setAssignedMons([]); return; }
        axios.get(`/assignment/user/${selectedGV}`).then(res => setAssignedMons(res.data || []));
    }, [selectedGV]);

    // Debounce search
    useEffect(() => {
        const t = setTimeout(() => {
            router.get('/assignment', { search }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(t);
    }, [search]);

    const handleSearch = () => {
        router.get('/assignment', { search }, { preserveState: true, replace: true });
    };

    const filteredGVs = danhSachGiangVien.filter(gv => {
        const matchesKhoa = !selectedKhoa || gv.makhoa == selectedKhoa;
        const matchesName = !gvSearch || gv.hoten.toLowerCase().includes(gvSearch.toLowerCase()) || gv.id.toString().includes(gvSearch);
        return matchesKhoa && matchesName;
    });

    const filteredMons = danhSachMonHoc.filter(mh => {
        // Lọc theo search keyword
        const matchesSearch = !monSearch || 
            mh.tenmonhoc.toLowerCase().includes(monSearch.toLowerCase()) || 
            mh.mamonhoc.toLowerCase().includes(monSearch.toLowerCase());
        
        if (!matchesSearch) return false;

        // Nếu chưa chọn GV, hiện tất cả (hoặc ẩn tuỳ ý, ở đây hiện tất cả cho search)
        if (!selectedGV) return true;

        // Lọc theo khoa: Cùng khoa hoặc môn chung (makhoa null)
        const gv = danhSachGiangVien.find(g => g.id === selectedGV);
        const userKhoa = gv?.makhoa;
        
        return mh.makhoa === null || mh.makhoa == userKhoa;
    });

    const toggleMon = (mamonhoc) => {
        setSelectedMons(prev =>
            prev.includes(mamonhoc) ? prev.filter(m => m !== mamonhoc) : [...prev, mamonhoc]
        );
    };

    const openModal = () => {
        setSelectedKhoa('');
        setGvSearch('');
        setSelectedGV('');
        setSelectedMons([]);
        setMonSearch('');
        setShowModal(true);
    };

    const handleSave = () => {
        if (!selectedGV) { alert('Vui lòng chọn giảng viên'); return; }
        if (selectedMons.length === 0) { alert('Vui lòng chọn ít nhất 1 môn học'); return; }

        router.post('/assignment', { manguoidung: selectedGV, danhSachMon: selectedMons }, {
            onSuccess: () => setShowModal(false),
        });
    };

    const handleDelete = (mamonhoc, uid) => {
        if (!confirm('Xóa phân công này?')) return;
        router.delete(`/assignment/${mamonhoc}/${uid}`, {
            preserveScroll: true,
            onError: (e) => alert(e?.message || 'Không thể xóa.'),
        });
    };

    const handleDeleteAll = (gv) => {
        if (!confirm(`Xóa toàn bộ phân công của ${gv.hoten}?`)) return;
        const uid = String(gv.manguoidung ?? gv.id ?? '');
        if (!uid) {
            alert('Không xác định được giảng viên.');
            return;
        }
        router.delete(`/assignment/user/${uid}`, {
            preserveScroll: true,
            onError: (e) => alert(e?.message || 'Không thể xóa.'),
        });
    };

    // Group by giảng viên for display
    const grouped = (danhSachPhanCong || []).reduce((acc, pc) => {
        if (!acc[pc.manguoidung]) {
            acc[pc.manguoidung] = { hoten: pc.hoten, manguoidung: pc.manguoidung, monhocs: [] };
        }
        acc[pc.manguoidung].monhocs.push({ mamonhoc: pc.mamonhoc, tenmonhoc: pc.tenmonhoc });
        return acc;
    }, {});

    return (
        <MainLayout user={user}>
            <Head title="Phân công giảng viên" />

            <div className="content">
                {flash?.success && (
                    <div className="alert alert-success alert-dismissible" role="alert">
                        {flash.success}<button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                )}
                {flash?.error && (
                    <div className="alert alert-danger alert-dismissible" role="alert">
                        {flash.error}<button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                )}

                <div className="block block-rounded">
                    <div className="block-header block-header-default d-flex justify-content-between">
                        <h3 className="block-title">Phân công giảng dạy</h3>
                        <button className="btn btn-hero btn-primary" onClick={openModal}>
                            <i className="fa fa-plus me-1"></i> Thêm phân công
                        </button>
                    </div>
                    <div className="block-content">
                        <div className="mb-4">
                            <div className="input-group">
                                <input type="text" className="form-control form-control-alt"
                                    placeholder="Tìm giảng viên, môn học..."
                                    value={search} 
                                    onChange={e => setSearch(e.target.value)}
                                    onKeyDown={e => e.key === 'Enter' && handleSearch()} />
                                <button type="button" className="btn btn-alt-secondary" onClick={handleSearch}>
                                    <i className="fa fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th style={{ width: '30%' }}>Giảng viên</th>
                                        <th>Môn được phân công</th>
                                        <th className="text-center" style={{ width: '120px' }}>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {Object.values(grouped).length === 0 ? (
                                        <tr><td colSpan={3} className="text-center text-muted py-4">Chưa có phân công nào</td></tr>
                                    ) : Object.values(grouped).map(gv => (
                                        <tr key={gv.manguoidung}>
                                            <td>
                                                <span className="fw-semibold">{gv.hoten}</span>
                                                <br />
                                                <small className="text-muted">ID: {gv.manguoidung}</small>
                                            </td>
                                            <td>
                                                <div className="d-flex flex-wrap gap-1">
                                                    {gv.monhocs.map(mh => (
                                                        <span key={mh.mamonhoc} className="badge bg-primary-light text-primary me-1">
                                                            {mh.mamonhoc} – {mh.tenmonhoc}
                                                            <button
                                                                type="button"
                                                                className="btn-close btn-close ms-1"
                                                                style={{ fontSize: '0.6rem' }}
                                                                onClick={() => handleDelete(mh.mamonhoc, gv.manguoidung)}
                                                            ></button>
                                                        </span>
                                                    ))}
                                                </div>
                                            </td>
                                            <td className="text-center">
                                                <button
                                                    className="btn btn-sm btn-alt-danger"
                                                    title="Xóa tất cả phân công"
                                                    onClick={() => handleDeleteAll(gv)}
                                                >
                                                    <i className="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {/* Modal thêm phân công */}
            {showModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-scrollable modal-xl">
                        <div className="modal-content">
                            <div className="modal-header bg-body-light">
                                <h5 className="modal-title">Thêm phân công giảng dạy</h5>
                                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                {/* Chọn giảng viên */}
                                <div className="row mb-4">
                                    <div className="col-md-4">
                                        <label className="form-label fw-semibold">Lọc theo khoa</label>
                                        <select className="form-select" value={selectedKhoa} onChange={e => setSelectedKhoa(e.target.value)}>
                                            <option value="">Tất cả khoa</option>
                                            {danhSachKhoa.map(k => (
                                                <option key={k.id} value={k.id}>{k.tenkhoa}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="col-md-8">
                                        <label className="form-label fw-semibold">Giảng viên <span className="text-danger">*</span></label>
                                        <div className="input-group mb-2">
                                            <span className="input-group-text"><i className="fa fa-search"></i></span>
                                            <input type="text" className="form-control" placeholder="Tìm tên giảng viên hoặc ID..."
                                                value={gvSearch} onChange={e => setGvSearch(e.target.value)} />
                                        </div>
                                        <select className="form-select" value={selectedGV} size="5" onChange={e => setSelectedGV(e.target.value)}>
                                            <option value="">-- Chọn giảng viên ({filteredGVs.length}) --</option>
                                            {filteredGVs.map(gv => (
                                                <option key={gv.id} value={gv.id}>
                                                    {gv.hoten} (ID: {gv.id}) {gv.tenkhoa ? ` - ${gv.tenkhoa}` : ''}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                {/* Tìm kiếm môn */}
                                <div className="mb-3">
                                    <div className="input-group">
                                        <input type="text" className="form-control" placeholder="Tìm môn học..."
                                            value={monSearch} 
                                            onChange={e => setMonSearch(e.target.value)}
                                            onKeyDown={e => e.key === 'Enter' && setMonSearch(monSearch)} />
                                        <button type="button" className="input-group-text btn btn-alt-secondary">
                                            <i className="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                                {/* Bảng môn học */}
                                <div className="table-responsive" style={{ maxHeight: '350px', overflowY: 'auto' }}>
                                    <table className="table table-vcenter table-bordered table-sm">
                                        <thead className="bg-body-light sticky-top">
                                            <tr>
                                                <th className="text-center" style={{ width: '60px' }}>Chọn</th>
                                                <th>Mã môn</th>
                                                <th>Tên môn học</th>
                                                <th className="text-center">Tín chỉ</th>
                                                <th className="text-center">LT</th>
                                                <th className="text-center">TH</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {filteredMons.map(mh => {
                                                const isAssigned = assignedMons.includes(mh.mamonhoc);
                                                const isChecked = selectedMons.includes(mh.mamonhoc);
                                                return (
                                                    <tr key={mh.mamonhoc} className={isAssigned ? 'table-success' : ''}>
                                                        <td className="text-center">
                                                            <input
                                                                type="checkbox"
                                                                className="form-check-input"
                                                                checked={isChecked || isAssigned}
                                                                disabled={isAssigned}
                                                                onChange={() => toggleMon(mh.mamonhoc)}
                                                            />
                                                        </td>
                                                        <td><code>{mh.mamonhoc}</code></td>
                                                        <td>
                                                            {mh.tenmonhoc}
                                                            {isAssigned && <span className="badge bg-success ms-2">Đã phân công</span>}
                                                        </td>
                                                        <td className="text-center">{mh.sotinchi}</td>
                                                        <td className="text-center">{mh.sotietlythuyet}</td>
                                                        <td className="text-center">{mh.sotietthuchanh}</td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>

                                {selectedMons.length > 0 && (
                                    <div className="mt-3 text-muted small">
                                        Đã chọn: {selectedMons.length} môn học
                                    </div>
                                )}
                            </div>
                            <div className="modal-footer bg-body-light">
                                <button className="btn btn-alt-secondary" onClick={() => setShowModal(false)}>Huỷ</button>
                                <button className="btn btn-primary" onClick={handleSave}>
                                    <i className="fa fa-save me-1"></i> Lưu phân công
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
