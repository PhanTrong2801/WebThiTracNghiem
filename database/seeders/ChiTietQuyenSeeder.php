<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Nhóm quyền 1
            ['manhomquyen' => 1, 'chucnang' => 'cauhoi', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'cauhoi', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'cauhoi', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'cauhoi', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'chuong', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'chuong', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'chuong', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'chuong', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'dethi', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'dethi', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'dethi', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'dethi', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'hocphan', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'hocphan', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'hocphan', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'hocphan', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'monhoc', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'monhoc', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'monhoc', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'monhoc', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'nguoidung', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'nguoidung', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'nguoidung', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'nguoidung', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'nhomquyen', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'nhomquyen', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'nhomquyen', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'nhomquyen', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'khoa', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'khoa', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'khoa', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'khoa', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'phancong', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'phancong', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'phancong', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'phancong', 'hanhdong' => 'view'],
            ['manhomquyen' => 1, 'chucnang' => 'thongbao', 'hanhdong' => 'create'],
            ['manhomquyen' => 1, 'chucnang' => 'thongbao', 'hanhdong' => 'delete'],
            ['manhomquyen' => 1, 'chucnang' => 'thongbao', 'hanhdong' => 'update'],
            ['manhomquyen' => 1, 'chucnang' => 'thongbao', 'hanhdong' => 'view'],

            // Nhóm quyền 2
            ['manhomquyen' => 2, 'chucnang' => 'cauhoi', 'hanhdong' => 'create'],
            ['manhomquyen' => 2, 'chucnang' => 'cauhoi', 'hanhdong' => 'view'],
            ['manhomquyen' => 2, 'chucnang' => 'dethi', 'hanhdong' => 'create'],
            ['manhomquyen' => 2, 'chucnang' => 'dethi', 'hanhdong' => 'delete'],
            ['manhomquyen' => 2, 'chucnang' => 'dethi', 'hanhdong' => 'update'],
            ['manhomquyen' => 2, 'chucnang' => 'dethi', 'hanhdong' => 'view'],
            ['manhomquyen' => 2, 'chucnang' => 'hocphan', 'hanhdong' => 'create'],
            ['manhomquyen' => 2, 'chucnang' => 'hocphan', 'hanhdong' => 'delete'],
            ['manhomquyen' => 2, 'chucnang' => 'hocphan', 'hanhdong' => 'update'],
            ['manhomquyen' => 2, 'chucnang' => 'hocphan', 'hanhdong' => 'view'],
            ['manhomquyen' => 2, 'chucnang' => 'tghocphan', 'hanhdong' => 'join'],
            ['manhomquyen' => 2, 'chucnang' => 'tgthi', 'hanhdong' => 'join'],
            ['manhomquyen' => 2, 'chucnang' => 'thongbao', 'hanhdong' => 'create'],
            ['manhomquyen' => 2, 'chucnang' => 'thongbao', 'hanhdong' => 'delete'],
            ['manhomquyen' => 2, 'chucnang' => 'thongbao', 'hanhdong' => 'update'],
            ['manhomquyen' => 2, 'chucnang' => 'thongbao', 'hanhdong' => 'view'],
            ['manhomquyen' => 2, 'chucnang' => 'chuong', 'hanhdong' => 'view'],

            // Nhóm quyền 3
            ['manhomquyen' => 3, 'chucnang' => 'tghocphan', 'hanhdong' => 'join'],
            ['manhomquyen' => 3, 'chucnang' => 'tgthi', 'hanhdong' => 'join'],
        ];

        DB::table('chitietquyen')->insert($data);
    }
}
