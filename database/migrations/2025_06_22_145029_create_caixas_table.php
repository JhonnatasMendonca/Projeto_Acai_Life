<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaixasTable extends Migration
{
    public function up()
    {
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->time('hora_abertura')->nullable();
            $table->time('hora_fechamento')->nullable();

            $table->decimal('valor_abertura', 10, 2)->default(0);
            $table->decimal('total_entradas', 10, 2)->default(0);
            $table->decimal('total_saidas', 10, 2)->default(0);
            $table->decimal('saldo_final', 10, 2)->default(0);

            $table->enum('status', ['aberto', 'fechado'])->default('aberto');

            // ✅ Novos campos FK para usuarios.id
            $table->unsignedBigInteger('usuario_abertura_id')->nullable();
            $table->foreign('usuario_abertura_id')
                ->references('id')
                ->on('usuarios')
                ->onDelete('set null');

            $table->unsignedBigInteger('usuario_fechamento_id')->nullable();
            $table->foreign('usuario_fechamento_id')
                ->references('id')
                ->on('usuarios')
                ->onDelete('set null');

            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('caixas');
    }
}
