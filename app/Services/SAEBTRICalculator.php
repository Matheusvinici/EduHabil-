<?php

namespace App\Services;

class SAEBTRICalculator
{
    const MAX_SCORE = 10;

    public static function calculateProficiency($respostas)
    {
        if ($respostas->isEmpty()) return 0;
        
        // Verifica se acertou tudo
        if ($respostas->every(fn($r) => $r->correta)) {
            return self::MAX_SCORE;
        }
        
        $theta = 0.0; // Habilidade inicial estimada
        
        // Estimação por máxima verossimilhança (MLE)
        for ($i = 0; $i < 20; $i++) {
            $sumNumerator = 0;
            $sumDenominator = 0;
            
            foreach ($respostas as $resposta) {
                $a = $resposta->tri_a ?? $resposta->pergunta->tri_a;
                $b = $resposta->tri_b ?? $resposta->pergunta->tri_b;
                $c = $resposta->tri_c ?? $resposta->pergunta->tri_c;
                
                $p = self::itemProbability($a, $b, $c, $theta);
                $sumNumerator += $a * ($resposta->correta - $p);
                $sumDenominator += $a * $a * $p * (1 - $p);
            }
            
            if (abs($sumNumerator) < 0.001) break; // Convergência
            
            if ($sumDenominator != 0) {
                $theta += $sumNumerator / $sumDenominator;
            }
        }
        
        // Calcular escore final
        $totalScore = 0;
        $maxScore = 0;
        
        foreach ($respostas as $resposta) {
            $a = $resposta->tri_a ?? $resposta->pergunta->tri_a;
            $b = $resposta->tri_b ?? $resposta->pergunta->tri_b;
            $c = $resposta->tri_c ?? $resposta->pergunta->tri_c;
            
            $totalScore += self::itemProbability($a, $b, $c, $theta);
            $maxScore += 1;
        }
        
        // Normalizar para escala 0-10
        return $maxScore > 0 ? ($totalScore / $maxScore) * self::MAX_SCORE : 0;
    }

    protected static function itemProbability($a, $b, $c, $theta)
    {
        // Modelo logístico de 3 parâmetros (3PL)
        return $c + (1 - $c) / (1 + exp(-1.7 * $a * ($theta - $b)));
    }
}