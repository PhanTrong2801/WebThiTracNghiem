import { Head } from '@inertiajs/react';

export default function UsersIndex({ users }) {
    return (
        <>
            <Head title="Danh sách Users" />
            <div className="content content-full">
                <div className="block block-rounded mt-4">
                    <div className="block-header block-header-default">
                        <h3 className="block-title">
                            <i className="fa fa-users me-2 text-primary"></i>
                            Danh sách Users
                        </h3>
                        <div className="block-options">
                            <span className="badge bg-primary">{users.length} người dùng</span>
                        </div>
                    </div>
                    <div className="block-content p-0">
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
                                {users.length === 0 ? (
                                    <tr>
                                        <td colSpan="4" className="text-center py-4 text-muted">
                                            <i className="fa fa-inbox fa-2x mb-2 d-block"></i>
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                ) : (
                                    users.map((user, index) => (
                                        <tr key={user.id}>
                                            <td className="text-muted">{index + 1}</td>
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
            </div>
        </>
    );
}
