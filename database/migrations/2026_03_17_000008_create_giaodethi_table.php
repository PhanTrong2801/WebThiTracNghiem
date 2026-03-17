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
        Schema::create('giaodethi', function (Blueprint $table) {
            $table->unsignedInteger('made');
            $table->unsignedInteger('manhom');

            $table->primary(['made', 'manhom']);

            $table->foreign('made', 'FK_GIAODETHI_DETHI')->references('made')->on('dethi')->onDelete('cascade');
            $table->foreign('manhom', 'FK_GIAODETHI_NHOM')->references('manhom')->on('nhom')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giaodethi');
    }
};
