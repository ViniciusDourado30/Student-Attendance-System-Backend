<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aluno extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name', // Alterado de 'full_name'
        'turma_id',
    ];

    /**
     * Get the turma that the aluno belongs to.
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }
}
