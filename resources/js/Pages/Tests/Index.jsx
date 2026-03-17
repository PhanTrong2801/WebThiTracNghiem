import React, { useEffect, useRef, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, router, Link } from '@inertiajs/react';

export default function TestsIndex() {
    const { auth, danhSachDeThi, flash, filters } = usePage().props;
    const user = auth.user;

    const { data: rows = [], links = [], total = 0, from = 0, to = 0 } = danhSachDeThi || {};

    const [search, setSearch] = useState(filters?.search || '');

    const isFirstRender = useRef(true);
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }
        const t = setTimeout(() => {
            router.get('/tests', { search }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(t);
    }, [search]);

    return (
        <MainLayout user={user}>
            <Head title="Đề kiểm tra" />

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
                        <h3 className="block-title">Danh sách đề thi</h3>
                        <div className="block-options">
                            <span className="badge bg-primary me-3">{from}–{to} / {total}</span>
                            <Link className="btn btn-hero btn-primary" href="/tests/create">
                                <i className="fa fa-plus me-1"></i> Tạo đề
                            </Link>
                        </div>
                    </div>

                    <div className="block-content">
                        <div className="mb-4">
                            <div className="input-group">
                                <input
                                    type="text"
                                    className="form-control form-control-alt"
                                    placeholder="Tìm theo tên đề..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                <span className="input-group-text bg-body border-0">
                                    <i className="fa fa-search"></i>
                                </span>
                            </div>
                        </div>

                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th className="text-center" style={{ width: 90 }}>Mã</th>
                                        <th>Tên đề</th>
                                        <th className="d-none d-md-table-cell" style={{ width: 240 }}>Môn</th>
                                        <th className="text-center d-none d-xl-table-cell" style={{ width: 140 }}>Thời gian</th>
                                        <th className="text-center" style={{ width: 200 }}>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length === 0 ? (
                                        <tr>
                                            <td colSpan={5} className="text-center text-muted py-4">Chưa có đề nào</td>
                                        </tr>
                                    ) : rows.map(t => (
                                        <tr key={t.made}>
                                            <td className="text-center fw-semibold text-muted">#{t.made}</td>
                                            <td>
                                                <div className="fw-semibold">{t.tende}</div>
                                                <div className="text-muted small">
                                                    {t.thoigianbatdau ? `BĐ: ${t.thoigianbatdau}` : 'Chưa đặt thời gian'}{t.thoigianketthuc ? ` · KT: ${t.thoigianketthuc}` : ''}
                                                </div>
                                            </td>
                                            <td className="d-none d-md-table-cell">
                                                <span className="fw-semibold">{t.monhoc?.tenmonhoc || t.monthi}</span>
                                                <div className="text-muted small"><code>{t.monthi}</code></div>
                                            </td>
                                            <td className="text-center d-none d-xl-table-cell">
                                                <span className="badge bg-info">{t.thoigianthi} phút</span>
                                            </td>
                                            <td className="text-center">
                                                <div className="btn-group">
                                                    <Link className="btn btn-sm btn-alt-info" href={`/tests/${t.made}/edit`} title="Cập nhật">
                                                        <i className="fa fa-pencil-alt"></i>
                                                    </Link>
                                                    <Link className="btn btn-sm btn-alt-primary" href={`/tests/${t.made}/select`} title="Chọn câu hỏi">
                                                        <i className="fa fa-list-check"></i>
                                                    </Link>
                                                    <Link className="btn btn-sm btn-alt-success" href={`/tests/${t.made}/results`} title="Kết quả">
                                                        <i className="fa fa-chart-column"></i>
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {links?.length > 3 && (
                        <div className="block-content block-content-full block-content-sm bg-body-light">
                            <Pagination links={links} />
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}

