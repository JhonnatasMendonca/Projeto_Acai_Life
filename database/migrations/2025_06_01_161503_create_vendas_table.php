<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendasTable extends Migration
{
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('set null');

            $table->unsignedBigInteger('usuario_id'); // << NOVO: FK para usuarios.id

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');

            $table->string('descricao')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('forma_pagamento', 50);

            $table->string('status', 20)->default('finalizada');
            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
