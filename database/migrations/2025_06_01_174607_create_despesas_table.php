<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDespesasTable extends Migration
{
    public function up()
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();

            $table->string('nome');
            $table->string('categoria')->nullable();
            $table->decimal('valor', 10, 2);
            $table->date('data_lancamento');
            $table->boolean('dia_fixo')->default(false);
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->text('observacao')->nullable();

            //  NOVO: FK usando usuario_id
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('despesas');
    }
}
