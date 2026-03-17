<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietKetQuaModel extends Model
{
    use HasFactory;

    protected $table = 'chitietketqua';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'makq',
        'macauhoi',
        'dapanchon',
    ];

    public function ketqua()
    {
        return $this->belongsTo(KetQuaModel::class, 'makq', 'makq');
    }

    public function cauhoi()
    {
        return $this->belongsTo(CauHoiModel::class, 'macauhoi', 'macauhoi');
    }

    public function cautraloiChon()
    {
        return $this->belongsTo(CauTraLoiModel::class, 'dapanchon', 'macautl');
    }
}

