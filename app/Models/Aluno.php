<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'turma_id',
    ];

    /**
     * Get the turma that owns the aluno.
     */
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}
