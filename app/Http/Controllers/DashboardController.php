<?php

namespace App\Http\Controllers;

use App\Models\Edital;
use App\Models\Empresa;
use App\Models\Projeto;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $empresa = Empresa::where('user_id', Auth::id())->first();
        
        $projetos = collect();
        $totalPropostas = 0;
        $rascunhos = 0;

        if ($empresa) {
            $projetos = Projeto::where('empresa_id', $empresa->id)
                ->with(['edital', 'documentosGerados'])//Eager loading — carrega os relacionamentos edital e documentosGerados em uma única query adicional, evitando o problema N+1
                ->latest()//ordena Desc
                ->take(10)//limita 10 por pag
                ->get();//executar a query
            
            $totalPropostas = Projeto::where('empresa_id', $empresa->id)->count();
            $rascunhos = Projeto::where('empresa_id', $empresa->id)->where('status', 'Em rascunho')->count();
        }

        $totalEditais = Edital::count();
        $editaisAbertos = Edital::where('status', 'Aberto')->count();

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
