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
        Schema::create('nhomquyen', function (Blueprint $table) {
            // Sử dụng id() hoặc bigIncremental để làm khóa chính chuẩn Laravel
            $table->bigIncrements('manhomquyen');
            $table->string('tennhomquyen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhomquyen');
    }
};
