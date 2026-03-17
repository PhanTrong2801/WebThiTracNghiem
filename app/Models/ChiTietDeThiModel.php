<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDeThiModel extends Model
{
    use HasFactory;

    protected $table = 'chitietdethi';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'made',
        'macauhoi',
        'thutu',
    ];

    public function dethi()
    {
        return $this->belongsTo(DeThiModel::class, 'made', 'made');
    }

    public function cauhoi()
    {
        return $this->belongsTo(CauHoiModel::class, 'macauhoi', 'macauhoi');
    }
}

