<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHoiModel extends Model
{
    use HasFactory;

    protected $table = 'cauhoi';
    protected $primaryKey = 'macauhoi';

    protected $fillable = [
        'noidung',
        'dokho',
        'mamonhoc',
        'machuong',
        'nguoitao',
        'trangthai',
    ];

    public $timestamps = false;

    /**
     * Môn học của câu hỏi
     */
    public function monhoc()
    {
        return $this->belongsTo(MonHocModel::class, 'mamonhoc', 'mamonhoc');
    }

    /**
     * Chương của câu hỏi
     */
    public function chuong()
    {
        return $this->belongsTo(ChuongModel::class, 'machuong', 'machuong');
    }

    /**
     * Người tạo câu hỏi
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'nguoitao', 'id');
    }

    /**
     * Các câu trả lời
     */
    public function cautraloi()
    {
        return $this->hasMany(CauTraLoiModel::class, 'macauhoi', 'macauhoi');
    }

    /**
     * Scope lọc các câu đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trangthai', 1);
    }
}
