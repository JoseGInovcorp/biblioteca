<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutorController extends Controller
{
    public function index(Request $request)
    {
        $query = Autor::withCount('livros');

        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->q . '%');
        }

        $sort = $request->get('sort', 'nome');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $autores = $query->paginate(10)->appends($request->query());

        return view('pages.autores.index', compact('autores', 'sort', 'direction'));
    }


    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return view('pages.autores.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('autores', 'public');
        }

        Autor::create($validated);

        return redirect()->route('autores.index')->with('success', 'Autor criado com sucesso!');
    }

    public function edit(Autor $autor)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return view('pages.autores.edit', compact('autor'));
    }

    public function update(Request $request, Autor $autor)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }
            $validated['foto'] = $request->file('foto')->store('autores', 'public');
        }

        $autor->update($validated);

        return redirect()->route('autores.index')->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroy(Autor $autor)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        if ($autor->foto) {
            Storage::disk('public')->delete($autor->foto);
        }
        $autor->delete();

        return redirect()->route('autores.index')->with('success', 'Autor apagado com sucesso!');
    }
}
