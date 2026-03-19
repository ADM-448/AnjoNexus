<?php

namespace App\Http\Controllers;

use App\Models\Edital;
use App\Models\Empresa;
use App\Models\Projeto;
use Illuminate\Support\Facades\Auth;

/**
 * 🎓 DICA DE ESTUDO: 
 * O DashboardController é um Controller bem simples (básicão).
 * O papel dele é apenas reunir números (Estatísticas) do banco de dados 
 * e mandar essas informações para a tela inicial (dashboard.blade.php).
 */
class DashboardController extends Controller
{
    /**
     * O método index() é disparado quando o usuário entra na página inicial (/dashboard)
     */
    public function index()
    {
        // 1. Quem é o usuário logado?
        // Auth::id() pega o ID do usuário que fez o login.
        // O ->first() pega a primeira empresa que ele encontrar vinculada a esse usuário.
        $empresa = Empresa::where('user_id', Auth::id())->first();
        
        // 2. Prepara variáveis vazias para não dar erro (caso não tenha empresa)
        $projetos = collect(); // collect() cria um array vazio anabolizado do Laravel
        $totalPropostas = 0;
        $rascunhos = 0;

        // 3. Se ele tiver empresa cadastrada, vamos buscar os dados dela
        if ($empresa) {
            
            // Pega a lista com os 10 últimos projetos ("Propostas Geradas") dessa empresa
            $projetos = Projeto::where('empresa_id', $empresa->id)
                ->with(['edital', 'documentosGerados']) // Eager loading — Pega na mesma ida ao banco os dados do edital para não deixar lento (Evita Problema N+1)
                ->latest() // latest() é o atalho para "ordena pelos mais recentes" (OrderBy DESC)
                ->take(10) // Pega no máximo os 10 primeiros
                ->get();   // Finaliza a query e puxa os resultados do banco de fato!
            
            // Conta quantas propostas totais a empresa tem (Número para mostrar nos quadradinhos coloridos)
            $totalPropostas = Projeto::where('empresa_id', $empresa->id)->count();
            
            // Conta quantos não foram enviados (Estão marcados só como Rascunho)
            $rascunhos = Projeto::where('empresa_id', $empresa->id)->where('status', 'Em rascunho')->count();
        }

        // 4. Pega dados globais (Do mundo todo, independente da empresa)
        // Conta todos os editais registrados e raspatos pelo "Radar"
        $totalEditais = Edital::count();
        // Conta só os que você ainda pode mandar projeto (Abertos)
        $editaisAbertos = Edital::where('status', 'Aberto')->count();

        // 5. Retorna a Tela
        // O compact() é uma função mágica do PHP.
        // Ela pega todas as suas variáveis soltas e transforma num "pacote" para entregar pro arquivo HTML / Blade.
        return view('dashboard', compact(
            'projetos',
            'totalEditais',
            'editaisAbertos', 
            'totalPropostas',
            'rascunhos',
            'empresa'
        ));
    }
}
