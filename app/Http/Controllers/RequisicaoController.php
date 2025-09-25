<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use App\Mail\RequisicaoCriada;
use App\Traits\RegistaLog;

class RequisicaoController extends Controller
{
    use RegistaLog;

    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->input('status');

        // Listagem com relações
        $query = Requisicao::with('livro', 'cidadao', 'review')->latest();

        // Cidadão vê apenas as suas requisições
        if (method_exists($user, 'isCidadao') && $user->isCidadao()) {
            $query->where('cidadao_id', $user->id);
        }

        // Filtro por status
        if ($status) {
            $query->where('status', $status);
        }

        // Indicadores
        $totalAtivas = Requisicao::where('status', 'ativa')->count();
        $ultimos30Dias = Requisicao::where('created_at', '>=', now()->subDays(30))->count();
        $entreguesHoje = Requisicao::where('status', 'entregue')
            ->whereDate('data_fim_real', now()->toDateString())
            ->count();

        $requisicoes = $query->paginate(10)->withQueryString();

        return view('pages.requisicoes.index', compact(
            'requisicoes',
            'status',
            'totalAtivas',
            'ultimos30Dias',
            'entreguesHoje'
        ));
    }

    public function create(Request $request)
    {
        // Livros sem requisição ativa
        $livrosDisponiveis = Livro::whereDoesntHave('requisicoes', function ($query) {
            $query->where('status', 'ativa');
        })->get();

        $livroSelecionado = $request->query('livro_id');

        // Admin pode escolher cidadão
        $cidadaos = [];
        $user = auth()->user();
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $cidadaos = User::where('role', 'cidadao')->orderBy('name')->get();
        }

        return view('pages.requisicoes.create', compact('livrosDisponiveis', 'livroSelecionado', 'cidadaos'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validação base
        $rules = [
            'livro_id' => 'required|exists:livros,id',
            'foto_cidadao' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Admin escolhe cidadão
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $rules['cidadao_id'] = [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('role', 'cidadao');
                }),
            ];
        }

        $validated = $request->validate($rules);

        // Cidadão alvo
        $cidadaoId = (method_exists($user, 'isAdmin') && $user->isAdmin())
            ? (int) $request->input('cidadao_id')
            : $user->id;

        // Limite de 3 ativas por cidadão
        $ativasCidadao = Requisicao::where('cidadao_id', $cidadaoId)
            ->where('status', 'ativa')
            ->count();

        if ($ativasCidadao >= 3) {
            $cidadaoNome = optional(User::find($cidadaoId))->name;
            $mensagem = $cidadaoNome
                ? "O cidadão {$cidadaoNome} já tem 3 requisições ativas."
                : "Este cidadão já tem 3 requisições ativas.";

            return redirect()
                ->route('requisicoes.create', ['livro_id' => $request->livro_id])
                ->withErrors(['cidadao_id' => $mensagem])
                ->withInput();
        }

        // Verificar stock disponível (para o teste de stock)
        $livro = Livro::find($request->livro_id);
        if ($livro && $livro->stock_venda <= 0) {
            return redirect()
                ->route('requisicoes.create')
                ->withErrors(['livro_id' => 'Este livro não tem stock disponível.'])
                ->withInput();
        }

        // Verificar se livro já está requisitado (ativa)
        $livroEmUso = Requisicao::where('livro_id', $request->livro_id)
            ->where('status', 'ativa')
            ->exists();

        if ($livroEmUso) {
            return redirect()
                ->route('requisicoes.create')
                ->withErrors(['livro_id' => 'Este livro já está requisitado.'])
                ->withInput();
        }

        // Campos calculados
        $validated['cidadao_id'] = $cidadaoId;
        $validated['data_inicio'] = now();
        $validated['data_fim_prevista'] = now()->addDays(5);
        $validated['status'] = 'ativa';

        // Upload foto, se existir
        if ($request->hasFile('foto_cidadao')) {
            $validated['foto_cidadao'] = $request->file('foto_cidadao')->store('cidadaos', 'public');
        }

        // Criar requisição
        $requisicao = Requisicao::create($validated);
        $requisicao->loadMissing('livro', 'cidadao');

        // Log
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $this->registarLog(
            'Requisições',
            $requisicao->id,
            "Criou a requisição #{$seq} para o livro '{$requisicao->livro->nome}'"
        );

        // Email cidadão
        Mail::to($requisicao->cidadao->email)->send(new RequisicaoCriada($requisicao));

        // Email admins
        $admins = User::where('role', 'admin')->pluck('email')->all();
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new RequisicaoCriada($requisicao));
        }

        return redirect()
            ->route('requisicoes.index')
            ->with('success', 'Requisição criada com sucesso!');
    }

    public function show(Requisicao $requisicao)
    {
        $requisicao->loadMissing('livro', 'cidadao', 'review');
        return view('pages.requisicoes.show', compact('requisicao'));
    }

    public function update(Request $request, Requisicao $requisicao)
    {
        $validated = $request->validate([
            'data_fim_real' => 'nullable|date',
            'status' => 'required|in:ativa,entregue',
        ]);

        $requisicao->update($validated);

        // Log
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $this->registarLog(
            'Requisições',
            $requisicao->id,
            "Atualizou a requisição #{$seq} para o livro '{$requisicao->livro->nome}' (novo estado: {$requisicao->status})"
        );

        $livro = $requisicao->livro;

        // Se ficou disponível após atualização, notificar alertas
        $ficouDisponivel = $livro->requisicoes()->where('status', 'ativa')->count() === 0;

        if ($ficouDisponivel) {
            foreach ($livro->alertas()->whereNull('notificado_em')->get() as $alerta) {
                try {
                    Mail::to($alerta->user->email)->send(new \App\Mail\LivroDisponivelMail($livro, $alerta));
                    $alerta->update(['notificado_em' => now()]);
                } catch (\Exception $e) {
                    \Log::error("Erro ao enviar alerta: " . $e->getMessage());
                }
            }
        }

        return redirect()
            ->route('requisicoes.index')
            ->with('success', 'Requisição atualizada!');
    }

    public function destroy(Requisicao $requisicao)
    {
        if ($requisicao->foto_cidadao) {
            Storage::disk('public')->delete($requisicao->foto_cidadao);
        }

        // Dados para log antes do delete
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $livroNome = optional($requisicao->livro)->nome ?? 'Desconhecido';

        // Log antes da eliminação
        $this->registarLog(
            'Requisições',
            $requisicao->id,
            "Apagou a requisição #{$seq} do livro '{$livroNome}'"
        );

        $requisicao->delete();

        return redirect()
            ->route('requisicoes.index')
            ->with('success', 'Requisição apagada!');
    }

    public function devolver(Requisicao $requisicao)
    {
        // Apenas devolve se estiver ativa
        if ($requisicao->status !== 'ativa') {
            return back()->with('warning', 'Esta requisição já foi devolvida ou não está ativa.');
        }

        // Atualizar estado e data de devolução (compatível com enum e testes)
        $requisicao->update([
            'status' => 'entregue',
            'data_fim_real' => now(),
        ]);

        // Log
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $this->registarLog(
            'Requisições',
            $requisicao->id,
            "Devolveu o livro '{$requisicao->livro->nome}' (requisição #{$seq})"
        );

        $livro = $requisicao->livro;

        // Verificar se o livro ficou disponível e notificar alertas pendentes
        $ficouDisponivel = $livro->requisicoes()->where('status', 'ativa')->count() === 0;

        if ($ficouDisponivel) {
            foreach ($livro->alertas()->whereNull('notificado_em')->get() as $alerta) {
                try {
                    Mail::to($alerta->user->email)->send(new \App\Mail\LivroDisponivelMail($livro, $alerta));
                    $alerta->update(['notificado_em' => now()]);
                } catch (\Exception $e) {
                    \Log::error("Erro ao enviar alerta: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Livro devolvido com sucesso!');
    }
}
