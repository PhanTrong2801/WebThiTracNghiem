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
            $table->id();
            // Phải dùng foreignId hoặc unsignedBigInteger để khớp với bigIncrements
            $table->unsignedBigInteger('manhomquyen');

            // Thiết lập khóa ngoại
            $table->foreign('manhomquyen')
                  ->references('manhomquyen')
                  ->on('nhomquyen')
                  ->onDelete('cascade');

            $table->timestamps();
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
