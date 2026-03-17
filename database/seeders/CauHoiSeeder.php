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
        $questions_ch1 = [
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

        $questions_ch2 = [
            'Trong Java, kiểu dữ liệu nào sau đây dùng để lưu trữ số nguyên có kích thước lớn nhất?',
            'Đâu là cách khai báo biến hằng trong Java?',
            'Toán tử nào dùng để so sánh bằng trong Java?',
            'Phương thức nào dùng để xuất dữ liệu ra màn hình và kết thúc bằng một dòng mới?',
            'Chú thích trên nhiều dòng trong Java bắt đầu và kết thúc bằng ký tự nào?',
            'Kiểu dữ liệu char trong Java có kích thước bao nhiêu bit?',
            'Đâu là một tên biến hợp lệ trong Java?',
            'Kết quả của biểu thức 10 % 3 trong Java là gì?',
            'Kiểu dữ liệu nào được sử dụng để lưu trữ giá trị đúng hoặc sai?',
            'Từ khóa nào dùng để thoát khỏi vòng lặp hiện tại?',
            'Biểu thức 5 + "Java" sẽ cho kết quả là gì?',
            'Để ép kiểu từ long sang int trong Java, ta sử dụng cú pháp nào?',
            'Toán tử && đại diện cho phép toán logic nào?',
            'Trong Java, các biến cục bộ (local variables) phải được... trước khi sử dụng.',
            'Giá trị mặc định của một biến số nguyên (int) khi được khai báo là thuộc tính của lớp là?',
            'Hậu tố nào bắt buộc phải có khi khai báo một biến kiểu float?',
            'Đâu là toán tử tăng giá trị lên 1 đơn vị?',
            'Kiểu dữ liệu String trong Java là:',
        ];

        foreach ($questions_ch1 as $index => $noidung) {
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

        foreach ($questions_ch2 as $index => $noidung) {
            DB::table('cauhoi')->insert([
                'macauhoi'   => count($questions_ch1) + $index + 1,
                'noidung'    => $noidung,
                'dokho'      => ($index % 3) + 1,
                'mamonhoc'   => 'CS03015',
                'machuong'   => 12,
                'nguoitao'   => 'DH222222',
                'trangthai'  => 1,
            ]);
        }
    }
}
