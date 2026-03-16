<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chuong', function (Blueprint $table) {
            $table->increments('machuong');
            $table->string('tenchuong', 255);
            $table->string('mamonhoc', 20);   // trỏ đến monhoc.mamonhoc (kiểu string)
            $table->tinyInteger('trangthai')->default(1);

            $table->foreign('mamonhoc')->references('mamonhoc')->on('monhoc')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuong');
    }
};
