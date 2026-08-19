<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'empconta',
        'empnome',
        'empemail',
        'emptelefone',
        'empchave',
        'empsenha_hash',
        'empstatus',
        'emptentativas_falhas',
        'empbloqueado_ate',
        'emptoken_recuperacao',
        'emptoken_expira_em',
        'empcriado_em',
        'empatualizado_em',
        'empcontad',
        'empendereco',
        'empcidade',
        'empestado',
    ];
}
