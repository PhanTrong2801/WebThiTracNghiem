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
        Schema::create('cauhoi', function (Blueprint $table) {
            $table->increments('macauhoi');
    $table->string('noidung', 500);
    $table->integer('dokho');
    $table->integer('mamonhoc');
    $table->integer('machuong');
    $table->string('nguoitao', 50)->nullable();
    $table->integer('trangthai')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cauhoi');
    }
};
