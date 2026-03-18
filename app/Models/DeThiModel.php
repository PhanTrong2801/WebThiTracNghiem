<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeThiModel extends Model
{
    use HasFactory;

    protected $table = 'dethi';
    protected $primaryKey = 'made';
    public $timestamps = false;

    protected $fillable = [
        'monthi',
        'nguoitao',
        'tende',
        'thoigiantao',
        'thoigianthi',
        'thoigianbatdau',
        'thoigianketthuc',
        'hienthibailam',
        'xemdiemthi',
        'xemdapan',
        'troncauhoi',
        'trondapan',
        'nopbaichuyentab',
        'loaide',
        'socaude',
        'socautb',
        'socaukho',
        'trangthai',
        'duocday',
    ];

    protected $casts = [
        'thoigiantao' => 'datetime',
        'thoigianbatdau' => 'datetime',
        'thoigianketthuc' => 'datetime',
        'hienthibailam' => 'boolean',
        'xemdiemthi' => 'boolean',
        'xemdapan' => 'boolean',
        'troncauhoi' => 'boolean',
        'trondapan' => 'boolean',
        'nopbaichuyentab' => 'boolean',
        'trangthai' => 'boolean',
    ];

    public function monhoc()
    {
        return $this->belongsTo(MonHocModel::class, 'monthi', 'mamonhoc');
    }

    public function nguoitaoUser()
    {
        return $this->belongsTo(UserModel::class, 'nguoitao', 'id');
    }

    public function cauhoi()
    {
        return $this->belongsToMany(CauHoiModel::class, 'chitietdethi', 'made', 'macauhoi')
            ->withPivot(['thutu'])
            ->orderBy('pivot_thutu');
    }

    public function ketqua()
    {
        return $this->hasMany(KetQuaModel::class, 'made', 'made');
    }

    public function nhomDuocGiao()
    {
        return $this->belongsToMany(NhomModel::class, 'giaodethi', 'made', 'manhom');
    }
}

