<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    // Dentro do modelo Turma.php
    protected $fillable = [
        'name',      // Antes era 'nome'
        'subject',   // Antes era 'materia'
        'user_id',
    ];

    /**
     * Get the user that owns the turma.
     * Define o relacionamento: uma turma pertence a um utilizador (professor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the alunos for the turma.
     * Define o relacionamento: uma turma pode ter muitos alunos.
     */
    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class);
    }
}
