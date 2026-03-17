<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoDeThiModel extends Model
{
    use HasFactory;

    protected $table = 'giaodethi';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'made',
        'manhom',
    ];

    public function dethi()
    {
        return $this->belongsTo(DeThiModel::class, 'made', 'made');
    }

    public function nhom()
    {
        return $this->belongsTo(NhomModel::class, 'manhom', 'manhom');
    }
}

