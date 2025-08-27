<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequisicaoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->input('status');

        $query = Requisicao::with('livro', 'cidadao')->latest();

        if ($user->isCidadao()) {
            $query->where('cidadao_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $requisicoes = $query->paginate(10)->withQueryString();

        return view('pages.requisicoes.index', compact('requisicoes', 'status'));
    }

    public function create()
    {
        $livrosDisponiveis = Livro::whereDoesntHave('requisicoes', function ($query) {
            $query->where('status', 'ativa');
        })->get();

        return view('pages.requisicoes.create', compact('livrosDisponiveis'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $ativas = $user->requisicoes()->where('status', 'ativa')->count();
        if ($ativas >= 3) {
            return redirect()
                ->route('requisicoes.create')
                ->withErrors(['limite' => 'Já tem 3 requisições ativas.'])
                ->withInput();
        }

        $livroId = $request->input('livro_id');
        $livroEmUso = Requisicao::where('livro_id', $livroId)
            ->where('status', 'ativa')
            ->exists();
        if ($livroEmUso) {
            return redirect()
                ->route('requisicoes.create')
                ->withErrors(['livro_id' => 'Este livro já está requisitado.'])
                ->withInput();
        }

        $validated = $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'foto_cidadao' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['cidadao_id'] = $user->id;
        $validated['data_inicio'] = now();
        $validated['data_fim_prevista'] = now()->addDays(5);

        if ($request->hasFile('foto_cidadao')) {
            $validated['foto_cidadao'] = $request->file('foto_cidadao')->store('cidadaos', 'public');
        }

        Requisicao::create($validated);

        return redirect()
            ->route('requisicoes.index')
            ->with('success', 'Requisição criada com sucesso!');
    }

    public function show(Requisicao $requisicao)
    {
        $requisicao->loadMissing('livro', 'cidadao');

        return view('pages.requisicoes.show', compact('requisicao'));
    }

    public function edit(Requisicao $requisicao)
    {
        return view('pages.requisicoes.edit', compact('requisicao'));
    }

    public function update(Request $request, Requisicao $requisicao)
    {
        $validated = $request->validate([
            'data_fim_real' => 'nullable|date',
            'status' => 'required|in:ativa,entregue',
        ]);

        $requisicao->update($validated);

        return redirect()
            ->route('requisicoes.index')
            ->with('success', 'Requisição atualizada!');
    }

    public function destroy(Requisicao $requisicao)
    {
        if ($requisicao->foto_cidadao) {
            Storage::disk('public')->delete($requisicao->foto_cidadao);
        }

        $requisicao->delete();

        return redirect()
            ->route('requisicoes.index')
            ->with('success', 'Requisição apagada!');
    }
}
