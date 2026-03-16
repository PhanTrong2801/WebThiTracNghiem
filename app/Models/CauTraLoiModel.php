<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauTraLoiModel extends Model
{
    use HasFactory;

    protected $table = 'cautraloi';
    protected $primaryKey = 'macautl';

    protected $fillable = [
        'macauhoi',
        'noidungtl',
        'ladapan',
    ];

    public $timestamps = false;

    /**
     * Thuộc về câu hỏi nào
     */
    public function cauhoi()
    {
        return $this->belongsTo(CauHoiModel::class, 'macauhoi', 'macauhoi');
    }
}
