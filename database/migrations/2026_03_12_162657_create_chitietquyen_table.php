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
        Schema::create('chitietquyen', function (Blueprint $table) {
            // Phải dùng foreignId hoặc unsignedBigInteger để khớp với bigIncrements
            $table->unsignedBigInteger('manhomquyen');
            $table->string('chucnang', 50);
            $table->string('hanhdong', 50);

            // Thiết lập khóa ngoại
            $table->foreign('manhomquyen')
                  ->references('manhomquyen')
                  ->on('nhomquyen')
                  ->onDelete('cascade');

            $table->foreign('chucnang')
                  ->references('chucnang')
                  ->on('danhmucchucnang')
                  ->onDelete('cascade');


            // Set composite primary key if needed, or stick with no primary key as per model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietquyen');
    }
};
