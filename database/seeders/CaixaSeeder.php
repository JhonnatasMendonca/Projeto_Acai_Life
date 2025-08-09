<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Caixa;
use Carbon\Carbon;

class CaixaSeeder extends Seeder
{
    public function run()
    {
        // Verifica se já existe um caixa aberto
        if (Caixa::where('status', 'aberto')->exists()) {
            $this->command->info('Já existe um caixa aberto.');
            return;
        }

        // Cria o caixa aberto
        Caixa::create([
            'data' => Carbon::today()->toDateString(),
            'hora_abertura' => Carbon::now()->format('H:i:s'),
            'hora_fechamento' => null,
            'valor_abertura' => 0.00,
            'total_entradas' => 0.00,
            'total_saidas' => 0.00,
            'saldo_final' => 0.00,
            'status' => 'aberto',
            'usuario_abertura_id' => '1',
            'usuario_fechamento_id' => null,
            'observacao' => 'Abertura automática via seeder',
        ]);

        $this->command->info('Caixa aberto criado com sucesso!');
    }
}
