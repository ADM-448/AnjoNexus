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
        Schema::create('editais', function (Blueprint $table) {
            $table->id();
            $table->text('titulo');
            $table->string('codigo_externo')->nullable(); // ID no site de origem, para deduplicação
            $table->string('orgao')->default('Finep');
            $table->string('email_contato')->nullable();
            $table->text('modalidade')->nullable();
            $table->decimal('orcamento_global', 15, 2)->nullable();
            $table->text('publico_alvo')->nullable();
            $table->text('temas')->nullable();
            $table->string('trl_min')->nullable();
            $table->string('trl_max')->nullable();
            $table->date('data_abertura')->nullable();
            $table->date('data_encerramento')->nullable();
            $table->enum('status', ['Aberto', 'Encerrado', 'Em breve'])->default('Aberto');
            $table->text('url_oficial')->nullable();
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
        Schema::dropIfExists('editais');
    }
};
