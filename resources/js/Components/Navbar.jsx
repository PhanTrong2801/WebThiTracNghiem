//
import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { NAVBAR_MENU } from './Config'; // Sửa lại đường dẫn import file Config.js cho đúng

const Navbar = ({ onToggleSidebar }) => {
    const { url } = usePage();
    const { auth } = usePage().props;
    const userRole = auth?.user_role || {};

    const buildNavbar = () => {
        return NAVBAR_MENU.map((group, idx) => {
            // Lọc các item dựa trên quyền (role) có tồn tại trong userRole hay không
            const filteredItems = group.navbarItem?.filter(item => 
                userRole.hasOwnProperty(item.role)
            ) || [];

            if (filteredItems.length === 0) return null;

            return (
                <React.Fragment key={idx}>
                    <li className="nav-main-heading">{group.name}</li>
                    {filteredItems.map((item, i) => (
                        <li className="nav-main-item" key={i}>
                            <Link 
                                className={`nav-main-link ${url.includes(item.url) ? 'active' : ''}`} 
                                href={`/${item.url}`}
                            >
                                <i className={`nav-main-link-icon ${item.icon}`}></i>
                                <span className="nav-main-link-name">{item.name}</span>
                            </Link>
                        </li>
                    ))}
                </React.Fragment>
            );
        });
    };

    return (
        <nav id="sidebar">
            <div className="bg-header-dark">
                <div className="content-header bg-white-5">
                    <Link className="fw-semibold text-white tracking-wide d-flex align-items-center" href="/">
                        <img src="/img/logo_truong.png" alt="Logo" style={{ width: '45px', height: '45px', marginRight: '8px' }} />
                        <span className="smini-hidden">
                            STU <span className="opacity-75 ms-1">Test</span>
                        </span>
                    </Link>
                    
                    {/* Options */}
                    <div>
                        {/* Dark Mode */}
                        <button type="button" className="btn btn-sm btn-alt-secondary" onClick={() => {
                            // Xử lý bằng React State thông qua Event thay vì gọi trực tiếp Dashmix API DOM
                            const currentMode = localStorage.getItem('dashmix_dark_mode') === 'true';
                            const newMode = !currentMode;
                            
                            localStorage.setItem('dashmix_dark_mode', newMode);
                            window.dispatchEvent(new Event('dark_mode_changed'));
                        }}>
                            <i className="far fa-moon" id="dark-mode-toggler"></i>
                        </button>
                        {/* END Dark Mode */}

                        {/* Close Sidebar, Visible only on mobile screens */}
                        <button type="button" className="d-lg-none btn btn-sm btn-alt-secondary ms-1" onClick={onToggleSidebar}>
                            <i className="fa fa-fw fa-times"></i>
                        </button>
                        {/* END Close Sidebar */}
                    </div>
                </div>
            </div>
            <div className="js-sidebar-scroll">
                <div className="content-side">
                    <ul className="nav-main">
                        <li className="nav-main-item">
                            <Link className={`nav-main-link ${url.includes('dashboard') ? 'active' : ''}`} href="/dashboard">
                                <i className="nav-main-link-icon fa fa-rocket"></i>
                                <span className="nav-main-link-name">Tổng quan</span>
                            </Link>
                        </li>
                        {buildNavbar()}
                    </ul>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
