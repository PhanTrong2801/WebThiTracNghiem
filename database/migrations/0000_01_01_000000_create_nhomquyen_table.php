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
            $table->bigIncrements('manhomquyen');
            $table->string('tennhomquyen');
            $table->integer('trangthai')->default(1); // THÊM DÒNG NÀY
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
