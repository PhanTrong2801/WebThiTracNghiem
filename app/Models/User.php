<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'sodienthoai',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'token',
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
    public function nhomQuyen()
    {
        return $this->belongsTo(NhomQuyen::class, 'manhomquyen', 'manhomquyen');
    }

    /**
     * Lấy danh sách quyền chi tiết dạng ["tghocphan" => ["join", "view"], ...]
     */
    public function getRolePermissions()
    {
        if (!$this->manhomquyen) {
            return [];
        }

        $chiTietQuyen = \App\Models\ChiTietQuyen::where('manhomquyen', $this->manhomquyen)->get();
        $roles = [];
        foreach ($chiTietQuyen as $item) {
            if (!isset($roles[$item->chucnang])) {
                $roles[$item->chucnang] = [];
            }
            $roles[$item->chucnang][] = $item->hanhdong;
        }

        return $roles;
    }
}

