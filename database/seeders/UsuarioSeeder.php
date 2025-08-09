<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'nome_usuario' => 'Jhonnatas',
                'senha_usuario' => Hash::make('Jhonnatas.56'),
                'login_usuario' => 'jhonnatas',
                'status_usuario' => true,
                'perfil_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}