<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCongModel extends Model
{
    protected $table = 'phancong';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'mamonhoc',
        'manguoidung',
    ];

    public function monhoc()
    {
        return $this->belongsTo(MonHocModel::class, 'mamonhoc', 'mamonhoc');
    }

    public function nguoidung()
    {
        return $this->belongsTo(UserModel::class, 'manguoidung', 'id');
    }
}
