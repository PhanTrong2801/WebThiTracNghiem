import React from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, Link } from '@inertiajs/react';

export default function TestSchedule() {
    const { auth, danhSachDeThi, flash } = usePage().props;
    const user = auth.user;

    const { data: rows = [], links = [], total = 0, from = 0, to = 0 } = danhSachDeThi || {};

    return (
        <MainLayout user={user}>
            <Head title="Lịch thi" />

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
                        <h3 className="block-title">Đề thi đã được giao</h3>
                        <span className="badge bg-primary">{from}–{to} / {total}</span>
                    </div>

                    <div className="block-content">
                        <div className="table-responsive">
                            <table className="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th className="text-center" style={{ width: 90 }}>Mã</th>
                                        <th>Tên đề</th>
                                        <th className="d-none d-md-table-cell" style={{ width: 240 }}>Môn</th>
                                        <th className="text-center d-none d-xl-table-cell" style={{ width: 220 }}>Thời gian</th>
                                        <th className="text-center" style={{ width: 140 }}>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.length === 0 ? (
                                        <tr><td colSpan={5} className="text-center text-muted py-4">Chưa có đề thi nào được giao</td></tr>
                                    ) : rows.map(t => (
                                        <tr key={t.made}>
                                            <td className="text-center fw-semibold text-muted">#{t.made}</td>
                                            <td>
                                                <div className="fw-semibold">{t.tende}</div>
                                                <div className="text-muted small">{t.thoigianthi} phút</div>
                                            </td>
                                            <td className="d-none d-md-table-cell">
                                                <span className="fw-semibold">{t.monhoc?.tenmonhoc || t.monthi}</span>
                                                <div className="text-muted small"><code>{t.monthi}</code></div>
                                            </td>
                                            <td className="text-center d-none d-xl-table-cell">
                                                <div className="small">
                                                    <div>BĐ: {t.thoigianbatdau || '--'}</div>
                                                    <div>KT: {t.thoigianketthuc || '--'}</div>
                                                </div>
                                            </td>
                                            <td className="text-center">
                                                <Link className="btn btn-sm btn-primary" href={`/tests/${t.made}/start`}>
                                                    Vào thi
                                                </Link>
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

