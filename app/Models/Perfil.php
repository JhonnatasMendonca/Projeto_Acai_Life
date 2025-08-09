<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfis';
    
    protected $fillable = ['nome', 'descricao'];

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'permissao_perfil');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'perfil_id');
    }
}
