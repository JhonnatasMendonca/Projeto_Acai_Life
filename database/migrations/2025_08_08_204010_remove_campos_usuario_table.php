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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('sobrenome_usuario');
            $table->dropColumn('telefone_usuario');
            $table->dropColumn('cep_usuario');
            $table->dropColumn('endereco_usuario');
            $table->dropColumn('email_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('sobrenome_usuario', 100)->nullable();
            $table->string('telefone_usuario', 11)->nullable();
            $table->string('cep_usuario', 11)->nullable();
            $table->string('endereco_usuario', 100)->nullable();
            $table->string('email_usuario', 100)->nullable();
        });
    }
};
