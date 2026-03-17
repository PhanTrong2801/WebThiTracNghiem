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
        Schema::create('chitietketqua', function (Blueprint $table) {
            $table->unsignedInteger('makq');
            $table->unsignedInteger('macauhoi');
            $table->unsignedInteger('dapanchon')->nullable();

            $table->primary(['makq', 'macauhoi']);

            $table->foreign('makq', 'FK_CHITIETKETQUA_KETQUA')->references('makq')->on('ketqua')->onDelete('cascade');
            $table->foreign('macauhoi', 'FK_CHITIETKETQUA_CAUHOI')->references('macauhoi')->on('cauhoi')->onDelete('cascade');
            $table->foreign('dapanchon', 'FK_CHITIETKETQUA_CAUTRALOI')->references('macautl')->on('cautraloi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietketqua');
    }
};
