<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->text('keywords')->nullable()->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};
