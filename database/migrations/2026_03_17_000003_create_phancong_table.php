<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phancong', function (Blueprint $table) {
            $table->string('mamonhoc', 20);
            $table->string('manguoidung', 50);

            $table->primary(['mamonhoc', 'manguoidung']);
            $table->foreign('mamonhoc')->references('mamonhoc')->on('monhoc');
            $table->foreign('manguoidung')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phancong');
    }
};
