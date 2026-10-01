<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'orcamento_id',
        'description',
        'brand',
        'unit',
        'quantity',
        'preco_lojista',
        'preco_cliente',
    ];

    protected function casts(): array
    {
        return [
            'preco_lojista' => 'decimal:2',
            'preco_cliente' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class, 'orcamento_id', 'idorc');
    }
}
