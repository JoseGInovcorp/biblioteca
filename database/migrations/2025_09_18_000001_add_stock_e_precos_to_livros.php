<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->unsignedInteger('stock_venda')->default(0)->after('id');
            $table->decimal('preco_venda', 8, 2)->default(0)->after('stock_venda');
            $table->decimal('preco_requisicao', 8, 2)->default(0)->after('preco_venda');
            $table->boolean('disponivel_para_requisicao')->default(true)->after('preco_requisicao');
        });
    }
    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropColumn(['stock_venda', 'preco_venda', 'preco_requisicao', 'disponivel_para_requisicao']);
        });
    }
};
