<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('envio_marmitas');

        Schema::create('pedidos_ajuda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero_sequencial')->unique();
            $table->string('codigo_publico', 32)->unique();
            $table->string('nome_recebedor');
            $table->string('telefone', 32);
            $table->text('endereco_completo_referencias');
            $table->text('itens');
            $table->enum('status', ['em_andamento', 'feito'])->default('em_andamento')->index();
            $table->string('nome_normalizado')->nullable();
            $table->text('endereco_normalizado')->nullable();
            $table->string('telefone_normalizado', 32)->nullable()->index();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('controle_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')
                ->unique()
                ->constrained('pedidos_ajuda')
                ->cascadeOnDelete();
            $table->string('nome_entregador')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controle_envios');
        Schema::dropIfExists('pedidos_ajuda');

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
};
