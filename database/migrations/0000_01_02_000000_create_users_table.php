<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mình đổi tên thành 'users' cho đúng chuẩn Laravel,
        // nhưng giữ cấu trúc cột của bạn.
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('email', 191)->unique(); // Nên thêm unique để tránh trùng email
            $table->string('googleid', 150)->nullable();
            $table->string('hoten', 255);
            $table->boolean('gioitinh')->nullable();
            $table->date('ngaysinh')->default('1990-01-01');
            $table->string('avatar', 255)->nullable();

            // Laravel Auth mặc định tìm cột 'password'
            $table->string('password', 255)->nullable();

            $table->integer('trangthai')->default(1);
            $table->string('sodienthoai', 20)->nullable(); // Nên để string cho SĐT có số 0 ở đầu
            $table->string('token', 255)->nullable();
            $table->string('otp', 10)->nullable();

            $table->unsignedBigInteger('manhomquyen')->nullable();
            // Lưu ý: bảng 'nhomquyen' phải được tạo TRƯỚC bảng này
            $table->foreign('manhomquyen', 'FK_NGUOIDUNG_NHOMQUYEN')
                  ->references('manhomquyen')
                  ->on('nhomquyen')
                  ->onDelete('set null'); // Tránh lỗi khi xóa nhóm quyền

            $table->rememberToken(); // Rất nên có để tính năng "Ghi nhớ đăng nhập" hoạt động
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // CHỈNH SỬA Ở ĐÂY: Khớp với id string(50) của bảng users
            $table->string('user_id', 50)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
