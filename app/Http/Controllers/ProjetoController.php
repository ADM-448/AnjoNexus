<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetoController extends Controller
{
    /**
     * Exibe o documento gerado e os detalhes de um projeto específico.
     */
    public function show($id)
    {
        // Garante que a empresa logada só pode ver os próprios projetos
        $empresa = Empresa::where('user_id', Auth::id())->first();

        if (!$empresa) {
            return redirect()->route('dashboard')->with('error', 'Cadastre sua empresa primeiro.');
        }

        $projeto = Projeto::with(['edital', 'documentosGerados'])
            ->where('empresa_id', $empresa->id)
            ->findOrFail($id);

        return view('projetos.show', compact('projeto'));
    }
}
