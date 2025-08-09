<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id(); // Cria um campo id BIGINT auto-incremento como primary key

            // $table->string('cpf_usuario', 11)->unique(); // CPF deixa de ser primary key, vira apenas unique
            $table->string('nome_usuario', 45);
            $table->string('sobrenome_usuario', 100);
            $table->string('telefone_usuario', 11);
            $table->string('senha_usuario', 100);
            $table->string('cep_usuario', 11);
            $table->string('endereco_usuario', 100);
            $table->string('email_usuario', 100)->unique()->nullable();
            $table->string('login_usuario', 45)->unique();
            $table->boolean('status_usuario')->default(true);

            $table->foreignId('perfil_id')->constrained('perfis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
