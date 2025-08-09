<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itens_compras', function (Blueprint $table) {
            $table->foreignId('produto_id')->nullable()->constrained('produtos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('itens_compras', function (Blueprint $table) {
            $table->dropForeign(['produto_id']);
            $table->dropColumn('produto_id');
        });
    }
};
