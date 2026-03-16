<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonHocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        $data = [
            // GS - makhoa: null
            ['GS33001', 'Toán A1 (Hàm 1 biến, chuỗi)', null, 4, 45, 15],
            ['GS33002', 'Toán A2 (Hàm nhiều biến, giải tích vec tơ)', null, 4, 45, 15],
            ['GS33003', 'Toán A3 (Đại số tuyến tính)', null, 3, 30, 15],
            ['GS59001', 'Tin học đại cương', null, 2, 30, 30],
            ['GS59002', 'Thực hành Tin học đại cương', null, 1, 45, 0],
            ['GS19007', 'Tiếng Anh 1', null, 2, 15, 30],
            ['GS19008', 'Tiếng Anh 2', null, 2, 15, 30],
            ['GS19009', 'Tiếng Anh 3', null, 2, 15, 30],
            ['GS19010', 'Tiếng Anh 4', null, 2, 15, 30],
            ['GS29001', 'Pháp luật Việt Nam đại cương', null, 3, 30, 15],
            ['GS79005', 'Triết học Mác – Lênin', null, 3, 45, 0],
            ['GS79006', 'Kinh tế chính trị Mác – Lênin', null, 2, 30, 0],
            ['GS79007', 'Chủ nghĩa xã hội khoa học', null, 2, 30, 0],
            ['GS79008', 'Lịch sử Đảng cộng sản Việt Nam', null, 2, 30, 0],
            ['GS79009', 'Tư tưởng Hồ Chí Minh', null, 2, 30, 0],

            // CS - makhoa: 1
            ['CS03047', 'Nhập môn công tác kỹ sư', 1, 2, 30, 0],
            ['CS03001', 'Kỹ thuật số', 1, 2, 15, 15],
            ['CS03002', 'Thí nghiệm Kỹ thuật số', 1, 1, 0, 30],
            ['CS03003', 'Kỹ thuật lập trình', 1, 3, 30, 15],
            ['CS03004', 'Thực hành Kỹ thuật lập trình', 1, 1, 0, 30],
            ['CS03005', 'Toán tin học', 1, 3, 30, 15],
            ['CS03007', 'Cấu trúc dữ liệu và thuật giải', 1, 3, 30, 15],
            ['CS03008', 'Cơ sở dữ liệu', 1, 3, 30, 15],
            ['CS03009', 'Hệ điều hành', 1, 3, 30, 15],
            ['CS03010', 'Thực hành Cấu trúc dữ liệu và thuật giải', 1, 1, 0, 30],
            ['CS03011', 'Thực hành Cơ sở dữ liệu', 1, 1, 0, 30],
            ['CS03012', 'Thực hành Hệ điều hành 1', 1, 1, 0, 30],
            ['CS03013', 'Công nghệ phần mềm', 1, 3, 30, 15],
            ['CS03014', 'Đồ án tin học', 1, 2, 0, 90],
            ['CS03015', 'Lập trình hướng đối tượng', 1, 3, 30, 15],
            ['CS03016', 'Thực hành Lập trình hướng đối tượng', 1, 1, 0, 30],
            ['CS09001', 'Nhập môn lập trình', 1, 3, 30, 15],
            ['CS09002', 'Thực hành Nhập môn lập trình', 1, 1, 0, 30],
            ['CS09003', 'Nhập môn Web và ứng dụng', 1, 3, 30, 15],
            ['CS09004', 'Thực hành Nhập môn Web và ứng dụng', 1, 1, 0, 30],
            ['CS03036', 'Lập trình web', 1, 3, 30, 15],

            // BA - makhoa: 2
            ['BA19001', 'Kinh tế vi mô', 2, 3, 45, 30],
            ['BA49001', 'Quản trị học', 2, 3, 45, 30],
            ['BA19002', 'Kinh tế vĩ mô', 2, 3, 45, 30],
            ['BA39002', 'Lý thuyết Tài chính - Tiền tệ', 2, 3, 45, 30],
            ['BA39001', 'Nguyên lý kế toán', 2, 3, 45, 30],
            ['BA49003', 'Giao tiếp kinh doanh', 2, 3, 45, 30],
            ['BA49004', 'Luật kinh tế', 2, 3, 45, 30],
            ['BA19003', 'Phân tích dữ liệu kinh doanh', 2, 3, 45, 30],
            ['BA29001', 'Marketing căn bản', 2, 3, 45, 30],
            ['BA39003', 'Thuế', 2, 2, 45, 15],

            // FT - makhoa: 3
            ['FT03003', 'TH Vi sinh đại cương', 3, 1, 0, 30],
            ['FT03005', 'Vi sinh đại cương', 3, 2, 30, 0],
            ['FT03006', 'Hóa lý', 3, 3, 30, 15],
            ['FT09002', 'Hóa học thực phẩm', 3, 4, 60, 0],
            ['FT09004', 'TH Hóa học thực phẩm', 3, 1, 0, 30],
            ['FT03004', 'Vật lý thực phẩm', 3, 2, 30, 0],
            ['FT03007', 'Hóa sinh thực phẩm', 3, 3, 30, 15],
            ['FT03042', 'Vi sinh Thực phẩm', 3, 2, 30, 0],
            ['FT09005', 'TH Vi sinh thực phẩm', 3, 1, 0, 30],
            ['FT03009', 'Phụ gia thực phẩm', 3, 2, 30, 0],
            ['FT09006', 'Dinh dưỡng', 3, 2, 15, 15],
        ];

        foreach ($data as $item) {
            DB::table('monhoc')->insert([
                'mamonhoc' => $item[0],
                'tenmonhoc' => $item[1],
                'makhoa' => $item[2],
                'sotinchi' => $item[3],
                'sotietlythuyet' => $item[4],
                'sotietthuchanh' => $item[5],
                'trangthai' => 1,
            ]);
        }
    }
}
