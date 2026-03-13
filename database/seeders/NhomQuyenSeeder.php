<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NhomQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        DB::table('nhomquyen')->insert([
            [
                'manhomquyen' => 1,
                'tennhomquyen' => 'Admin',
                'trangthai' => 1,
            ],
            [
                'manhomquyen' => 2,
                'tennhomquyen' => 'Giảng viên',
                'trangthai' => 1,
            ],
            [
                'manhomquyen' => 3,
                'tennhomquyen' => 'Sinh viên',
                'trangthai' => 1,
            ],
        ]);
    }
}
