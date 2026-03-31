<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class UserModel extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserModelFactory> */
    use HasFactory, Notifiable;
    
    protected $table = 'users';

    // Khóa chính là string, không tự tăng
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'hoten',
        'email',
        'password',
        'gioitinh',
        'ngaysinh',
        'avatar',
        'trangthai',
        'manhomquyen',
        'makhoa',
        'sodienthoai',
        'otp',
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'token',
        'otp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'makhoa' => 'string',
        ];
    }

    /**
     * Tùy chỉnh tên cột lưu token ghi nhớ (Mặc định là remember_token)
     */
    public function getRememberTokenName()
    {
        return 'token'; 
    }

    public function setRememberToken($value)
    {
        $this->token = $value;
    }

    /**
     * Nhóm quyền của user
     */
    public function nhomquyen()
    {
        return $this->belongsTo(NhomQuyenModel::class, 'manhomquyen', 'manhomquyen');
    }

    /**
     * Khoa của user
     */
    public function khoa()
    {
        return $this->belongsTo(KhoaModel::class, 'makhoa', 'id');
    }


    /**
     * Lấy danh sách quyền chi tiết dạng ["tghocphan" => ["join", "view"], ...]
     */
    public function getRolePermissions()
    {
        if (!$this->manhomquyen) {
            return [];
        }

        $chiTietQuyen = \App\Models\ChiTietQuyenModel::where('manhomquyen', $this->manhomquyen)->get();
        $roles = [];
        foreach ($chiTietQuyen as $item) {
            if (!isset($roles[$item->chucnang])) {
                $roles[$item->chucnang] = [];
            }
            $roles[$item->chucnang][] = $item->hanhdong;
        }

        return $roles;
    }

    /**
     * Tạo người dùng mới
     */
    public function createUser($id, $email, $fullname, $password, $ngaysinh, $gioitinh, $role, $trangthai, $makhoa = null)
    {
        $data = [
            'id' => $id,
            'email' => $email,
            'hoten' => $fullname,
            'password' => Hash::make($password),
            'ngaysinh' => $ngaysinh,
            'gioitinh' => $gioitinh,
            'manhomquyen' => $role,
            'trangthai' => $trangthai,
            'makhoa' => $makhoa,
        ];

        return self::create($data);
    }

    /**
     * Cập nhật người dùng
     */
    public function updateUser($id, $email, $fullname, $password, $ngaysinh, $gioitinh, $role, $trangthai, $makhoa = null)
    {
        $user = self::find($id);
        if (!$user) return false;

        $data = [
            'email' => $email,
            'hoten' => $fullname,
            'ngaysinh' => $ngaysinh,
            'gioitinh' => $gioitinh,
            'manhomquyen' => $role,
            'trangthai' => $trangthai,
            'makhoa' => $makhoa,
        ];

        if ($password != '') {
            $data['password'] = Hash::make($password);
        }

        return $user->update($data);
    }

    /**
     * Xóa người dùng
     */
    public function deleteUser($id)
    {
        $user = self::find($id);
        return $user ? $user->delete() : false;
    }

    /**
     * Kiểm tra user tồn tại qua MSSV hoặc Email
     */
    public function checkUser($mssv, $email)
    {
        return self::where('id', $mssv)
                   ->orWhere('email', $email)
                   ->get();
    }

    /**
     * Cập nhật avatar
     */
    public function uploadAvatar($id, $tmpName, $imageExtension, $validImageExtension, $name)
    {
        if (!in_array($imageExtension, $validImageExtension)) {
            return false;
        }

        $newImageName = $name . "-" . uniqid() . '.' . $imageExtension;
        $destinationPath = public_path('media/avatars');

        // Tạo thư mục nếu chưa có
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        move_uploaded_file($tmpName, $destinationPath . '/' . $newImageName);
        
        return self::find($id)->update(['avatar' => $newImageName]);
    }

    /**
     * Cập nhật thông tin hồ sơ
     */
    public function updateProfile($fullname, $gioitinh, $ngaysinh, $email, $id)
    {
        return self::find($id)->update([
            'email' => $email,
            'hoten' => $fullname,
            'gioitinh' => $gioitinh,
            'ngaysinh' => $ngaysinh
        ]);
    }

    /**
     * Thay đổi mật khẩu
     */
    public function changePassword($id, $newPassword)
    {
        $user = self::find($id);
        if (!$user) return false;
        
        $user->password = Hash::make($newPassword);
        return $user->save();
    }

    /**
     * Kiểm tra mật khẩu cũ
     */
    public function checkPassword($id, $password)
    {
        $user = self::find($id);
        return Hash::check($password, $user->password);
    }

    /**
     * Kiểm tra email đã tồn tại chưa (trừ user hiện tại)
     */
    public function checkEmailExist($email, $excludeId = null)
    {
        $query = self::where('email', $email);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    /**
     * Lấy OTP
     */
    public function getOTP($email)
    {
        $user = self::where('email', $email)->first();
        return $user ? $user->otp : null;
    }

    /**
     * Cập nhật OTP
     */
    public function updateOTP($email, $otp)
    {
        return self::where('email', $email)->update(['otp' => $otp]);
    }

    /**
     * Kiểm tra OTP
     */
    public function checkOTP($email, $otp)
    {
        return self::where('email', $email)->where('otp', $otp)->exists();
    }

    /**
     * Thêm nhiều user từ file Excel
     */
    public function addFile($data, $pass)
    {
        foreach ($data as $user) {
            self::create([
                'id' => $user['mssv'],
                'email' => $user['email'],
                'hoten' => $user['fullname'],
                'password' => Hash::make($pass),
                'trangthai' => $user['trangthai'],
                'manhomquyen' => $user['nhomquyen'],
                'gioitinh' => 1,
                'ngaysinh' => null,
            ]);
        }
        return true;
    }

    /**
     * Lấy tất cả nhóm quyền đang hoạt động
     */
    public function getAllRoles()
    {
        return \App\Models\NhomQuyenModel::where('trangthai', 1)->get();
    }
}

