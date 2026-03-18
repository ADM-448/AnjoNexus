<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documentos_gerados', function (Blueprint $table) {
            // Padrão moderno: usa o change() em vez de SQL puro
            $table->foreignId('secao_id')->nullable()->change();
            $table->text('prompt_utilizado')->nullable()->after('conteudo_ia');
        });
    }

    public function down()
    {
        // Limpar registros que não tem seção antes de tentar voltar para NOT NULL
        DB::table('documentos_gerados')->whereNull('secao_id')->delete();

        Schema::table('documentos_gerados', function (Blueprint $table) {
            $table->foreignId('secao_id')->nullable(false)->change();
            $table->dropColumn('prompt_utilizado');
        });
    }
};