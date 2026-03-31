import { useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function UpdateProfileInformationForm({ mustVerifyEmail }) {
    const { auth, flash: pageFlash } = usePage().props;
    const user = auth.user;
    const [avatarPreview, setAvatarPreview] = useState(`/media/avatars/${user.avatar || 'avatar2.jpg'}`);

    const { data, setData, post, errors, processing, recentlySuccessful } = useForm({
        hoten: user.hoten || '',
        email: user.email || '',
        ngaysinh: user.ngaysinh || '',
        gioitinh: user.gioitinh ?? 1,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('profile.update'), { preserveScroll: true });
    };

    const handleAvatarChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setAvatarPreview(URL.createObjectURL(file));
            const formData = new FormData();
            formData.append('file-img', file);
            fetch('/profile/avatar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }
    };

    return (
        <div className="row push p-sm-2 p-lg-4">
            <div className="offset-xl-1 col-xl-4 order-xl-1">
                <p className="bg-body-light p-4 rounded-3 text-muted fs-sm">
                    Thông tin tài khoản của bạn. Vui lòng cập nhật đầy đủ.
                </p>
            </div>
            <div className="col-xl-6 order-xl-0">
                <form onSubmit={submit} encType="multipart/form-data">
                    <div className="mb-4">
                        <label className="form-label">Mã sinh viên</label>
                        <input type="text" className="form-control" value={user.id} disabled />
                    </div>

                    <div className="mb-4">
                        <label className="form-label" htmlFor="dm-profile-edit-name">Họ và tên</label>
                        <input 
                            type="text" 
                            className={`form-control ${errors.hoten ? 'is-invalid' : ''}`} 
                            id="dm-profile-edit-name" 
                            name="hoten"
                            value={data.hoten} 
                            onChange={(e) => setData('hoten', e.target.value)} 
                            required 
                        />
                        {errors.hoten && <div className="invalid-feedback d-block">{errors.hoten}</div>}
                    </div>

                    <div className="mb-4">
                        <label className="form-label" htmlFor="dm-profile-edit-email">Địa chỉ email</label>
                        <input 
                            type="email" 
                            className={`form-control ${errors.email ? 'is-invalid' : ''}`} 
                            id="dm-profile-edit-email" 
                            name="email"
                            value={data.email} 
                            onChange={(e) => setData('email', e.target.value)} 
                            required 
                        />
                        {errors.email && <div className="invalid-feedback d-block">{errors.email}</div>}
                    </div>

                    <div className="mb-3 d-flex gap-4">
                        <label className="form-label">Giới tính</label>
                        <div className="space-x-2">
                            <div className="form-check form-check-inline">
                                <input className="form-check-input" type="radio" id="gender-male" name="gioitinh" value="1" checked={data.gioitinh == 1} onChange={() => setData('gioitinh', 1)} />
                                <label className="form-check-label" htmlFor="gender-male">Nam</label>
                            </div>
                            <div className="form-check form-check-inline">
                                <input className="form-check-input" type="radio" id="gender-female" name="gioitinh" value="0" checked={data.gioitinh == 0} onChange={() => setData('gioitinh', 0)} />
                                <label className="form-check-label" htmlFor="gender-female">Nữ</label>
                            </div>
                        </div>
                    </div>

                    <div className="mb-4">
                        <label className="form-label" htmlFor="user_ngaysinh">Ngày sinh</label>
                        <input 
                            type="date" 
                            className={`form-control ${errors.ngaysinh ? 'is-invalid' : ''}`} 
                            id="user_ngaysinh" 
                            name="ngaysinh"
                            value={data.ngaysinh} 
                            onChange={(e) => setData('ngaysinh', e.target.value)} 
                        />
                        {errors.ngaysinh && <div className="invalid-feedback d-block">{errors.ngaysinh}</div>}
                    </div>

                    <div className="mb-4">
                        <label className="form-label">Ảnh đại diện</label>
                        <div className="push mb-2">
                            <img className="img-avatar" src={avatarPreview} alt="Avatar" />
                        </div>
                        <label className="form-label" htmlFor="dm-profile-edit-avatar">Chọn ảnh đại diện mới</label>
                        <input 
                            className={`form-control ${errors['file-img'] ? 'is-invalid' : ''}`}
                            type="file" 
                            id="dm-profile-edit-avatar" 
                            name="file-img" 
                            accept="image/*"
                            onChange={handleAvatarChange}
                        />
                        {errors['file-img'] && <div className="invalid-feedback d-block">{errors['file-img']}</div>}
                    </div>

                    <button type="submit" className="btn btn-alt-primary" disabled={processing}>
                        <i className="fa fa-check-circle opacity-50 me-1"></i> Cập nhật hồ sơ
                    </button>

                    {(recentlySuccessful || pageFlash?.status) && (
                        <span className="text-success ms-2">Đã lưu!</span>
                    )}
                </form>
            </div>
        </div>
    );
}
