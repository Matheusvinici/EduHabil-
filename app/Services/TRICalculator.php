<?php

namespace App\Services;

class TRICalculator
{
    public static function calculateQuestionScore($a, $b, $c, $isCorrect, $theta = 0)
    {
        // Fórmula TRI completa
        $probability = $c + (1 - $c) / (1 + exp(-1.7 * $a * ($theta - $b)));
        
        return $isCorrect ? $probability : (1 - $probability);
    }
    
    public static function calculateTotalScore($responses)
    {
        if ($responses->isEmpty()) return 0;
        
        $totalScore = 0;
        $maxPossibleScore = 0;
        $theta = 0; // Habilidade inicial estimada
        
        // Primeiro verifica se acertou tudo
        $acertouTudo = $responses->every(fn($r) => $r->correta);
        
        if ($acertouTudo) {
            return 10.0; // Retorna 10 diretamente se acertou tudo
        }
        
        // Ajusta o theta (habilidade) iterativamente
        for ($i = 0; $i < 10; $i++) { // 10 iterações para convergência
            $sumNumerator = 0;
            $sumDenominator = 0;
            
            foreach ($responses as $response) {
                $a = $response->pergunta->tri_a ?? 1.0;
                $b = $response->pergunta->tri_b ?? 0.0;
                $c = $response->pergunta->tri_c ?? 0.25;
                
                $p = self::calculateQuestionScore($a, $b, $c, true, $theta);
                $sumNumerator += $a * ($response->correta - $p);
                $sumDenominator += $a * $a * $p * (1 - $p);
            }
            
            if ($sumDenominator != 0) {
                $theta += $sumNumerator / $sumDenominator;
            }
        }
        
        // Calcula o score final com theta ajustado
        foreach ($responses as $response) {
            $a = $response->pergunta->tri_a ?? 1.0;
            $b = $response->pergunta->tri_b ?? 0.0;
            $c = $response->pergunta->tri_c ?? 0.25;
            
            $totalScore += self::calculateQuestionScore($a, $b, $c, $response->correta, $theta);
            $maxPossibleScore += 1;
        }
        
        // Normaliza para escala 0-10
        return $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 10 : 0;
    }
}