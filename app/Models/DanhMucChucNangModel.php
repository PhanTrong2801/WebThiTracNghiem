<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucChucNangModel extends Model
{
    protected $table = 'danhmucchucnang';
    protected $primaryKey = 'chucnang';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['chucnang', 'tenchucnang'];
}
