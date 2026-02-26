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
        Schema::create('envio_marmitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('quantidade_marmitas');
            $table->unsignedInteger('quantidade_agua')->default(0);
            $table->text('endereco');
            $table->text('notas')->nullable();
            $table->enum('status', ['em_andamento', 'feito'])->default('em_andamento')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envio_marmitas');
    }
};
