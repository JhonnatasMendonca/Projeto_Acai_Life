<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    protected $table = 'permissoes';
    
    protected $fillable = ['nome', 'descricao'];

    public function perfis()
    {
        return $this->belongsToMany(Perfil::class, 'permissao_perfil');
    }
}
