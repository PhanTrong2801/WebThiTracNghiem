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
        Schema::create('chitietquyen', function (Blueprint $table) {
            $table->integer('manhomquyen');
            $table->string('chucnang', 50);
            $table->string('hanhdong', 50);
            $table->primary(['manhomquyen', 'chucnang', 'hanhdong']);
            $table->foreign('manhomquyen')->references('manhomquyen')->on('nhomquyen');
            $table->foreign('chucnang')->references('chucnang')->on('danhmucchucnang');
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
