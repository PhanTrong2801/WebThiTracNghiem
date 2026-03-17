import React, { useEffect, useMemo, useRef, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage } from '@inertiajs/react';
import axios from 'axios';

function loadChartJsOnce() {
    return new Promise((resolve) => {
        if (window.Chart) return resolve(true);
        const existing = document.querySelector('script[data-chartjs="1"]');
        if (existing) {
            existing.addEventListener('load', () => resolve(true));
            return;
        }
        const s = document.createElement('script');
        s.src = '/js/plugins/chart.js/chart.js';
        s.async = true;
        s.dataset.chartjs = '1';
        s.onload = () => resolve(true);
        document.body.appendChild(s);
    });
}

export default function TestDetail() {
    const { auth, test, groups, defaultGroup } = usePage().props;
    const user = auth.user;

    const [manhom, setManhom] = useState(defaultGroup || 0);
    const [stats, setStats] = useState(null);
    const [scores, setScores] = useState([]);
    const [loadingStats, setLoadingStats] = useState(false);
    const [loadingScores, setLoadingScores] = useState(false);

    const [activeTab, setActiveTab] = useState('list'); // list | stats

    // View result detail modal
    const [showModal, setShowModal] = useState(false);
    const [detailLoading, setDetailLoading] = useState(false);
    const [detail, setDetail] = useState(null);

    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    const groupOptions = useMemo(() => (groups || []), [groups]);

    const fetchStats = async () => {
        setLoadingStats(true);
        try {
            const res = await axios.get(`/tests/${test.made}/statistical`, { params: { manhom } });
            setStats(res.data);
        } finally {
            setLoadingStats(false);
        }
    };

    const fetchScores = async () => {
        setLoadingScores(true);
        try {
            const res = await axios.get(`/tests/${test.made}/scores`, { params: { manhom } });
            setScores(res.data || []);
        } finally {
            setLoadingScores(false);
        }
    };

    useEffect(() => {
        if (!manhom) {
            setStats(null);
            setScores([]);
            return;
        }
        fetchStats();
        fetchScores();
    }, [manhom]);

    // Draw chart when stats changes (or when tab becomes visible)
    useEffect(() => {
        if (activeTab !== 'stats') return;
        if (!stats?.thong_ke_diem) return;

        // wait a tick so <canvas> is mounted after tab switch
        const raf = requestAnimationFrame(() => {
            if (!chartRef.current) return;

            (async () => {
                await loadChartJsOnce();
                if (!window.Chart) return;

                const labels = ['0-1', '1-2', '2-3', '3-4', '4-5', '5-6', '6-7', '7-8', '8-9', '9-10'];
                const data = stats.thong_ke_diem;

                if (chartInstance.current) {
                    chartInstance.current.destroy();
                    chartInstance.current = null;
                }

                chartInstance.current = new window.Chart(chartRef.current, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Số lượng',
                            data,
                            backgroundColor: 'rgba(66, 165, 245, 0.6)',
                            borderColor: 'rgba(66, 165, 245, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            })();
        });

        return () => cancelAnimationFrame(raf);
    }, [stats, activeTab]);

    const openResultDetail = async (makq) => {
        setShowModal(true);
        setDetailLoading(true);
        setDetail(null);
        try {
            const res = await axios.get(`/tests/result/${makq}/detail`);
            setDetail(res.data);
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
            <Head title="Chi tiết đề thi" />

            <div className="content">
                <div className="block block-rounded">
                    <div className="block-header block-header-default d-flex justify-content-between align-items-center">
                        <div>
                            <h3 className="block-title">Chi tiết đề thi</h3>
                            <div className="text-muted small">
                                <span className="fw-semibold">{test.tende}</span> · <code>{test.monthi}</code> · {test.monhoc?.tenmonhoc}
                            </div>
                        </div>
                        <div className="d-flex gap-2 align-items-center">
                            <select className="form-select form-select-alt" value={manhom} onChange={(e) => setManhom(Number(e.target.value))} style={{ minWidth: 260 }}>
                                {groupOptions.length === 0 ? (
                                    <option value={0}>Chưa giao nhóm</option>
                                ) : groupOptions.map(g => (
                                    <option key={g.manhom} value={g.manhom}>
                                        #{g.manhom} - {g.tennhom} ({g.namhoc}-{Number(g.namhoc) + 1} HK{g.hocky})
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="block-content">
                        <ul className="nav nav-tabs nav-tabs-alt">
                            <li className="nav-item">
                                <button className={`nav-link ${activeTab === 'list' ? 'active' : ''}`} onClick={() => setActiveTab('list')}>
                                    Danh sách
                                </button>
                            </li>
                            <li className="nav-item">
                                <button className={`nav-link ${activeTab === 'stats' ? 'active' : ''}`} onClick={() => setActiveTab('stats')}>
                                    Thống kê
                                </button>
                            </li>
                        </ul>

                        {activeTab === 'list' && (
                            <div className="pt-3">
                                {loadingScores ? (
                                    <div className="text-muted py-4 text-center">Đang tải...</div>
                                ) : (
                                    <div className="table-responsive">
                                        <table className="table table-vcenter table-hover">
                                            <thead>
                                                <tr>
                                                    <th style={{ width: 120 }}>Mã SV</th>
                                                    <th>Họ tên</th>
                                                    <th className="d-none d-md-table-cell">Email</th>
                                                    <th className="text-center" style={{ width: 120 }}>Điểm</th>
                                                    <th className="text-center" style={{ width: 120 }}>Xem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {scores.length === 0 ? (
                                                    <tr><td colSpan={5} className="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
                                                ) : scores.map(r => (
                                                    <tr key={r.manguoidung}>
                                                        <td><code>{r.manguoidung}</code></td>
                                                        <td className="fw-semibold">{r.hoten}</td>
                                                        <td className="d-none d-md-table-cell text-muted small">{r.email}</td>
                                                        <td className="text-center">
                                                            {r.diemthi === null || r.diemthi === undefined ? (
                                                                <span className="badge bg-secondary">--</span>
                                                            ) : (
                                                                <span className="badge bg-success">{r.diemthi}</span>
                                                            )}
                                                        </td>
                                                        <td className="text-center">
                                                            {r.makq ? (
                                                                <button className="btn btn-sm btn-alt-info" title="Xem bài làm" onClick={() => openResultDetail(r.makq)}>
                                                                    <i className="fa fa-eye"></i>
                                                                </button>
                                                            ) : (
                                                                <span className="text-muted small">--</span>
                                                            )}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}
                            </div>
                        )}

                        {activeTab === 'stats' && (
                            <div className="pt-3">
                                {loadingStats || !stats ? (
                                    <div className="text-muted py-4 text-center">Đang tải thống kê...</div>
                                ) : (
                                    <>
                                        <div className="row g-3 mb-3">
                                            <div className="col-md-3">
                                                <div className="block block-rounded mb-0">
                                                    <div className="block-content">
                                                        <div className="text-muted small">Đã nộp bài</div>
                                                        <div className="fs-3 fw-bold">{stats.da_nop_bai}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-md-3">
                                                <div className="block block-rounded mb-0">
                                                    <div className="block-content">
                                                        <div className="text-muted small">Chưa nộp bài</div>
                                                        <div className="fs-3 fw-bold">{stats.chua_nop_bai}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-md-3">
                                                <div className="block block-rounded mb-0">
                                                    <div className="block-content">
                                                        <div className="text-muted small">Không thi</div>
                                                        <div className="fs-3 fw-bold">{stats.khong_thi}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-md-3">
                                                <div className="block block-rounded mb-0">
                                                    <div className="block-content">
                                                        <div className="text-muted small">Điểm TB / Cao nhất</div>
                                                        <div className="fs-4 fw-bold">{stats.diem_trung_binh} / {stats.diem_cao_nhat}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="block block-rounded">
                                            <div className="block-header block-header-default">
                                                <h3 className="block-title">Phổ điểm</h3>
                                            </div>
                                            <div className="block-content" style={{ height: 320 }}>
                                                <canvas ref={chartRef}></canvas>
                                            </div>
                                        </div>
                                    </>
                                )}
                            </div>
                        )}
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
                                ) : (
                                    <div>
                                        {detail.questions.map((q, idx) => {
                                            const correctIds = new Set((q.correct_ids || []).map(String));
                                            const chosen = q.dapanchon ? String(q.dapanchon) : '';
                                            const isCorrect = chosen && correctIds.size > 0 ? correctIds.has(chosen) : null;
                                            return (
                                                <div key={q.macauhoi || idx} className="block block-rounded border mb-3">
                                                    <div className="block-header block-header-default d-flex justify-content-between">
                                                        <div className="fw-semibold">Câu {idx + 1}</div>
                                                        {isCorrect === null ? null : (
                                                            isCorrect ? <span className="badge bg-success">Đúng</span> : <span className="badge bg-danger">Sai</span>
                                                        )}
                                                    </div>
                                                    <div className="block-content">
                                                        <div className="mb-3" dangerouslySetInnerHTML={{ __html: q.noidung }} />
                                                        <div className="list-group">
                                                            {(q.cautraloi || []).map((a, aidx) => {
                                                                const isChosen = !!a.is_chosen;
                                                                const isAnsCorrect = a.ladapan === true;
                                                                const cls = isChosen
                                                                    ? (isAnsCorrect ? 'list-group-item list-group-item-success' : 'list-group-item list-group-item-danger')
                                                                    : 'list-group-item';
                                                                return (
                                                                    <div key={a.macautl || aidx} className={cls}>
                                                                        <span className="badge bg-secondary me-2">{String.fromCharCode(65 + aidx)}</span>
                                                                        <span>{a.noidungtl}</span>
                                                                        {isChosen && <span className="ms-2 badge bg-primary">Bạn chọn</span>}
                                                                        {isAnsCorrect && <span className="ms-2 badge bg-success">Đáp án đúng</span>}
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

