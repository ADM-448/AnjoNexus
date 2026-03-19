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

        Schema::table('empresas', function (Blueprint $table) {
            $table->string('razao_social')->nullable()->change();
            $table->string('cnpj')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('razao_social')->nullable(false)->change();
            $table->string('cnpj')->nullable(false)->change();
        });
    }
};
