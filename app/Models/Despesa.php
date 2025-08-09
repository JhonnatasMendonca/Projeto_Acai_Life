<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'categoria',
        'valor',
        'data_lancamento',
        'status',
        'observacao',
        'usuario_id', 
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_lancamento' => 'date',
        'dia_fixo' => 'boolean',
    ];

    public function usuario() 
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }
}
