<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietQuyenModel extends Model
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
        return $this->belongsTo(NhomQuyenModel::class, 'manhomquyen', 'manhomquyen');
    }

    /**
     * Danh mục chức năng
     */
    public function danhMucChucNang()
    {
        return $this->belongsTo(DanhMucChucNangModel::class, 'chucnang', 'chucnang');
    }
}
