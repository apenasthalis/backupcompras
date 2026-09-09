<?php

use App\Models\Empresa;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const EMPRESAS = [
        'JOHN DEERE' => [
            'empnome' => 'John Deere',
            'empemail' => 'contato@johndeere.com.br',
            'empendereco' => 'Av. Estados, 1200',
            'empcidade' => 'Catalão',
            'empestado' => 'GO',
            'segmento' => 'Máquinas Agrícolas',
        ],
        'IRMAO SOARES' => [
            'empnome' => 'Irmão Soares',
            'empemail' => 'contato@irmaosoares.com.br',
            'empendereco' => 'Av. Brasil, 500',
            'empcidade' => 'Goiatuba',
            'empestado' => 'GO',
            'segmento' => 'Material de Construção',
        ],
        'FARMACIA SAO MIGUEL ACREUNA' => [
            'empnome' => 'Farmácia São Miguel',
            'empemail' => 'contato@farmaciasaomiguel.com.br',
            'emptelefone' => '(64) 99911-2233',
            'empendereco' => 'Rua Comercial, 120',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Farmácia',
        ],
        'FARMACIA POPULAR ACREUNA' => [
            'empnome' => 'Farmácia Popular de Acreúna',
            'empemail' => 'contato@farmaciapopular.com.br',
            'emptelefone' => '(64) 99922-3344',
            'empendereco' => 'Av. Rio Verde, 45',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Farmácia',
        ],
        'DROGARIA NOSSA SENHORA' => [
            'empnome' => 'Drogaria Nossa Senhora',
            'empemail' => 'contato@drogarianossasenhorago.com.br',
            'emptelefone' => '(64) 99933-4455',
            'empendereco' => 'Praça Central, 88',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Farmácia',
        ],
        'MATERIAL DE CONSTRUCAO ACREUNA' => [
            'empnome' => 'Material de Construção Acreúna',
            'empemail' => 'contato@mdcacreuna.com.br',
            'emptelefone' => '(64) 99944-5566',
            'empendereco' => 'Av. Bandeirantes, 300',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Material de Construção',
        ],
        'ELETRICA ACREUNA' => [
            'empnome' => 'Elétrica Acreúna',
            'empemail' => 'contato@eletricaacreuna.com.br',
            'emptelefone' => '(64) 99955-6677',
            'empendereco' => 'Rua das Indústrias, 210',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Material Elétrico',
        ],
        'GAS ACREUNA' => [
            'empnome' => 'Gás Acreúna',
            'empemail' => 'contato@gasacreuna.com.br',
            'emptelefone' => '(64) 99966-7788',
            'empendereco' => 'Av. dos Pinhais, 77',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Gás',
        ],
        'AREIAL ACREUNA' => [
            'empnome' => 'Areial Acreúna',
            'empemail' => 'contato@areialacreuna.com.br',
            'emptelefone' => '(64) 99977-8899',
            'empendereco' => 'Estrada Municipal, km 2',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Areia',
        ],
        'FERRO E ACO ACREUNA' => [
            'empnome' => 'Ferro e Aço Acreúna',
            'empemail' => 'contato@ferroeacoacreuna.com.br',
            'emptelefone' => '(64) 99988-9900',
            'empendereco' => 'Av. dos Trabalhadores, 410',
            'empcidade' => 'Acreúna',
            'empestado' => 'GO',
            'segmento' => 'Ferro',
        ],
        'FARMACIA GOIANIA' => [
            'empnome' => 'Farmácia Central Goiânia',
            'empemail' => 'contato@farmaciacentralgo.com.br',
            'emptelefone' => '(62) 99911-1122',
            'empendereco' => 'Av. Goiás, 1500',
            'empcidade' => 'Goiânia',
            'empestado' => 'GO',
            'segmento' => 'Farmácia',
        ],
        'CONSTRUCAO GOIANIA' => [
            'empnome' => 'Construtora Goiânia Materiais',
            'empemail' => 'contato@construtoragoiania.com.br',
            'emptelefone' => '(62) 99922-2233',
            'empendereco' => 'Av. Anhanguera, 800',
            'empcidade' => 'Goiânia',
            'empestado' => 'GO',
            'segmento' => 'Material de Construção',
        ],
        'ELETRICA GOIANIA' => [
            'empnome' => 'Elétrica Goiânia',
            'empemail' => 'contato@eletricagoiania.com.br',
            'emptelefone' => '(62) 99933-3344',
            'empendereco' => 'Av. T-63, 320',
            'empcidade' => 'Goiânia',
            'empestado' => 'GO',
            'segmento' => 'Material Elétrico',
        ],
        'GAS GOIANIA' => [
            'empnome' => 'Gás Goiânia',
            'empemail' => 'contato@gasgoiania.com.br',
            'emptelefone' => '(62) 99944-4455',
            'empendereco' => 'Rua 9, 150',
            'empcidade' => 'Goiânia',
            'empestado' => 'GO',
            'segmento' => 'Gás',
        ],
        'FARMACIA CATALAO' => [
            'empnome' => 'Farmácia Catalão',
            'empemail' => 'contato@farmaciacatalao.com.br',
            'emptelefone' => '(64) 99955-5566',
            'empendereco' => 'Av. Raulina Fonseca, 600',
            'empcidade' => 'Catalão',
            'empestado' => 'GO',
            'segmento' => 'Farmácia',
        ],
        'ELETRICA CATALAO' => [
            'empnome' => 'Elétrica Catalão',
            'empemail' => 'contato@eletricacatalao.com.br',
            'emptelefone' => '(64) 99966-6677',
            'empendereco' => 'Av. Vinte de Agosto, 200',
            'empcidade' => 'Catalão',
            'empestado' => 'GO',
            'segmento' => 'Material Elétrico',
        ],
    ];

    public function up(): void
    {
        foreach (self::EMPRESAS as $empcontad => $dados) {
            Empresa::updateOrCreate(
                ['empcontad' => $empcontad],
                array_merge($dados, ['empconta' => $empcontad, 'empstatus' => 'ativo'])
            );
        }
    }

    public function down(): void
    {
        Empresa::whereIn('empcontad', array_keys(self::EMPRESAS))->delete();
    }
};