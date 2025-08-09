<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('ativo')->default(true);
            $table->boolean('usa_insumo')->default(true);
        });

        Schema::create('produto_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('cascade');
            $table->decimal('quantidade', 10, 3)->nullable(); // quantidade usada no produto
            $table->decimal('gramatura', 10, 3)->nullable(); // gramatura individual do insumo no produto
            $table->string('unidade_medida')->nullable(); // g, ml, un
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('ativo');
            $table->dropColumn('usa_insumo');
        });

        Schema::dropIfExists('produto_insumo');
    }
};
