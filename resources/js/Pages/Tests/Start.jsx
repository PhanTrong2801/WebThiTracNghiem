import React from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, Link } from '@inertiajs/react';

export default function TestStart() {
    const { auth, test, ketqua, flash, now } = usePage().props;
    const user = auth.user;

    const hasSubmitted = ketqua?.diemthi !== null && ketqua?.diemthi !== undefined;

    return (
        <MainLayout user={user}>
            <Head title="Bắt đầu thi" />

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
                    <div className="block-header block-header-default">
                        <h3 className="block-title">Thông tin đề thi</h3>
                    </div>
                    <div className="block-content">
                        <div className="row g-3">
                            <div className="col-md-8">
                                <div className="fs-4 fw-semibold">{test.tende}</div>
                                <div className="text-muted">
                                    <span className="me-2"><code>{test.monthi}</code></span>
                                    <span>{test.monhoc?.tenmonhoc}</span>
                                </div>
                            </div>
                            <div className="col-md-4 text-md-end">
                                <div className="badge bg-info fs-6">{test.thoigianthi} phút</div>
                                <div className="text-muted small mt-1">Hiện tại: {now}</div>
                            </div>
                        </div>

                        <hr />

                        <div className="row g-3">
                            <div className="col-md-6">
                                <div className="text-muted small">Bắt đầu</div>
                                <div className="fw-semibold">{test.thoigianbatdau || '--'}</div>
                            </div>
                            <div className="col-md-6">
                                <div className="text-muted small">Kết thúc</div>
                                <div className="fw-semibold">{test.thoigianketthuc || '--'}</div>
                            </div>
                        </div>

                        <div className="mt-4 d-flex justify-content-between align-items-center">
                            <Link href="/client/test" className="btn btn-alt-secondary">
                                <i className="fa fa-arrow-left me-1"></i> Danh sách đề
                            </Link>

                            {!hasSubmitted ? (
                                <Link href={`/tests/${test.made}/take`} className="btn btn-hero btn-primary">
                                    <i className="fa fa-play me-1"></i> Bắt đầu làm bài
                                </Link>
                            ) : (
                                <div className="text-end">
                                    <div className="text-muted small">Điểm của bạn</div>
                                    <div className="fs-3 fw-bold text-success">{ketqua.diemthi}</div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}

