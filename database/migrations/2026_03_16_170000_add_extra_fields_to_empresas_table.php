<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Contato
            $table->string('telefone')->nullable()->after('cnpj');
            $table->string('email_contato')->nullable()->after('telefone');
            $table->string('representante_legal')->nullable()->after('email_contato');
            $table->string('cargo_representante')->nullable()->after('representante_legal');

            // Sobre o Projeto / Tese de Captação
            $table->text('problema_que_resolve')->nullable()->after('historico_inovacao');
            $table->text('quem_e_impactado')->nullable()->after('problema_que_resolve');
            $table->text('solucao_proposta')->nullable()->after('quem_e_impactado');
            $table->text('como_funciona_na_pratica')->nullable()->after('solucao_proposta');
            $table->string('estagio_solucao')->nullable()->after('como_funciona_na_pratica'); // Ideia, Protótipo, Validação, Operação
            $table->text('diferenciais')->nullable()->after('estagio_solucao');
            $table->text('propriedade_intelectual')->nullable()->after('diferenciais');
            $table->string('segmento_mercado')->nullable()->after('propriedade_intelectual');
            $table->string('publico_alvo_empresa')->nullable()->after('segmento_mercado');

            // Impactos
            $table->text('impacto_economico')->nullable()->after('publico_alvo_empresa');
            $table->text('impacto_social')->nullable()->after('impacto_economico');
            $table->text('impacto_ambiental')->nullable()->after('impacto_social');
            $table->text('metricas_indicadores')->nullable()->after('impacto_ambiental');

            // Financeiro
            $table->string('tipo_recurso_interesse')->nullable()->after('metricas_indicadores'); // Subvenção, Financiamento, Equity
            $table->text('como_recurso_sera_utilizado')->nullable()->after('tipo_recurso_interesse');
        });
    }

    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'telefone', 'email_contato', 'representante_legal', 'cargo_representante',
                'problema_que_resolve', 'quem_e_impactado', 'solucao_proposta', 'como_funciona_na_pratica',
                'estagio_solucao', 'diferenciais', 'propriedade_intelectual', 'segmento_mercado',
                'publico_alvo_empresa', 'impacto_economico', 'impacto_social', 'impacto_ambiental',
                'metricas_indicadores', 'tipo_recurso_interesse', 'como_recurso_sera_utilizado'
            ]);
        });
    }
};
