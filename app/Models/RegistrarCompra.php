<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrarCompra extends Model
{
    use HasFactory;

    protected $table = 'registrar_compras';

    protected $fillable = [
        'nome_fornecedor',
        'data_compra',
        'valor_total',
        'forma_pagamento',
    ];

    public function itensCompra()
    {
        return $this->hasMany(ItensCompra::class, 'registrar_compra_id');
    }
}
