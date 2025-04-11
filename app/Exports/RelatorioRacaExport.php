<?php

namespace App\Exports;

use App\Http\Controllers\RelatorioController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class RelatorioRacaExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $relatorioController;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->relatorioController = new RelatorioController();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = $this->relatorioController->estatisticasRaca($this->request);

        if ($data instanceof \Illuminate\View\View) {
            $data = $data->getData();
        }

        return collect($data['estatisticasPorRaca'])->map(function ($item) {
            return [
                'Raça/Cor' => $item->raca ?: 'Não informado',
                'Total Respostas' => $item->total_respostas,
                '% do Total' => $item->percentual_total . '%',
                'Acertos' => $item->acertos,
                '% Acerto' => $item->percentual_acerto . '%',
                'Média Ponderada (0-10)' => $item->media_ponderada,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Raça/Cor',
            'Total Respostas',
            '% do Total',
            'Acertos',
            '% Acerto',
            'Média Ponderada (0-10)',
        ];
    }
}