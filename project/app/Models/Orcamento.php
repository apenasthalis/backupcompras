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
        'desconto',
        'frete',
        'solicita_desconto',
    ];

    protected function casts(): array
    {
        return [
            'desconto' => 'decimal:2',
            'frete' => 'decimal:2',
            'solicita_desconto' => 'boolean',
        ];
    }

    public function rotuloStatus(): string
    {
        return match ($this->status) {
            'C' => 'Cobrado',
            'M' => 'Modificado',
            'D' => 'Desconto Solicitado',
            'P' => 'Pronto',
            default => 'Aberto',
        };
    }

    public function classeStatus(): string
    {
        return match ($this->status) {
            'C' => 'badge-cobrado',
            'M' => 'badge-modificado',
            'D' => 'badge-desconto',
            default => 'badge-aberto',
        };
    }

    public function totalLojista(): float
    {
        return round((float) $this->itens->sum(
            fn (UserProduct $item): float => (float) $item->preco_lojista * (float) $item->quantity
        ), 2);
    }

    public function totalCliente(): float
    {
        return round((float) $this->itens->sum(
            fn (UserProduct $item): float => (float) $item->preco_cliente * (float) $item->quantity
        ), 2);
    }

    public function totalFinal(): float
    {
        return round(
            $this->totalCliente() * (1 - ((float) $this->desconto) / 100) + (float) $this->frete,
            2
        );
    }

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
