<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Remover a FK errada
            $table->dropForeign(['requisicao_id']);

            // Criar a FK correta para a tabela 'requisicoes'
            $table->foreign('requisicao_id')
                ->references('id')
                ->on('requisicoes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['requisicao_id']);
            $table->foreign('requisicao_id')
                ->references('id')
                ->on('requisicaos') // volta ao errado, só para rollback
                ->onDelete('cascade');
        });
    }
};
