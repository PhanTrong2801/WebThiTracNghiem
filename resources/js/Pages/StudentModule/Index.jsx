import React, { useState, useEffect } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, router } from '@inertiajs/react';
import axios from 'axios';

export default function StudentModuleIndex() {
    const { auth, joinedGroups, filters, flash } = usePage().props;
    const user = auth.user;

    const [hienthi, setHienthi] = useState(filters?.hienthi ?? 1);
    const [search, setSearch] = useState(filters?.search || '');

    // Modal Join Group
    const [showJoinModal, setShowJoinModal] = useState(false);
    const [mamoi, setMamoi] = useState('');
    const [errors, setErrors] = useState({});

    // Offcanvas for details
    const [showOffcanvas, setShowOffcanvas] = useState(false);
    const [selectedGroup, setSelectedGroup] = useState(null);
    const [members, setMembers] = useState([]);
    const [activeTab, setActiveTab] = useState('tests'); // tests, announcements, friends

    const handleJoin = () => {
        router.post('/student/modules/join', { mamoi }, {
            onSuccess: () => {
                setShowJoinModal(false);
                setMamoi('');
                setErrors({});
            },
            onError: (err) => setErrors(err)
        });
    };

    const handleLeave = (id) => {
        if (confirm('Bạn có chắc muốn rời khỏi nhóm này?')) {
            router.delete(`/student/modules/${id}/leave`);
        }
    };

    const toggleVisibility = (id, currentVal) => {
        router.patch(`/student/modules/${id}/visibility`, { 
            hienthi: currentVal === 1 ? 0 : 1 
        });
    };

    // Debounce search
    useEffect(() => {
        const t = setTimeout(() => {
            router.get('/student/modules', { hienthi, search }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(t);
    }, [search, hienthi]);

    const handleFilterChange = (val) => {
        setHienthi(val);
    };

    const openDetails = (group) => {
        setSelectedGroup(group);
        setShowOffcanvas(true);
        setActiveTab('tests');
        // Load members
        axios.get(`/student/modules/${group.manhom}/members`)
            .then(res => setMembers(res.data))
            .catch(err => console.error(err));
    };

    const filteredGroups = joinedGroups.filter(g => 
        g.tennhom.toLowerCase().includes(search.toLowerCase()) ||
        g.monhoc?.tenmonhoc.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <MainLayout user={user}>
            <Head title="Học phần của tôi" />

            <div className="content">
                <div className="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2 text-center text-sm-start">
                    <div className="flex-grow-1">
                        <h1 className="h3 fw-bold mb-2">Học phần của tôi</h1>
                        <h2 className="fs-base lh-base fw-medium text-muted mb-0">
                            Tham gia và quản lý các nhóm học tập của bạn.
                        </h2>
                    </div>
                </div>
            </div>

            <div className="content">
                <div className="row mb-4">
                    <div className="col-md-8">
                        <div className="input-group">
                            <button className="btn btn-alt-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i className="fa fa-filter me-1"></i>
                                {hienthi === 1 ? 'Đang học tập' : 'Đã ẩn'}
                            </button>
                            <ul className="dropdown-menu">
                                <li><button className="dropdown-item" onClick={() => handleFilterChange(1)}>Đang học tập</button></li>
                                <li><button className="dropdown-item" onClick={() => handleFilterChange(0)}>Đã ẩn</button></li>
                            </ul>
                            <span className="input-group-text bg-white border-end-0">
                                <i className="fa fa-search text-muted"></i>
                            </span>
                            <input 
                                type="text" 
                                className="form-control border-start-0" 
                                placeholder="Tìm tên nhóm hoặc môn học..." 
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="col-md-4 text-end">
                        <button 
                            className="btn btn-primary"
                            onClick={() => setShowJoinModal(true)}
                        >
                            <i className="fa fa-plus me-1"></i> Tham gia nhóm
                        </button>
                    </div>
                </div>

                <div className="row">
                    {filteredGroups.length > 0 ? filteredGroups.map(group => (
                        <div className="col-md-6 col-xl-4" key={group.manhom}>
                            <div className="block block-rounded block-link-pop h-100 mb-0 d-flex flex-column">
                                <div className="block-content block-content-full flex-grow-1">
                                    <div className="d-flex align-items-center justify-content-between mb-3">
                                        <div className={`item item-circle ${hienthi === 1 ? 'bg-primary-light' : 'bg-gray-light'}`}>
                                            <i className={`fa fa-layer-group ${hienthi === 1 ? 'text-primary' : 'text-gray'}`}></i>
                                        </div>
                                        <div className="dropdown">
                                            <button type="button" className="btn btn-sm btn-alt-secondary" data-bs-toggle="dropdown">
                                                <i className="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div className="dropdown-menu dropdown-menu-end">
                                                <a className="dropdown-item" href="#" onClick={(e) => { e.preventDefault(); toggleVisibility(group.manhom, hienthi); }}>
                                                    <i className={`fa fa-fw ${hienthi === 1 ? 'fa-eye-slash' : 'fa-eye'} me-1`}></i>
                                                    {hienthi === 1 ? 'Ẩn nhóm' : 'Hiện nhóm'}
                                                </a>
                                                <div className="dropdown-divider"></div>
                                                <a className="dropdown-item text-danger" href="#" onClick={(e) => { e.preventDefault(); handleLeave(group.manhom); }}>
                                                    <i className="fa fa-sign-out-alt me-1"></i> Rời nhóm
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 className="mb-1 cursor-pointer" onClick={() => openDetails(group)}>{group.tennhom}</h4>
                                    <p className="fs-sm text-muted mb-2">
                                        {group.monhoc?.tenmonhoc}
                                    </p>
                                    <div className="fs-sm text-muted">
                                        <i className="fa fa-user-tie me-1"></i> {group.giang_vien_user?.hoten || 'Chưa có GV'}
                                    </div>
                                    <div className="fs-sm text-muted">
                                         {group.namhoc}-{group.namhoc + 1} - HK {group.hocky}
                                    </div>
                                </div>
                                <div className="block-content block-content-full block-content-sm bg-body-light fs-sm text-center">
                                    <button className="btn btn-sm btn-alt-primary w-100" onClick={() => openDetails(group)}>
                                        Xem chi tiết
                                    </button>
                                </div>
                            </div>
                        </div>
                    )) : (
                        <div className="col-12 text-center py-5">
                            <i className="fa fa-layer-group fa-3x text-muted mb-3"></i>
                            <p className="text-muted">Bạn chưa tham gia nhóm học phần nào.</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Modal Tham gia nhóm */}
            {showJoinModal && (
                <div className="modal fade show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="block block-rounded block-themed block-transparent mb-0">
                                <div className="block-header bg-primary">
                                    <h3 className="block-title">Tham gia nhóm học phần</h3>
                                    <div className="block-options">
                                        <button type="button" className="btn-block-option" onClick={() => setShowJoinModal(false)}>
                                            <i className="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div className="block-content p-4">
                                    <div className="mb-4">
                                        <label className="form-label">Mã mời</label>
                                        <input 
                                            type="text" 
                                            className={`form-control ${errors.mamoi ? 'is-invalid' : ''}`}
                                            placeholder="Nhập mã mời 7 ký tự"
                                            value={mamoi}
                                            onChange={(e) => setMamoi(e.target.value)}
                                            maxLength={7}
                                        />
                                        {errors.mamoi && <div className="invalid-feedback">{errors.mamoi}</div>}
                                    </div>
                                    <div className="alert alert-info fs-sm">
                                        <p className="mb-0"><i className="fa fa-info-circle me-1"></i> Mã mời được giảng viên cung cấp, gồm 7 ký tự (chữ hoặc số).</p>
                                    </div>
                                </div>
                                <div className="block-content block-content-full text-end bg-body-light">
                                    <button type="button" className="btn btn-sm btn-alt-secondary me-1" onClick={() => setShowJoinModal(false)}>Hủy</button>
                                    <button type="button" className="btn btn-sm btn-primary" onClick={handleJoin}>Tham gia</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Offcanvas Details */}
            <div className={`offcanvas offcanvas-end ${showOffcanvas ? 'show' : ''}`} style={{ visibility: showOffcanvas ? 'visible' : 'hidden', width: '400px' }}>
                <div className="offcanvas-header border-bottom">
                    <h5 className="offcanvas-title">{selectedGroup?.tennhom}</h5>
                    <button type="button" className="btn-close text-reset" onClick={() => setShowOffcanvas(false)}></button>
                </div>
                <div className="offcanvas-body p-0">
                    <ul className="nav nav-tabs nav-tabs-block nav-justified" role="tablist">
                        <li className="nav-item">
                            <button className={`nav-link ${activeTab === 'tests' ? 'active' : ''}`} onClick={() => setActiveTab('tests')}>
                                <i className="fa fa-clipboard-list me-1"></i> Đề thi
                            </button>
                        </li>
                        <li className="nav-item">
                            <button className={`nav-link ${activeTab === 'friends' ? 'active' : ''}`} onClick={() => setActiveTab('friends')}>
                                <i className="far fa-user-circle me-1"></i> Bạn bè
                            </button>
                        </li>
                    </ul>
                    <div className="tab-content p-3">
                        {activeTab === 'tests' && (
                            <div className="tab-pane active">
                                <p className="text-center text-muted py-4">Chưa có đề thi nào trong nhóm này.</p>
                            </div>
                        )}
                        {activeTab === 'friends' && (
                            <div className="tab-pane active">
                                <div className="block block-rounded block-bordered mb-2">
                                    <div className="block-header block-header-default">
                                        <h3 className="block-title fs-sm">Thành viên ({members.length})</h3>
                                    </div>
                                    <div className="block-content p-0">
                                        <ul className="nav-items pb-0">
                                            {members.map(m => (
                                                <li key={m.id}>
                                                    <div className="d-flex py-2 px-3 align-items-center">
                                                        <div className="me-3">
                                                            <img className="img-avatar img-avatar32" src={m.avatar ? `/media/avatars/${m.avatar}` : '/media/avatars/avatar10.jpg'} alt="" />
                                                        </div>
                                                        <div className="flex-grow-1">
                                                            <div className="fw-semibold fs-sm">{m.hoten}</div>
                                                            <div className="fs-xs text-muted">{m.id === user.id ? 'Bạn' : m.email}</div>
                                                        </div>
                                                    </div>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            {showOffcanvas && <div className="offcanvas-backdrop fade show" onClick={() => setShowOffcanvas(false)}></div>}
        </MainLayout>
    );
}
