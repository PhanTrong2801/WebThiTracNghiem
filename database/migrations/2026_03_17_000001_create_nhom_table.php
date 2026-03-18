<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nhom', function (Blueprint $table) {
            $table->increments('manhom');
            $table->string('tennhom', 255);
            $table->string('mamoi', 50)->nullable()->unique();
            $table->integer('siso')->default(0);
            $table->string('ghichu', 255)->nullable();
            $table->integer('namhoc')->nullable();
            $table->integer('hocky')->nullable();
            $table->tinyInteger('trangthai')->default(1);
            $table->tinyInteger('hienthi')->default(1);
            $table->tinyInteger('duocday')->default(1);
            $table->string('giangvien', 50)->default('');
            $table->string('mamonhoc', 20);

            $table->foreign('mamonhoc')->references('mamonhoc')->on('monhoc')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhom');
    }
};
