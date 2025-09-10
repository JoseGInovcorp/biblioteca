<?php

namespace App\Console\Commands;

use App\Models\Genero;
use Illuminate\Console\Command;

class LimparGeneros extends Command
{
    protected $signature = 'generos:limpar';
    protected $description = 'Normaliza nomes de géneros existentes, traduz para PT e remove duplicados irrelevantes';

    public function handle(): int
    {
        $map = [
            'Fiction' => 'Ficção',
            'Literary Collections' => 'Coleções Literárias',
            'Authors, Portuguese' => 'Autores Portugueses',
            'Children\'s stories' => 'Infantil',
            'Children' => 'Infantil',
            'Young Adult Fiction' => 'Ficção Juvenil',
            'Biography & Autobiography' => 'Biografia',
            'Education' => 'Educação',
            'Philosophy' => 'Filosofia',
            'Psychology' => 'Psicologia',
            'Self-Help' => 'Autoajuda',
            'Health & Fitness' => 'Saúde e Fitness',
            'Child rearing' => 'Parentalidade',
            'Human behavior' => 'Comportamento Humano',
            'Business ethics' => 'Ética Empresarial',
            'Finance, Personal' => 'Finanças Pessoais',
            'Cooking' => 'Culinária',
            'History' => 'História',
            'Travel' => 'Viagens',
            'Boston (Mass.)' => 'Outros',
            'Carpenters' => 'Outros',
            'Bushido' => 'Filosofia',
        ];

        $generos = Genero::all();

        foreach ($generos as $genero) {
            $nomeOriginal = $genero->nome;
            $nomeNormalizado = trim(preg_replace('/\s+/', ' ', $nomeOriginal));

            if (isset($map[$nomeNormalizado])) {
                $novoNome = $map[$nomeNormalizado];

                // Se já existir género com o nome traduzido, funde
                $generoExistente = Genero::where('nome', $novoNome)->first();
                if ($generoExistente && $generoExistente->id !== $genero->id) {
                    // Mover livros para o género existente
                    $genero->livros()->syncWithoutDetaching($generoExistente->livros->pluck('id')->toArray());
                    $genero->delete();
                    $this->info("Fundido '{$nomeOriginal}' em '{$novoNome}'");
                    continue;
                }

                $genero->update(['nome' => $novoNome]);
                $this->info("Atualizado '{$nomeOriginal}' para '{$novoNome}'");
            }
        }

        $this->info('Limpeza concluída.');
        return self::SUCCESS;
    }
}
