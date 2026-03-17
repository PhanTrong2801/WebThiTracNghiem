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
        Schema::create('ketqua', function (Blueprint $table) {
            $table->increments('makq');
            $table->unsignedInteger('made');
            $table->string('manguoidung', 50);
            $table->double('diemthi')->nullable();
            $table->dateTime('thoigianvaothi')->useCurrent()->nullable();
            $table->integer('thoigianlambai')->nullable();
            $table->integer('socaudung')->nullable();
            $table->integer('solanchuyentab')->default(0);

            $table->foreign('made', 'FK_KETQUA_DETHI')->references('made')->on('dethi')->onDelete('cascade');
            $table->foreign('manguoidung', 'FK_ketqua_nguoidung')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ketqua');
    }
};
