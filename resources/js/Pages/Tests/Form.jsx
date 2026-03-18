import React, { useEffect, useMemo, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, router, Link } from '@inertiajs/react';
import axios from 'axios';

export default function TestForm() {
    const { auth, danhSachMonHoc, danhSachNhom, selectedNhomIds, selectedChuongIds, test, action, errors = {} } = usePage().props;
    const user = auth.user;

    const isEdit = action === 'update' && test;

    const [form, setForm] = useState({
        monthi: test?.monthi || '',
        tende: test?.tende || '',
        thoigianthi: test?.thoigianthi || 45,
        thoigianbatdau: test?.thoigianbatdau || '',
        thoigianketthuc: test?.thoigianketthuc || '',
        hienthibailam: !!test?.hienthibailam,
        xemdiemthi: !!test?.xemdiemthi,
        xemdapan: !!test?.xemdapan,
        troncauhoi: !!test?.troncauhoi,
        trondapan: !!test?.trondapan,
        nopbaichuyentab: !!test?.nopbaichuyentab,
        loaide: test?.loaide ?? 0,
        socaude: test?.socaude ?? 0,
        socautb: test?.socautb ?? 0,
        socaukho: test?.socaukho ?? 0,
        trangthai: test?.trangthai ?? 1,
    });

    const [chapters, setChapters] = useState([]);
    const [nhomIds, setNhomIds] = useState((selectedNhomIds || []).map(Number));
    const [chuongIds, setChuongIds] = useState((selectedChuongIds || []).map(Number));

    useEffect(() => {
        // Ensure default subject selection for create
        if (!isEdit && !form.monthi && (danhSachMonHoc?.length || 0) > 0) {
            setForm(prev => ({ ...prev, monthi: danhSachMonHoc[0].mamonhoc }));
        }
    }, []);

    // Load chapters by subject (for auto tests / optional filter)
    useEffect(() => {
        if (!form.monthi) { setChapters([]); setChuongIds([]); return; }
        axios.get(`/chapters/subject/${form.monthi}`)
            .then(res => setChapters(res.data || []))
            .catch(() => setChapters([]));
    }, [form.monthi]);

    const setField = (name, value) => setForm(prev => ({ ...prev, [name]: value }));

    const filteredNhom = useMemo(() => {
        const all = danhSachNhom || [];
        let result = all.filter(n => Number(n.hienthi) === 1); // chỉ hiện nhóm đang hoạt động
        if (form.monthi) {
            result = result.filter(n => String(n.mamonhoc) === String(form.monthi));
        }
        return result;
    }, [danhSachNhom, form.monthi]);

    const toggleId = (setter, list, id) => {
        const num = Number(id);
        setter(prev => prev.includes(num) ? prev.filter(x => x !== num) : [...prev, num]);
    };

    const onSubmit = (e) => {
        e.preventDefault();
        const payload = {
            ...form,
            nhom_ids: nhomIds,
            chuong_ids: chuongIds,
            // checkbox values
            hienthibailam: form.hienthibailam ? 1 : 0,
            xemdiemthi: form.xemdiemthi ? 1 : 0,
            xemdapan: form.xemdapan ? 1 : 0,
            troncauhoi: form.troncauhoi ? 1 : 0,
            trondapan: form.trondapan ? 1 : 0,
            nopbaichuyentab: form.nopbaichuyentab ? 1 : 0,
            trangthai: form.trangthai ? 1 : 0,
        };

        if (isEdit) {
            router.put(`/tests/${test.made}`, payload);
        } else {
            router.post('/tests', payload);
        }
    };

    return (
        <MainLayout user={user}>
            <Head title={isEdit ? 'Cập nhật đề kiểm tra' : 'Tạo đề kiểm tra'} />

            <div className="content">
                <div className="block block-rounded">
                    <div className="block-header block-header-default d-flex justify-content-between">
                        <h3 className="block-title">{isEdit ? 'Cập nhật đề kiểm tra' : 'Tạo đề kiểm tra'}</h3>
                        <div className="block-options">
                            <Link className="btn btn-sm btn-alt-secondary" href="/tests">
                                <i className="fa fa-arrow-left me-1"></i> Quay lại
                            </Link>
                        </div>
                    </div>

                    <div className="block-content">
                        <form onSubmit={onSubmit}>
                            <div className="row g-4 mb-4">
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Môn thi <span className="text-danger">*</span></label>
                                    <select
                                        className={`form-select ${errors.monthi ? 'is-invalid' : ''}`}
                                        value={form.monthi}
                                        onChange={(e) => { setField('monthi', e.target.value); setNhomIds([]); setChuongIds([]); }}
                                    >
                                        <option value="">-- Chọn môn --</option>
                                        {danhSachMonHoc?.map(mh => (
                                            <option key={mh.mamonhoc} value={mh.mamonhoc}>
                                                {mh.mamonhoc} - {mh.tenmonhoc}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.monthi && <div className="invalid-feedback">{errors.monthi}</div>}
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label fw-semibold">Tên đề <span className="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.tende ? 'is-invalid' : ''}`}
                                        value={form.tende}
                                        onChange={(e) => setField('tende', e.target.value)}
                                        placeholder="VD: Kiểm tra giữa kỳ..."
                                    />
                                    {errors.tende && <div className="invalid-feedback">{errors.tende}</div>}
                                </div>
                            </div>

                            <div className="row g-4 mb-4">
                                <div className="col-md-3">
                                    <label className="form-label fw-semibold">Thời gian thi (phút) <span className="text-danger">*</span></label>
                                    <input
                                        type="number"
                                        className={`form-control ${errors.thoigianthi ? 'is-invalid' : ''}`}
                                        value={form.thoigianthi}
                                        min={1}
                                        max={600}
                                        onChange={(e) => setField('thoigianthi', e.target.value)}
                                    />
                                    {errors.thoigianthi && <div className="invalid-feedback">{errors.thoigianthi}</div>}
                                </div>
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">Thời gian bắt đầu</label>
                                    <input
                                        type="datetime-local"
                                        className={`form-control ${errors.thoigianbatdau ? 'is-invalid' : ''}`}
                                        value={form.thoigianbatdau ? form.thoigianbatdau.replace(' ', 'T') : ''}
                                        onChange={(e) => setField('thoigianbatdau', e.target.value)}
                                    />
                                    {errors.thoigianbatdau && <div className="invalid-feedback">{errors.thoigianbatdau}</div>}
                                </div>
                                <div className="col-md-5">
                                    <label className="form-label fw-semibold">Thời gian kết thúc</label>
                                    <input
                                        type="datetime-local"
                                        className={`form-control ${errors.thoigianketthuc ? 'is-invalid' : ''}`}
                                        value={form.thoigianketthuc ? form.thoigianketthuc.replace(' ', 'T') : ''}
                                        onChange={(e) => setField('thoigianketthuc', e.target.value)}
                                    />
                                    {errors.thoigianketthuc && <div className="invalid-feedback">{errors.thoigianketthuc}</div>}
                                </div>
                            </div>

                            <div className="row g-4 mb-4">
                                <div className="col-md-4">
                                    <label className="form-label fw-semibold">Loại đề</label>
                                    <select className="form-select" value={form.loaide} onChange={(e) => setField('loaide', e.target.value)}>
                                        <option value={0}>Thủ công (chọn câu hỏi)</option>
                                        <option value={1}>Tự động (theo chương/độ khó)</option>
                                    </select>
                                </div>
                                <div className="col-md-8">
                                    <label className="form-label fw-semibold">Số câu (chỉ dùng cho đề tự động)</label>
                                    <div className="row g-2">
                                        <div className="col-md-4">
                                            <div className="input-group">
                                                <span className="input-group-text bg-success text-white" title="Dễ">Cơ bản</span>
                                                <input type="number" className="form-control" placeholder="0" value={form.socaude}
                                                    onChange={(e) => setField('socaude', e.target.value)} min={0} />
                                            </div>
                                        </div>
                                        <div className="col-md-4">
                                            <div className="input-group">
                                                <span className="input-group-text bg-warning text-dark" title="Trung bình">Trung bình</span>
                                                <input type="number" className="form-control" placeholder="0" value={form.socautb}
                                                    onChange={(e) => setField('socautb', e.target.value)} min={0} />
                                            </div>
                                        </div>
                                        <div className="col-md-4">
                                            <div className="input-group">
                                                <span className="input-group-text bg-danger text-white" title="Khó">Khó</span>
                                                <input type="number" className="form-control" placeholder="0" value={form.socaukho}
                                                    onChange={(e) => setField('socaukho', e.target.value)} min={0} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Chọn chương (phục vụ đề tự động / phân loại nội dung đề) */}
                            <div className="row g-4 mb-4">
                                <div className="col-12">
                                    <label className="form-label fw-semibold">
                                        Chương {String(form.loaide) === '1' && <span className="text-danger">*</span>}
                                    </label>
                                    {errors.chuong_ids && <div className="text-danger small mb-2">{errors.chuong_ids}</div>}

                                    {chapters.length === 0 ? (
                                        <div className="text-muted small">Chưa có chương cho môn này (hoặc bạn không có quyền xem chương).</div>
                                    ) : (
                                        <div className="row g-2">
                                            {chapters.map(ch => (
                                                <div key={ch.machuong} className="col-md-4">
                                                    <label className="form-check">
                                                        <input
                                                            className="form-check-input"
                                                            type="checkbox"
                                                            checked={chuongIds.includes(Number(ch.machuong))}
                                                            onChange={() => toggleId(setChuongIds, chuongIds, ch.machuong)}
                                                            disabled={!form.monthi}
                                                        />
                                                        <span className="form-check-label">
                                                            <span className="badge bg-secondary me-2">C{ch.machuong}</span>
                                                            {ch.tenchuong}
                                                        </span>
                                                    </label>
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    {String(form.loaide) === '1' && (
                                        <div className="text-muted small mt-2">
                                            Đề tự động sẽ lấy ngẫu nhiên câu hỏi trong các chương đã chọn theo số lượng dễ/trung bình/khó.
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Giao đề cho nhóm học phần */}
                            <div className="row g-4 mb-4">
                                <div className="col-12">
                                    <label className="form-label fw-semibold">Giao cho nhóm học phần</label>
                                    <div className="text-muted small mb-2">
                                        Nếu không chọn nhóm, sinh viên sẽ không thấy đề trong mục “Đề thi”.
                                    </div>

                                    {filteredNhom.length === 0 ? (
                                        <div className="text-muted small">Chưa có nhóm học phần cho môn này. Hãy tạo nhóm trong “Nhóm học phần” trước.</div>
                                    ) : (
                                        <div className="row g-2">
                                            {filteredNhom.map(n => (
                                                <div key={n.manhom} className="col-md-4">
                                                    <label className="form-check">
                                                        <input
                                                            className="form-check-input"
                                                            type="checkbox"
                                                            checked={nhomIds.includes(Number(n.manhom))}
                                                            onChange={() => toggleId(setNhomIds, nhomIds, n.manhom)}
                                                        />
                                                        <span className="form-check-label">
                                                            <span className={`badge me-2 ${Number(n.hienthi) === 0 ? 'bg-secondary' : 'bg-primary'}`}>#{n.manhom}</span>
                                                            {n.tennhom}
                                                            <span className="text-muted ms-2 small">{n.namhoc}-{Number(n.namhoc) + 1} · HK {n.hocky}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="row g-3 mb-4">
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="troncauhoi" checked={form.troncauhoi} onChange={(e) => setField('troncauhoi', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="troncauhoi">Trộn câu hỏi</label>
                                    </div>
                                </div>
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="trondapan" checked={form.trondapan} onChange={(e) => setField('trondapan', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="trondapan">Trộn đáp án</label>
                                    </div>
                                </div>
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="nopbaichuyentab" checked={form.nopbaichuyentab} onChange={(e) => setField('nopbaichuyentab', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="nopbaichuyentab">Nộp bài khi chuyển tab</label>
                                    </div>
                                </div>
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="trangthai" checked={!!form.trangthai} onChange={(e) => setField('trangthai', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="trangthai">Kích hoạt đề</label>
                                    </div>
                                </div>
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="xemdiemthi" checked={form.xemdiemthi} onChange={(e) => setField('xemdiemthi', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="xemdiemthi">Cho xem điểm</label>
                                    </div>
                                </div>
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="xemdapan" checked={form.xemdapan} onChange={(e) => setField('xemdapan', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="xemdapan">Cho xem đáp án</label>
                                    </div>
                                </div>
                                <div className="col-md-3">
                                    <div className="form-check">
                                        <input className="form-check-input" type="checkbox" id="hienthibailam" checked={form.hienthibailam} onChange={(e) => setField('hienthibailam', e.target.checked)} />
                                        <label className="form-check-label" htmlFor="hienthibailam">Hiển thị bài làm</label>
                                    </div>
                                </div>
                            </div>

                            <div className="block-content block-content-full bg-body-light d-flex justify-content-end gap-2">
                                <Link href="/tests" className="btn btn-alt-secondary">Huỷ</Link>
                                <button type="submit" className="btn btn-primary">
                                    <i className="fa fa-save me-1"></i> {isEdit ? 'Cập nhật' : 'Tạo đề'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}

