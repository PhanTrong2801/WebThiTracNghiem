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
        $this->call(KhoaSeeder::class);
        $this->call(NhomQuyenSeeder::class);

        // User::factory(10)->create();
        $this->call(UsersSeeder::class);
        $this->call(DanhMucChucNangSeeder::class);
        $this->call(ChiTietQuyenSeeder::class);
        $this->call(MonHocSeeder::class);
    }
}
