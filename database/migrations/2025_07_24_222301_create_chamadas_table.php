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
        Schema::create('chamadas', function (Blueprint $table) {
            $table->id(); // id da chamada
            
            // id do usuario (professor que fez a chamada)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // id da sala (turma)
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            
            $table->date('data'); // data da chamada
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chamadas');
    }
};
