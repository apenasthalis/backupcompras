<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orcamento extends Model
{
    protected $primaryKey = 'idorc';

    public $timestamps = false;

    protected $fillable = [
        'idempresa',
        'empnome',
        'empendereco',
        'empcidade',
        'empestado',
        'idcliente',
        'clinome',
        'cliendereco',
        'clicidade',
        'cliestado',
        'dtcri',
        'status',
        'tipstatus',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idcliente', 'contad');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'idempresa', 'empcontad');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(UserProduct::class, 'orcamento_id', 'idorc');
    }
}
