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
        Schema::create('dethitudong', function (Blueprint $table) {
            $table->unsignedInteger('made');
            $table->unsignedInteger('machuong');

            $table->primary(['made', 'machuong']);

            $table->foreign('made', 'FK_DETHITUDONG_DETHI')->references('made')->on('dethi')->onDelete('cascade');
            $table->foreign('machuong', 'FK_DETHITUDONG_CHUONG')->references('machuong')->on('chuong')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dethitudong');
    }
};
