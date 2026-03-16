<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CauTraLoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cautraloi = [
            // Câu 1: OOP là viết tắt của:
            ['macauhoi' => 1, 'noidungtl' => 'Object Open Programming', 'ladapan' => 0],
            ['macauhoi' => 1, 'noidungtl' => 'Open Object Programming', 'ladapan' => 0],
            ['macauhoi' => 1, 'noidungtl' => 'Object Oriented Programming.', 'ladapan' => 1],
            ['macauhoi' => 1, 'noidungtl' => 'Object Oriented Proccessing.', 'ladapan' => 0],

            // Câu 2: Đặc điểm cơ bản của lập trình hướng đối tượng thể hiện ở:
            ['macauhoi' => 2, 'noidungtl' => 'Tính đóng gói, tính kế thừa, tính đa hình, tính đặc biệt hóa.', 'ladapan' => 0],
            ['macauhoi' => 2, 'noidungtl' => 'Tính đóng gói, tính kế thừa, tính đa hình, tính trừu tượng.', 'ladapan' => 1],
            ['macauhoi' => 2, 'noidungtl' => 'Tính chia nhỏ, tính kế thừa.', 'ladapan' => 0],
            ['macauhoi' => 2, 'noidungtl' => 'Tính đóng gói, tính trừu tượng.', 'ladapan' => 0],

            // Câu 3: Lập trình hướng đối tượng là:
            ['macauhoi' => 3, 'noidungtl' => 'Lập trình hướng đối tượng là phương pháp đặt trọng tâm vào các đối tượng, nó không cho phép dữ liệu chuyển động một cách tự do trong hệ thống.', 'ladapan' => 1],
            ['macauhoi' => 3, 'noidungtl' => 'Lập trình hướng đối tượng là phương pháp lập trình cơ bản gần với mã máy', 'ladapan' => 0],
            ['macauhoi' => 3, 'noidungtl' => 'Lập trình hướng đối tượng là phương pháp mới của lập trình máy tính, chia chương trình thành các hàm; quan tâm đến chức năng của hệ thống.', 'ladapan' => 0],
            ['macauhoi' => 3, 'noidungtl' => 'Lập trình hướng đối tượng là phương pháp đặt trọng tâm vào các chức năng, cấu trúc chương trình được xây dựng theo cách tiếp cận hướng chức năng.', 'ladapan' => 0],

            // Câu 4: Thế nào được gọi là hiện tượng nạp chồng?
            ['macauhoi' => 4, 'noidungtl' => 'Hiện tượng lớp con kế thừa định nghĩa một hàm hoàn toàn giống lớp cha.', 'ladapan' => 1],
            ['macauhoi' => 4, 'noidungtl' => 'Hiện tượng lớp con kế thừa định nghĩa một hàm cùng tên nhưng khác kiểu với một hàm ở lớp cha.', 'ladapan' => 0],
            ['macauhoi' => 4, 'noidungtl' => 'Hiện tượng lớp con kế thừa định nghĩa một hàm cùng tên, cùng kiểu với một hàm ở lớp cha nhưng khác đối số', 'ladapan' => 0],
            ['macauhoi' => 4, 'noidungtl' => 'Hiện tượng lớp con kế thừa định nghĩa một hàm cùng tên, cùng các đối số nhưng khác kiểu với một hàm ở lớp cha.', 'ladapan' => 0],

            // Câu 5: Trong java, khi khai báo không có từ khóa quyền truy cập thì mặc định là:
            ['macauhoi' => 5, 'noidungtl' => 'public.', 'ladapan' => 0],
            ['macauhoi' => 5, 'noidungtl' => 'protected.', 'ladapan' => 0],
            ['macauhoi' => 5, 'noidungtl' => 'friendly.', 'ladapan' => 1],
            ['macauhoi' => 5, 'noidungtl' => 'private.', 'ladapan' => 0],

            // Câu 6: Đối với quyền truy cập nào thì cho phép truy cập các lớp con trong cùng gói với lớp cha?
            ['macauhoi' => 6, 'noidungtl' => 'private, friendly, protected.', 'ladapan' => 0],
            ['macauhoi' => 6, 'noidungtl' => 'friendly, public.', 'ladapan' => 0],
            ['macauhoi' => 6, 'noidungtl' => 'friendly, protected, public.', 'ladapan' => 1],
            ['macauhoi' => 6, 'noidungtl' => 'public, protected.', 'ladapan' => 0],

            // Câu 7: Khi xây dựng phương thức khởi tạo (constructor), việc thường làm là:
            ['macauhoi' => 7, 'noidungtl' => 'Khởi tạo giá trị cho các thành phần dữ liệu của đối tượng.', 'ladapan' => 1],
            ['macauhoi' => 7, 'noidungtl' => 'Khai báo kiểu cho các thành phần dữ liệu của đối tượng.', 'ladapan' => 0],
            ['macauhoi' => 7, 'noidungtl' => 'Khai báo các phương thức của đối tượng.', 'ladapan' => 0],
            ['macauhoi' => 7, 'noidungtl' => 'Tất cả đều sai.', 'ladapan' => 0],

            // Câu 8: Phương thức khởi tạo (constructor) là phương thức được thực thi:
            ['macauhoi' => 8, 'noidungtl' => 'Lúc hủy đối tượng.', 'ladapan' => 0],
            ['macauhoi' => 8, 'noidungtl' => 'Lúc tạo đối tượng.', 'ladapan' => 1],
            ['macauhoi' => 8, 'noidungtl' => 'Lúc sử dụng đối tượng.', 'ladapan' => 0],
            ['macauhoi' => 8, 'noidungtl' => 'Cả ba câu trên đều đúng.', 'ladapan' => 0],

            // Câu 9: Tên của phương thức khởi tạo:
            ['macauhoi' => 9, 'noidungtl' => 'Không được trùng với tên lớp.', 'ladapan' => 0],
            ['macauhoi' => 9, 'noidungtl' => 'Phải trùng với tên lớp.', 'ladapan' => 1],
            ['macauhoi' => 9, 'noidungtl' => 'Đặt tên tùy ý.', 'ladapan' => 0],
            ['macauhoi' => 9, 'noidungtl' => 'Tất cả đều đúng.', 'ladapan' => 0],

            // Câu 10: Đối tượng sống kể từ khi:
            ['macauhoi' => 10, 'noidungtl' => 'Khởi tạo đối tượng (bằng toán tử new) cho đến hết phương trình.', 'ladapan' => 0],
            ['macauhoi' => 10, 'noidungtl' => 'Khởi tạo đối tượng (bằng toán tử new) cho đến hết phương thức chứa nó.', 'ladapan' => 0],
            ['macauhoi' => 10, 'noidungtl' => 'Khởi tạo đối tượng (bằng toán tử new) cho đến hết khối chứa nó.', 'ladapan' => 1],
            ['macauhoi' => 10, 'noidungtl' => 'Tất cả đều đúng.', 'ladapan' => 0],

            // Câu 11: Chọn câu đúng nhất đối với hướng dẫn tạo lớp:
            ['macauhoi' => 11, 'noidungtl' => 'Lấy danh từ chính mô tả khái niệm làm tên lớp.', 'ladapan' => 0],
            ['macauhoi' => 11, 'noidungtl' => 'Lấy các danh từ mô tả cho khái niệm chính làm thuộc tính.', 'ladapan' => 0],
            ['macauhoi' => 11, 'noidungtl' => 'Lấy các động từ tác động lên đối tượng làm phương thức.', 'ladapan' => 1],
            ['macauhoi' => 11, 'noidungtl' => 'Tất cả đều đúng.', 'ladapan' => 0],

            // Câu 12: Từ khóa static có thể đứng trước:
            ['macauhoi' => 12, 'noidungtl' => 'Thành phần dữ liệu của lớp.', 'ladapan' => 0],
            ['macauhoi' => 12, 'noidungtl' => 'Phương thức của lớp.', 'ladapan' => 0],
            ['macauhoi' => 12, 'noidungtl' => 'Đoạn code.', 'ladapan' => 0],
            ['macauhoi' => 12, 'noidungtl' => 'Tất cả đều đúng.', 'ladapan' => 1],

            // Câu 13: System.out.println(Math.round(Math.random()*1000000)%100)
            ['macauhoi' => 13, 'noidungtl' => 'Kết quả xuất ra giá trị từ 0 đến 99.', 'ladapan' => 0],
            ['macauhoi' => 13, 'noidungtl' => 'Kết quả xuất ra giá trị từ 0 đến 100.', 'ladapan' => 1],
            ['macauhoi' => 13, 'noidungtl' => 'Kết quả xuất ra giá trị từ 1 đến 99.', 'ladapan' => 0],
            ['macauhoi' => 13, 'noidungtl' => 'Kết quả xuất ra giá trị từ 1 đến 100.', 'ladapan' => 0],

            // Câu 14: Khi định nghĩa lớp con, từ khóa extends trong Java:
            ['macauhoi' => 14, 'noidungtl' => 'Đặt trước tên lớp con.', 'ladapan' => 0],
            ['macauhoi' => 14, 'noidungtl' => 'Đặt trước tên lớp cha.', 'ladapan' => 1],
            ['macauhoi' => 14, 'noidungtl' => 'Đặt sau tên lớp cha.', 'ladapan' => 0],
            ['macauhoi' => 14, 'noidungtl' => 'Tất cả đều sai.', 'ladapan' => 0],

            // Câu 15: Khi định nghĩa một lớp con:
            ['macauhoi' => 15, 'noidungtl' => 'Được phép khai báo thêm các thành phần dữ liệu.', 'ladapan' => 0],
            ['macauhoi' => 15, 'noidungtl' => 'Được phép khai báo thêm các phương thức.', 'ladapan' => 0],
            ['macauhoi' => 15, 'noidungtl' => 'A, B đều sai.', 'ladapan' => 0],
            ['macauhoi' => 15, 'noidungtl' => 'A, B đều đúng.', 'ladapan' => 1],

            // Câu 16: Từ khóa this trong Java là:
            ['macauhoi' => 16, 'noidungtl' => 'Đối tượng cha của đối tượng đang thao tác.', 'ladapan' => 0],
            ['macauhoi' => 16, 'noidungtl' => 'Đối tượng đang thao tác.', 'ladapan' => 1],
            ['macauhoi' => 16, 'noidungtl' => 'Cả 2 đều đúng.', 'ladapan' => 0],
            ['macauhoi' => 16, 'noidungtl' => 'Cả 2 đều sai.', 'ladapan' => 0],

            // Câu 17: Nếu modifier của lớp là public thì tên file .java:
            ['macauhoi' => 17, 'noidungtl' => 'Phải trùng với tên class, phân biệt chữ hoa và thường.', 'ladapan' => 1],
            ['macauhoi' => 17, 'noidungtl' => 'Phải trùng với tên class, không phân biệt chữ hoa và thường.', 'ladapan' => 0],
            ['macauhoi' => 17, 'noidungtl' => 'Không phải trùng với tên class.', 'ladapan' => 0],
            ['macauhoi' => 17, 'noidungtl' => 'Tất cả đều sai.', 'ladapan' => 0],

            // Câu 18: Biến đối tượng trong Java là:
            ['macauhoi' => 18, 'noidungtl' => 'Tham chiếu (địa chỉ) của vùng nhớ chứa dữ liệu của đối tượng.', 'ladapan' => 1],
            ['macauhoi' => 18, 'noidungtl' => 'Biến static.', 'ladapan' => 0],
            ['macauhoi' => 18, 'noidungtl' => 'Biến cục bộ.', 'ladapan' => 0],
            ['macauhoi' => 18, 'noidungtl' => 'Tất cả đều sai.', 'ladapan' => 0],

            // Câu 19: Khi một thành phần của class được khai báo modifier là friendly thì thành phần đó:
            ['macauhoi' => 19, 'noidungtl' => 'Không cho phép các đối tượng thuộc các class cùng package (cùng thư mục) truy cập.', 'ladapan' => 0],
            ['macauhoi' => 19, 'noidungtl' => 'Cho phép các đối tượng thuộc các class cùng package (cùng thư mục) truy cập.', 'ladapan' => 1],
            ['macauhoi' => 19, 'noidungtl' => 'Cho phép các đối tượng thuộc các class cùng package (cùng thư mục) truy cập và khác package truy cập.', 'ladapan' => 0],
            ['macauhoi' => 19, 'noidungtl' => 'Tất cả đều đúng.', 'ladapan' => 0],

            // Câu 20: Ưu điểm của OOP:
            ['macauhoi' => 20, 'noidungtl' => 'Dễ mô tả các quan hệ phân cấp trong thế giới thực.', 'ladapan' => 0],
            ['macauhoi' => 20, 'noidungtl' => 'Có tính bảo mật cao.', 'ladapan' => 0],
            ['macauhoi' => 20, 'noidungtl' => 'Câu A, B đúng.', 'ladapan' => 1],
            ['macauhoi' => 20, 'noidungtl' => 'Câu A, B sai.', 'ladapan' => 0],

            // Câu 21: Ưu điểm của OOP (phần 2):
            ['macauhoi' => 21, 'noidungtl' => 'Dễ tái sử dụng code.', 'ladapan' => 0],
            ['macauhoi' => 21, 'noidungtl' => 'Bảo mật kém.', 'ladapan' => 0],
            ['macauhoi' => 21, 'noidungtl' => 'Có tính bảo mật cao.', 'ladapan' => 0],
            ['macauhoi' => 21, 'noidungtl' => 'A, C đúng.', 'ladapan' => 1],

            // Câu 22: Ưu điểm của class file trong Java là:
            ['macauhoi' => 22, 'noidungtl' => 'Java class file có thể được dùng ở bất kỳ platform nào.', 'ladapan' => 0],
            ['macauhoi' => 22, 'noidungtl' => 'Tính module hóa cao, dùng bộ nhớ tốt hơn với class file hơn là file thực thi vì class file cần một bước dịch nữa mới được CPU thực thi.', 'ladapan' => 0],
            ['macauhoi' => 22, 'noidungtl' => 'Cả 2 đều đúng.', 'ladapan' => 0],
            ['macauhoi' => 22, 'noidungtl' => 'Cả 2 đều sai.', 'ladapan' => 1],

            // Câu 23: Source code của java có tên mở rộng là:
            ['macauhoi' => 23, 'noidungtl' => '.class.', 'ladapan' => 0],
            ['macauhoi' => 23, 'noidungtl' => '.java.', 'ladapan' => 1],
            ['macauhoi' => 23, 'noidungtl' => '.com.', 'ladapan' => 0],
            ['macauhoi' => 23, 'noidungtl' => 'Tất cả đều sai.', 'ladapan' => 0],

            // Câu 24: JDK bao gồm các thành phần chính:
            ['macauhoi' => 24, 'noidungtl' => 'Classes, Compiler, Debugger, Java Runtime Environment.', 'ladapan' => 1],
            ['macauhoi' => 24, 'noidungtl' => 'Classes, Compiler, Debugger.', 'ladapan' => 0],
            ['macauhoi' => 24, 'noidungtl' => 'Classes, Compiler, Java Runtime Environment.', 'ladapan' => 0],
            ['macauhoi' => 24, 'noidungtl' => 'Compiler, Debugger, Java Runtime Environment.', 'ladapan' => 0],

            // Câu 25: Lớp Student. Giả sử x là một đối tượng. Chọn phát biểu đúng trong OOP:
            ['macauhoi' => 25, 'noidungtl' => 'int age = x.getAge();', 'ladapan' => 1],
            ['macauhoi' => 25, 'noidungtl' => 'getAge(x);', 'ladapan' => 0],
            ['macauhoi' => 25, 'noidungtl' => 'getName(x);', 'ladapan' => 0],
            ['macauhoi' => 25, 'noidungtl' => 'int age = getAge(x);', 'ladapan' => 0],

            // Câu 26: Bao đóng là một đặc tính của OOP nhằm để:
            ['macauhoi' => 26, 'noidungtl' => 'Che dấu dữ liệu.', 'ladapan' => 0],
            ['macauhoi' => 26, 'noidungtl' => 'Bên ngoài chỉ giao tiếp được với đối tượng thông qua một số phương thức.', 'ladapan' => 1],
            ['macauhoi' => 26, 'noidungtl' => 'Cả 2 câu A, B đều đúng.', 'ladapan' => 0],
            ['macauhoi' => 26, 'noidungtl' => 'Cả 2 câu A, B đều sai.', 'ladapan' => 0],

            // Câu 27: Các từ khóa cho cấu trúc rẽ nhánh của Java gồm:
            ['macauhoi' => 27, 'noidungtl' => 'if, else, switch, case, default, break.', 'ladapan' => 1],
            ['macauhoi' => 27, 'noidungtl' => 'IF, ELSE, SWITCH, CASE, DEFAULT, BREAK.', 'ladapan' => 0],
            ['macauhoi' => 27, 'noidungtl' => 'Cả 2 câu A, B đều đúng.', 'ladapan' => 0],
            ['macauhoi' => 27, 'noidungtl' => 'Cả 2 câu A, B đều sai.', 'ladapan' => 0],

            // Câu 28: Các hằng trong Java gồm:
            ['macauhoi' => 28, 'noidungtl' => 'True, False, Null.', 'ladapan' => 0],
            ['macauhoi' => 28, 'noidungtl' => 'TRUE, FALSE, NULL.', 'ladapan' => 0],
            ['macauhoi' => 28, 'noidungtl' => 'True, False, NULL.', 'ladapan' => 1],
            ['macauhoi' => 28, 'noidungtl' => 'true, false, null.', 'ladapan' => 0],

            // Câu 29: Tên đầu tiên của Java là gì?
            ['macauhoi' => 29, 'noidungtl' => 'Java.', 'ladapan' => 0],
            ['macauhoi' => 29, 'noidungtl' => 'Oak.', 'ladapan' => 1],
            ['macauhoi' => 29, 'noidungtl' => 'Cafe', 'ladapan' => 0],
            ['macauhoi' => 29, 'noidungtl' => 'James golings.', 'ladapan' => 0],

            // Câu 30: Lớp XX với phương thức Display, đối tượng objX. Để gọi phương thức Display ta sử dụng:
            ['macauhoi' => 30, 'noidungtl' => 'XX.Display;', 'ladapan' => 0],
            ['macauhoi' => 30, 'noidungtl' => 'XX.Display();', 'ladapan' => 0],
            ['macauhoi' => 30, 'noidungtl' => 'objX.Display();', 'ladapan' => 1],
            ['macauhoi' => 30, 'noidungtl' => 'Display();', 'ladapan' => 0],

            // Câu 31: Đối tượng là gì?
            ['macauhoi' => 31, 'noidungtl' => 'Các lớp được tạo thể hiện từ đó;', 'ladapan' => 0],
            ['macauhoi' => 31, 'noidungtl' => 'Một thể hiện của lớp;', 'ladapan' => 1],
            ['macauhoi' => 31, 'noidungtl' => 'Một tham chiếu đến một thuộc tính;', 'ladapan' => 0],
            ['macauhoi' => 31, 'noidungtl' => 'Một biến;', 'ladapan' => 0],

            // Câu 32: Từ khoá nào được sử dụng để khai báo một phương thức trong Java?
            ['macauhoi' => 32, 'noidungtl' => 'Method', 'ladapan' => 1],
            ['macauhoi' => 32, 'noidungtl' => 'Function', 'ladapan' => 0],
            ['macauhoi' => 32, 'noidungtl' => 'Procedure', 'ladapan' => 0],
            ['macauhoi' => 32, 'noidungtl' => 'Class;', 'ladapan' => 0],

            // Câu 33: Từ khoá nào được sử dụng để khai báo một biến trong Java?
            ['macauhoi' => 33, 'noidungtl' => 'Var;', 'ladapan' => 1],
            ['macauhoi' => 33, 'noidungtl' => 'Int;', 'ladapan' => 0],
            ['macauhoi' => 33, 'noidungtl' => 'Class;', 'ladapan' => 0],
            ['macauhoi' => 33, 'noidungtl' => 'Display', 'ladapan' => 0],

            // Câu 34: Từ khoá nào được sử dụng để khai báo một phương thức làm việc với đối tượng của lớp?
            ['macauhoi' => 34, 'noidungtl' => 'This', 'ladapan' => 1],
            ['macauhoi' => 34, 'noidungtl' => 'Super', 'ladapan' => 0],
            ['macauhoi' => 34, 'noidungtl' => 'Private', 'ladapan' => 0],
            ['macauhoi' => 34, 'noidungtl' => 'Tất cả đều sai;', 'ladapan' => 0],

            // Câu 35: Từ khoá nào được sử dụng để khai báo một phương thức được ghi đè trong Java?
            ['macauhoi' => 35, 'noidungtl' => 'Override', 'ladapan' => 1],
            ['macauhoi' => 35, 'noidungtl' => 'Overload', 'ladapan' => 0],
            ['macauhoi' => 35, 'noidungtl' => 'Super', 'ladapan' => 0],
            ['macauhoi' => 35, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 0],

            // Câu 36: Từ khoá nào được sử dụng để khai báo một phương thức được gọi tự động khi một đối tượng được tạo ra?
            ['macauhoi' => 36, 'noidungtl' => 'Constructor', 'ladapan' => 1],
            ['macauhoi' => 36, 'noidungtl' => 'Destructor', 'ladapan' => 0],
            ['macauhoi' => 36, 'noidungtl' => 'Initializer', 'ladapan' => 0],
            ['macauhoi' => 36, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 0],

            // Câu 37: Từ khoá nào được sử dụng để khai báo một phương thức hoạt động như một lớp trừu tượng?
            ['macauhoi' => 37, 'noidungtl' => 'Abstract', 'ladapan' => 1],
            ['macauhoi' => 37, 'noidungtl' => 'Virtual', 'ladapan' => 0],
            ['macauhoi' => 37, 'noidungtl' => 'Static', 'ladapan' => 0],
            ['macauhoi' => 37, 'noidungtl' => 'Tất cả đều sai', 'ladapan' => 0],

            // Câu 38: Từ khoá nào được sử dụng để khai báo một lớp trừu tượng trong Java?
            ['macauhoi' => 38, 'noidungtl' => 'Abstract', 'ladapan' => 1],
            ['macauhoi' => 38, 'noidungtl' => 'Virtual', 'ladapan' => 0],
            ['macauhoi' => 38, 'noidungtl' => 'Static', 'ladapan' => 0],
            ['macauhoi' => 38, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 0],

            // Câu 39: Trong Java, lớp con là gì?
            ['macauhoi' => 39, 'noidungtl' => 'Lớp được kế thừa từ lớp cha', 'ladapan' => 1],
            ['macauhoi' => 39, 'noidungtl' => 'Lớp được tạo bởi một đối tượng', 'ladapan' => 0],
            ['macauhoi' => 39, 'noidungtl' => 'Lớp được tạo bởi một phương thức', 'ladapan' => 0],
            ['macauhoi' => 39, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 0],

            // Câu 40: Đặc điểm của Tính bao gói trong Lập trình hướng đối tượng:
            ['macauhoi' => 40, 'noidungtl' => 'Cơ chế chia chương trình thành các hàm và thủ tục thực hiện các chức năng riêng rẽ.', 'ladapan' => 0],
            ['macauhoi' => 40, 'noidungtl' => 'Cơ chế cho thấy một hàm có thể có nhiều thể hiện khác nhau ở từng thời điểm.', 'ladapan' => 0],
            ['macauhoi' => 40, 'noidungtl' => 'Cơ chế ràng buộc dữ liệu và thao tác trên dữ liệu đó thành một thể thống nhất, tránh được các tác động bất ngờ từ bên ngoài. Thể thống nhất này gọi là đối tượng.', 'ladapan' => 1],
            ['macauhoi' => 40, 'noidungtl' => 'Cơ chế không cho phép các thành phần khác truy cập đến bên trong nó.', 'ladapan' => 0],

            // Câu 41: Tính kế thừa trong lập trình hướng đối tượng:
            ['macauhoi' => 41, 'noidungtl' => 'Khả năng xây dựng các lớp mới từ các lớp cũ, lớp mới được gọi là lớp dẫn xuất, lớp cũ được gọi là lớp cơ sở', 'ladapan' => 1],
            ['macauhoi' => 41, 'noidungtl' => 'Khả năng sử dụng lại các hàm đã xây dựng', 'ladapan' => 0],
            ['macauhoi' => 41, 'noidungtl' => 'Khả năng sử dụng lại các kiểu dữ liệu đã xây dựng', 'ladapan' => 0],
            ['macauhoi' => 41, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 0],

            // Câu 42: Trong kế thừa. Lớp mới có thuật ngữ tiếng Anh là:
            ['macauhoi' => 42, 'noidungtl' => 'Derived Class', 'ladapan' => 1],
            ['macauhoi' => 42, 'noidungtl' => 'Base Class', 'ladapan' => 0],
            ['macauhoi' => 42, 'noidungtl' => 'Inheritance Class', 'ladapan' => 0],
            ['macauhoi' => 42, 'noidungtl' => 'Object Class', 'ladapan' => 0],

            // Câu 43: Trong kế thừa. Lớp cha có thuật ngữ tiếng Anh là:
            ['macauhoi' => 43, 'noidungtl' => 'Base Class', 'ladapan' => 0],
            ['macauhoi' => 43, 'noidungtl' => 'Derived Class', 'ladapan' => 1],
            ['macauhoi' => 43, 'noidungtl' => 'Inheritance Class', 'ladapan' => 0],
            ['macauhoi' => 43, 'noidungtl' => 'Object Class', 'ladapan' => 0],

            // Câu 44: Đặc điểm của Tính đa hình?
            ['macauhoi' => 44, 'noidungtl' => 'Khả năng một hàm, thủ tục có thể được kế thừa lại', 'ladapan' => 0],
            ['macauhoi' => 44, 'noidungtl' => 'Khả năng một thông điệp có thể được truyền lại cho lớp con của nó', 'ladapan' => 0],
            ['macauhoi' => 44, 'noidungtl' => 'Khả năng một hàm, thủ tục được sử dụng lại', 'ladapan' => 0],
            ['macauhoi' => 44, 'noidungtl' => 'Khả năng một thông điệp có thể thay đổi cách thể hiện của nó theo lớp cụ thể của đối tượng được nhận thông điệp', 'ladapan' => 1],

            // Câu 45: Khái niệm Lớp đối tượng?
            ['macauhoi' => 45, 'noidungtl' => 'Một thiết kế hay mẫu cho các đối tượng cùng kiểu', 'ladapan' => 1],
            ['macauhoi' => 45, 'noidungtl' => 'Một thể hiện cụ thể cho các đối tượng', 'ladapan' => 0],
            ['macauhoi' => 45, 'noidungtl' => 'Tập các phần tử cùng loại', 'ladapan' => 0],
            ['macauhoi' => 45, 'noidungtl' => 'Tập các giá trị cùng loại', 'ladapan' => 0],

            // Câu 46: Sau khi khai báo và xây dựng thành công lớp đối tượng Sinh viên. Khi đó lớp đối tượng Sinh viên còn được gọi là:
            ['macauhoi' => 46, 'noidungtl' => 'Đối tượng', 'ladapan' => 1],
            ['macauhoi' => 46, 'noidungtl' => 'Kiểu dữ liệu trừu tượng', 'ladapan' => 0],
            ['macauhoi' => 46, 'noidungtl' => 'Lớp cơ sở', 'ladapan' => 0],
            ['macauhoi' => 46, 'noidungtl' => 'Tất cả đều sai', 'ladapan' => 0],

            // Câu 47: Trong các phương án sau, phương án nào mô tả đối tượng:
            ['macauhoi' => 47, 'noidungtl' => 'Máy tính', 'ladapan' => 0],
            ['macauhoi' => 47, 'noidungtl' => 'Xe đạp', 'ladapan' => 0],
            ['macauhoi' => 47, 'noidungtl' => 'Quả cam', 'ladapan' => 0],
            ['macauhoi' => 47, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 1],

            // Câu 48: Muốn lập trình hướng đối tượng, bạn cần phải phân tích chương trình, bài toán thành các:
            ['macauhoi' => 48, 'noidungtl' => 'Hàm, thủ tục', 'ladapan' => 0],
            ['macauhoi' => 48, 'noidungtl' => 'Các module', 'ladapan' => 0],
            ['macauhoi' => 48, 'noidungtl' => 'Các đối tượng từ đó xây dựng các lớp đối tượng tương ứng', 'ladapan' => 1],
            ['macauhoi' => 48, 'noidungtl' => 'Các thông điệp', 'ladapan' => 0],

            // Câu 49: Trong phương án sau, phương án mô tả tính đa hình là:
            ['macauhoi' => 49, 'noidungtl' => 'Các lớp Điểm, Hình tròn, Hình vuông, Hình chữ nhật… đều có phương thức Vẽ', 'ladapan' => 1],
            ['macauhoi' => 49, 'noidungtl' => 'Lớp hình vuông kế thừa lớp hình chữ nhật', 'ladapan' => 0],
            ['macauhoi' => 49, 'noidungtl' => 'Lớp hình tròn kế thừa lớp điểm', 'ladapan' => 0],
            ['macauhoi' => 49, 'noidungtl' => 'Lớp Điểm, Hình tròn cùng có hàm tạo, hàm hủy', 'ladapan' => 0],

            // Câu 50: Phương pháp lập trình tuần tự là:
            ['macauhoi' => 50, 'noidungtl' => 'Phương pháp lập trình với việc cấu trúc hóa dữ liệu và cấu trúc hóa chương trình để tránh các lệnh nhảy', 'ladapan' => 0],
            ['macauhoi' => 50, 'noidungtl' => 'Phương pháp lập trình với cách liệt kê các lệnh kế tiếp', 'ladapan' => 1],
            ['macauhoi' => 50, 'noidungtl' => 'Phương pháp lập trình được cấu trúc nghiêm ngặt với cấu trúc dạng module', 'ladapan' => 0],
            ['macauhoi' => 50, 'noidungtl' => 'Phương pháp xây dựng chương trình ứng dụng theo quan điểm dựa trên các cấu trúc dữ liệu trừu tượng, các thể hiện cụ thể của cấu trúc và quan hệ giữa chúng', 'ladapan' => 0],

            // Câu 51: Phương pháp lập trình cấu trúc là:
            ['macauhoi' => 51, 'noidungtl' => 'Phương pháp lập trình với việc cấu trúc hóa dữ liệu và cấu trúc hóa chương trình để tránh các lệnh nhảy', 'ladapan' => 1],
            ['macauhoi' => 51, 'noidungtl' => 'Phương pháp lập trình với cách liệt kê các lệnh kế tiếp', 'ladapan' => 0],
            ['macauhoi' => 51, 'noidungtl' => 'Phương pháp lập trình được cấu trúc nghiêm ngặt với cấu trúc dạng module', 'ladapan' => 0],
            ['macauhoi' => 51, 'noidungtl' => 'Phương pháp xây dựng chương trình ứng dụng theo quan điểm dựa trên các cấu trúc dữ liệu trừu tượng, các thể hiện cụ thể của cấu trúc và quan hệ giữa chúng', 'ladapan' => 0],

            // Câu 52: Khi khai báo và xây dựng thành công lớp đối tượng, để truy cập vào thành phần của lớp ta phải:
            ['macauhoi' => 52, 'noidungtl' => 'Phương pháp lập trình với cách liệt kê các lệnh kế tiếp', 'ladapan' => 0],
            ['macauhoi' => 52, 'noidungtl' => 'Phương pháp lập trình với việc cấu trúc hóa dữ liệu và cấu trúc hóa chương trình để tránh các lệnh nhảy', 'ladapan' => 1],
            ['macauhoi' => 52, 'noidungtl' => 'Phương pháp lập trình được cấu trúc nghiêm ngặt với cấu trúc dạng module', 'ladapan' => 0],
            ['macauhoi' => 52, 'noidungtl' => 'Phương pháp xây dựng chương trình ứng dụng theo quan điểm dựa trên các cấu trúc dữ liệu trừu tượng, các thể hiện cụ thể của cấu trúc và quan hệ giữa chúng', 'ladapan' => 0],

            // Câu 53: Phương pháp lập trình module là:
            ['macauhoi' => 53, 'noidungtl' => 'Phương pháp lập trình với việc cấu trúc hóa dữ liệu và cấu trúc hóa chương trình để tránh các lệnh nhảy', 'ladapan' => 0],
            ['macauhoi' => 53, 'noidungtl' => 'Phương pháp lập trình với cách liệt kê các lệnh kế tiếp', 'ladapan' => 0],
            ['macauhoi' => 53, 'noidungtl' => 'Phương pháp lập trình được cấu trúc nghiêm ngặt với cấu trúc dạng module', 'ladapan' => 1],
            ['macauhoi' => 53, 'noidungtl' => 'Phương pháp xây dựng chương trình ứng dụng theo quan điểm dựa trên các cấu trúc dữ liệu trừu tượng, các thể hiện cụ thể của cấu trúc và quan hệ giữa chúng', 'ladapan' => 0],

            // Câu 54: Khái niệm Trừu tượng hóa:
            ['macauhoi' => 54, 'noidungtl' => 'Phương pháp chỉ quan tâm đến những chi tiết cần thiết (chi tiết chính) và bỏ qua những chi tiết không cần thiết', 'ladapan' => 1],
            ['macauhoi' => 54, 'noidungtl' => 'Phương pháp quan tâm đến mọi chi tiết của đối tượng', 'ladapan' => 0],
            ['macauhoi' => 54, 'noidungtl' => 'Phương pháp thay thế những chi tiết chính bằng những chi tiết tương tự', 'ladapan' => 0],
            ['macauhoi' => 54, 'noidungtl' => 'Không có phương án chính xác', 'ladapan' => 0],

            // Câu 55: Khi khai báo và xây dựng một lớp ta cần phải xác định rõ thành phần:
            ['macauhoi' => 55, 'noidungtl' => 'Dữ liệu và đối tượng của lớp', 'ladapan' => 0],
            ['macauhoi' => 55, 'noidungtl' => 'Vô số thành phần', 'ladapan' => 0],
            ['macauhoi' => 55, 'noidungtl' => 'Khái niệm và đối tượng của lớp', 'ladapan' => 0],
            ['macauhoi' => 55, 'noidungtl' => 'Thuộc tính (dữ liệu) và phương thức (hành vi) của lớp', 'ladapan' => 1],

            // Câu 56: Chọn câu đúng về thành phần public của lớp:
            ['macauhoi' => 56, 'noidungtl' => 'Tại chương trình chính chỉ có thể truy cập đến thành phần public của lớp', 'ladapan' => 1],
            ['macauhoi' => 56, 'noidungtl' => 'Tại chương trình chính chỉ có thể truy cập đến thành phần private của lớp', 'ladapan' => 0],
            ['macauhoi' => 56, 'noidungtl' => 'Tại chương trình chính có thể truy cập đến bất kì thành phần nào của lớp', 'ladapan' => 0],
            ['macauhoi' => 56, 'noidungtl' => 'Tại chương trình chính không thể truy cập đến thành phần nào của lớp', 'ladapan' => 0],

            // Câu 57: Khi khai báo lớp trong các ngôn ngữ lập trình hướng đối tượng phải sử dụng từ khóa:
            ['macauhoi' => 57, 'noidungtl' => 'Object.', 'ladapan' => 0],
            ['macauhoi' => 57, 'noidungtl' => 'Record', 'ladapan' => 0],
            ['macauhoi' => 57, 'noidungtl' => 'File', 'ladapan' => 0],
            ['macauhoi' => 57, 'noidungtl' => 'Class', 'ladapan' => 1],

            // Câu 58: Khái niệm của Phương thức là:
            ['macauhoi' => 58, 'noidungtl' => 'Là dữ liệu trình bày các đặc điểm của một đối tượng', 'ladapan' => 0],
            ['macauhoi' => 58, 'noidungtl' => 'Tất cả đều đúng', 'ladapan' => 0],
            ['macauhoi' => 58, 'noidungtl' => 'Là những chức năng của đối tượng', 'ladapan' => 0],
            ['macauhoi' => 58, 'noidungtl' => 'Liên quan tới những thứ mà đối tượng có thể làm. Một phương thức đáp ứng một chức năng tác động lên dữ liệu của đối tượng', 'ladapan' => 1],

            // Câu 59: Cho lớp người hãy xác định đâu là các thuộc tính của lớp người:
            ['macauhoi' => 59, 'noidungtl' => 'Ăn, Uống, Chân, Tay', 'ladapan' => 0],
            ['macauhoi' => 59, 'noidungtl' => 'Hát, học, vui, cười', 'ladapan' => 0],
            ['macauhoi' => 59, 'noidungtl' => 'Tất cả đều sai', 'ladapan' => 0],
            ['macauhoi' => 59, 'noidungtl' => 'Chân, Tay, Mắt, Mũi, Tên, Ngày sinh', 'ladapan' => 1],

            // Câu 60: Cho lớp Điểm trong hệ tọa độ xOy. Các phương thức có thể của lớp điểm là:
            ['macauhoi' => 60, 'noidungtl' => 'Dịch chuyển, Thiết lập toạ độ', 'ladapan' => 1],
            ['macauhoi' => 60, 'noidungtl' => 'Tung độ, hoành độ', 'ladapan' => 0],
            ['macauhoi' => 60, 'noidungtl' => 'Tung độ, hoành độ, cao độ', 'ladapan' => 0],
            ['macauhoi' => 60, 'noidungtl' => 'Tung độ, cao độ', 'ladapan' => 0],
        ];

        DB::table('cautraloi')->insert($cautraloi);
    }
}
