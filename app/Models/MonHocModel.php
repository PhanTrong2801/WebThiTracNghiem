<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonHocModel extends Model
{
    use HasFactory;

    protected $table = 'monhoc';

    // Laravel mặc định id là PK tự tăng. Migration của bạn có $table->id() và $table->string('mamonhoc')->unique()
    // Vì vậy id vẫn là PK, mamonhoc là mã định danh duy nhất.

    protected $fillable = [
        'mamonhoc',
        'tenmonhoc',
        'makhoa',
        'sotinchi',
        'sotietlythuyet',
        'sotietthuchanh',
        'trangthai',
    ];

    public $timestamps = false;

    /**
     * Thuộc về khoa nào
     */
    public function khoa()
    {
        return $this->belongsTo(KhoaModel::class, 'makhoa', 'id');
    }

    /**
     * Scope lọc các môn học đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trangthai', 1);
    }
}
