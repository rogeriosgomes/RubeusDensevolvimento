<?php
require_once "autoload/autoload.php";

require_once"src/Totvs/Pessoa.php";
require_once"src/Totvs/Filiacao.php";
require_once"src/Totvs/Aluno.php";

$aluno = new Aluno;
$aluno->GetAluno( "-1"
)


// $filiacao = new Filiacao;
// $codFiliacao = $filiacao->GetFiliacao(69848, 
//                                       42706, 
//                                       "P",
//                                       "integracao",
//                                       '2023-02-06');

// echo $codFiliacao;



// $pessoaFiliacao = new Pessoa;
// $codPessoaFiliacao = $pessoaFiliacao->ValidaSePessoaExiste("ROGERIO SILVEIRA GOMES", "1982-04-05", "GO","RIO VERDE", "");

// $pessoaFilho = new Pessoa;
// $codPessoaFilho = $pessoaFilho->ValidaSePessoaExiste("Arthur Siman Gomes", "2013-08-13", "DF", "Brasília", "");

// $filiacao = new Filiacao;
// $codFiliacao = $filiacao->VinculoDaFiliacao($codPessoaFilho, $codPessoaFiliacao);


// echo "codigo pessoa do Pai do aluno é $codPessoaFiliacao.";
// echo '<br>';
// echo "codigo pessoa do aluno é $codPessoaFilho.";
// echo '<br>';
// echo "codigo de filialacao entre o pai e o aluno é $codFiliacao.";


// $pessoa2 = new Pessoa;
// $novapessoa =$pessoa2->IncluiPessoa('79398',
//                                     "teste Integração", 
//                                     '2000-05-04',
//                                     '63647750042', 
//                                     'DF',
//                                     'Brasília',
//                                     'teste25@gmail.com',
//                                     'M',
//                                     'S', 
//                                     '72015605', 
//                                     'teste',
//                                     's/n', 
//                                     'testecomplemento',
//                                     'tagua', 
//                                     'DF', 
//                                     'Brasília', 
//                                     '61-99999998',
//                                     'integracao',
//                                     '2023-02-06'
//                                 );


// // var_dump($codpessoa);
// // echo "<br>";
//  print($novapessoa);

?>