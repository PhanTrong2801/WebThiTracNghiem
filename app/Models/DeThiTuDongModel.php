<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeThiTuDongModel extends Model
{
    use HasFactory;

    protected $table = 'dethitudong';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'made',
        'machuong',
    ];

    public function dethi()
    {
        return $this->belongsTo(DeThiModel::class, 'made', 'made');
    }

    public function chuong()
    {
        return $this->belongsTo(ChuongModel::class, 'machuong', 'machuong');
    }
}

