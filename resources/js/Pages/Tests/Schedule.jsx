import React from 'react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';
import { Head, usePage, Link, router } from '@inertiajs/react';

export default function TestSchedule() {
    const { auth, assignedExams, completedExams, filters, flash } = usePage().props;
    const user = auth.user;

    const [values, setValues] = React.useState({
        search: filters.search || '',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/tests/schedule', values, {
            preserveState: true,
            replace: true,
        });
    };

    const handleChange = (e) => {
        const key = e.target.id;
        const value = e.target.value;
        setValues(prev => ({ ...prev, [key]: value }));
    };

    const ExamTable = ({ title, paginatedData, emptyMessage, isCompleted }) => {
        const exams = paginatedData?.data || [];
        const links = paginatedData?.links || [];
        const total = paginatedData?.total || 0;

        return (
            <div className="block block-rounded h-100 mb-0 d-flex flex-column">
                <div className="block-header block-header-default">
                    <h3 className="block-title">{title}</h3>
                    <span className="badge bg-primary">{total}</span>
                </div>
                <div className="block-content flex-grow-1">
                    <div className="table-responsive">
                        <table className="table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th>Tên đề</th>
                                    <th className="d-none d-md-table-cell">Môn</th>
                                    <th className="text-center" style={{ width: 120 }}>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                {exams.length === 0 ? (
                                    <tr><td colSpan={3} className="text-center text-muted py-4 small">{emptyMessage}</td></tr>
                                ) : exams.map(t => (
                                    <tr key={t.made}>
                                        <td>
                                            <div className="fw-semibold small text-uppercase">{t.tende}</div>
                                            <div className="text-muted small">{t.thoigianthi} phút</div>
                                        </td>
                                        <td className="d-none d-md-table-cell small">
                                            <span className="fw-semibold">{t.monhoc?.tenmonhoc || t.monthi}</span>
                                        </td>
                                        <td className="text-center">
                                            {isCompleted ? (
                                                <Link className="btn btn-sm btn-success w-100" href={`/tests/${t.made}/start`}>
                                                    Kết quả
                                                </Link>
                                            ) : (
                                                <Link className="btn btn-sm btn-primary w-100" href={`/tests/${t.made}/start`}>
                                                    Vào thi
                                                </Link>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
                {links.length > 3 && (
                    <div className="block-content block-content-full block-content-sm bg-body-light mt-auto">
                        <Pagination links={links} />
                    </div>
                )}
            </div>
        );
    };

    return (
        <MainLayout user={user}>
            <Head title="Lịch thi & Kết quả" />

            <div className="content">
                <div className="block block-rounded">
                    <div className="block-content block-content-full">
                        <form onSubmit={handleSearch} className="row g-3">
                            <div className="col-md-10">
                                <div className="input-group">
                                    <span className="input-group-text bg-white border-0 shadow-sm">
                                        <i className="fa fa-search text-primary"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        className="form-control border-0 shadow-sm ps-0" 
                                        id="search" 
                                        placeholder="Tìm kiếm theo tên đề thi hoặc tên môn học..." 
                                        value={values.search}
                                        onChange={handleChange}
                                    />
                                </div>
                            </div>
                            <div className="col-md-2">
                                <button type="submit" className="btn btn-primary w-100 shadow-sm">
                                    Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

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

                <div className="row g-4">
                    <div className="col-xl-6">
                        <ExamTable 
                            title="Đề thi đã được giao" 
                            paginatedData={assignedExams} 
                            emptyMessage="Chưa có đề thi nào phù hợp" 
                            isCompleted={false}
                        />
                    </div>
                    <div className="col-xl-6">
                        <ExamTable 
                            title="Đề thi đã làm bài" 
                            paginatedData={completedExams} 
                            emptyMessage="Chưa có kết quả nào phù hợp" 
                            isCompleted={true}
                        />
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}

