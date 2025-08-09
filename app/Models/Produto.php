<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'nome_produto',
        'categoria',
        'preco_venda',
        'preco_custo',
        'descricao',
        'estoque_inicial',
        'usa_insumo',
        'gramatura_insumo'
    ];

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'produto_insumo')
            ->withPivot('quantidade', 'gramatura', 'unidade_medida')
            ->withTimestamps();
    }
}
