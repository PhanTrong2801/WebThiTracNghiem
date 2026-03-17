<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaModel extends Model
{
    use HasFactory;

    protected $table = 'ketqua';
    protected $primaryKey = 'makq';
    public $timestamps = false;

    protected $fillable = [
        'made',
        'manguoidung',
        'diemthi',
        'thoigianvaothi',
        'thoigianlambai',
        'socaudung',
        'solanchuyentab',
    ];

    protected $casts = [
        'thoigianvaothi' => 'datetime',
    ];

    public function dethi()
    {
        return $this->belongsTo(DeThiModel::class, 'made', 'made');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'manguoidung', 'id');
    }

    public function chitiet()
    {
        return $this->hasMany(ChiTietKetQuaModel::class, 'makq', 'makq');
    }
}

