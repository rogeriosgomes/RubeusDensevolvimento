<?php


//Arquivos totvs
require_once "src/Totvs/ConnectTotvs.php"; 
require_once "src/Totvs/Pessoa.php";
require_once "src/Totvs/Filiacao.php";
require_once "src/Totvs/Aluno.php";
require_once "src/Totvs/Cliente.php";
require_once "src/Totvs/RespAcademico.php";

//Arquivos do rubeus
  require_once "src/Rubeus/ConnectRubeus.php";
  require_once "src/Rubeus/Evento.php";
  require_once "src/Rubeus/Contato.php";
// require_once "src/Rubeus/Responsavel.php";
  require_once "src/Rubeus/Mensagem.php";
//Arquivos de conexões
  require_once "Connection/Connection.php";
//require_once "Connection/conexao.php"; 
//arquivos de funçoes
 require_once "Repositorio/Funcoes.php";
 

date_default_timezone_set('America/Sao_Paulo'); 
header('Content-Type: application/json');
$request = file_get_contents('php://input'); //recebe json do webhook do Rubeus
$evento = new \App\Rubeus\Evento;
$result = $evento->RecebeEvento($request);



if($result[0]["tipo"]["id"] == 1287 or $result[0]["tipo"]["id"] == 1491 or $result[0]["tipo"]["id"] == 1687)
{
    $contato = new \App\Rubeus\Contato;
    $ContatatoAluno = $contato ->RecebeContato($result);
    
    $idContato = $ContatatoAluno['Idcontato'];
    $dadosMatricula = print_r($ContatatoAluno , true);

    //$ContatatoAluno['NOME']

    //valida se aluno existe na tabela de pessoa ou aluno
    $aluno = new \App\Totvs\Aluno;
    $validaaluno =  array($aluno->ValidaAluno( $idContato,
                                               $ContatatoAluno['nome'], 
                                               $ContatatoAluno['data_nascimento_aluno'], 
                                               $ContatatoAluno['estado_natal_aluno'], 
                                               $ContatatoAluno['naturalidade_aluno'], 
                                               $ContatatoAluno['cpf']));


     // inserindo o aluno no Totvs
    
     $codigo_do_aluno =  $validaaluno[0]["Resultado"]['CODPESSOA'];
     $ra_do_aluno     =  $validaaluno[0]["Resultado"]['RA'];

    // se a pessoa do aluno já esta cadastrado no sistema será atualizado os dados dos aluno
    // verifica os campos: bairro e telefone(esta sendo informado responsavel)
    $aluno = new \App\Totvs\Aluno;
    $ra= $aluno->CriarAluno($idContato,
                            $codigo_do_aluno,
                            $ra_do_aluno ,
                            $ContatatoAluno['nome'], 
                            $ContatatoAluno['data_nascimento_aluno'],
                            $ContatatoAluno['cpf'],
                            $ContatatoAluno['estado_natal_aluno'], 
                            $ContatatoAluno['naturalidade_aluno'], 
                            $ContatatoAluno['email'], 
                            $ContatatoAluno['sexo'], 
                            'S', 
                            $ContatatoAluno['cep'], 
                            $ContatatoAluno['endereco_casa'], 
                            $ContatatoAluno['numero_casa'],
                            '',
                            $ContatatoAluno['bairro'],
                            $ContatatoAluno['uf'], 
                            $ContatatoAluno['cidade'], 
                            $ContatatoAluno['telefone'],
                            $ContatatoAluno['ano_escolar'], 
                            $login_integracao, 
                            date( 'Y/m/d h:i:s'));

    

 
    $pessoa = new \App\Totvs\Pessoa;    
    $codigo_do_aluno = $pessoa->ValidaSePessoaExiste($idContato,
                                                     $ContatatoAluno['nome'],
                                                     $ContatatoAluno['data_nascimento_aluno'],
                                                     $ContatatoAluno['estado_natal_aluno'], 
                                                     $ContatatoAluno['naturalidade_aluno'],
                                                     $ContatatoAluno['cpf'] );

    $evento = print_r( $codigo_do_aluno , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento( $idContato, 'Codigo do Aluno', $evento );  


    $cliente = new \App\Totvs\Cliente;
    $codcfo = $cliente->CriarCliente(  $idContato,
                                       $ContatatoAluno['tipo_resp_financ'],
                                       $ContatatoAluno['nome_resp_financ'],
                                       $ContatatoAluno['cpf_resp_financ'] ,
                                       $ContatatoAluno['endereco_casa'],
                                       $ContatatoAluno['numero_casa'], 
                                       $ContatatoAluno['bairro'],
                                       $ContatatoAluno['cidade'],
                                       $ContatatoAluno['uf'],
                                       $ContatatoAluno['cep'], 
                                       $ContatatoAluno['fone_resp_financ'], 
                                       $ContatatoAluno['email_resp_financ'], 
                                       $ContatatoAluno['dt_nasc_resp_financ'],  
                                       'S',  
                                       $ContatatoAluno['estado_natal_resp_financ'], 
                                       $ContatatoAluno['cidade_natal_resp_financ'], 
                                       $login_integracao, 
                                       date( 'Y/m/d h:i:s'));
    
    $evento = print_r( $codcfo , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento( $idContato, 'cliente', $evento );   

   
    //vincula o pai ou mãe como responsável financeiro do aluno
    if ($ContatatoAluno['tipo_resp_financ'] == 'Pai' or $ContatatoAluno['tipo_resp_financ'] == 'Mãe' or $ContatatoAluno['tipo_resp_financ']  == "M\u00e3e" ) 
    { 
       $pessoa = new \App\Totvs\Pessoa;
       $codPessoaRespFinan = $pessoa ->ValidaSePessoaExiste($idContato,
                                                            $ContatatoAluno['nome_resp_financ'],
                                                            $ContatatoAluno['dt_nasc_resp_financ'],
                                                            $ContatatoAluno['estado_natal_resp_financ'], 
                                                            $ContatatoAluno['cidade_natal_resp_financ'], 
                                                            $ContatatoAluno['cpf_resp_financ'] );
       
        $filiacao = new \App\Totvs\Filiacao;
        $vinculoDaFiliacao = $filiacao->VinculoDaFiliacao($idContato,
                                                          $codigo_do_aluno, 
                                                          $codPessoaRespFinan);
        if ($vinculoDaFiliacao == '0')
        {
            $filiacao = new \App\Totvs\Filiacao;
            $vinculoDaFiliacao = $filiacao->GetFiliacao($idContato,
                                                        $codPessoaRespFinan, 
                                                        $codigo_do_aluno, 
                                                        $ContatatoAluno['tipo_resp_financ'], 
                                                        $login_integracao, 
                                                        date( 'Y/m/d h:i:s'));
        }
        
        $evento = print_r( $vinculoDaFiliacao , true);
        $conn = new \App\Connection\Conn;
        $conn->InserirEvento( $idContato, 'Fil. Resp. Financ.', $evento ); 
        
        
    }

    $respAcademico = new \App\Totvs\RespAcademico;
    $codRespAcad = $respAcademico->CriarRespAcademico($idContato,
                                                      $ContatatoAluno['tipo_resp_acad'],
                                                      $ContatatoAluno['nome_resp_acad'],
                                                      $ContatatoAluno['dt_nasc_resp_acad'],
                                                      $ContatatoAluno['cpf_resp_acad'],
                                                      $ContatatoAluno['estado_natal_resp_acad'], 
                                                      $ContatatoAluno['cidade_natal_resp_acad'],
                                                      $ContatatoAluno['email_resp_financ'], 
                                                      $ContatatoAluno['sexo'], 
                                                      'S', 
                                                      $ContatatoAluno['cep'], 
                                                      $ContatatoAluno['endereco_casa'], 
                                                      $ContatatoAluno['numero_casa'],
                                                      '',
                                                      $ContatatoAluno['bairro'],
                                                      $ContatatoAluno['uf'], 
                                                      $ContatatoAluno['cidade'], 
                                                      $ContatatoAluno['fone_resp_acad'], 
                                                      $login_integracao, 
                                                      date( 'Y/m/d h:i:s' ));

    $evento = print_r( $codRespAcad , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento( $idContato, 'Resp Academico', $evento ); 
   
  
  
    //vincula o pai ou mãe como responsável acadêmico do aluno
    if ($ContatatoAluno['tipo_resp_acad'] == 'Pai' or $ContatatoAluno['tipo_resp_acad'] == 'Mãe' or $ContatatoAluno['tipo_resp_acad']  == "M\u00e3e" ) 
    {
        $filiacao = new App\Totvs\Filiacao;
        $vinculoDaFiliacao = $filiacao->VinculoDaFiliacao($idContato,
                                                          $codigo_do_aluno, 
                                                          $codRespAcad);
        if ($vinculoDaFiliacao == '0')
        {
            $filiacao = new App\Totvs\Filiacao;
            $vinculoDaFiliacao = $filiacao->GetFiliacao($idContato,
                                                        $codRespAcad, 
                                                        $codigo_do_aluno, 
                                                        $ContatatoAluno['tipo_resp_acad'], 
                                                        $login_integracao, 
                                                        date( 'Y/m/d h:i:s'));
        }
        
        $evento = print_r( $vinculoDaFiliacao , true);
        $conn = new \App\Connection\Conn;
        $conn->InserirEvento( $idContato, 'Fil. Resp Acad.', $evento ); 
       
    }

    $aluno = new \App\Totvs\Aluno;
    $vinculaResponsaveis = $aluno->VinculaResponsaveis($idContato,
                                                       $ra,
                                                       $codcfo,
                                                       $ContatatoAluno['tipo_resp_financ'],
                                                       $codRespAcad,
                                                       $ContatatoAluno['tipo_resp_acad']
                                                       );
    $evento = print_r( $vinculaResponsaveis , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento( $idContato, 'vinc. dos respons.', $evento ); 
   
  
    // // //------------------------Mandar retorno da integração para o rubeus---------------------------------------
   
    $mensagemEvento = new \App\Rubeus\Mensagem;
    $mensagem = print_r(mb_strtoupper($mensagemEvento->MensagemConfirmacao($ra)), true);

    $evento = print_r( $mensagem , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento( $idContato, 'mensagem', $evento ); 
  
    $retornoRubeus = $mensagemEvento->EnvioEvento($ContatatoAluno['Idcontato'], $mensagem );
    
    $evento = print_r( $retornoRubeus , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento( $idContato, 'Evento p Rubeus', $evento ); 
  
                  

  }



?>