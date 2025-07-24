<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presenca extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'chamada_id',
        'aluno_id',
        'status',
    ];

    /**
     * Get the chamada that this presenca record belongs to.
     * Define o relacionamento: um registo de presença pertence a uma chamada.
     */
    public function chamada(): BelongsTo
    {
        return $this->belongsTo(Chamada::class);
    }

    /**
     * Get the aluno for this presenca record.
     * Define o relacionamento: um registo de presença pertence a um aluno.
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }
}
