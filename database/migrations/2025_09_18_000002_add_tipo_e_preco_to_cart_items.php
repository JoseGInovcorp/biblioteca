<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->enum('tipo_encomenda', ['compra', 'requisicao'])->default('compra')->after('livro_id');
            $table->decimal('preco_unitario', 8, 2)->default(0)->after('tipo_encomenda');
        });
    }
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['tipo_encomenda', 'preco_unitario']);
        });
    }
};
