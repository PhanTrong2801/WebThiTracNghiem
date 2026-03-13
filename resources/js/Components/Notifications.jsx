import React from 'react';

const Notifications = ({ userId }) => {
    return (
        <div data-role="tghocphan" data-action="join" className="dropdown d-inline-block">
            <button 
                type="button" 
                className="btn btn-alt-secondary btn-show-notifications" 
                data-id={userId} 
                id="page-header-notifications-dropdown"
                data-bs-toggle="dropdown" 
                aria-haspopup="true" 
                aria-expanded="false"
            >
                <i className="fa fa-fw fa-bell"></i>
            </button>
            <div 
                className="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" 
                style={{ width: '25rem' }}
                aria-labelledby="page-header-notifications-dropdown"
            >
                <div className="bg-primary-dark rounded-top fw-semibold text-white text-center p-3">
                    Thông báo
                </div>
                <ul className="nav-items my-2 list-notifications">
                    {/* Render danh sách thông báo ở đây trong tương lai */}
                    {/* <li className="text-muted p-3 text-center">Chưa có thông báo nào</li> */}
                </ul>
            </div>
        </div>
    );
};

export default Notifications;
