<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perfis = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Perfil com permissões administrativas',
                'permissoes' => range(1, 14),
            ],
            [
                'nome' => 'Atendente',
                'descricao' => 'Perfil com permissões de atendente da loja',
                'permissoes' => [4,13,14], 
            ],
        ];

        foreach ($perfis as $perfil) {
            // Cria o perfil
            $perfilId = DB::table('perfis')->insertGetId([
                'nome' => $perfil['nome'],
                'descricao' => $perfil['descricao'],
            ]);

            // Relaciona permissões ao perfil (pivot table: perfil_permissao)
            if (!empty($perfil['permissoes']) && is_array($perfil['permissoes'])) {
                foreach ($perfil['permissoes'] as $permissaoId) {
                    DB::table('permissao_perfil')->insert([
                        'perfil_id' => $perfilId,
                        'permissao_id' => $permissaoId,
                    ]);
                }
            }
        }
    }
}