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

        // 🔹 Incluímos 'review' para saber no index se já existe review associada
        $query = Requisicao::with('livro', 'cidadao', 'review')->latest();

        if ($user->isCidadao()) {
            $query->where('cidadao_id', $user->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // 📊 Indicadores
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
        $livrosDisponiveis = Livro::whereDoesntHave('requisicoes', function ($query) {
            $query->where('status', 'ativa');
        })->get();

        $livroSelecionado = $request->query('livro_id');

        $cidadaos = [];
        if (auth()->user()->isAdmin()) {
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

        // Valida cidadão se for Admin
        if ($user->isAdmin()) {
            $rules['cidadao_id'] = [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('role', 'cidadao');
                })
            ];
        }

        $validated = $request->validate($rules);

        // Determinar cidadão alvo
        $cidadaoId = $user->isAdmin() ? (int) $request->input('cidadao_id') : $user->id;

        // Limite de 3 requisições ativas
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

        // Validar disponibilidade do livro
        $livroEmUso = Requisicao::where('livro_id', $request->livro_id)
            ->where('status', 'ativa')
            ->exists();

        if ($livroEmUso) {
            return redirect()
                ->route('requisicoes.create')
                ->withErrors(['livro_id' => 'Este livro já está requisitado.'])
                ->withInput();
        }

        // Preencher campos
        $validated['cidadao_id'] = $cidadaoId;
        $validated['data_inicio'] = now();
        $validated['data_fim_prevista'] = now()->addDays(5);

        if ($request->hasFile('foto_cidadao')) {
            $validated['foto_cidadao'] = $request->file('foto_cidadao')->store('cidadaos', 'public');
        }

        // Criar requisição
        $requisicao = Requisicao::create($validated);
        $requisicao->loadMissing('livro', 'cidadao');

        // 📜 Registar log da criação (inclui número sequencial se existir)
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $this->registarLog(
            'Requisições',
            $requisicao->id,
            "Criou a requisição #{$seq} para o livro '{$requisicao->livro->nome}'"
        );

        // 📧 Enviar email para cidadão
        Mail::to($requisicao->cidadao->email)
            ->send(new RequisicaoCriada($requisicao));

        // 📧 Enviar email separado para cada admin
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

        // 📜 Registar log da atualização (inclui número sequencial)
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $this->registarLog(
            'Requisições',
            $requisicao->id,
            "Atualizou a requisição #{$seq} para o livro '{$requisicao->livro->nome}' (novo estado: {$requisicao->status})"
        );

        $livro = $requisicao->livro;

        // Verifica se o livro ficou disponível após esta entrega
        $ficouDisponivel = $livro->requisicoes()->where('status', 'ativa')->count() === 0;

        if ($ficouDisponivel) {
            \Log::info("📡 Livro {$livro->id} ficou disponível após entrega. Verificando alertas...");

            foreach ($livro->alertas()->whereNull('notificado_em')->get() as $alerta) {
                try {
                    Mail::to($alerta->user->email)->send(new \App\Mail\LivroDisponivelMail($livro, $alerta));
                    $alerta->update(['notificado_em' => now()]);
                    \Log::info("📧 Alerta enviado para {$alerta->user->email}");
                } catch (\Exception $e) {
                    \Log::error("❌ Erro ao enviar alerta: " . $e->getMessage());
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

        // Guardar dados para o log antes da eliminação
        $seq = $requisicao->numero_sequencial ?? $requisicao->id;
        $livroNome = optional($requisicao->livro)->nome ?? 'Desconhecido';

        // 📜 Registar log da eliminação antes do delete
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

        // Atualizar estado e data de devolução
        $requisicao->update([
            'status' => 'entregue',
            'data_fim_real' => now(),
        ]);

        // 📜 Registar log da devolução (inclui número sequencial)
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
            \Log::info("📡 Livro {$livro->id} ficou disponível após devolução. Verificando alertas...");

            foreach ($livro->alertas()->whereNull('notificado_em')->get() as $alerta) {
                try {
                    Mail::to($alerta->user->email)->send(new \App\Mail\LivroDisponivelMail($livro, $alerta));
                    $alerta->update(['notificado_em' => now()]);
                    \Log::info("📧 Alerta enviado para {$alerta->user->email}");
                } catch (\Exception $e) {
                    \Log::error("❌ Erro ao enviar alerta: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Livro devolvido com sucesso!');
    }
}
