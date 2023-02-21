<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function show($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep); // remove caracteres não numéricos
        $cacheKey = "cep_{$cep}";
        $cacheTtl = 86400; // tempo em segundos (24 horas)

        $endereco = Cache::get($cacheKey);
        if (!$endereco) {
            $url = config('services.viacep.url') . "{$cep}/json/";
            $response = Http::get($url);
            $endereco = $response->json();
            Cache::put($cacheKey, $endereco, $cacheTtl);
        }

        return response()->json($endereco);
    }
}
