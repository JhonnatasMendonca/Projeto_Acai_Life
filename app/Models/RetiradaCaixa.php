<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetiradaCaixa extends Model
{
    use HasFactory;

    protected $table = 'retirada_caixa';

    protected $fillable = [
        'caixa_id',
        'usuario_id', // atualizado
        'valor',
        'descricao',
        'data_retirada',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_retirada' => 'datetime',
    ];

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }
}
