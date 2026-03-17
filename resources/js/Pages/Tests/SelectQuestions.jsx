import React, { useEffect, useMemo, useRef, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, router, Link } from '@inertiajs/react';

export default function SelectQuestions() {
    const { auth, test, questions, selectedIds, filters, flash, chapters } = usePage().props;
    const user = auth.user;

    const { data: rows = [], links = [], total = 0, from = 0, to = 0 } = questions || {};
    const initial = (selectedIds || []).map(String);

    const [selected, setSelected] = useState(initial);
    const [search, setSearch] = useState(filters?.search || '');
    const [dokho, setDokho] = useState(filters?.dokho || '');
    const [machuong, setMaChuong] = useState(filters?.machuong || '');

    const isFirstRender = useRef(true);
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        const t = setTimeout(() => {
            router.get(`/tests/${test.made}/select`, { search, dokho, machuong }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(t);
    }, [search, dokho, machuong]);

    const selectedSet = useMemo(() => new Set(selected), [selected]);

    const toggle = (id) => {
        const sid = String(id);
        setSelected(prev => prev.includes(sid) ? prev.filter(x => x !== sid) : [...prev, sid]);
    };

    const save = () => {
        router.post(`/tests/${test.made}/select`, { selectedIds: selected.map(Number) });
    };

    return (
        <MainLayout user={user}>
            <Head title="Chọn câu hỏi" />

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
                        <div>
                            <h3 className="block-title">Chọn câu hỏi cho đề</h3>
                            <div className="text-muted small">
                                <span className="fw-semibold">{test.tende}</span> · <code>{test.monthi}</code> · {test.monhoc?.tenmonhoc}
                            </div>
                        </div>
                        <div className="block-options d-flex gap-2 align-items-center">
                            <span className="badge bg-primary">Đã chọn: {selected.length}</span>
                            <Link className="btn btn-sm btn-alt-secondary" href="/tests">
                                <i className="fa fa-arrow-left me-1"></i> Danh sách
                            </Link>
                            <button className="btn btn-sm btn-primary" onClick={save}>
                                <i className="fa fa-save me-1"></i> Lưu
                            </button>
                        </div>
                    </div>

                    <div className="block-content">
                        <div className="row g-2 mb-4">
                            <div className="col-md-3">
                                <select className="form-select form-select-alt" value={dokho} onChange={(e) => setDokho(e.target.value)}>
                                    <option value="">-- Độ khó --</option>
                                    <option value="1">Cơ bản</option>
                                    <option value="2">Trung bình</option>
                                    <option value="3">Nâng cao</option>
                                </select>
                            </div>
                            <div className="col-md-3">
                                <select className="form-select form-select-alt" value={machuong} onChange={(e) => setMaChuong(e.target.value)}>
                                    <option value="">-- Chương --</option>
                                    {(chapters || []).map(ch => (
                                        <option key={ch.machuong} value={ch.machuong}>{ch.tenchuong}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-6">
                                <div className="input-group">
                                    <input
                                        type="text"
                                        className="form-control form-control-alt"
                                        placeholder="Tìm nội dung câu hỏi..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                    />
                                    <span className="input-group-text bg-body border-0">
                                        <i className="fa fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th className="text-center" style={{ width: 70 }}>Chọn</th>
                                        <th className="text-center" style={{ width: 90 }}>ID</th>
                                        <th>Nội dung</th>
                                        <th className="text-center d-none d-xl-table-cell" style={{ width: 140 }}>Độ khó</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length === 0 ? (
                                        <tr><td colSpan={4} className="text-center text-muted py-4">Không có câu hỏi phù hợp</td></tr>
                                    ) : rows.map(q => (
                                        <tr key={q.macauhoi} className={selectedSet.has(String(q.macauhoi)) ? 'table-success' : ''}>
                                            <td className="text-center">
                                                <input
                                                    type="checkbox"
                                                    className="form-check-input"
                                                    checked={selectedSet.has(String(q.macauhoi))}
                                                    onChange={() => toggle(q.macauhoi)}
                                                />
                                            </td>
                                            <td className="text-center fw-semibold text-muted">#{q.macauhoi}</td>
                                            <td style={{ maxWidth: 700 }}>
                                                <div dangerouslySetInnerHTML={{ __html: q.noidung }} />
                                                {q.cautraloi?.length > 0 && (
                                                    <div className="mt-2 small text-muted">
                                                        {q.cautraloi.slice(0, 4).map((a, idx) => (
                                                            <span key={a.macautl || idx} className={`badge me-1 ${a.ladapan ? 'bg-success' : 'bg-secondary'}`}>
                                                                {String.fromCharCode(65 + idx)}{a.ladapan ? ' ✓' : ''}
                                                            </span>
                                                        ))}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="text-center d-none d-xl-table-cell">
                                                {q.dokho == 1 && <span className="badge bg-success">Cơ bản</span>}
                                                {q.dokho == 2 && <span className="badge bg-warning">Trung bình</span>}
                                                {q.dokho == 3 && <span className="badge bg-danger">Nâng cao</span>}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {links?.length > 3 && (
                        <div className="block-content block-content-full block-content-sm bg-body-light d-flex justify-content-between align-items-center">
                            <div className="text-muted small">{from}–{to} / {total}</div>
                            <Pagination links={links} />
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}

