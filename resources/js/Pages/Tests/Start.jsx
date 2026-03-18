import React, { useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, Link } from '@inertiajs/react';
import axios from 'axios';

export default function TestStart() {
    const { auth, test, ketqua, flash, now, error } = usePage().props;
    const user = auth.user;

    const hasSubmitted = ketqua?.diemthi !== null && ketqua?.diemthi !== undefined;
    // Cho phép xem chi tiết nếu đã nộp bài (hasSubmitted). Kiểm tra backend để áp dụng giới hạn hienthibailam.
    const canViewDetail = hasSubmitted && ketqua?.makq;

    const [showModal, setShowModal] = useState(false);
    const [detailLoading, setDetailLoading] = useState(false);
    const [detail, setDetail] = useState(null);

    const openDetail = async () => {
        if (!ketqua?.makq) return;
        setShowModal(true);
        setDetailLoading(true);
        setDetail(null);
        try {
            const res = await axios.get(`/tests/result/${ketqua.makq}/detail`);
            setDetail(res.data);
        } catch (err) {
            const msg = err.response?.data?.message || err.response?.data?.error || 'Không thể xem chi tiết bài thi';
            setDetail({ error: msg });
        } finally {
            setDetailLoading(false);
        }
    };

    const closeModal = () => {
        setShowModal(false);
        setDetail(null);
        setDetailLoading(false);
    };

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
                {error && (
                    <div className="alert alert-warning alert-dismissible" role="alert">
                        <div className="d-flex align-items-center gap-2">
                            <i className="fa fa-exclamation-triangle fs-5"></i>
                            <div>
                                <div className="fw-semibold">{error}</div>
                                <div className="small mt-1">Vui lòng liên hệ giáo viên để được hỗ trợ.</div>
                            </div>
                        </div>
                        <button type="button" className="btn-close" data-bs-dismiss="alert"></button>
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

                            {!hasSubmitted && !error ? (
                                <Link href={`/tests/${test.made}/take`} className="btn btn-hero btn-primary">
                                    <i className="fa fa-play me-1"></i> Bắt đầu làm bài
                                </Link>
                            ) : hasSubmitted ? (
                                <div className="text-end">
                                    <div className="text-muted small">Điểm của bạn</div>
                                    <div className="fs-3 fw-bold text-success">{ketqua.diemthi}</div>
                                    {canViewDetail && (
                                        <button type="button" className="btn btn-sm btn-alt-primary mt-2" onClick={openDetail}>
                                            <i className="fa fa-eye me-1"></i> Xem chi tiết bài thi
                                        </button>
                                    )}
                                </div>
                            ) : null}
                        </div>
                    </div>
                </div>
            </div>

            {showModal && (
                <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-scrollable modal-xl">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Chi tiết bài làm</h5>
                                <button type="button" className="btn-close" onClick={closeModal}></button>
                            </div>
                            <div className="modal-body">
                                {detailLoading || !detail ? (
                                    <div className="text-muted py-4 text-center">Đang tải...</div>
                                ) : detail.error ? (
                                    <div className="alert alert-danger">{detail.error}</div>
                                ) : (
                                    <div>
                                        <div className="mb-3 d-flex gap-3">
                                            {detail.xemdapan === 1 && (
                                                <span className="badge bg-success"><i className="fa fa-check me-1"></i> Được xem đáp án</span>
                                            )}
                                            {detail.hienthibailam === 1 && (
                                                <span className="badge bg-primary"><i className="fa fa-eye me-1"></i> Được xem bài làm</span>
                                            )}
                                        </div>
                                        {detail.questions.length === 0 ? (
                                            <div className="text-muted text-center py-4">Chưa có câu hỏi nào</div>
                                        ) : detail.questions.map((q, idx) => {
                                            const correctIds = new Set((q.correct_ids || []).map(String));
                                            const chosen = q.dapanchon ? String(q.dapanchon) : '';
                                            const isCorrect = chosen && correctIds.size > 0 ? correctIds.has(chosen) : null;

                                            // Hiển thị badge Đúng/Sai khi được xem đáp án
                                            const showResult = detail.xemdapan === 1 && isCorrect !== null;

                                            return (
                                                <div key={q.macauhoi || idx} className="block block-rounded border mb-3">
                                                    <div className="block-header block-header-default d-flex justify-content-between">
                                                        <div className="fw-semibold">Câu {idx + 1}</div>
                                                        {showResult ? (
                                                            isCorrect ? <span className="badge bg-success">Đúng</span> : <span className="badge bg-danger">Sai</span>
                                                        ) : (
                                                            <span className="badge bg-secondary">{q.dokho || '?'}</span>
                                                        )}
                                                    </div>
                                                    <div className="block-content">
                                                        <div className="mb-3" dangerouslySetInnerHTML={{ __html: q.noidung }} />
                                                        <div className="list-group">
                                                            {(q.cautraloi || []).map((a, aidx) => {
                                                                const isChosen = !!a.is_chosen;
                                                                const isAnsCorrect = a.ladapan === true;

                                                                // Chỉ hiển thị màu khi được xem bài làm
                                                                let cls = 'list-group-item';
                                                                if (detail.hienthibailam === 1) {
                                                                    if (isChosen && isAnsCorrect) cls = 'list-group-item list-group-item-success';
                                                                    else if (isChosen && !isAnsCorrect) cls = 'list-group-item list-group-item-danger';
                                                                    else if (isAnsCorrect && detail.xemdapan === 1) cls = 'list-group-item list-group-item-success';
                                                                }

                                                                return (
                                                                    <div key={a.macautl || aidx} className={cls}>
                                                                        <span className="badge bg-secondary me-2">{String.fromCharCode(65 + aidx)}</span>
                                                                        <span>{a.noidungtl}</span>
                                                                        {isChosen && detail.hienthibailam === 1 && <span className="ms-2 badge bg-primary">Bạn chọn</span>}
                                                                        {isAnsCorrect && detail.xemdapan === 1 && <span className="ms-2 badge bg-success">Đáp án đúng</span>}
                                                                    </div>
                                                                );
                                                            })}
                                                        </div>
                                                        {detail.xemdapan === 1 && correctIds.size > 0 && isCorrect === false && (
                                                            <div className="mt-2 text-muted small">
                                                                Đáp án đúng: {Array.from(correctIds).join(', ')}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-alt-secondary" onClick={closeModal}>Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}

