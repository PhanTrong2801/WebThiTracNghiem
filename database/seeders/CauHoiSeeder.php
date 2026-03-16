<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CauHoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            'OOP là viết tắt của:',
            'Đặc điểm cơ bản của lập trình hướng đối tượng thể hiện ở:',
            'Lập trình hướng đối tượng là:',
            'Thế nào được gọi là hiện tượng nạp chồng ?',
            'Trong java, khi khai báo một thuộc tính hoặc một hàm của một lớp mà không có từ khóa quyền truy cập thì mặc định quyền truy cập là gì?',
            'Đối với quyền truy cập nào thì cho phép truy cập các lớp con trong cùng gói với lớp cha ?',
            'Khi xây dựng phương thức khởi tạo (constructor), việc thường làm là :',
            'Phương thức khởi tạo (constructor) là phương thức được thực thi :',
            'Tên của phương thức khởi tạo:',
            'Đối tượng sống kể từ khi:',
            'Chọn câu đúng nhất đối với hướng dẫn tạo lớp:',
            'Từ khóa static có thể đứng trước:',
            'Cho biết kết quả câu lệnh sau: System.out.println(Math.round(Math.random()*1000000)%100);',
            'Khi định nghĩa lớp con, từ khóa extends trong Java:',
            'Khi định nghĩa một lớp con:',
            'Từ khóa this trong Java là:',
            'Nếu modifier của lớp là public thì tên file .java:',
            'Biến đối tượng trong Java là:',
            'Khi một thành phần của class được khai báo modifier là friendly thì thành phần đó:',
            'Ưu điểm của OOP:',
            'Ưu điểm của OOP (Duplicate nội dung từ yêu cầu):',
            'Ưu điểm của class file trong Java là:',
            'Source code của java có tên mở rộng là:',
            'JDK bao gồm các thành phần chính:',
            'Lớp Student có các thuộc tính: name, age và các phương thức: getName(), getAge(). Giả sử x là một đối tượng thuộc lớp Student. Chọn phát biểu đúng trong OOP:',
            'Bao đóng là một đặt tính của OOP nhằm để:',
            'Các từ khóa cho cấu trúc rẽ nhánh của Java gồm:',
            'Các hằng trong Java gồm:',
            'Tên đầu tiên của Java là gì?',
            'Giả sử đã định nghĩa lớp XX với một phương thức thông thường là Display, sau đó sinh ra đối tượng objX từ lớp XX. Để gọi phương thức Display ta sử dụng cú pháp nào ?',
            'Đối tượng là gì ?',
            'Từ khoá nào được sử dụng để khai báo một phương thức trong Java?',
            'Từ khoá nào được sử dụng để khai báo một biến trong Java?',
            'Từ khoá nào được sử dụng để khai báo một phương thức làm việc với đối tượng của lớp?',
            'Từ khoá nào được sử dụng để khai báo một phương thức được ghi đè trong Java?',
            'Từ khoá nào được sử dụng để khai báo một phương thức được gọi tự động khi một đối tượng được tạo ra?',
            'Từ khoá nào được sử dụng để khai báo một phương thức hoạt động như một lớp trừu tượng?',
            'Từ khoá nào được sử dụng để khai báo một lớp trừu tượng trong Java?',
            'Trong Java, lớp con là gì?',
            'Đặc điểm của Tính bao gói trong Lập trình hướng đối tượng:',
            'Tính kế thừa trong lập trình hướng đối tượng:',
            'Trong kế thừa. Lớp mới có thuật ngữ tiếng Anh là:',
            'Trong kế thừa. Lớp cha có thuật ngữ tiếng Anh là:',
            'Đặc điểm của Tính đa hình ?',
            'Khái niệm Lớp đối tượng?',
            'Sau khi khai báo và xây dựng thành công lớp đối tượng Sinh viên. Khi đó lớp đối tượng Sinh viên còn được gọi là :',
            'Trong các phương án sau, phương án nào mô tả đối tượng:',
            'Muốn lập trình hướng đối tượng, bạn cần phải phân tích chương trình, bài toán thành các:',
            'Trong phương án sau, phương án mô tả tính đa hình là',
            'Phương pháp lập trình tuần tự là:',
            'Phương pháp lập trình cấu trúc là:',
            'Khi khai báo và xây dựng thành công lớp đối tượng, để truy cập vào thành phần của lớp ta phải:',
            'Phương pháp lập trình module là:',
            'Khái niệm Trừu tượng hóa:',
            'Khi khai báo và xây dựng một lớp ta cần phải xác định rõ thành phần:',
            'Chọn câu đúng về thành phần public của lớp:',
            'Khi khai báo lớp trong các ngôn ngữ lập trình hướng đối tượng phải sử dụng từ khóa:',
            'Khái niệm của Phương thức là:',
            'Cho lớp người hãy xác định đâu là các thuộc tính của lớp người:',
            'Cho lớp Điểm trong hệ tọa độ xOy. Các phương thức có thể của lớp điểm là:',
        ];

        foreach ($questions as $index => $noidung) {
            DB::table('cauhoi')->insert([
                'macauhoi'   => $index + 1,
                'noidung'    => $noidung,
                'dokho'      => ($index % 3) + 1, 
                'mamonhoc'   => 'CS03015',
                'machuong'   => 11, 
                'nguoitao'   => 'DH1111111',
                'trangthai'  => 1,
            ]);
        }
    }
}
