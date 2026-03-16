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
            Schema::create('monhoc', function (Blueprint $table) {
                $table->id();
                $table->string('mamonhoc')->unique();
                $table->string('tenmonhoc', 255);
                $table->unsignedBigInteger('makhoa')->nullable();
                $table->integer('sotinchi')->nullable();
                $table->integer('sotietlythuyet')->nullable();
                $table->integer('sotietthuchanh')->nullable();
                $table->boolean('trangthai')->nullable();

                $table->foreign('makhoa', 'FK_MONHOC_KHOA')
                    ->references('id')
                    ->on('khoa')
                    ->onDelete('set null');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('monhoc');
        }
    };
