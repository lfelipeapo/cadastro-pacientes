<?php

namespace Database\Factories;

use App\Helpers\Validate;
use App\Models\Paciente;
use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\Factory;


class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    public function definition()
    {
        $validator = new Validate();

        $cpf = $this->faker->unique()->numerify('###########');

        while (!$validator->validateCPF($cpf)) {
            $cpf = $this->faker->unique()->numerify('###########');
        }

        $cns = $this->faker->unique()->numerify('###############');

        while (!$validator->validaCns($cns) && !$validator->validaCnsProv($cns)) {
            $cns = $this->faker->unique()->numerify('###############');
        }

        return [
            'nome_completo' => $this->faker->name(),
            'nome_mae' => $this->faker->name(),
            'data_nascimento' => $this->faker->date(),
            'cpf' => $cpf,
            'cns' => $cns,
            'endereco_id' => Endereco::factory(),
        ];
    }
}
