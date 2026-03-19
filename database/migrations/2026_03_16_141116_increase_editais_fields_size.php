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
        if (config('database.default') === 'sqlite' || env('DB_CONNECTION') === 'sqlite') {
            return;
        }

        Schema::table('editais', function (Blueprint $table) {
            // Mudando para text() para suportar títulos e URLs gigantes (vinda de redirecionamento do Google por exemplo)
            $table->text('titulo')->change();
            $table->text('url_oficial')->change();
            $table->text('modalidade')->change();
            $table->text('publico_alvo')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Trunca os dados para 255 caracteres antes de encolher a coluna para evitar erro de rollback
        \Illuminate\Support\Facades\DB::table('editais')->update([
            'titulo' => \Illuminate\Support\Facades\DB::raw('LEFT(titulo, 255)'),
            'url_oficial' => \Illuminate\Support\Facades\DB::raw('LEFT(url_oficial, 255)'),
            'modalidade' => \Illuminate\Support\Facades\DB::raw('LEFT(modalidade, 255)'),
            'publico_alvo' => \Illuminate\Support\Facades\DB::raw('LEFT(publico_alvo, 255)'),
        ]);

        Schema::table('editais', function (Blueprint $table) {
            $table->string('titulo')->change();
            $table->string('url_oficial')->change();
            $table->string('modalidade')->change();
            $table->string('publico_alvo')->change();
        });
    }
};
