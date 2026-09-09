<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Segmento;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $segmentos = [
            'Areia' => [
                'Areia Fina',
                'Areia Média',
                'Areia Grossa',
                'Areia Silimosa',
                'Areia Pavimentação',
                'Areia Limpa',
                'Areia Lavada',
                'Areia Sapo',
                'Areia Betoneira',
                'Areia Concreto',
            ],
            'Farmácia' => [
                'Dipirona 500mg',
                'Paracetamol 750mg',
                'Ibuprofeno 600mg',
                'Amoxicilina 500mg',
                'Omeprazol 20mg',
                'Losartana 50mg',
                'Rivotril 2mg',
                'Dorflex',
                'Band-Aim Comum',
                'Álcool Gel 70%',
                'Soro Fisiológico 500ml',
                'Luva Descartável P',
                'Máscara Cirúrgica',
                'Termômetro Digital',
                'Gaze Estéril',
            ],
            'Ferro' => [
                'Barra de Ferro 3/8"',
                'Barra de Ferro 1/2"',
                'Barra de Ferro 5/8"',
                'Barra de Ferro 3/4"',
                'Tela de Cadarço',
                'Tela Soldada',
                'Fio de Solda 3/32"',
                'Fio de Solda 1/8"',
                'Eletrodo E7018',
                'Chapa de Ferro 2mm',
                'Chapa de Ferro 3mm',
                'Tubo de Ferro 2"',
                'Tubo de Ferro 3"',
                'Cantoneira 2"',
                'Cantoneira 3"',
            ],
            'Gás' => [
                'Botijão de Gás 13kg',
                'Botijão de Gás 25kg',
                'Gás de Cozinha 13kg',
                'Gás.GL 25kg',
                'Abraçadeira para Mangueira',
                'Mangueira de Gás 1m',
                'Regulador de Pressão',
                'Válvula de Gás',
                'Conector para Tubulação',
                'Fita Veda Rosca',
            ],
            'Máquinas Agrícolas' => [
                'Filtro de Ar John Deere',
                'Filtro de Óleo John Deere',
                'Filtro de Combustível John Deere',
                'Correia Dentada',
                'Lâmina de Enxada',
                'Enxada Rotativa',
                'Pá de Escavadeira',
                'Lâmina de Trator',
                'Óleo Hidráulico 20L',
                'Óleo de Motor 15W40',
                'Fluido de Transmissão',
                'Rolamento de Roda',
                'Corrente de Esteira',
                'Garfo de Pá Carregadeira',
                'Corpo de Filtro',
            ],
            'Material de Construção' => [
                'Cimento CP-II 50kg',
                'Areia Média m³',
                'Pedra Britada m³',
                'Tijolo 6 Furos',
                'Bloco de Concreto 14x19x39',
                'Argamassa AC-II 20kg',
                'Areia Fina m³',
                'Telha Cerâmica',
                'Telha de Fibrocimento',
                'Tubo PVC 100mm 6m',
                'Tubo PVC 25mm 6m',
                'Joelho PVC 100mm',
                'Conector PVC 100mm',
                'Tinta Latex Branca 25L',
                'Tinta Latex Branca 3.6L',
                'Verniz Sintético 3.6L',
                'Massa corrida 25kg',
                'Massa corrida 1kg',
                'Lixa d\'Água #220',
                'Lixa de Parede #80',
                'Disco de Corte 4 1/2"',
                'Parafuso Chumbador',
                'Espaçador para Piso',
                'Rejunte Cinza 1kg',
                'Rejunte Branco 1kg',
                'Fita Veda Rosca 18mm',
                'Fita Crepe 48mm',
                'Nível de Bolha 60cm',
                'Nível de Bolha 120cm',
                'Chave Inglesa 12"',
                'Alicate Universal',
                'Trena 5m',
            ],
            'Material Elétrico' => [
                'Fio 2.5mm² 100m',
                'Fio 4mm² 100m',
                'Fio 6mm² 100m',
                'Fio 10mm² 100m',
                'Fio 16mm² 100m',
                'Tomada 2P+T 10A',
                'Interruptor Simples',
                'Interruptor Paralelo',
                'Quadro de Distribuição 12',
                'Quadro de Distribuição 18',
                'Disjuntor Monopolar 16A',
                'Disjuntor Monopolar 25A',
                'Disjuntor Bipolar 32A',
                'Disjuntor Tripolar 50A',
                'DR 2P 30mA',
                'DR 4P 30mA',
                'Lâmpada LED 9W',
                'Lâmpada LED 15W',
                'Lâmpada LED tubular 18W',
                'Abraçadeira Nylon 100un',
                'Eletroduto 25mm 3m',
                'Eletroduto 32mm 3m',
                'Luva de Eletroduto 25mm',
                'Curva de Eletroduto 25mm',
                'Caixa de Passagem 100x100',
                'Caixa de Ligação 4x2',
                'Tubo Flexível 25mm',
                'Fita Isolante',
                'Terminal Ligação Rápida',
            ],
        ];

        foreach ($segmentos as $segmentoNome => $produtos) {
            $segmento = Segmento::firstOrCreate(['name' => $segmentoNome]);

            foreach ($produtos as $nome) {
                Product::firstOrCreate(
                    ['name' => $nome],
                    ['segmento_id' => $segmento->id]
                );
            }
        }
    }
}
