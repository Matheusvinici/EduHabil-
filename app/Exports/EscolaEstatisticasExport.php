<?php

namespace App\Exports;

use App\Http\Controllers\RelatorioController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EscolaEstatisticasExport implements WithMultipleSheets
{
    protected $simuladoId;

    public function __construct($simuladoId)
    {
        $this->simuladoId = $simuladoId;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        $controller = new RelatorioController();
        $estatisticas = $controller->calcularEstatisticasPorEscola($this->simuladoId);
        
        // Adicione uma aba para cada escola
        foreach ($estatisticas as $escola) {
            $sheets[] = new EscolaSheet($escola);
        }
        
        // Adicione uma aba com dados consolidados
        $sheets[] = new ConsolidadoSheet($estatisticas);
        
        return $sheets;
    }
}