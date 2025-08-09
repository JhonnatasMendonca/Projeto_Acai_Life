<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    // Nome da tabela (opcional, só se não seguir o padrão Laravel)
    protected $table = 'insumos';

    // Atributos que podem ser atribuídos em massa
    protected $fillable = [
        'nome_insumo',
        'categoria_insumo',
        'descricao_insumo',
        'preco_custo',
        'estoque_insumo',
        'unidade_medida',
        'peso_total',
    ];

    // Casts automáticos de tipos de dados
    protected $casts = [
        'preco_custo' => 'decimal:2',
        'peso_total' => 'decimal:2',
    ];

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_insumo')
            ->withPivot('quantidade', 'gramatura', 'unidade_medida')
            ->withTimestamps();
    }

    public function itensCompra()
    {
        return $this->hasMany(ItensCompra::class);
    }
}
