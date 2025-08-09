<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 
        'usuario_id', // << ATUALIZADO
        'descricao',
        'subtotal',
        'desconto',
        'total',
        'forma_pagamento',
        'status',
        'observacao',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id'); // << ATUALIZADO
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_venda')
                    ->withPivot('quantidade', 'preco_unitario')
                    ->withTimestamps();
    }
}

