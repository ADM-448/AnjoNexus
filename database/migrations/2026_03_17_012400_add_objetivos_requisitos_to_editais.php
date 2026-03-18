<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->text('objetivos')->nullable()->after('temas');
            $table->text('requisitos')->nullable()->after('objetivos');
            $table->boolean('ia_enriquecido')->default(false)->after('requisitos');
        });
    }

    public function down()
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->dropColumn(['objetivos', 'requisitos', 'ia_enriquecido']);
        });
    }
};