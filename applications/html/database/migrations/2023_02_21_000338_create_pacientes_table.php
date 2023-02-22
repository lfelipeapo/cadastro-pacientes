<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo');
            $table->string('nome_mae');
            $table->date('data_nascimento');
            $table->string('cpf', 11)->unique();
            $table->string('cns', 15)->unique();
            $table->unsignedBigInteger('endereco_id')->nullable();
            $table->foreign('endereco_id')->references('id')->on('enderecos')->onDelete('set null');
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
        Schema::dropIfExists('pacientes');
    }
};
