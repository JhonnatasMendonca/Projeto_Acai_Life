<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrarComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrar_compras', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fornecedor')->nullable();
            // $table->foreignId('fornecedor_id')->constrained('fornecedor')->onDelete('cascade');
            $table->date('data_compra');
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->string('forma_pagamento', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrar_compras');
    }
}
