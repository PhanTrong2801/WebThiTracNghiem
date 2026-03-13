<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietQuyen extends Model
{
    protected $table = 'chitietquyen';
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = null; // Composite PK - không dùng single PK

    protected $fillable = ['manhomquyen', 'chucnang', 'hanhdong'];

    /**
     * Nhóm quyền chứa chi tiết này
     */
    public function nhomQuyen()
    {
        return $this->belongsTo(NhomQuyen::class, 'manhomquyen', 'manhomquyen');
    }

    /**
     * Danh mục chức năng
     */
    public function danhMucChucNang()
    {
        return $this->belongsTo(DanhMucChucNang::class, 'chucnang', 'chucnang');
    }
}
