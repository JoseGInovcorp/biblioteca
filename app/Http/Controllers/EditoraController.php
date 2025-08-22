<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Editora::withCount('livros');

        if ($request->filled('q')) {
            $query->where('nome', 'like', "%{$request->q}%");
        }

        $sort = $request->get('sort', 'nome');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $editoras = $query->paginate(10)->appends($request->query());

        return view('pages.editoras.index', compact('editoras', 'sort', 'direction'));
    }

    public function create()
    {
        return view('pages.editoras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            $validated['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        Editora::create($validated);

        return redirect()->route('editoras.index')->with('success', 'Editora criada com sucesso!');
    }

    public function edit(Editora $editora)
    {
        return view('pages.editoras.edit', compact('editora'));
    }

    public function update(Request $request, Editora $editora)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            $validated['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        $editora->update($validated);

        return redirect()->route('editoras.index')->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy(Editora $editora)
    {
        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }
        $editora->delete();

        return redirect()->route('editoras.index')->with('success', 'Editora apagada com sucesso!');
    }
}
