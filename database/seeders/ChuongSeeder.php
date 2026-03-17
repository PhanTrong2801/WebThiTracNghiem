<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chapters = [
            // Lập trình web (CS03036)
            ['CS03036', 'Chương 1: Cơ bản về PHP'],
            ['CS03036', 'Chương 2: Biến và kiểu dữ liệu'],
            ['CS03036', 'Chương 3: Câu lệnh điều khiển'],
            ['CS03036', 'Chương 4: Vòng lặp'],
            ['CS03036', 'Chương 5: Mảng'],
            ['CS03036', 'Chương 6: Hàm'],
            ['CS03036', 'Chương 7: Xử lý chuỗi'],
            ['CS03036', 'Chương 8: Xử lý ngày tháng'],
            ['CS03036', 'Chương 9: Xử lý file'],
            ['CS03036', 'Chương 10: Lập trình hướng đối tượng (OOP)'],

            ['CS03015', 'Chương 1: Ngôn ngữ lập trình Java'],
            ['CS03015', 'Chương 2: Cú pháp cơ bản của Java'],


            // Nhập môn web và ứng dụng (CS09003)
            ['CS09003', 'Tổng quan về World Wide Web'],
            ['CS09003', 'Ngôn ngữ đánh dấu văn bản HTML'],
            ['CS09003', 'Định dạng trang web với CSS'],
            ['CS09003', 'Cơ bản về JavaScript'],
            ['CS09003', 'DOM và xử lý sự kiện'],

            // Tiếng Anh 1 (GS19007)
            ['GS19007', 'Unit 1: Nice to meet you'],
            ['GS19007', 'Unit 2: Work and study'],
            ['GS19007', 'Unit 3: Daily life'],
            ['GS19007', 'Unit 4: Free time'],
            ['GS19007', 'Unit 5: Places'],

            // Toán A1 (Toán 1) (GS33001)
            ['GS33001', 'Chương 1: Tập hợp và ánh xạ'],
            ['GS33001', 'Chương 2: Số phức'],
            ['GS33001', 'Chương 3: Giới hạn và sự liên tục của hàm số'],
            ['GS33001', 'Chương 4: Đạo hàm và vi phân'],
            ['GS33001', 'Chương 5: Các định lý về hàm khả vi'],

            // Kỹ thuật số (CS03001)
            ['CS03001', 'Chương 1: Hệ thống số và mã'],
            ['CS03001', 'Chương 2: Đại số Boole và các cổng logic'],
            ['CS03001', 'Chương 3: Tối thiểu hóa hàm Boole'],
            ['CS03001', 'Chương 4: Mạch logic tổ hợp'],
            ['CS03001', 'Chương 5: Flip-Flops và các thiết bị liên quan'],

            // Cơ sở dữ liệu (CS03008)
            ['CS03008', 'Chương 1: Tổng quan về hệ cơ sở dữ liệu'],
            ['CS03008', 'Chương 2: Mô hình thực thể liên kết (ER)'],
            ['CS03008', 'Chương 3: Mô hình dữ liệu quan hệ'],
            ['CS03008', 'Chương 4: Ngôn ngữ SQL'],
            ['CS03008', 'Chương 5: Ràng buộc toàn vẹn'],

            // Công nghệ phần mềm (CS03013)
            ['CS03013', 'Chương 1: Tổng quan về Công nghệ phần mềm'],
            ['CS03013', 'Chương 2: Quy trình phần mềm'],
            ['CS03013', 'Chương 3: Phân tích yêu cầu'],
            ['CS03013', 'Chương 4: Thiết kế phần mềm'],
            ['CS03013', 'Chương 5: Kiểm thử và bảo trì'],

        ];

        foreach ($chapters as $chapter) {
            DB::table('chuong')->insert([
                'mamonhoc' => $chapter[0],
                'tenchuong' => $chapter[1],
                'trangthai' => 1,
            ]);
        }
    }
}
