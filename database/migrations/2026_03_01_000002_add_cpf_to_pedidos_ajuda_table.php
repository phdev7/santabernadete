<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos_ajuda', function (Blueprint $table) {
            if (! Schema::hasColumn('pedidos_ajuda', 'cpf')) {
                $table->string('cpf', 20)->nullable()->after('nome_recebedor');
            }

            if (! Schema::hasColumn('pedidos_ajuda', 'cpf_normalizado')) {
                $table->string('cpf_normalizado', 20)->nullable()->after('telefone_normalizado')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos_ajuda', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos_ajuda', 'cpf_normalizado')) {
                $table->dropIndex('pedidos_ajuda_cpf_normalizado_index');
                $table->dropColumn('cpf_normalizado');
            }

            if (Schema::hasColumn('pedidos_ajuda', 'cpf')) {
                $table->dropColumn('cpf');
            }
        });
    }
};
