<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('khoa')->insert([
            ['tenkhoa' => 'Công nghệ thông tin'],
            ['tenkhoa' => 'Quản trị kinh doanh'],
            ['tenkhoa' => 'Công nghệ thực phẩm'],
            ['tenkhoa' => 'Thiết kế đồ họa'],
            ['tenkhoa' => 'Công nghệ kỹ thuật điện, điện tử'],
            ['tenkhoa' => 'Quản lý xây dựng'],
        ]);
    }


}
