<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(KhoaSeeder::class);
        $this->call(NhomQuyenSeeder::class);

        // User::factory(10)->create();
        $this->call(UsersSeeder::class);

        $this->call(DanhMucChucNangSeeder::class);
        $this->call(ChiTietQuyenSeeder::class);
        $this->call(MonHocSeeder::class);
        $this->call(ChuongSeeder::class);
        $this->call(CauHoiSeeder::class);
        $this->call(CauTraLoiSeeder::class);

        // Phân công giảng viên
        DB::table('phancong')->insertOrIgnore([
            'mamonhoc' => 'CS03015',
            'manguoidung' => 'DH1111111',
        ]);

        // Tạo nhóm học phần: LTHDT T4
        $manhom = DB::table('nhom')->insertGetId([
            'tennhom' => 'LTHDT T4',
            'mamoi' => 'LTHDT_T4_2025_1',
            'siso' => 1,
            'ghichu' => 'Ca 4',
            'namhoc' => 2025,
            'hocky' => 1,
            'trangthai' => 1,
            'hienthi' => 1,
            'giangvien' => 'DH1111111',
            'mamonhoc' => 'CS03015',
        ]);

        // Thêm sinh viên DH333333 vào nhóm học phần
        DB::table('chitietnhom')->insertOrIgnore([
            'manhom' => $manhom,
            'manguoidung' => 'DH333333',
            'hienthi' => 1,
        ]);
    }
}
