import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        id: '',
        password: '',
        remember: false,
    });

    // State để ẩn/hiện mật khẩu
    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        const savedId = localStorage.getItem('remembered_id');
        const savedPassword = localStorage.getItem('remembered_password');
        const expiry = localStorage.getItem('remembered_expiry');
        
        // Kiểm tra xem đã quá 5 ngày chưa
        const isExpired = expiry && new Date().getTime() > parseInt(expiry);

        if (isExpired) {
            // Hết hạn -> Xóa mật khẩu và expiry, giữ lại ID
            localStorage.removeItem('remembered_password');
            localStorage.removeItem('remembered_expiry');
            
            if (savedId) {
                setData((prevData) => ({
                    ...prevData,
                    id: savedId,
                    remember: true, 
                }));
            }
        } else {
            // Vẫn còn hạn -> Điền thông tin vào form
            if (savedId && savedPassword) {
                setData((prevData) => ({
                    ...prevData,
                    id: savedId,
                    password: savedPassword,
                    remember: true,
                }));
            } else if (savedId) {
                setData((prevData) => ({
                    ...prevData,
                    id: savedId,
                    remember: true,
                }));
            }
        }
    }, []);

    const submit = (e) => {
        e.preventDefault();

        // Xử lý lưu trữ LocalStorage
        if (data.remember) {
            const expiryTime = new Date().getTime() + 5 * 24 * 60 * 60 * 1000;
            localStorage.setItem('remembered_id', data.id);
            localStorage.setItem('remembered_password', data.password);
            localStorage.setItem('remembered_expiry', expiryTime.toString());
        } else {
            localStorage.removeItem('remembered_id');
            localStorage.removeItem('remembered_password');
            localStorage.removeItem('remembered_expiry');
        }

        post(route('login'), {
            onError: () => {
                // Nếu đăng nhập sai (có thể do đổi pass ở máy khác)
                // Xóa pass cũ đã lưu để người dùng nhập lại cái mới
                localStorage.removeItem('remembered_password');
                localStorage.removeItem('remembered_expiry');
                setShowPassword(false);
            },
            onFinish: () => reset('password'),
        });
    };

    return (
        <div
            id="page-container"
            className="main-content-boxed remember-theme login-page-container"
        >
            <Head title="Đăng nhập" />

            <div className="block block-rounded shadow-lg overflow-hidden login-box">
                <div className="row g-0">
                    {/* Left panel: branding */}
                    <div className="col-md-5 bg-body-extra-light d-flex flex-column align-items-center justify-content-center p-5 border-end">
                        <Link href="/">
                            <img
                                src="/img/logo_truong.png"
                                alt="Logo Trường STU"
                                className="img-fluid mb-4 login-logo"
                            />
                        </Link>
                        <h2 className="fw-bold fs-4 text-center mb-2">STU Test</h2>
                        <p className="text-muted text-center fs-sm mb-0">
                            Hệ thống thi trắc nghiệm<br />
                            Phòng Công tác Sinh viên
                        </p>
                    </div>

                    {/* Right panel: form */}
                    <div className="col-md-7 bg-body p-5">
                        <div className="mb-5">
                            <h3 className="fw-bold mb-1">Đăng nhập</h3>
                            <p className="text-muted fs-sm mb-0">Vui lòng nhập thông tin tài khoản của bạn</p>
                        </div>

                        {status && (
                            <div className="alert alert-success fs-sm mb-4">{status}</div>
                        )}

                        <form onSubmit={submit}>
                            {/* ID Field */}
                            <div className="mb-4">
                                <label htmlFor="id" className="form-label fw-semibold">
                                    Mã sinh viên / Giáo viên
                                </label>
                                <div className="input-group">
                                    <span className="input-group-text bg-body-extra-light border-end-0">
                                        <i className="fa fa-user text-muted"></i>
                                    </span>
                                    <input
                                        id="id"
                                        type="text"
                                        name="id"
                                        value={data.id}
                                        autoComplete="username"
                                        autoFocus
                                        placeholder="Ví dụ: DH52200001"
                                        onChange={(e) => setData('id', e.target.value)}
                                        className="form-control border-start-0 ps-0"
                                    />
                                </div>
                                {errors.id && <div className="text-danger fs-sm mt-1">{errors.id}</div>}
                            </div>

                            {/* Password Field */}
                            <div className="mb-4">
                                <label htmlFor="password" className="form-label fw-semibold">
                                    Mật khẩu
                                </label>
                                <div className="input-group">
                                    <span className="input-group-text bg-body-extra-light border-end-0">
                                        <i className="fa fa-lock text-muted"></i>
                                    </span>
                                    <input
                                        id="password"
                                        type={showPassword ? "text" : "password"}
                                        name="password"
                                        value={data.password}
                                        autoComplete="current-password"
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="form-control border-start-0 border-end-0 ps-0"
                                    />
                                    <button
                                        type="button"
                                        className="input-group-text bg-body-extra-light border-start-0 login-password-toggle"
                                        onClick={() => setShowPassword(!showPassword)}
                                    >
                                        <i className={`fa ${showPassword ? 'fa-eye-slash' : 'fa-eye'} text-muted`}></i>
                                    </button>
                                </div>
                                {errors.password && <div className="text-danger fs-sm mt-1">{errors.password}</div>}
                            </div>

                            {/* Remember me & Forgot password */}
                            <div className="d-flex align-items-center justify-content-between mb-4">
                                <label className="d-flex align-items-center gap-2 mb-0 login-remember-label">
                                    <Checkbox
                                        name="remember"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                    />
                                    <span className="fs-sm text-muted">Ghi nhớ đăng nhập</span>
                                </label>
                                {canResetPassword && (
                                    <Link
                                        href={route('password.request')}
                                        className="fs-sm text-primary text-decoration-none"
                                    >
                                        Quên mật khẩu?
                                    </Link>
                                )}
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                className="btn btn-primary w-100 py-2 fw-semibold"
                                disabled={processing}
                            >
                                {processing ? (
                                    <><i className="fa fa-spinner fa-spin me-2"></i>Đang xử lý...</>
                                ) : (
                                    <><i className="fa fa-right-to-bracket me-2"></i>Đăng nhập</>
                                )}
                            </button>
                        </form>

                        <div className="mt-4 text-center">
                            <Link href="/" className="fs-sm text-muted text-decoration-none">
                                <i className="fa fa-arrow-left me-1"></i>Quay về trang chủ
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}