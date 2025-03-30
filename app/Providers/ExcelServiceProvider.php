<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider as BaseExcelServiceProvider;

class ExcelServiceProvider extends BaseExcelServiceProvider
{
    protected function bindClasses()
    {
        $this->app->singleton('excel', function ($app) {
            return new \Maatwebsite\Excel\Excel(
                $app->make(\Maatwebsite\Excel\Files\Filesystem::class),
                $app->make(\Maatwebsite\Excel\Exporter::class),
                $app->make(\Maatwebsite\Excel\Importer::class)
            );
        });

        $this->app->alias('excel', \Maatwebsite\Excel\Excel::class);
        $this->app->alias('excel', \Maatwebsite\Excel\Exporter::class);
        $this->app->alias('excel', \Maatwebsite\Excel\Importer::class);
    }
}