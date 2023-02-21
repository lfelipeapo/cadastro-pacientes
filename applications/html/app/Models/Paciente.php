<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_completo',
        'nome_mae',
        'data_nascimento',
        'cpf',
        'cns',
        'endereco_id',
        'foto_url',
    ];

    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }
}
