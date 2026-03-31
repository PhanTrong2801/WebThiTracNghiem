import { useForm, usePage } from '@inertiajs/react';
import { useRef } from 'react';

export default function UpdatePasswordForm() {
    const passwordInput = useRef();
    const currentPasswordInput = useRef();
    const { flash: pageFlash } = usePage().props;

    const { data, setData, post, errors, reset, processing, recentlySuccessful } = useForm({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    const updatePassword = (e) => {
        e.preventDefault();
        post(route('profile.password'), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (errorsBag) => {
                if (errorsBag.new_password) {
                    reset('new_password', 'new_password_confirmation');
                    passwordInput.current?.focus();
                }
                if (errorsBag.current_password) {
                    reset('current_password');
                    currentPasswordInput.current?.focus();
                }
            },
        });
    };

    return (
        <div className="row push p-sm-2 p-lg-4">
            <div className="offset-xl-1 col-xl-4 order-xl-1">
                <p className="bg-body-light p-4 rounded-3 text-muted fs-sm">
                    Thay đổi mật khẩu đăng nhập là cách dễ dàng để bảo vệ tài khoản của bạn.
                </p>
            </div>
            <div className="col-xl-6 order-xl-0">
                <form onSubmit={updatePassword}>
                    <div className="mb-4">
                        <label className="form-label" htmlFor="current-password">Mật khẩu hiện tại</label>
                        <input 
                            type="password" 
                            className={`form-control ${errors.current_password ? 'is-invalid' : ''}`} 
                            id="current-password" 
                            name="current_password"
                            value={data.current_password} 
                            onChange={(e) => setData('current_password', e.target.value)} 
                            required
                            autoComplete="current-password"
                        />
                        {errors.current_password && <div className="invalid-feedback d-block">{errors.current_password}</div>}
                    </div>

                    <div className="mb-4">
                        <label className="form-label" htmlFor="new-password">Mật khẩu mới</label>
                        <input 
                            type="password" 
                            className={`form-control ${errors.new_password ? 'is-invalid' : ''}`} 
                            id="new-password" 
                            name="new_password"
                            value={data.new_password} 
                            onChange={(e) => setData('new_password', e.target.value)} 
                            required
                            autoComplete="new-password"
                        />
                        {errors.new_password && <div className="invalid-feedback d-block">{errors.new_password}</div>}
                    </div>

                    <div className="mb-4">
                        <label className="form-label" htmlFor="password-new-confirm">Xác nhận mật khẩu mới</label>
                        <input 
                            type="password" 
                            className={`form-control ${errors.new_password_confirmation ? 'is-invalid' : ''}`} 
                            id="password-new-confirm" 
                            name="new_password_confirmation"
                            value={data.new_password_confirmation} 
                            onChange={(e) => setData('new_password_confirmation', e.target.value)} 
                            required
                            autoComplete="new-password"
                        />
                        {errors.new_password_confirmation && <div className="invalid-feedback d-block">{errors.new_password_confirmation}</div>}
                    </div>

                    <button type="submit" className="btn btn-alt-primary" disabled={processing}>
                        <i className="fa fa-check-circle opacity-50 me-1"></i> Cập nhật mật khẩu
                    </button>

                    {(recentlySuccessful || pageFlash?.status) && (
                        <span className="text-success ms-2">Đổi mật khẩu thành công!</span>
                    )}
                </form>
            </div>
        </div>
    );
}
