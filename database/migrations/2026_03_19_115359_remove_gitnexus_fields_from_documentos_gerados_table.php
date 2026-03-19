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
        Schema::table('documentos_gerados', function (Blueprint $table) {
            $table->dropColumn(['justificativa_tecnica', 'risco_projeto', 'estagio_maturidade']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentos_gerados', function (Blueprint $table) {
            $table->text('justificativa_tecnica')->nullable();
            $table->text('risco_projeto')->nullable();
            $table->string('estagio_maturidade')->nullable();
        });
    }
};
