<?php

namespace App\Jobs;

use App\Helpers\Validate;
use App\Models\Endereco;
use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;

class ProcessarImportacaoPacientes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $arquivo;

    public function __construct($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    public function getArquivo()
    {
        return $this->arquivo;
    }

    public function handle()
    {
        // Abre o arquivo .csv para leitura

        $arquivo = new UploadedFile(storage_path('app/' . $this->arquivo), $this->arquivo);

        $handle = fopen($arquivo->getRealPath(), 'r');

        if ($handle) {
            // Lê e processa cada linha do arquivo .csv
            while (($linha = fgetcsv($handle, 0, ',')) !== false) {
                // Cria um novo paciente com os dados da linha
                $paciente = new Paciente([
                    'nome_completo' => $linha[0],
                    'nome_mae' => $linha[1],
                    'data_nascimento' => $linha[2],
                    'cpf' => $linha[3],
                    'cns' => $linha[4],
                    'foto_url' => $linha[5],
                ]);

                $validator = Validator::make($paciente->toArray(), [
                    'cpf' => function ($attribute, $value, $fail) {
                        $validate = new Validate;
                        if (!$validate->validateCpf($value)) {
                            $fail('O CPF informado é inválido.');
                        }
                    },
                    'cns' => function ($attribute, $value, $fail) {
                        $validate = new Validate;
                        if (!$validate->validaCns($value) && !$validate->validaCnsProv($value)) {
                            $fail('O CNS informado é inválido.');
                        }
                    },
                ]);

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }

                $paciente->save();

                // Cria um novo endereço com os dados da linha
                $endereco = new Endereco([
                    'cep' => $linha[6],
                    'logradouro' => $linha[7],
                    'numero' => $linha[8],
                    'complemento' => $linha[9],
                    'bairro' => $linha[10],
                    'cidade' => $linha[11],
                    'estado' => $linha[12],
                ]);

                $endereco->save();

                $paciente->endereco_id = $endereco->id;
                $paciente->save();
            }

            fclose($handle);
        }
    }
}
