<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->renameColumn('bibliografia', 'descricao');
        });
    }

    public function down()
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->renameColumn('descricao', 'bibliografia');
        });
    }
};
