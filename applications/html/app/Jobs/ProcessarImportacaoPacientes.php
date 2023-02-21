<?php

namespace App\Jobs;

use App\Models\Endereco;
use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessarImportacaoPacientes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $arquivo;

    public function __construct($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    public function handle()
    {
        // Abre o arquivo .csv para leitura
        $handle = fopen($this->arquivo->getRealPath(), 'r');

        if ($handle) {
            // Lê e processa cada linha do arquivo .csv
            while (($linha = fgetcsv($handle, 0, ',')) !== false) {
                // Cria um novo paciente com os dados da linha
                $paciente = new Paciente([
                    'nome_completo' => $linha[0],
                    'nome_completo_mae' => $linha[1],
                    'data_nascimento' => $linha[2],
                    'cpf' => $linha[3],
                    'cns' => $linha[4],
                ]);

                $paciente->save();

                // Cria um novo endereço com os dados da linha
                $endereco = new Endereco([
                    'cep' => $linha[5],
                    'logradouro' => $linha[6],
                    'numero' => $linha[7],
                    'complemento' => $linha[8],
                    'bairro' => $linha[9],
                    'cidade' => $linha[10],
                    'estado' => $linha[11],
                ]);

                $endereco->save();

                $paciente->endereco_id = $endereco->id;
                $paciente->save();
            }

            fclose($handle);
        }
    }
}
