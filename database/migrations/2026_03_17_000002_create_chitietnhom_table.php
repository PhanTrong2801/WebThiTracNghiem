<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chitietnhom', function (Blueprint $table) {
            $table->unsignedInteger('manhom');
            $table->string('manguoidung', 50);
            $table->tinyInteger('hienthi')->default(1);

            $table->primary(['manhom', 'manguoidung']);
            $table->foreign('manhom')->references('manhom')->on('nhom')->onDelete('cascade');
            $table->foreign('manguoidung')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chitietnhom');
    }
};
