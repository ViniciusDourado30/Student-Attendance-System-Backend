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
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna 'image' no início da tabela
            // com o link DIRETO para a imagem padrão.
            $table->string('image')
                  ->nullable()
                  ->default('/images/default.png') 
                  ->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Permite reverter a migração, removendo a coluna.
            $table->dropColumn('image');
        });
    }
};