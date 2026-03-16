<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhomModel extends Model
{
    use HasFactory;

    protected $table = 'nhom';
    protected $primaryKey = 'manhom';

    protected $fillable = [
        'tennhom',
        'mamoi',
        'siso',
        'ghichu',
        'namhoc',
        'hocky',
        'trangthai',
        'hienthi',
        'giangvien',
        'mamonhoc',
    ];

    public $timestamps = false;

    /**
     * Môn học của nhóm
     */
    public function monhoc()
    {
        return $this->belongsTo(MonHocModel::class, 'mamonhoc', 'mamonhoc');
    }

    /**
     * Giảng viên phụ trách
     */
    public function giangVienUser()
    {
        return $this->belongsTo(UserModel::class, 'giangvien', 'id');
    }

    /**
     * Chi tiết nhóm (sinh viên)
     */
    public function chitietnhom()
    {
        return $this->hasMany(ChiTietNhomModel::class, 'manhom', 'manhom');
    }

    /**
     * Sinh viên trong nhóm (many-to-many thông qua chitietnhom)
     */
    public function sinhvien()
    {
        return $this->belongsToMany(
            UserModel::class,
            'chitietnhom',
            'manhom',
            'manguoidung'
        )->withPivot('hienthi');
    }

    /**
     * Scope: chỉ nhóm đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trangthai', 1);
    }
}
