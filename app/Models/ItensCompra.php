<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensCompra extends Model
{
    use HasFactory;

    protected $table = 'itens_compras';

    protected $fillable = [
        'registrar_compra_id',
        'produto_id',
        'insumo_id',
        'quantidade',
        'preco_custo',
        'subtotal',
    ];

    public function compra()
    {
        return $this->belongsTo(RegistrarCompra::class, 'registrar_compra_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
