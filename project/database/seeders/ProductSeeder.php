<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Parafuso Aço Inox M6',
            'Porca Sextavada M8',
            'Arruela Lisa 1/2',
            'Chave Inglesa 12"',
            'Fita Veda Rosca 18mm',
            "Lixa d'Água #220",
            'Tinta Esmalte Branco',
            'Disco Corte 4.1/2"',
            'Cano PVC 25mm 3m',
            'Joelho PVC 25mm',
        ];

        foreach ($products as $name) {
            Product::create(['name' => $name]);
        }
    }
}
