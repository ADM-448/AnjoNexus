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
        Schema::table('editais', function (Blueprint $table) {
            $table->json('payload_origem')->nullable()->after('url_oficial')->comment('Dados brutos JSON vindos do scraper');
            $table->timestamp('last_scanned_at')->nullable()->after('updated_at')->comment('Última vez que este edital foi detectado em uma varredura');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('editais', function (Blueprint $table) {
            $table->dropColumn(['payload_origem', 'last_scanned_at']);
        });
    }
};
