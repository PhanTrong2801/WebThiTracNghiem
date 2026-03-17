import React, { useEffect, useMemo, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, router } from '@inertiajs/react';

export default function TakeTest() {
    const { auth, test, ketqua, questions, flash } = usePage().props;
    const user = auth.user;

    const [answers, setAnswers] = useState({});
    const [secondsLeft, setSecondsLeft] = useState((test?.thoigianthi || 0) * 60);
    const [tabSwitchCount, setTabSwitchCount] = useState(0);

    const totalQuestions = questions?.length || 0;

    const formatTime = (s) => {
        const mm = String(Math.floor(s / 60)).padStart(2, '0');
        const ss = String(s % 60).padStart(2, '0');
        return `${mm}:${ss}`;
    };

    const submit = () => {
        router.post(`/tests/${test.made}/submit`, { answers });
    };

    // Countdown
    useEffect(() => {
        const timer = setInterval(() => {
            setSecondsLeft(prev => {
                if (prev <= 1) {
                    clearInterval(timer);
                    submit();
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);
        return () => clearInterval(timer);
    }, []);

    // Auto submit on tab switch (if enabled)
    useEffect(() => {
        if (!test?.nopbaichuyentab) return;

        const onVisibility = () => {
            if (document.hidden) {
                setTabSwitchCount(prev => {
                    const next = prev + 1;
                    // nộp ngay lần đầu tiên chuyển tab (giống yêu cầu cũ)
                    submit();
                    return next;
                });
            }
        };
        document.addEventListener('visibilitychange', onVisibility);
        return () => document.removeEventListener('visibilitychange', onVisibility);
    }, [test?.nopbaichuyentab]);

    const answeredCount = useMemo(() => Object.keys(answers || {}).length, [answers]);

    return (
        <MainLayout user={user}>
            <Head title="Làm bài thi" />

            <div className="content">
                {flash?.error && (
                    <div className="alert alert-danger alert-dismissible" role="alert">
                        {flash.error}<button type="button" className="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                )}

                <div className="block block-rounded">
                    <div className="block-header block-header-default d-flex justify-content-between align-items-center">
                        <div>
                            <h3 className="block-title">{test.tende}</h3>
                            <div className="text-muted small">
                                <code>{test.monthi}</code> · {totalQuestions} câu · Đã chọn: {answeredCount}
                                {test?.nopbaichuyentab ? ` · Chuyển tab: ${tabSwitchCount}` : ''}
                            </div>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                            <span className={`badge fs-6 ${secondsLeft <= 60 ? 'bg-danger' : 'bg-info'}`}>
                                <i className="fa fa-clock me-1"></i> {formatTime(secondsLeft)}
                            </span>
                            <button className="btn btn-sm btn-danger" onClick={() => {
                                if (confirm('Nộp bài ngay bây giờ?')) submit();
                            }}>
                                <i className="fa fa-paper-plane me-1"></i> Nộp bài
                            </button>
                        </div>
                    </div>

                    <div className="block-content">
                        {questions?.map((q, idx) => (
                            <div key={q.macauhoi} className="block block-rounded border mb-3">
                                <div className="block-header block-header-default">
                                    <div className="fw-semibold">Câu {idx + 1} <span className="text-muted">#{q.macauhoi}</span></div>
                                </div>
                                <div className="block-content">
                                    <div className="mb-3" dangerouslySetInnerHTML={{ __html: q.noidung }} />

                                    <div className="list-group">
                                        {q.cautraloi?.map((a, aidx) => {
                                            const key = `${q.macauhoi}_${a.macautl}`;
                                            const isChecked = String(answers[q.macauhoi] || '') === String(a.macautl);
                                            return (
                                                <label key={key} className={`list-group-item d-flex align-items-start gap-2 ${isChecked ? 'active' : ''}`} style={{ cursor: 'pointer' }}>
                                                    <input
                                                        className="form-check-input mt-1"
                                                        type="radio"
                                                        name={`q_${q.macauhoi}`}
                                                        checked={isChecked}
                                                        onChange={() => setAnswers(prev => ({ ...prev, [q.macauhoi]: a.macautl }))}
                                                    />
                                                    <div className="flex-grow-1">
                                                        <span className="badge bg-secondary me-2">{String.fromCharCode(65 + aidx)}</span>
                                                        <span>{a.noidungtl}</span>
                                                    </div>
                                                </label>
                                            );
                                        })}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="block-content block-content-full bg-body-light d-flex justify-content-end">
                        <button className="btn btn-primary" onClick={submit}>
                            <i className="fa fa-paper-plane me-1"></i> Nộp bài
                        </button>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}

