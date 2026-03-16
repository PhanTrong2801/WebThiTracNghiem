<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuongModel extends Model
{
    use HasFactory;

    protected $table = 'chuong';
    protected $primaryKey = 'machuong';

    protected $fillable = [
        'tenchuong',
        'mamonhoc',
        'trangthai',
    ];

    public $timestamps = false; // Based on migration, there are no timestamps

    /**
     * Thuộc về môn học nào
     */
    public function monhoc()
    {
        return $this->belongsTo(MonHocModel::class, 'mamonhoc', 'mamonhoc');
    }

    /**
     * Scope lọc các chương đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trangthai', 1);
    }
}
