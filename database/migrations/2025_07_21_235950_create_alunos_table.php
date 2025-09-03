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
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // ALTERAÇÃO CRÍTICA: A coluna 'turma_id' agora pode ser nula (nullable).
            // onDelete('set null') significa que se uma turma for apagada,
            // os alunos dessa turma não serão apagados, apenas ficarão sem turma.
            $table->foreignId('turma_id')->nullable()->constrained('turmas')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};