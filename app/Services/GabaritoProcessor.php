<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GabaritoProcessor
{
    public function __construct(
        private ImageManager $imageManager = new ImageManager(new Driver())
    ) {}

    /**
     * Processa a imagem do gabarito e retorna as respostas detectadas
     */
    public function processar(string $imagePath, int $totalQuestoes): array
    {
        // 1. Pré-processa a imagem (melhora a qualidade para OCR)
        $processedPath = $this->preProcessarImagem($imagePath);

        // 2. Envia para o serviço de OCR (API ou Docker)
        $texto = $this->extrairTexto($processedPath);

        // 3. Remove o arquivo temporário
        Storage::delete($processedPath);

        // 4. Interpreta as respostas do gabarito
        return $this->interpretarGabarito($texto, $totalQuestoes);
    }

    private function preProcessarImagem(string $path): string
    {
        $img = $this->imageManager->read(storage_path('app/' . $path));
        
        return $img->greyscale()
            ->contrast(40)
            ->threshold(65)
            ->save($newPath = storage_path('app/processed_' . basename($path)))
            ->basePath();
    }

    private function extrairTexto(string $imagePath): string
    {
        // Opção 1: Usando API (recomendado para produção)
        return $this->usarOcrApi($imagePath);

        // Opção 2: Usando Tesseract em Docker (para desenvolvimento)
        // return $this->usarTesseractDocker($imagePath);
    }

    private function usarOcrApi(string $imagePath): string
    {
        $response = Http::withHeaders([
            'apikey' => env('OCR_API_KEY')
        ])->attach(
            'file', file_get_contents(storage_path('app/' . $imagePath)), 'gabarito.jpg'
        )->post('https://api.ocr.space/parse/image');

        return $response->json()['ParsedResults'][0]['ParsedText'] ?? '';
    }

    private function interpretarGabarito(string $texto, int $totalQuestoes): array
    {
        // Lógica para mapear o texto extraído para as respostas
        // Exemplo simplificado (adaptar conforme seu formato de gabarito)
        $respostas = [];
        $linhas = explode("\n", trim($texto));

        foreach ($linhas as $linha) {
            if (preg_match('/^([1-9]\d*)[\s.:-]+([A-D])$/i', trim($linha), $matches)) {
                $questao = (int)$matches[1];
                $resposta = strtoupper($matches[2]);
                if ($questao <= $totalQuestoes) {
                    $respostas[$questao] = $resposta;
                }
            }
        }

        return $respostas;
    }
}