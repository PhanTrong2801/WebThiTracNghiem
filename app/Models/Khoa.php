<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;

    protected $table = 'khoa';

    public $timestamps = false;

    protected $fillable = [
        'tenkhoa',
    ];

    /**
     * Danh sách user thuộc khoa
     */
    public function users()
    {
        return $this->hasMany(User::class, 'makhoa', 'id');
    }
}
