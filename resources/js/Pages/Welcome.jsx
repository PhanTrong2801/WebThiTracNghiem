import React, { useEffect } from 'react';
import { Link } from '@inertiajs/react';

const Welcome = ({ auth }) => {
    useEffect(() => {
        if (window.Dashmix) {
            window.Dashmix.helpers(['js-appear', 'js-slider']);
            window.Dashmix.init();
        }

        const handleThemeChange = () => {
            const isDarkMode = localStorage.getItem('dashmix_dark_mode') === 'true';
            const container = document.getElementById('page-container');
            if (container) {
                if (isDarkMode) container.classList.add('dark-mode');
                else container.classList.remove('dark-mode');
            }
        };

        window.addEventListener('dark_mode_changed', handleThemeChange);
        window.addEventListener('storage', handleThemeChange);

        return () => {
            window.removeEventListener('dark_mode_changed', handleThemeChange);
            window.removeEventListener('storage', handleThemeChange);
        };
    }, []);

    // Team members data
    const teamMembers = [
        { name: 'Trịnh Minh Giàu', id: 'DH52200608' },
        { name: 'Võ Lê Minh Khang', id: 'DH52200854' },
        { name: 'Nguyễn Hoàng Khoa', id: 'DH52200912' },
        { name: 'Vũ Thành Nhật Minh', id: 'DH52201068' },
        { name: 'Võ Lê Minh Thịnh', id: 'DH52201508' },
        { name: 'Phan Thanh Trọng', id: 'DH52201659' }
    ];

    // Khởi tạo container dark-mode 
    const isDarkMode = typeof window !== 'undefined' && localStorage.getItem('dashmix_dark_mode') === 'true';

    return (
        <div id="page-container" className={`sidebar-dark side-scroll page-header-fixed page-header-glass main-content-boxed remember-theme ${isDarkMode ? 'dark-mode' : ''}`}>
            {/* Header */}
            <header id="page-header">
                <div className="content-header">
                    <div className="space-x-1 d-flex align-items-center space-x-2 animated zoomInRight">
                        <Link className="link-fx fw-bold" href="/">
                            <i className="fa fa-fire text-primary"></i>
                            <span className="fs-4 text-dual"> </span><span className="fs-4 text-primary">STU</span>
                        </Link>
                    </div>
                    <div className="space-x-1">
                        <ul className="nav-main nav-main-horizontal nav-main-hover nav">
                            <li className="nav-main-item">
                                <button type="button" className="btn btn-hero rounded-pill btn-light px-3" onClick={() => {
                                    const currentMode = localStorage.getItem('dashmix_dark_mode') === 'true';
                                    const newMode = !currentMode;
                                    
                                    localStorage.setItem('dashmix_dark_mode', newMode);
                                    window.dispatchEvent(new Event('dark_mode_changed'));
                                }}>
                                    <i className="fa fa-moon" id="dark-mode-toggler"></i>
                                </button>
                            </li>
                            {!auth?.user ? (
                                <li className="nav-main-item">
                                    <Link className="btn btn-hero btn-light rounded-pill" href="/login">
                                        <i className="fa fa-right-to-bracket me-2"></i>Đăng nhập
                                    </Link>
                                </li>
                            ) : (
                                <li className="nav-main-item">
                                    <Link className="btn btn-hero btn-primary rounded-pill" href="/dashboard">
                                        <i className="fa fa-rocket me-2"></i>Dashboard
                                    </Link>
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
            </header>
            {/* END Header */}

            {/* Main Container */}
            <main id="main-container">
                {/* Hero */}
                <div className="hero bg-body-extra-light hero-lg overflow-hidden">
                    <div className="hero-inner">
                        <div className="position-relative d-flex align-items-center justify-content-center">
                            <div className="content content-full">
                                <div className="row g-6 w-100 py-7 overflow-hidden text-center">
                                    <div className="col-12 py-4 d-flex flex-column align-items-center justify-content-center" data-toggle="appear" data-class="animated fadeInUp">
                                        <div className="mb-4" data-toggle="appear" data-class="animated fadeInDown">
                                            <img src="/img/logo_truong.png" alt="Logo STU" className="img-fluid" style={{ maxWidth: '400px' }} />
                                        </div>
                                        <h1 className="fw-bold fs-1 mb-4">
                                            Hệ thống thi và tạo đề thi trắc nghiệm online của STU.
                                        </h1>
                                        <p className="text-muted fw-medium mb-5" style={{ maxWidth: '600px' }}>
                                            Nền tảng hỗ trợ Phòng Công tác Sinh viên tổ chức hiệu quả các kỳ thi trắc nghiệm trực tuyến. Cung cấp công cụ mạnh mẽ để tạo đề thi, quản lý ngân hàng câu hỏi và giám sát kết quả một cách chính xác.
                                        </p>
                                        <div>
                                            <Link className="btn btn-primary py-3 px-4 m-1 rounded-pill fw-semibold" href="/register">
                                                <i className="fa fa-arrow-right opacity-50 me-1"></i> Tham gia ngay
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {/* END Hero */}

                {/* Section 1 */}
                <div className="position-relative bg-body-extra-light" id="section--1">
                    <div className="position-absolute top-0 end-0 bottom-0 start-0 bg-body-light skew-y-1"></div>
                    <div className="position-relative">
                        <div className="content content-full my-5">
                            <div className="row g-0 justify-content-center text-center">
                                <div className="col-xl-4 col-md-6 mb-4" data-toggle="appear" data-class="animated flipInX">
                                    <div className="block block-rounded block-bordered block-fx-pop h-100 mb-0">
                                        <div className="block-content block-content-full py-5 px-4 text-center">
                                            <div className="d-inline-block bg-body-light rounded-circle p-4 mb-4">
                                                <i className="fa fa-cubes fa-2x text-primary"></i>
                                            </div>
                                            <h3 className="h4 fw-bold mb-3">Lưu trạng thái khi gặp sự cố</h3>
                                            <p className="fw-medium text-muted mb-0">Hệ thống tự động lưu trữ bài làm của sinh viên, đảm bảo an toàn kết quả thi ngay cả khi gặp sự cố về mạng hoặc thiết bị.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-4 col-md-6 mb-4" data-toggle="appear" data-class="animated flipInX" data-timeout="200">
                                    <div className="block block-rounded block-bordered block-fx-pop h-100 mb-0">
                                        <div className="block-content block-content-full py-5 px-4 text-center">
                                            <div className="d-inline-block bg-body-light rounded-circle p-4 mb-4">
                                                <i className="fa fa-code fa-2x text-info"></i>
                                            </div>
                                            <h3 className="h4 fw-bold mb-3">Quản lý đề thi thông minh</h3>
                                            <p className="fw-medium text-muted mb-0">Hỗ trợ chuyên viên thiết lập đề thi nhanh chóng từ ngân hàng câu hỏi, đảm bảo tính khách quan và tiết kiệm thời gian tổ chức.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-4 col-md-6 mb-4" data-toggle="appear" data-class="animated flipInX" data-timeout="400">
                                    <div className="block block-rounded block-bordered block-fx-pop h-100 mb-0">
                                        <div className="block-content block-content-full py-5 px-4 text-center">
                                            <div className="d-inline-block bg-body-light rounded-circle p-4 mb-4">
                                                <i className="fa fa-rocket fa-2x text-success"></i>
                                            </div>
                                            <h3 className="h4 fw-bold mb-3">Phân loại câu hỏi</h3>
                                            <p className="fw-medium text-muted mb-0">Tổ chức ngân hàng câu hỏi theo từng chuyên đề, mức độ khó dễ, giúp cấu trúc đề thi một cách khoa học và chính xác.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {/* END Section 1 */}

                {/* Section 2 */}
                <div className="bg-body-extra-light">
                    <div className="content content-full">
                        <div className="row">
                            <div className="order-md-1 col-md-6 d-flex align-items-center justify-content-center">
                                <img src="/media/various/landing_1.png" alt="" className="feature__img" data-toggle="appear" data-class="animated fadeInRight" />
                            </div>
                            <div className="order-md-0 col-md-6 d-flex align-items-center">
                                <div>
                                    <h3 className="h3 mb-4 fw-bolder" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200">
                                        Tổ chức kỳ thi trực tuyến một cách toàn diện
                                    </h3>
                                    <p className="mb-4" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200">
                                        Hệ thống đáp ứng đa dạng các hình thức kiểm tra đánh giá, hỗ trợ Phòng Công tác Sinh viên tổ chức thi hiệu quả:
                                    </p>
                                    <ul className="text-dark mb-4 m-0 list-unstyled">
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="200">Ngân hàng câu hỏi trắc nghiệm phong phú</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="400">Tạo đề thi với cấu trúc linh hoạt</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="600">Quản lý danh sách sinh viên dự thi</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="800">Xuất báo cáo, thống kê điểm số tự động</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-body">
                    <div className="content content-full">
                        <div className="row">
                            <div className="order-md-0 col-md-6 d-flex align-items-center justify-content-center">
                                <img src="/media/various/landing_2.png" alt="" className="feature__img" data-toggle="appear" data-class="animated fadeInLeft" />
                            </div>
                            <div className="order-md-1 col-md-6 d-flex align-items-center">
                                <div>
                                    <h3 className="h3 mb-4 fw-bolder" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200">
                                        Chủ động thiết lập thời gian và lịch thi
                                    </h3>
                                    <p className="mb-4" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200">
                                        Quản lý chặt chẽ quá trình làm bài của sinh viên thông qua các tùy chọn giới hạn thời gian:
                                    </p>
                                    <ul className="text-dark mb-4 m-0 list-unstyled">
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="200">Thiết lập thời lượng làm bài cho từng ca thi</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="400">Giới hạn thời gian mở/đóng hệ thống làm bài</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="600">Lên lịch thi trước cho nhiều đợt kiểm tra quy mô lớn</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-body-extra-light">
                    <div className="content content-full">
                        <div className="row">
                            <div className="order-md-1 col-md-6 d-flex align-items-center justify-content-center">
                                <img src="/media/various/landing_3.png" alt="" className="feature__img" data-toggle="appear" data-class="animated fadeInRight" />
                            </div>
                            <div className="order-md-0 col-md-6 d-flex align-items-center">
                                <div>
                                    <h3 className="h3 mb-4 fw-bolder" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200">
                                        Sinh viên tham gia thi trực tiếp trên trình duyệt
                                    </h3>
                                    <p className="mb-2" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200">
                                        Nền tảng thi trắc nghiệm trực tuyến của STU đem lại trải nghiệm thân thiện, mượt mà và an toàn cho sinh viên dự thi.
                                    </p>
                                    <ul className="text-dark mb-4 m-0 list-unstyled">
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="200">Đảm bảo tính bảo mật và công bằng trong thi cử</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="400">Giao diện trực quan, sinh viên dễ dàng thao tác làm bài</li>
                                        <li className="list-landing" data-toggle="appear" data-class="animated fadeInUp" data-offset="-200" data-timeout="600">Tương thích hoàn hảo trên thiết bị di động và máy tính</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Section: Team */}
                <div className="bg-body-extra-light">
                    <div className="content content-full">
                        <div className="pt-7 pb-5">
                            <div className="position-relative">
                                <h2 className="fw-bold mb-2 text-center" data-toggle="appear" data-class="animated fadeInDown" data-offset="-200">
                                    <span className="text-primary">Các thành viên tham gia</span> nhóm 8
                                </h2>
                                <h3 className="h4 fw-medium text-muted text-center mb-5" data-toggle="appear" data-class="animated fadeInDown" data-offset="-200">
                                    Dự án trang web thi trác nghiệm cho phòng công tác sinh viên 
                                </h3>
                            </div>
                            <div className="row text-center slider-team">
                                {teamMembers.map((member, idx) => (
                                    <div className="p-3 col-md-4 col-sm-6" data-toggle="appear" data-class="animated flipInX" key={idx}>
                                        <div className="block block-rounded block-bordered border-primary border-top border-3 bg-body text-center h-100 mb-0 shadow-sm">
                                            <div className="block-content block-content-full py-5">
                                                <p className="fw-bold fs-5 mb-1">{member.name}</p>
                                                <p className="fs-sm text-muted fw-medium mb-0">
                                                    {member.id}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
                {/* END Section 2 */}

            </main>
            {/* END Main Container */}

            {/* Footer */}
            <footer id="page-footer" className="footer-static bg-body-extra-light">
                <div className="content py-4">
                    {/* Footer Content */}
                    <div className="row items-push fs-sm border-bottom pt-4 text-center">
                        {/* Row 1: Logo */}
                        <div className="col-12 mb-3">
                            <img src="/img/logo_truong.png" alt="Logo Trường" className="img-fluid mx-auto d-block" style={{ maxWidth: '250px' }} />
                        </div>
                        
                        {/* Row 2: Information */}
                        <div className="col-12 mb-3">
                            <h3 className="fw-semibold mb-2">Trường Đại học Công nghệ Sài Gòn</h3>
                            <div className="fs-sm">
                                180 Cao Lỗ, Phường Chánh Hưng, Tp. Hồ Chí Minh<br />
                                Điện thoại: (028) 38 505 520<br />
                            </div>
                        </div>

                        {/* Row 3: Social Links */}
                        <div className="col-12">
                            <h3 className="fw-semibold mb-2">Kết nối</h3>
                            <ul className="list list-inline mb-0">
                                <li className="list-inline-item">
                                    <a className="fw-semibold" href="https://www.facebook.com/">
                                        <i className="fab fa-2x fa-facebook-square text-primary"></i>
                                    </a>
                                </li>
                                <li className="list-inline-item">
                                    <a className="fw-semibold" href="#!">
                                        <i className="fab fa-2x fa-facebook-messenger text-info"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    {/* END Footer Content */}

                    {/* Footer Copyright */}
                    <div className="row fs-sm pt-4">
                        <div className="col-md-6 offset-md-3 text-center">
                            Copyright © {new Date().getFullYear()} STU TEST. All rights reserved.
                        </div>
                    </div>
                    {/* END Footer Copyright */}
                </div>
            </footer>
            {/* END Footer */}
        </div>
    );
};

export default Welcome;