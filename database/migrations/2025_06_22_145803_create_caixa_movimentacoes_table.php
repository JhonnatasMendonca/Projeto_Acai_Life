<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaixaMovimentacoesTable extends Migration
{
    public function up()
    {
        Schema::create('caixa_movimentacoes', function (Blueprint $table) {
            $table->id();

            // FK para caixas (ok)
            $table->foreignId('caixa_id')
                  ->constrained('caixas')
                  ->onDelete('cascade');

            $table->enum('tipo', ['entrada', 'saida']);
            $table->string('descricao');
            $table->decimal('valor', 10, 2);

            // ✅ Substituir CPF por usuario_id
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('caixa_movimentacoes');
    }
}
