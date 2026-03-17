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
        Schema::create('dethi', function (Blueprint $table) {
            $table->increments('made');
            $table->string('monthi', 20)->nullable();
            $table->string('nguoitao', 50)->nullable();
            $table->string('tende', 255)->nullable();
            $table->dateTime('thoigiantao')->useCurrent()->nullable();
            $table->integer('thoigianthi')->nullable();
            $table->dateTime('thoigianbatdau')->nullable();
            $table->dateTime('thoigianketthuc')->nullable();
            $table->boolean('hienthibailam')->nullable();
            $table->boolean('xemdiemthi')->nullable();
            $table->boolean('xemdapan')->nullable();
            $table->boolean('troncauhoi')->nullable();
            $table->boolean('trondapan')->nullable();
            $table->boolean('nopbaichuyentab')->nullable();
            $table->integer('loaide')->nullable();
            $table->integer('socaude')->nullable();
            $table->integer('socautb')->nullable();
            $table->integer('socaukho')->nullable();
            $table->boolean('trangthai')->default(1);

            $table->foreign('monthi')->references('mamonhoc')->on('monhoc')->onDelete('cascade');
            $table->foreign('nguoitao')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dethi');
    }
};
