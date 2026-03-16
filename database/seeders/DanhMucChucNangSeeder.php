<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhMucChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danhmucchucnang')->insert([
            ['chucnang' => 'caidat', 'tenchucnang' => 'Cài đặt'],
            ['chucnang' => 'cauhoi', 'tenchucnang' => 'Quản lý câu hỏi'],
            ['chucnang' => 'chuong', 'tenchucnang' => 'Quản lý chương'],
            ['chucnang' => 'dethi', 'tenchucnang' => 'Quản lý đề thi'],
            ['chucnang' => 'hocphan', 'tenchucnang' => 'Quản lý học phần'],
            ['chucnang' => 'khoa', 'tenchucnang' => 'Quản lý ngành/khoa'],
            ['chucnang' => 'monhoc', 'tenchucnang' => 'Quản lý môn học'],
            ['chucnang' => 'nguoidung', 'tenchucnang' => 'Quản lý người dùng'],
            ['chucnang' => 'nhomquyen', 'tenchucnang' => 'Quản lý nhóm quyền'],
            ['chucnang' => 'phancong', 'tenchucnang' => 'Quản lý phân công'],
            ['chucnang' => 'sinhvien', 'tenchucnang' => 'Sinh viên'],
            ['chucnang' => 'tghocphan', 'tenchucnang' => 'Tham gia học phần'],
            ['chucnang' => 'tgthi', 'tenchucnang' => 'Tham gia thi'],
            ['chucnang' => 'thongbao', 'tenchucnang' => 'Thông báo'],
        ]);
    }
}
