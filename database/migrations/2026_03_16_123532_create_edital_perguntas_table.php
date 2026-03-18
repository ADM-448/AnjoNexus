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
        Schema::create('edital_perguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secao_id')->constrained('edital_secoes')->onDelete('cascade');
            $table->text('texto');
            $table->enum('tipo', ['text', 'textarea'])->default('textarea');
            $table->integer('max_palavras')->nullable();
            $table->integer('ordem')->default(0);
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
        Schema::dropIfExists('edital_perguntas');
    }
};
