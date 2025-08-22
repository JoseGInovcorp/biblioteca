<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Editora;

class EditorasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $editoras = [
            ['nome' => 'Porto Editora', 'logotipo' => 'porto.png'],
            ['nome' => 'Bertrand', 'logotipo' => 'bertrand.png'],
            ['nome' => 'Almedina', 'logotipo' => 'almedina.png'],
        ];

        foreach ($editoras as $editora) {
            Editora::create($editora);
        }
    }
}
