<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessarImportacaoPacientes;
use App\Models\Paciente;
use App\Models\Endereco;
use App\Helpers\Validate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $pacientes = Paciente::with('endereco')
            ->where(function ($query) use ($search) {
                $query->where('nome_completo', 'like', "%$search%")
                    ->orWhere('cns', 'like', "%$search%");
            })
            ->orderBy('nome_completo')
            ->paginate(10);

        return response()->json([
            'data' => $pacientes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'nome_completo' => 'required|string|max:255',
            'nome_mae' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'cpf' => 'required|string|max:11|unique:pacientes,cpf',
            'cns' => 'required|string|max:15|unique:pacientes,cns',
            'endereco_cep' => 'required|string|max:8',
            'endereco_logradouro' => 'required|string|max:255',
            'endereco_numero' => 'required|string|max:20',
            'endereco_complemento' => 'nullable|string|max:255',
            'endereco_bairro' => 'required|string|max:255',
            'endereco_cidade' => 'required|string|max:255',
            'endereco_estado' => 'required|string|max:2',
            'foto' => 'nullable|image|max:10240',
        ], [
            'cpf.unique' => 'Já existe um paciente cadastrado com o CPF informado',
            'cns.unique' => 'Já existe um paciente cadastrado com o CNS informado',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $cns = $request->input('cns');
        $cpf = $request->input('cpf');
        $validator = new Validate();

        // Validação de CPF
        if (!$validator->validateCPF($cpf)) {
            return response()->json(['error' => 'CPF inválido.'], 422);
        }

        // Validação de CNS
        if (substr($cns, 0, 1) == '1' || substr($cns, 0, 1) == '2') {
            $valido = $validator->validaCns($cns);
        } elseif (substr($cns, 0, 1) == '7' || substr($cns, 0, 1) == '8' || substr($cns, 0, 1) == '9') {
            $valido = $validator->validaCnsProv($cns);
        } else {
            $valido = false;
        }

        if (!$valido) {
            return response()->json(['error' => 'CNS inválido.'], 422);
        }

        $endereco = Endereco::create([
            'cep' => $request->input('endereco_cep'),
            'logradouro' => $request->input('endereco_logradouro'),
            'numero' => $request->input('endereco_numero'),
            'complemento' => $request->input('endereco_complemento'),
            'bairro' => $request->input('endereco_bairro'),
            'cidade' => $request->input('endereco_cidade'),
            'estado' => $request->input('endereco_estado'),
        ]);

        $foto = $request->file('foto');
        if ($foto) {
            $path = $foto->store('public/fotos');
            $url = Storage::url($path);
        }

        try {
            $paciente = new Paciente([
                'nome_completo' => $request->input('nome_completo'),
                'nome_mae' => $request->input('nome_mae'),
                'data_nascimento' => $request->input('data_nascimento'),
                'cpf' => $cpf,
                'cns' => $cns,
                'foto_url' => $url ?? null,
            ]);

            $paciente->endereco()->associate($endereco);
            $paciente->save();

            return response()->json([
                'data' => $paciente,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            $endereco->delete();
            return response()->json(['error' => 'Erro ao criar paciente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paciente = Paciente::with('endereco')->find($id);

        if (!$paciente) {
            return response()->json([
                'error' => 'Paciente não encontrado',
            ], 404);
        }

        return response()->json([
            'data' => $paciente,
        ]);
    }

    /**
     * Importes determinated patient.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        // Verifica se foi enviado um arquivo no corpo da requisição
        if (!$request->hasFile('arquivo')) {
            return response()->json(['message' => 'Nenhum arquivo enviado'], 400);
        }

        // Verifica se o arquivo enviado é um arquivo CSV válido
        $arquivo = $request->file('arquivo');
        if (!$arquivo->isValid() || $arquivo->getClientOriginalExtension() !== 'csv') {
            return response()->json(['message' => 'Arquivo inválido'], 400);
        }

        // Coloca a importação na fila
        ProcessarImportacaoPacientes::dispatch($arquivo->getClientOriginalName());

        // Retorna uma mensagem de sucesso
        return response()->json(['message' => 'Importação de pacientes em andamento'], 202);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['message' => 'Paciente não encontrado'], 404);
        }

        $validation = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'nome_mae' => 'required|string|max:255',
            'data_nascimento' => 'required|date_format:Y-m-d',
            'foto' => 'nullable|image|max:2048',
            'cep' => 'required|string',
            'logradouro' => 'required|string',
            'numero' => 'required|string',
            'complemento' => 'nullable|string',
            'bairro' => 'required|string',
            'cidade' => 'required|string',
            'estado' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $endereco = Endereco::firstOrCreate(
            ['cep' => $request->cep],
            [
                'endereco' => $request->logradouro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
            ]
        );

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public');
            $paciente->foto_url = $path;
        }

        $paciente->nome_completo = $request->nome;
        $paciente->nome_mae = $request->nome_mae;
        $paciente->data_nascimento = $request->data_nascimento;
        $paciente->endereco()->associate($endereco);

        if ($request->has('cpf') && $paciente->cpf !== $request->cpf) {
            $exists = Paciente::where('cpf', $request->cpf)
                ->where('id', '<>', $paciente->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'message' => 'CPF já cadastrado em outro paciente'
                ], 400);
            }
            $paciente->cpf = $request->cpf;
        }

        if ($request->has('cns') && $paciente->cns !== $request->cns) {
            $exists = Paciente::where('cns', $request->cns)
                ->where('id', '<>', $paciente->id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'message' => 'CNS já cadastrado em outro paciente'
                ], 400);
            }
            $paciente->cns = $request->cns;
        }

        $paciente->save();

        return response()->json([
            'message' => 'Paciente atualizado com sucesso'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json(['message' => 'Paciente não encontrado'], 404);
        }

        DB::beginTransaction();

        try {
            $paciente->delete();

            DB::commit();

            return response()->json(['message' => 'Paciente excluído com sucesso'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Ocorreu um erro ao excluir o paciente: ' . $e->getMessage()], 500);
        }
    }
}
