import { Head, usePage } from '@inertiajs/react';
import MainLayout from '@/Components/MainLayouts';
import Pagination from '@/Components/Pagination';

export default function UsersIndex() {
    const { auth, users } = usePage().props;
    
    console.log("Users Prop:", users);
    console.log("Auth Prop:", auth);

    if (!users || !auth) {
        return (
            <div className="p-4 text-center">
                <i className="fa fa-spinner fa-spin fa-2x mb-3 text-primary"></i>
                <div>Đang tải dữ liệu hoặc thiếu thông tin xác thực...</div>
            </div>
        );
    }

    const { data: userList = [], links = [], total = 0, from = 0, to = 0 } = users;
    const user = auth.user;

    return (
        <MainLayout user={user}>
            <Head title="Danh sách Users" />
            <div className="content content-full">
                <div className="block block-rounded mt-4 shadow-sm">
                    <div className="block-header block-header-default">
                        <h3 className="block-title">
                            <i className="fa fa-users me-2 text-primary"></i>
                            Danh sách Users
                        </h3>
                        <div className="block-options">
                            <span className="badge bg-primary">{from}-{to} / {total} người dùng</span>
                        </div>
                    </div>
                    <div className="block-content p-0">
                        <div className="table-responsive">
                            <table className="table table-hover table-vcenter mb-0">
                                <thead className="table-light">
                                    <tr>
                                        <th style={{ width: '50px' }}>#</th>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {userList.length === 0 ? (
                                        <tr>
                                            <td colSpan="4" className="text-center py-4 text-muted">
                                                <i className="fa fa-inbox fa-2x mb-2 d-block"></i>
                                                Không có dữ liệu
                                            </td>
                                        </tr>
                                    ) : (
                                        userList.map((user, index) => (
                                            <tr key={user.id}>
                                                <td className="text-muted">{from + index}</td>
                                                <td><span className="fw-semibold">{user.id}</span></td>
                                                <td>{user.hoten}</td>
                                                <td className="text-muted">{user.email}</td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div className="block-content block-content-full block-content-sm bg-body-light">
                        <Pagination links={links} />
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
