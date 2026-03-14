//
import React, { useEffect, useState } from 'react';
import Navbar from './Navbar';
import Header from './Header';
import Footer from './Footer';

const MainLayout = ({ children, user }) => {
    // Khởi tạo trạng thái class
    const [isDarkMode, setIsDarkMode] = useState(false);
    const [sidebarMini, setSidebarMini] = useState(false);
    const [sidebarOpen, setSidebarOpen] = useState(false); // Thêm state cho mobile sidebar

    const toggleSidebarMini = () => setSidebarMini(prev => !prev);
    const toggleSidebar = () => {
        setSidebarOpen(prev => !prev);
    };

    // Log để debug xem state có thay đổi không
    useEffect(() => {
        console.log("Sidebar Open State:", sidebarOpen);
    }, [sidebarOpen]);

    useEffect(() => {
        // Cập nhật lúc mount
        setIsDarkMode(typeof window !== 'undefined' && localStorage.getItem('dashmix_dark_mode') === 'true');

        const handleThemeChange = () => {
            setIsDarkMode(localStorage.getItem('dashmix_dark_mode') === 'true');
        };

        window.addEventListener('dark_mode_changed', handleThemeChange);
        window.addEventListener('storage', handleThemeChange);

        return () => {
            window.removeEventListener('dark_mode_changed', handleThemeChange);
            window.removeEventListener('storage', handleThemeChange);
        };
    }, []);

    return (
        /* Các class mặc định + dark-mode nếu có lưu trong bộ nhớ */
        <div 
            id="page-container" 
            className={`sidebar-o enable-page-overlay side-scroll page-header-fixed main-content-narrow remember-theme 
                ${sidebarMini ? 'sidebar-mini' : ''} 
                ${sidebarOpen ? 'sidebar-open sidebar-o-xs' : ''} 
                ${isDarkMode ? 'sidebar-dark page-header-dark dark-mode' : 'sidebar-light page-header-light'}`}
        >
            
            {/* Navbar & Header */}
            <Navbar onToggleSidebar={toggleSidebar} />
            <Header 
                user={user} 
                onToggleSidebarMini={toggleSidebarMini} 
                onToggleSidebar={toggleSidebar} 
                isDarkMode={isDarkMode} 
            />

            {/* Khi ở mobile, click vào overlay để đóng sidebar. Hiển thị overlay khi sidebar đang mở ở mobile */}
            <div 
                id="page-overlay" 
                style={{ display: sidebarOpen ? 'block' : 'none' }}
                onClick={() => setSidebarOpen(false)}
            ></div>

            {/* Main Container */}
            <main id="main-container">
                {children}
            </main>

            {/* Footer */}
            <Footer />
        </div>
    );
};

export default MainLayout;