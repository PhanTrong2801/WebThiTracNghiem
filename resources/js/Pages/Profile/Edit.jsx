import { Head, usePage } from '@inertiajs/react';
import MainLayout from '@/Components/MainLayouts';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import { useEffect } from 'react';

export default function Edit({ mustVerifyEmail }) {
    const { auth, flash } = usePage().props;

    if (!auth) return <div>Loading...</div>;

    return (
        <MainLayout user={auth.user}>
            <Head title="Trang cá nhân" />
            <div className="content content-full">
                {/* Flash Messages */}
                {flash?.success && (
                    <div className="alert alert-success rounded-2 mb-0 mt-4" role="alert">
                        <i className="fa fa-check-circle me-2"></i>
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="alert alert-danger rounded-2 mb-0 mt-4" role="alert">
                        <i className="fa fa-exclamation-circle me-2"></i>
                        {flash.error}
                    </div>
                )}
                {flash?.status && (
                    <div className="alert alert-success rounded-2 mb-0 mt-4" role="alert">
                        <i className="fa fa-check-circle me-2"></i>
                        {flash.status}
                    </div>
                )}

                {/* Hero Section */}
                <div className="rounded border overflow-hidden push">
                    <div className="bg-image pt-9" style={{ backgroundImage: "url('./public/media/photos/photo24@2x.jpg')" }}></div>
                    <div className="px-4 py-3 bg-body-extra-light d-flex flex-column flex-md-row align-items-center">
                        <a className="d-block img-link mt-n5" href="javascript:void(0)">
                            <img 
                                className="img-avatar img-avatar128 img-avatar-thumb" 
                                src={`/media/avatars/${auth.user.avatar || 'avatar2.jpg'}`} 
                                alt="Avatar" 
                            />
                        </a>
                        <div className="ms-3 flex-grow-1 text-center text-md-start my-3 my-md-0">
                            <h1 className="fs-4 fw-bold mb-1">{auth.user.hoten}</h1>
                            <h2 className="fs-sm fw-medium text-muted mb-0">Chỉnh sửa hồ sơ</h2>
                        </div>
                    </div>
                </div>

                {/* Edit Account */}
                <div className="block block-bordered block-rounded">
                    <ul className="nav nav-tabs nav-tabs-alt" role="tablist">
                        <li className="nav-item">
                            <button className="nav-link active space-x-1" id="account-profile-tab" data-bs-toggle="tab" data-bs-target="#account-profile" role="tab" aria-controls="account-profile" aria-selected="true">
                                <i className="fa fa-user-circle d-sm-none"></i>
                                <span className="d-none d-sm-inline">Hồ sơ</span>
                            </button>
                        </li>
                        <li className="nav-item">
                            <button className="nav-link space-x-1" id="account-password-tab" data-bs-toggle="tab" data-bs-target="#account-password" role="tab" aria-controls="account-password" aria-selected="false">
                                <i className="fa fa-asterisk d-sm-none"></i>
                                <span className="d-none d-sm-inline">Mật khẩu</span>
                            </button>
                        </li>
                    </ul>

                    <div className="block-content tab-content">
                        {/* Profile Tab */}
                        <div className="tab-pane active" id="account-profile" role="tabpanel" aria-labelledby="account-profile-tab">
                            <UpdateProfileInformationForm mustVerifyEmail={mustVerifyEmail} />
                        </div>

                        {/* Password Tab */}
                        <div className="tab-pane" id="account-password" role="tabpanel" aria-labelledby="account-password-tab">
                            <UpdatePasswordForm />
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
