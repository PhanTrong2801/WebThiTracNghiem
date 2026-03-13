<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhomQuyen extends Model
{
    protected $table = 'nhomquyen';
    protected $primaryKey = 'manhomquyen';
    public $timestamps = false;

    protected $fillable = ['tennhomquyen', 'trangthai'];

    /**
     * Lấy danh sách chi tiết quyền
     */
    public function chiTietQuyen()
    {
        return $this->hasMany(ChiTietQuyen::class, 'manhomquyen', 'manhomquyen');
    }

    /**
     * Lấy danh sách người dùng thuộc nhóm quyền
     */
    public function users()
    {
        return $this->hasMany(User::class, 'manhomquyen', 'manhomquyen');
    }

    /**
     * Scope: chỉ lấy nhóm quyền đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trangthai', 1);
    }
}
