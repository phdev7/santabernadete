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
        Schema::create('mantimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('status', ['vermelho', 'amarelo', 'abastecido'])->default('vermelho')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantimentos');
    }
};
