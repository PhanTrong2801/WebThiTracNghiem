<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietNhomModel extends Model
{
    protected $table = 'chitietnhom';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'manhom',
        'manguoidung',
        'hienthi',
    ];

    public function nhom()
    {
        return $this->belongsTo(NhomModel::class, 'manhom', 'manhom');
    }

    public function nguoidung()
    {
        return $this->belongsTo(UserModel::class, 'manguoidung', 'id');
    }
}
