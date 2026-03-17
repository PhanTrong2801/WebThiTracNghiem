import React from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, Link } from '@inertiajs/react';

export default function TestResults() {
    const { auth, test, danhSachKetQua, flash } = usePage().props;
    const user = auth.user;

    const { data: rows = [], links = [], total = 0, from = 0, to = 0 } = danhSachKetQua || {};

    return (
        <MainLayout user={user}>
            <Head title="Kết quả thi" />

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
                    <div className="block-header block-header-default d-flex justify-content-between align-items-center">
                        <div>
                            <h3 className="block-title">Kết quả</h3>
                            <div className="text-muted small">
                                <span className="fw-semibold">{test.tende}</span> · <code>{test.monthi}</code> · {test.monhoc?.tenmonhoc}
                            </div>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                            <span className="badge bg-primary">{from}–{to} / {total}</span>
                            <Link className="btn btn-sm btn-alt-secondary" href="/tests">
                                <i className="fa fa-arrow-left me-1"></i> Danh sách đề
                            </Link>
                        </div>
                    </div>

                    <div className="block-content">
                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th className="text-center" style={{ width: 90 }}>Mã KQ</th>
                                        <th>Sinh viên</th>
                                        <th className="text-center" style={{ width: 140 }}>Điểm</th>
                                        <th className="text-center d-none d-md-table-cell" style={{ width: 220 }}>Vào thi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length === 0 ? (
                                        <tr><td colSpan={4} className="text-center text-muted py-4">Chưa có ai làm bài</td></tr>
                                    ) : rows.map(r => (
                                        <tr key={r.makq}>
                                            <td className="text-center fw-semibold text-muted">#{r.makq}</td>
                                            <td>
                                                <div className="fw-semibold">{r.user?.hoten || r.user?.name || r.manguoidung}</div>
                                                <div className="text-muted small">ID: {r.manguoidung}</div>
                                            </td>
                                            <td className="text-center">
                                                {r.diemthi === null || r.diemthi === undefined ? (
                                                    <span className="badge bg-secondary">Chưa nộp</span>
                                                ) : (
                                                    <span className="badge bg-success fs-6">{r.diemthi}</span>
                                                )}
                                            </td>
                                            <td className="text-center d-none d-md-table-cell">
                                                <span className="text-muted small">{r.thoigianvaothi || '--'}</span>
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

