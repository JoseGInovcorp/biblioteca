<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Nova tabela de géneros
        Schema::create('generos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->timestamps();
        });

        // Adiciona coluna genero_id à tabela livros
        Schema::table('livros', function (Blueprint $table) {
            $table->foreignId('genero_id')
                ->nullable()
                ->constrained('generos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropConstrainedForeignId('genero_id');
        });

        Schema::dropIfExists('generos');
    }
};
