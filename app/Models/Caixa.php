<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'hora_abertura',
        'hora_fechamento',
        'valor_abertura',
        'total_entradas',
        'total_saidas',
        'saldo_final',
        'status',
        'usuario_abertura_id',  // ✅ Atualizado
        'usuario_fechamento_id', // ✅ Atualizado
        'observacao',
    ];

    public function movimentacoes()
    {
        return $this->hasMany(CaixaMovimentacao::class);
    }

    public function usuarioAbertura()
    {
        return $this->belongsTo(Usuario::class, 'usuario_abertura_id', 'id');
    }

    public function usuarioFechamento()
    {
        return $this->belongsTo(Usuario::class, 'usuario_fechamento_id', 'id');
    }
}
