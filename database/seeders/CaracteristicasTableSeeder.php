<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaracteristicasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém todas as deficiências existentes
        $deficiencias = DB::table('deficiencias')->get();

        // Características pré-definidas para cada deficiência
        $caracteristicasPorDeficiencia = [
            'Deficiência Intelectual' => [
                ['nome' => 'Dificuldade de aprendizagem', 'descricao' => 'Dificuldade em adquirir e reter novos conhecimentos.'],
                ['nome' => 'Comportamento adaptativo limitado', 'descricao' => 'Desafios em habilidades sociais e práticas.'],
            ],
            'Transtorno do Espectro Autista (TEA)' => [
                ['nome' => 'Dificuldade na comunicação social', 'descricao' => 'Desafios em interações sociais e comunicação.'],
                ['nome' => 'Comportamentos repetitivos', 'descricao' => 'Repetição de movimentos ou interesses restritos.'],
            ],
            'Deficiência Física' => [
                ['nome' => 'Mobilidade reduzida', 'descricao' => 'Dificuldade em se locomover de forma independente.'],
                ['nome' => 'Uso de dispositivos de auxílio', 'descricao' => 'Necessidade de cadeira de rodas, muletas, etc.'],
            ],
            'Deficiência Visual' => [
                ['nome' => 'Baixa visão', 'descricao' => 'Visão parcial, mas com limitações significativas.'],
                ['nome' => 'Cegueira total', 'descricao' => 'Perda completa da visão.'],
            ],
            'Deficiência Auditiva' => [
                ['nome' => 'Perda auditiva parcial', 'descricao' => 'Dificuldade em ouvir sons em determinadas frequências.'],
                ['nome' => 'Surdez total', 'descricao' => 'Incapacidade de ouvir qualquer som.'],
            ],
            'Surdocegueira' => [
                ['nome' => 'Comunicação tátil', 'descricao' => 'Uso de métodos como o tadoma ou libras tátil.'],
                ['nome' => 'Dependência de guias', 'descricao' => 'Necessidade de auxílio para locomoção e comunicação.'],
            ],
            'Deficiência Múltipla' => [
                ['nome' => 'Combinação de deficiências', 'descricao' => 'Presença de duas ou mais deficiências simultâneas.'],
                ['nome' => 'Necessidades complexas', 'descricao' => 'Requer suporte especializado em múltiplas áreas.'],
            ],
            'Altas Habilidades/Superdotação' => [
                ['nome' => 'Aprendizado acelerado', 'descricao' => 'Capacidade de aprender rapidamente e com profundidade.'],
                ['nome' => 'Criatividade elevada', 'descricao' => 'Habilidade de pensar fora da caixa e resolver problemas de forma inovadora.'],
            ],
        ];

        // Insere as características para cada deficiência
        foreach ($deficiencias as $deficiencia) {
            if (isset($caracteristicasPorDeficiencia[$deficiencia->nome])) {
                foreach ($caracteristicasPorDeficiencia[$deficiencia->nome] as $caracteristica) {
                    DB::table('caracteristicas')->insert([
                        'deficiencia_id' => $deficiencia->id,
                        'nome' => $caracteristica['nome'],
                        'descricao' => $caracteristica['descricao'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}