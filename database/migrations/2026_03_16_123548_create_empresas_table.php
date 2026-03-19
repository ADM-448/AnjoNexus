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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('razao_social')->nullable();
            $table->string('cnpj')->unique()->nullable();
            $table->string('porte')->nullable();
            $table->string('setor')->nullable();
            $table->string('estado')->nullable();
            $table->integer('n_funcionarios')->nullable();
            $table->string('faturamento_anual')->nullable();
            $table->text('historico_inovacao')->nullable();
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
        Schema::dropIfExists('empresas');
    }
};
