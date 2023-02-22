<?php

namespace Database\Factories;

use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnderecoFactory extends Factory
{
    protected $model = Endereco::class;

    public function definition()
    {
        return [
            'cep' => $this->faker->numerify('########'),
            'logradouro' => $this->faker->streetName,
            'numero' => $this->faker->buildingNumber,
            'complemento' => $this->faker->optional()->secondaryAddress,
            'bairro' => $this->faker->citySuffix,
            'cidade' => $this->faker->city,
            'estado' => $this->faker->stateAbbr,
        ];
    }
}
