<?php

namespace Database\Seeders;

use App\Models\User;
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
        $this->call(NhomQuyenSeeder::class);
        // User::factory(10)->create();

        \DB::table('users')->insert([
            [
                'id' => '1',
                'hoten' => 'Test',
                'email' => 'test@gmail.com',
                'gioitinh' => 0,
                'ngaysinh' => '1989-12-01',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 1,
            ],
            [
                'id' => 'DH123456',
                'hoten' => 'Thay hung',
                'email' => 'hung@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '1989-01-01',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 1,
            ],
            [
                'id' => 'DH52200608',
                'hoten' => 'Trịnh Minh Giàu',
                'email' => 'giau@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-02-27',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 2,
            ],
            [
                'id' => 'DH52200854',
                'hoten' => 'Võ Lê Minh Khang',
                'email' => 'khang@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-10-26',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 2,
            ],
            [
                'id' => 'DH52200855',
                'hoten' => 'Nguyễn Hoàng Khoa',
                'email' => 'khoa@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-06-13',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 3,
            ],
            [
                'id' => 'DH52201068',
                'hoten' => 'Vũ Thành Nhật Minh',
                'email' => 'minh@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-07-22',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 1,
            ],
            [
                'id' => 'DH52201508',
                'hoten' => 'Võ Lê Minh Thịnh',
                'email' => 'thinh@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-10-26',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 2,
            ],
            [
                'id' => 'DH52201659',
                'hoten' => 'Phan Thanh Trọng',
                'email' => 'trong@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-01-28',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 1,
            ],
            [
                'id' => 'DH1111111',
                'hoten' => 'admin',
                'email' => 'admin@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-01-28',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 1,
            ],
            [
                'id' => 'DH222222',
                'hoten' => 'Giáo viên',
                'email' => 'giaovien@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-02-27',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 2,
            ],
            [
                'id' => 'DH333333',
                'hoten' => 'Sinh Viên',
                'email' => 'sinhvien@gmail.com',
                'gioitinh' => 1,
                'ngaysinh' => '2004-02-27',
                'password' => Hash::make('123456'),
                'trangthai' => 1,
                'sodienthoai' => null,
                'manhomquyen' => 3,
            ],
        ]);
        $this->call(DanhMucChucNangSeeder::class);
        $this->call(ChiTietQuyenSeeder::class);
    }
}
