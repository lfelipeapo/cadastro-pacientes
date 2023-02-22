<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index(Request $request)
    {
        $enderecos = Endereco::all();

        return response()->json([
            'data' => $enderecos,
        ]);
    }

    public function create()
    {
        return response()->json([
            'message' => 'Não implementado',
        ], 501);
    }

    public function store(Request $request)
    {
        $endereco = Endereco::create($request->all());

        return response()->json([
            'data' => $endereco,
        ], 201);
    }

    public function show(Endereco $endereco)
    {
        $pacientes = $endereco->pacientes()->get();

        return response()->json([
            'data' => [
                'endereco' => $endereco,
                'pacientes' => $pacientes,
            ],
        ]);
    }

    public function edit(Endereco $endereco)
    {
        return response()->json([
            'message' => 'Não implementado',
        ], 501);
    }

    public function update(Request $request, Endereco $endereco)
    {
        $endereco->update($request->all());

        return response()->json([
            'data' => $endereco,
        ], 200);
    }

    public function destroy(Endereco $endereco)
    {
        $endereco->delete();

        return response()->json([
            'message' => 'Endereço excluído com sucesso.',
        ], 200);
    }
}
