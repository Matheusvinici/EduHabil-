<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questoes = [
            // Questões 1-50 (Habilidade 1)
            ['id' => 1, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Qual número indica a quantidade de lápis em um estojo com 8 lápis?', 'alternativa_a' => '8 (quantidade)', 'alternativa_b' => '1º (ordem)', 'alternativa_c' => '10 cm (medida)', 'alternativa_d' => '12345 (código)', 'resposta_correta' => 'A'],
            ['id' => 2, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em uma corrida, João chegou em 2º lugar. O número 2 indica o quê?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'B'],
            ['id' => 3, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 15 no relógio indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Ordem', 'resposta_correta' => 'C'],
            ['id' => 4, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número da camisa de um jogador de futebol indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Código de identificação', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 5, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um prédio, o apartamento 302 indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Código de identificação', 'resposta_correta' => 'D'],
            ['id' => 6, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 50 kg em uma balança indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'A'],
            ['id' => 7, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 12 na placa do carro indica o quê?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Código de identificação', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Medida', 'resposta_correta' => 'B'],
            ['id' => 8, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um supermercado, há 25 maçãs na prateleira. O número 25 indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'D'],
            ['id' => 9, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 3 na posição do pódio indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'A'],
            ['id' => 10, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 30 cm em uma régua indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 11, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Um ônibus tem o número 102 na frente. Esse número indica o quê?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código de identificação', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 12, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 100 em uma nota de dinheiro indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 13, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'No elevador, o número 5 indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Ordem', 'resposta_correta' => 'D'],
            ['id' => 14, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em uma corrida de bicicleta, o ciclista com o número 7 na camisa usa esse número para indicar o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'B'],
            ['id' => 15, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em uma régua, a marca de 15 cm indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Código', 'resposta_correta' => 'C'],
            ['id' => 16, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número do RG de uma pessoa indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Código', 'resposta_correta' => 'D'],
            ['id' => 17, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 40 em uma caixa de lápis indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Código', 'resposta_correta' => 'B'],
            ['id' => 18, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 24 na camisa de um jogador indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'A'],
            ['id' => 19, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'A casa de Maria tem o número 321. Esse número indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 20, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um sorteio, João pegou o número 5. Esse número indica o quê?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 21, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 100 no velocímetro do carro indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'D'],
            ['id' => 22, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'A placa do carro de Carlos tem o número 4567. Esse número é um exemplo de quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Código', 'alternativa_d' => 'Ordem', 'resposta_correta' => 'C'],
            ['id' => 23, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 60 kg em uma balança indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Código', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'A'],
            ['id' => 24, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 1º indica o quê em um concurso?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'A'],
            ['id' => 25, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um estacionamento, há 50 carros. O número 50 indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Ordem', 'resposta_correta' => 'B'],
            ['id' => 26, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número do passaporte de uma pessoa indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Código', 'resposta_correta' => 'D'],
            ['id' => 27, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em uma escola, o aluno foi chamado pelo número da matrícula. Esse número indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 28, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 10 kg em um pacote de arroz indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'D'],
            ['id' => 29, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 9 na numeração da casa de Pedro indica o quê?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 30, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um ranking de melhores alunos, o número 1 indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Ordem', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 31, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 7º indica o quê em uma sequência?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'B'],
            ['id' => 32, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 250 em uma conta de luz indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 33, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 100 no marcador de velocidade do carro indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'D'],
            ['id' => 34, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Um aluno tem o número 1245 no crachá da escola. Esse número indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'C'],
            ['id' => 35, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em uma maratona, Pedro ficou em 10º lugar. O número 10 indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'A'],
            ['id' => 36, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 500 na embalagem de suco indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'D'],
            ['id' => 37, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Qual das opções mostra um número usado como código?', 'alternativa_a' => '50 cm', 'alternativa_b' => '5º lugar', 'alternativa_c' => 'RG 102030', 'alternativa_d' => '15 carros', 'resposta_correta' => 'C'],
            ['id' => 38, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'A ficha de atendimento tem o número 32. Esse número indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'B'],
            ['id' => 39, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 70 cm em um livro indica o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Código', 'resposta_correta' => 'A'],
            ['id' => 40, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 44 na camisa do jogador representa o quê?', 'alternativa_a' => 'Medida', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'C'],
            ['id' => 41, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 365 em um calendário representa o quê?', 'alternativa_a' => 'Quantidade', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'A'],
            ['id' => 42, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um campeonato, Ana ficou na 2ª posição. O número 2 indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Quantidade', 'alternativa_c' => 'Código', 'alternativa_d' => 'Medida', 'resposta_correta' => 'A'],
            ['id' => 43, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'No restaurante, Carlos pegou a senha 57. O número 57 indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 44, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 1,75 m na ficha médica indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 45, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'A casa de João tem o número 18. Esse número representa o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'C'],
            ['id' => 46, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 200 ml em uma garrafa indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'C'],
            ['id' => 47, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Um aluno tem 42 lápis. O número 42 indica o quê?', 'alternativa_a' => 'Código', 'alternativa_b' => 'Ordem', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'C'],
            ['id' => 48, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'O número 90 na régua indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Medida', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'C'],
            ['id' => 49, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em uma viagem, o velocímetro marcava 110 km/h. Esse número indica o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Medida', 'alternativa_c' => 'Código', 'alternativa_d' => 'Quantidade', 'resposta_correta' => 'B'],
            ['id' => 50, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 1, 'enunciado' => 'Em um ônibus, o número 345 representa o quê?', 'alternativa_a' => 'Ordem', 'alternativa_b' => 'Código', 'alternativa_c' => 'Quantidade', 'alternativa_d' => 'Medida', 'resposta_correta' => 'B'],

            // Questões 51-100 (Habilidade 2)
            ['id' => 51, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'João tinha 125 figurinhas e ganhou mais 234. Quantas ele tem agora?', 'alternativa_a' => '349', 'alternativa_b' => '359', 'alternativa_c' => '369', 'alternativa_d' => '379', 'resposta_correta' => 'B'],
            ['id' => 52, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Maria comprou 432 balas e deu 215 para seu irmão. Com quantas ficou?', 'alternativa_a' => '237', 'alternativa_b' => '217', 'alternativa_c' => '227', 'alternativa_d' => '219', 'resposta_correta' => 'C'],
            ['id' => 53, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um mercado tinha 500 laranjas. Foram vendidas 275. Quantas restaram?', 'alternativa_a' => '215', 'alternativa_b' => '225', 'alternativa_c' => '235', 'alternativa_d' => '245', 'resposta_correta' => 'A'],
            ['id' => 54, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Lucas tinha 689 reais e gastou 357. Quanto sobrou?', 'alternativa_a' => '312', 'alternativa_b' => '322', 'alternativa_c' => '332', 'alternativa_d' => '342', 'resposta_correta' => 'B'],
            ['id' => 55, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um caminhão transporta 721 caixas. Foram descarregadas 398. Quantas restam?', 'alternativa_a' => '323', 'alternativa_b' => '333', 'alternativa_c' => '343', 'alternativa_d' => '353', 'resposta_correta' => 'A'],
            ['id' => 56, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um clube tem 275 sócios e ganhou mais 142. Quantos sócios tem agora?', 'alternativa_a' => '407', 'alternativa_b' => '417', 'alternativa_c' => '397', 'alternativa_d' => '387', 'resposta_correta' => 'A'],
            ['id' => 57, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Em uma escola há 246 alunos no turno da manhã e 312 no da tarde. Quantos alunos há no total?', 'alternativa_a' => '558', 'alternativa_b' => '548', 'alternativa_c' => '568', 'alternativa_d' => '578', 'resposta_correta' => 'C'],
            ['id' => 58, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Bruno leu 125 páginas de um livro de 345 páginas. Quantas faltam?', 'alternativa_a' => '230', 'alternativa_b' => '220', 'alternativa_c' => '210', 'alternativa_d' => '200', 'resposta_correta' => 'A'],
            ['id' => 59, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um ônibus tem 462 passageiros e desceram 237. Quantos ficaram?', 'alternativa_a' => '215', 'alternativa_b' => '225', 'alternativa_c' => '235', 'alternativa_d' => '245', 'resposta_correta' => 'A'],
            ['id' => 60, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Uma padaria fez 325 pães e vendeu 198. Quantos sobraram?', 'alternativa_a' => '127', 'alternativa_b' => '137', 'alternativa_c' => '147', 'alternativa_d' => '157', 'resposta_correta' => 'A'],
            ['id' => 61, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Paulo comprou 185 doces e deu 72 para os amigos. Com quantos ficou?', 'alternativa_a' => '103', 'alternativa_b' => '113', 'alternativa_c' => '123', 'alternativa_d' => '133', 'resposta_correta' => 'C'],
            ['id' => 62, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um parque recebeu 800 visitantes em um dia e 567 no outro. Quantos ao todo?', 'alternativa_a' => '1367', 'alternativa_b' => '1357', 'alternativa_c' => '1347', 'alternativa_d' => '1337', 'resposta_correta' => 'A'],
            ['id' => 63, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Em uma feira, havia 750 frutas. Foram vendidas 389. Quantas sobraram?', 'alternativa_a' => '361', 'alternativa_b' => '371', 'alternativa_c' => '381', 'alternativa_d' => '391', 'resposta_correta' => 'A'],
            ['id' => 64, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Em uma biblioteca há 658 livros. Foram emprestados 312. Quantos restam?', 'alternativa_a' => '346', 'alternativa_b' => '356', 'alternativa_c' => '366', 'alternativa_d' => '376', 'resposta_correta' => 'A'],
            ['id' => 65, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Uma loja recebeu 245 camisas e vendeu 123. Quantas sobraram?', 'alternativa_a' => '112', 'alternativa_b' => '122', 'alternativa_c' => '132', 'alternativa_d' => '142', 'resposta_correta' => 'B'],
            ['id' => 66, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Na sala de aula há 372 cadeiras e chegaram mais 164. Quantas ao todo?', 'alternativa_a' => '536', 'alternativa_b' => '526', 'alternativa_c' => '516', 'alternativa_d' => '506', 'resposta_correta' => 'A'],
            ['id' => 67, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Uma família viajou 915 km. Já percorreram 572 km. Quantos faltam?', 'alternativa_a' => '333', 'alternativa_b' => '343', 'alternativa_c' => '353', 'alternativa_d' => '363', 'resposta_correta' => 'A'],
            ['id' => 68, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um caminhão leva 432 caixas e descarregou 287. Quantas ficaram?', 'alternativa_a' => '135', 'alternativa_b' => '145', 'alternativa_c' => '155', 'alternativa_d' => '165', 'resposta_correta' => 'A'],
            ['id' => 69, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Em um estádio cabem 1.200 pessoas. Já entraram 875. Quantas ainda podem entrar?', 'alternativa_a' => '325', 'alternativa_b' => '335', 'alternativa_c' => '345', 'alternativa_d' => '355', 'resposta_correta' => 'A'],
            ['id' => 70, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Um mercado recebeu 645 garrafas de água e vendeu 324. Quantas restaram?', 'alternativa_a' => '321', 'alternativa_b' => '311', 'alternativa_c' => '301', 'alternativa_d' => '291', 'resposta_correta' => 'A'],
            ['id' => 71, 'ano_id' => 2, 'disciplina_id' => 1, 'habilidade_id' => 2, 'enunciado' => 'Uma professora comprou 768 lápis e distribuiu 429. Quantos sobraram?', 'alternativa_a' => '339', 'alternativa_b' => '349', 'alternativa_c' => '359', 'alternativa_d' => '369', 'resposta_correta' => 'A'],
        ];
        DB::table('questoes')->insert($questoes);

    }
    
}