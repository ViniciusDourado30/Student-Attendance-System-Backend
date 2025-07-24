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
        Schema::create('presencas', function (Blueprint $table) {
            $table->id(); // ID do registo de presença

            // Conecta este registo a uma chamada específica
            $table->foreignId('chamada_id')->constrained('chamadas')->onDelete('cascade');

            // Conecta este registo a um aluno específico
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');

            // Guarda o status: 'presente', 'ausente', ou 'justificado'
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presencas');
    }
};
