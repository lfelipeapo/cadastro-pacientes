<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Endereco;

class EnderecoSeeder extends Seeder
{
    public function run()
    {
        Endereco::factory()->count(50)->create();
    }
}
