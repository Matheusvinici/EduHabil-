<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // IDs de ano e disciplina (ajuste conforme necessário)
        $anoId = 2; // ID do 2º ano
        $disciplinaMatematicaId = 1; // ID da disciplina de Matemática
        $disciplinaPortuguesId = 2; // ID da disciplina de Língua Portuguesa

        // Habilidades de Matemática
        $habilidadesMatematica = [
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Reconhecer o que os números naturais indicam em diferentes situações: quantidade, ordem, medida ou código de identificação'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Resolver problemas de adição ou de subtração, envolvendo números naturais de até 3 ordens, com os significados de juntar, acrescentar, separar ou retirar'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar a posição ordinal de um objeto ou termo em uma sequência (1º, 2º etc.)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Resolver problemas de multiplicação ou de divisão (por 2, 3, 4 ou 5), envolvendo números naturais, com os significados de formação de grupos iguais ou proporcionalidade (incluindo dobro, metade, triplo ou terça parte)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Escrever números naturais de até 3 ordens em sua representação por algarismos ou em língua materna'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Associar o registro numérico de números naturais de até 3 ordens ao registro em língua materna'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Analisar argumentações sobre a resolução de problemas de adição, subtração, multiplicação ou divisão envolvendo números naturais'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Comparar ou ordenar quantidades de objetos (até 2 ordens)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Comparar ou ordenar números naturais, de até 3 ordens, com ou sem suporte da reta numérica'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar a ordem ocupada por um algarismo ou seu valor posicional (ou valor relativo) em um número natural de até 3 ordens'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Calcular o resultado de adições ou subtrações, envolvendo números naturais de até 3 ordens'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Compor ou decompor números naturais de até 3 ordens por meio de diferentes adições'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar a classificação ou classificar objetos ou representações por figuras, por meio de atributos, tais como cor, forma e medida'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Inferir ou descrever atributos ou propriedades comuns que os elementos que constituem uma sequência de números naturais apresentam'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Inferir o padrão ou a regularidade de uma sequência de números naturais ordenados, de objetos ou de figuras'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Inferir os elementos ausentes em uma sequência de números naturais ordenados, de objetos ou de figuras'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar a localização ou a descrição/esboço do deslocamento de pessoas e/ou de objetos em representações bidimensionais (mapas, croquis etc.)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Descrever ou esboçar o deslocamento de pessoas e/ou objetos em representações bidimensionais (mapas, croquis etc.) ou plantas de ambientes, de acordo com condições dadas'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Reconhecer/ nomear figuras geométricas espaciais (cubo, bloco retangular, pirâmide, cone, cilindro e esfera), relacionando-as com objetos do mundo físico'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Reconhecer/ nomear figuras geométricas planas (círculo, quadrado, retângulo e triângulo)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Comparar comprimentos, capacidades ou massas ou ordenar imagens de objetos com base na comparação visual de seus comprimentos, capacidades ou massas'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Determinar a data de início, a data de término ou a duração de um acontecimento entre duas datas'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Estimar/ inferir medida de comprimento, capacidade ou massa de objetos, utilizando unidades de medida convencionais ou não'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Medir comprimento, capacidade ou massa de objetos'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Determinar o horário de início, o horário de término ou a duração de um acontecimento'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar a medida do comprimento, da capacidade ou da massa de objetos, dada a imagem de um instrumento de medida'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Resolver problemas que envolvam moedas e/ou cédulas do sistema monetário brasileiro'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Reconhecer unidades de medida e/ou instrumentos utilizados para medir comprimento, tempo, massa ou capacidade'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar sequência de acontecimentos relativos a um dia'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Identificar datas, dias da semana, ou meses do ano em calendário ou escrever uma data, apresentando o dia, o mês e o ano'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Relacionar valores de moedas e/ou cédulas do sistema monetário brasileiro, com base nas imagens desses objetos'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Classificar resultados de eventos cotidianos aleatórios como “pouco prováveis”, “muito prováveis”, “certos” ou “impossíveis”'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Representar os dados de uma pesquisa estatística ou de um levantamento em listas, tabelas (simples ou de dupla entrada) ou gráficos (barras simples, colunas simples ou pictóricos)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Ler/ identificar ou comparar dados estatísticos ou informações expressos em tabelas (simples ou de dupla entrada)'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaMatematicaId, 'descricao' => 'Ler/ identificar ou comparar dados estatísticos expressos em gráficos (barras simples, colunas simples ou pictóricos)'],
        ];

        // Habilidades de Língua Portuguesa
        $habilidadesPortugues = [
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Relacionar elementos sonoros das palavras com sua representação escrita'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Ler palavras'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Escrever palavras'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Ler frases'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Localizar informações explícitas em textos'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Reconhecer a finalidade de um texto'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Inferir o assunto de um texto'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Inferir informações em textos verbais'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Inferir informações em textos que articulam linguagem verbal e não verbal'],
            ['ano_id' => $anoId, 'disciplina_id' => $disciplinaPortuguesId, 'descricao' => 'Escrever texto'],
        ];

        // Inserir habilidades de Matemática
        DB::table('habilidades')->insert($habilidadesMatematica);

        // Inserir habilidades de Língua Portuguesa
        DB::table('habilidades')->insert($habilidadesPortugues);
    }
}