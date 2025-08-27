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
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero_sequencial')->unique();
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->foreignId('cidadao_id')->constrained('users')->onDelete('cascade');
            $table->string('foto_cidadao')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim_prevista');
            $table->date('data_fim_real')->nullable();
            $table->enum('status', ['ativa', 'entregue'])->default('ativa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};
