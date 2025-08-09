<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaixaMovimentacao extends Model
{
    use HasFactory;

    protected $table = 'caixa_movimentacoes';

    protected $fillable = [
        'caixa_id',
        'tipo',
        'descricao',
        'valor',
        'usuario_id', // ✅ Corrigido
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
