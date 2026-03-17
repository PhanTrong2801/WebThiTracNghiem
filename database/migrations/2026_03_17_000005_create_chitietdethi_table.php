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
        Schema::create('chitietdethi', function (Blueprint $table) {
            $table->unsignedInteger('made');
            $table->unsignedInteger('macauhoi');
            $table->integer('thutu')->nullable();
            $table->primary(['made', 'macauhoi']);

            $table->foreign('made')->references('made')->on('dethi')->onDelete('cascade');
            $table->foreign('macauhoi')->references('macauhoi')->on('cauhoi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietdethi');
    }
};
