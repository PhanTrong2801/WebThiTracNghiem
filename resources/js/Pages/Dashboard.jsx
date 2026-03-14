import React, { useEffect, useState } from 'react';
import MainLayout from '@/Components/MainLayouts';
import { Head, usePage, Link } from '@inertiajs/react';
import axios from 'axios';

export default function Dashboard() {
    const { auth, stats, roleName, roleId } = usePage().props;
    const user = auth.user;
    const [email, setEmail] = useState('');
    const [showOnboarding, setShowOnboarding] = useState(false);

    useEffect(() => {
        // Init slick slider nếu sử dụng template jQuery
        if (window.jQuery && window.jQuery('.js-slider').length) {
            window.jQuery('.js-slider').slick();
        }

        // Kiểm tra xem user đã có email chưa (Mô phỏng checkEmail trong controller cũ)
        if (!user.email || user.email.trim() === '') {
            setShowOnboarding(true);
            // Mở modal thông qua bootstrap/jQuery nếu dùng template
            setTimeout(() => {
                if (window.jQuery) {
                    window.jQuery('#modal-onboarding').modal('show');
                }
            }, 500);
        }
    }, [user.email]);

    const handleUpdateEmail = () => {
        if (!email) return;
        
        axios.post('/updateEmail', { email })
            .then(res => {
                if(window.jQuery) {
                    window.jQuery('#modal-onboarding').modal('hide');
                }
                setShowOnboarding(false);
                // Có thể reload page hoặc Inertia reload ở đây
            })
            .catch(err => {
                console.error("Lỗi cập nhật email", err);
            });
    };

    // Card Stats Component dùng chung
    const StatCard = ({ title, value, icon, link, colorClass }) => (
        <div className="col-sm-6 col-xl-3">
            <Link className="block block-rounded block-link-pop text-center" href={link || '#'}>
                <div className="block-content block-content-full">
                    <div className={`item item-circle bg-${colorClass}-light mx-auto my-3`}>
                        <i className={`fa ${icon} text-${colorClass}`}></i>
                    </div>
                    <div className="fs-1 fw-bold">{value}</div>
                    <div className="text-muted mb-3">{title}</div>
                </div>
            </Link>
        </div>
    );

    return (
        <MainLayout user={user}>
            <Head title="Trang tổng quan" />

            {/* Hero Section */}
            <div className="bg-body-light">
                <div className="content content-full">
                    <div className="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2 text-center text-sm-start">
                        <div className="flex-grow-1">
                            <h1 className="h3 fw-bold mb-2">
                                Chào mừng, {user.hoten}!
                            </h1>
                            <h2 className="fs-base lh-base fw-medium text-muted mb-0">
                                Vai trò hiện tại của bạn là <span className="fw-semibold text-primary">{roleName}</span>.
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            {/* Nội dung Dashboard tuỳ theo chức vụ (Dựa trên Quyền/Chức năng chi tiết) */}
            <div className="content">
                {/* ---------- ROLE 1: ADMIN (Có quyền quản lý người dùng hoặc nhóm quyền) ---------- */}
                {(auth.user_role?.nguoidung || auth.user_role?.nhomquyen) && (
                    <div className="row">
                        <StatCard 
                            title="Khoa" 
                            value={stats?.totalKhoa || 0} 
                            icon="fa-building" 
                            link="/khoa" 
                            colorClass="primary" 
                        />
                        <StatCard 
                            title="Người dùng" 
                            value={stats?.totalUsers || 0} 
                            icon="fa-users" 
                            link="/users" 
                            colorClass="dark" 
                        />
                        <StatCard 
                            title="Quản trị viên" 
                            value={stats?.totalAdmins || 0} 
                            icon="fa-user-shield" 
                            link="#" 
                            colorClass="danger" 
                        />
                        <StatCard 
                            title="Giảng viên" 
                            value={stats?.totalTeachers || 0} 
                            icon="fa-chalkboard-user" 
                            link="#" 
                            colorClass="info" 
                        />
                        <StatCard 
                            title="Môn học" 
                            value={stats?.totalSubjects || 0} 
                            icon="fa-book" 
                            link="/monhoc" 
                            colorClass="warning" 
                        />
                        <StatCard 
                            title="Chương" 
                            value={stats?.totalChapters || 0} 
                            icon="fa-list-ol" 
                            link="/chapter" 
                            colorClass="primary" 
                        />
                        <StatCard 
                            title="Sinh viên" 
                            value={stats?.totalStudents || 0} 
                            icon="fa-user-graduate" 
                            link="#" 
                            colorClass="success" 
                        />
                        <StatCard 
                            title="Nhóm Quyền" 
                            value={stats?.totalRoles || 0} 
                            icon="fa-lock" 
                            link="/roles" 
                            colorClass="warning" 
                        />
                    </div>
                )}


                {/* ---------- ROLE 2: GIẢNG VIÊN (Có quyền tham gia giảng dạy/tạo đề) ---------- */}
                {(auth.user_role?.dethi || auth.user_role?.hocphan || auth.user_role?.cauhoi) && (
                    <div className="row">
                        <StatCard 
                            title="Học phần quản lý" 
                            value={0} // Dữ liệu giả
                            icon="fa-book" 
                            link="#" 
                            colorClass="primary" 
                        />
                        <StatCard 
                            title="Sinh viên" 
                            value={stats?.totalStudents || 0} 
                            icon="fa-users" 
                            link="#" 
                            colorClass="success" 
                        />
                        <StatCard 
                            title="Ngân hàng câu hỏi" 
                            value={0} // Dữ liệu giả 
                            icon="fa-list" 
                            link="#" 
                            colorClass="warning" 
                        />
                        <StatCard 
                            title="Đề thi đã tạo" 
                            value={0} // Dữ liệu giả
                            icon="fa-file-lines" 
                            link="#" 
                            colorClass="info" 
                        />
                    </div>
                )}

                {/* ---------- ROLE 3: SINH VIÊN (Có quyền tham gia học phần hoặc thi) ---------- */}
                {(auth.user_role?.tghocphan || auth.user_role?.tgthi) && (
                    <div className="row">
                        <StatCard 
                            title="Học phần tham gia" 
                            value={0} // Dữ liệu giả
                            icon="fa-book-open" 
                            link="#" 
                            colorClass="primary" 
                        />
                        <StatCard 
                            title="Bài thi sắp tới" 
                            value={0} // Dữ liệu giả
                            icon="fa-clock" 
                            link="#" 
                            colorClass="warning" 
                        />
                        <StatCard 
                            title="Kết quả bài thi" 
                            value={0} // Dữ liệu giả
                            icon="fa-check-circle" 
                            link="#" 
                            colorClass="success" 
                        />
                    </div>
                )}

            </div>

            {/* Modal Onboarding */}
            <div className="modal fade" id="modal-onboarding" tabIndex="-1" role="dialog" aria-labelledby="modal-onboarding" aria-hidden="true">
                <div className="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div className="modal-content rounded overflow-hidden bg-image bg-image-bottom border-0" style={{ backgroundImage: "url('/public/media/photos/photo23.jpg')" }}>
                        <div className="row">
                            <div className="col-md-5">
                                <div className="p-3 text-end text-md-start">
                                    <a className="fw-semibold text-white" href="#" data-bs-dismiss="modal" aria-label="Close">
                                        Chuyển tiếp
                                    </a>
                                </div>
                            </div>
                            <div className="col-md-7">
                                <div className="bg-body-extra-light shadow-lg">
                                    <div className="js-slider slick-dotted-inner" data-dots="true" data-arrows="false" data-infinite="false">
                                        
                                        <div className="p-5">
                                            <i className="fa fa-award fa-3x text-muted my-4"></i>
                                            <h3 className="fs-2 fw-light mb-2">Chào mừng các bạn đến với hệ thống!</h3>
                                            <p className="text-muted">
                                                Hệ thống thi trực tuyến kiểm tra online, bạn vui lòng điền đầy đủ thông tin để sử dụng đầy đủ các tính năng của chúng tôi
                                            </p>
                                            <button type="button" className="btn btn-alt-primary mb-4" onClick={() => window.jQuery && window.jQuery('.js-slider').slick('slickGoTo', 1)}>
                                                Vui lòng thêm thông tin <i className="fa fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>

                                        <div className="slick-slide p-5">
                                            <i className="fa fa-user-check fa-3x text-muted my-4"></i>
                                            <h3 className="fs-2 fw-light">Hãy để chúng tôi biết thêm thông tin về bạn</h3>
                                            <form className="mb-4" onSubmit={(e) => { e.preventDefault(); handleUpdateEmail(); }}>
                                                <div className="mb-4">
                                                    <input 
                                                        type="email" 
                                                        className="form-control form-control-alt" 
                                                        id="email" 
                                                        name="onboard-email" 
                                                        placeholder="Nhập địa chỉ email của bạn ..."
                                                        value={email}
                                                        onChange={(e) => setEmail(e.target.value)}
                                                        required
                                                    />
                                                </div>
                                            </form>

                                            <button type="button" className="btn btn-primary mb-4" id="btn-email" onClick={handleUpdateEmail}>
                                                Cập nhật <i className="fa fa-check opacity-50 ms-1"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
