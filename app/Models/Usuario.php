<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Perfil; 

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome_usuario',
        // 'sobrenome_usuario',
        // 'telefone_usuario',
        'senha_usuario',
        // 'cep_usuario',
        // 'endereco_usuario',
        // 'email_usuario',
        'login_usuario',
        'status_usuario',
        'perfil_id',
    ];

    protected $hidden = [
        'senha_usuario',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    public function permissoes()
    {
        return $this->perfil->permissoes();
    }
}
