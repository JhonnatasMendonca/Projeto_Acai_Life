<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_insumo', 45);
            $table->string('categoria_insumo', 45);
            $table->string('descricao_insumo', 255)->nullable();
            $table->decimal('preco_custo', 10, 2);
            $table->integer('estoque_insumo');
            $table->boolean('elegivel_para_produto')->default(false);
            $table->string('unidade_medida', 45);
            $table->decimal('peso_total', 10, 2)->nullable();
            $table->decimal('peso_para_produto', 10, 2)->nullable();
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
        Schema::dropIfExists('insumos');
    }
}
