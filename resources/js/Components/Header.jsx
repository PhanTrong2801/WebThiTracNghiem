import React from 'react';
import { Link } from '@inertiajs/react';
import Notifications from './Notifications';

const Header = ({ user, onToggleSidebarMini, onToggleSidebar, isDarkMode }) => {
    return (
        <header id="page-header">
            <div className="content-header">
                <div>
                    <button type="button" className="btn btn-alt-secondary me-1 d-lg-none" onClick={onToggleSidebar}>
                        <i className="fa fa-fw fa-bars"></i>
                    </button>
                    <button type="button" className="btn btn-alt-secondary me-1 d-none d-lg-inline-block" onClick={onToggleSidebarMini}>
                        <i className="fa fa-fw fa-ellipsis-v"></i>
                    </button>
                </div>
                
                <div className="d-flex align-items-center">
                    <Notifications userId={user?.id} />

                    <div className="dropdown d-inline-block ms-2">
                        <button type="button" className="btn btn-alt-secondary d-flex align-items-center" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i className="far fa-fw fa-user-circle"></i>
                            <i className="fa fa-fw fa-angle-down d-none opacity-50 d-sm-inline-block"></i>
                        </button>
                        <div className="dropdown-menu dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
                            <div className="bg-body-light rounded-top fw-semibold text-center p-3 border-bottom">
                                <span className="avatar-Account">
                                    <img className="img-avatar img-avatar48 img-avatar-thumb" src={`/public/media/avatars/${user?.avatar || 'avatar2.jpg'}`} alt=""/>
                                </span>
                                <div className="pt-2 load-nameAccount">
                                    <a className="fw-semibold" style={{ color: '#3b82f6' }}>
                                        {user?.hoten || user?.name || 'Người dùng'}
                                    </a>
                                </div>
                            </div>
                            <div className="p-2">
                                <Link className="dropdown-item" href="/profile">
                                    <i className="si si-settings me-2 fa-fw icon-dropdown-item"></i> Tài khoản
                                </Link>
                                <Link className="dropdown-item" href={route('logout')} method="post" as="button">
                                    <i className="si si-logout me-2 fa-fw icon-dropdown-item"></i> Đăng xuất
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* Loader */}
            <div id="page-header-loader" className="overlay-header bg-header-dark">
                <div className="bg-white-10">
                    <div className="content-header">
                        <div className="w-100 text-center">
                            <i className="fa fa-fw fa-sun fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
};

export default Header;