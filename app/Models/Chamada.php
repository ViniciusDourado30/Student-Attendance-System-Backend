<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chamada extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'user_id',
        'turma_id',
        'data',
    ];

    /**
     * Get the user that created the chamada.
     * Define o relacionamento: uma chamada pertence a um utilizador (professor).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the turma for this chamada.
     * Define o relacionamento: uma chamada pertence a uma turma.
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }
}
