<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_credits' => \App\Models\User::sum('creditos_ia'),
            'total_documents' => \App\Models\DocumentoGerado::count(),
            'total_editais' => \App\Models\Edital::count(),
        ];

        $recentUsers = \App\Models\User::latest()->take(5)->get();

        return view('admin.index', compact('stats', 'recentUsers'));
    }

    public function users()
    {
        $users = \App\Models\User::paginate(20);
        return view('admin.users', compact('users'));
    }

    public function addCredits(Request $request, \App\Models\User $user)
    {
        $request->validate(['credits' => 'required|integer|min:1']);
        $user->increment('creditos_ia', $request->credits);

        return back()->with('success', "Adicionados {$request->credits} créditos para {$user->name}");
    }
}
