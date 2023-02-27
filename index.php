<?php


require_once "autoload/autoload.php";
//Arquivos totvs
// require_once"src/Totvs/ConnectTotvs.php";
require_once"src/Totvs/Pessoa.php";
require_once"src/Totvs/Filiacao.php";
require_once"src/Totvs/Aluno.php";
require_once"src/Totvs/Cliente.php";
require_once"src/Totvs/RespAcademico.php";

//Arquivos do rubeus
require_once"src/Rubeus/ConnectRubeus.php";
require_once"src/Rubeus/Evento.php";
require_once"src/Rubeus/Contato.php";
require_once"src/Rubeus/Responsavel.php";
//Arquivos de conexões
require_once"Connection/Connection.php";
 //arquivos de funçoes
require_once"Funcao/Funcao.php";
 

date_default_timezone_set('America/Sao_Paulo'); 
header('Content-Type: application/json');
$request = file_get_contents('php://input'); //recebe json do webhook do Rubeus
$result = RecebeEvento($request, $connection);



if($result[0]["tipo"]["id"] ==  1285 )
{    
   // $dados_resp_fin= strip_tags($json_data[0]["descricao"]);
    $dados=strip_tags($result[0]["descricao"]);
   
    $respFinanceiro = new Responsavel;
    $respFinanceiro ->idEvento        = $result[0]["id"];
    $respFinanceiro ->contato         = $result[0]["contato"]["id"];
    $respFinanceiro ->codigo          = $result[0]["contato"]["codigo"];
    $respFinanceiro ->tipo            = str_replace(': ', '', strrchr($dados,':'));
    $respFinanceiro ->nome            = strtoupper(get_parts($dados, 'Nome do Responsável: ', 'Telefone Celular - Filiação 1:'));
    $respFinanceiro ->email           = get_parts($dados, 'E-mail - Filiação 1: ', 'Data de Nascimento do Responsável:');
    $respFinanceiro ->datdtNascimento =  get_parts($dados, 'Data de Nascimento do Responsável: ', 'CPF do Responsável:');
    $respFinanceiro ->cpf             = get_parts($dados, 'CPF do Responsável: ', 'Naturalidade - Filiação 1:');
    $respFinanceiro ->rg              = '';
    $respFinanceiro ->fone            = get_parts($dados, 'Telefone Celular - Filiação 1: +55', 'E-mail - Filiação 1');
    $respFinanceiro ->estadoCivil     ='';
    $respFinanceiro ->naturalidade    = get_parts($dados, 'Naturalidade - Filiação 1: ', 'Tipo de Responsável:');
    
     
     
     $stmt_financ = $connection ->prepare("INSERT INTO RESPFINANCEIRO(idevento, evento, idcontato, codigo, tipo, nome, email, dtnascimento, 
                                cpf, rg, telefone, estadocivil, naturalidade) VALUES (:IDEVENTO,'Completou a quarta etapa',:CONTATO,
                                :CODIGO,:TIPO,:NOME,:EMAIL,:DTNASCIMENTO,:CPF,:RG,:TELEFONE,:ESTADOCIVIL,:NATURALIDADE)");
        
        $stmt_financ->bindParam(":IDEVENTO", $respFinanceiro ->idEvento);
        $stmt_financ->bindParam(":CONTATO", $respFinanceiro ->contato);
        $stmt_financ->bindParam(":CODIGO",$respFinanceiro ->codigo);
        $stmt_financ->bindParam(":TIPO", $respFinanceiro ->tipo );
        $stmt_financ->bindParam(":NOME", $respFinanceiro ->nome);
        $stmt_financ->bindParam(":EMAIL", $respFinanceiro ->email);
        $stmt_financ->bindParam(":DTNASCIMENTO", $respFinanceiro ->datdtNascimento);
        $stmt_financ->bindParam(":CPF", $respFinanceiro ->cpf);
        $stmt_financ->bindParam(":RG", $respFinanceiro ->rg);
        $stmt_financ->bindParam(":TELEFONE", $respFinanceiro ->fone);
        $stmt_financ->bindParam(":ESTADOCIVIL",$respFinanceiro ->estadoCivil);
        $stmt_financ->bindParam(":NATURALIDADE", $respFinanceiro ->naturalidade);
        
        $stmt_financ -> execute();

        
}

if($result[0]["tipo"]["id"] ==  1286 )
{
    $dadosAcademico=strip_tags($result[0]["descricao"]);

    $respAcademico = new Responsavel;
    $respAcademico ->idEvento        = $result[0]["id"];
    $respAcademico ->contato         = $result[0]["contato"]["id"];
    $respAcademico ->codigo          = $result[0]["contato"]["codigo"];
    $respAcademico ->tipo            = str_replace(': ', '', strrchr($dadosAcademico,':'));
    $respAcademico ->nome            = strtoupper(get_parts($dadosAcademico, 'Nome do Responsável Acadêmico 2: ', 'Telefone do Responsável Acadêmico 2'));
    $respAcademico ->email           = get_parts($dadosAcademico, 'E-mail do Responsável Acadêmico 2: ', 'Data de Nascimento do Responsável Acadêmico 2');
    $respAcademico ->datdtNascimento = get_parts($dadosAcademico, 'Data de Nascimento do Responsável Acadêmico 2: ', 'CPF do Responsável Acadêmico 2:');
    $respAcademico ->cpf             = get_parts($dadosAcademico, 'CPF do Responsável Acadêmico 2: ', 'Naturalidade Filiação 2:');
    $respAcademico ->rg              = '';
    $respAcademico ->fone            = get_parts($dadosAcademico, 'Telefone do Responsável Acadêmico 2: +55', 'E-mail do Responsável Acadêmico 2:');
    $respAcademico ->estadoCivil     ='';
    $respAcademico ->naturalidade    = get_parts($dadosAcademico, 'Naturalidade Filiação 2: ', 'Tipo de Responsável Acadêmico:');

    $stmt_acad = $connection ->prepare("INSERT INTO RESPACADEMICO(idevento, evento, idcontato, codigo, tipo, nome, email, dtnascimento, 
    cpf, rg, telefone, estadocivil, naturalidade) VALUES (:IDEVENTO,'Completou a quinta etapa',:CONTATO,
    :CODIGO,:TIPO,:NOME,:EMAIL,:DTNASCIMENTO,:CPF,:RG,:TELEFONE,:ESTADOCIVIL,:NATURALIDADE)");

        $stmt_acad->bindParam(":IDEVENTO", $respAcademico ->idEvento);
        $stmt_acad->bindParam(":CONTATO", $respAcademico ->contato);
        $stmt_acad->bindParam(":CODIGO",$respAcademico ->codigo);
        $stmt_acad->bindParam(":TIPO", $respAcademico ->tipo );
        $stmt_acad->bindParam(":NOME", $respAcademico ->nome);
        $stmt_acad->bindParam(":EMAIL", $respAcademico ->email);
        $stmt_acad->bindParam(":DTNASCIMENTO", $respAcademico ->datdtNascimento);
        $stmt_acad->bindParam(":CPF", $respAcademico ->cpf);
        $stmt_acad->bindParam(":RG", $respAcademico ->rg);
        $stmt_acad->bindParam(":TELEFONE", $respAcademico ->fone);
        $stmt_acad->bindParam(":ESTADOCIVIL",$respAcademico ->estadoCivil);
        $stmt_acad->bindParam(":NATURALIDADE", $respAcademico ->naturalidade);

        $stmt_acad -> execute();

}


if($result[0]["tipo"]["id"] == 1298)
{
    $contato = new Contato;
    $ContatatoAluno = $contato ->RecebeContato($result,$connection);
    $idContato = $ContatatoAluno['Idcontato'];
    $dadosMatricula = print_r($ContatatoAluno , true);
    

    InserirEvento($connection, $idContato, 'Dados', $dadosMatricula );

    //$ContatatoAluno['NOME']

    // valida se aluno existe na tabela de pessoa ou aluno
    $aluno = new Aluno;
    $validaaluno =  array($aluno->validaaluno( $ContatatoAluno['nome'], $ContatatoAluno['data_nascimento_aluno'], $ContatatoAluno['estado_natal_aluno'], $ContatatoAluno['naturalidade_aluno'], $ContatatoAluno['cpf']));

     // inserindo o aluno no Totvs
    
     $codigo_do_aluno =  $validaaluno[0]["Resultado"]['CODPESSOA'];
     $ra_do_aluno     = $validaaluno[0]["Resultado"]['RA'];
     
     $evento = print_r( $validaaluno , true);
     InserirEvento($connection, $idContato, 'valida aluno', $evento );

    // se a pessoa do aluno já esta cadastrado no sistema será atualizado os dados dos aluno
    // verifica os campos: bairro e telefone(esta sendo informado responsavel)
    $aluno = new Aluno;
    $ra= $aluno->CriarAluno($codigo_do_aluno,
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

    $evento = print_r( $ra , true);
    InserirEvento($connection, $idContato, 'Aluno', $evento );      
        
    $pessoa = new Pessoa;    
    $codigo_do_aluno = $pessoa->ValidaSePessoaExiste($ContatatoAluno['nome'],
                                                     $ContatatoAluno['data_nascimento_aluno'],
                                                     $ContatatoAluno['estado_natal_aluno'], 
                                                     $ContatatoAluno['naturalidade_aluno'],
                                                     $ContatatoAluno['cpf'] );

    $evento = print_r( $codigo_do_aluno , true);
    InserirEvento($connection, $idContato, 'Codigo do Aluno', $evento );  
 
    $cliente = new Cliente;
    $codcfo = $cliente->CriarCliente($ContatatoAluno['tipo_resp_financ'],
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
    InserirEvento($connection, $idContato, 'cliente', $evento );                                     
   
    //vincula o pai ou mãe como responsável financeiro do aluno
    if ($ContatatoAluno['tipo_resp_financ'] == 'Pai' or $ContatatoAluno['tipo_resp_financ'] == 'Mãe' or $ContatatoAluno['tipo_resp_financ']  == "M\u00e3e" ) 
    { 
       $pessoa = new Pessoa;
       $codPessoaRespFinan = $pessoa ->ValidaSePessoaExiste($ContatatoAluno['nome_resp_financ'],
                                                            $ContatatoAluno['dt_nasc_resp_financ'],
                                                            $ContatatoAluno['estado_natal_resp_financ'], 
                                                            $ContatatoAluno['cidade_natal_resp_financ'], 
                                                            $ContatatoAluno['cpf_resp_financ'] );
       
        $filiacao = new Filiacao;
        $vinculoDaFiliacao = $filiacao->VinculoDaFiliacao($codigo_do_aluno, $codPessoaRespFinan);
        if ($vinculoDaFiliacao == '0')
        {
            $filiacao = new Filiacao;
            $vinculoDaFiliacao = $filiacao->GetFiliacao($codPessoaRespFinan, 
                                            $codigo_do_aluno, 
                                            $ContatatoAluno['tipo_resp_financ'], 
                                            $login_integracao, 
                                            date( 'Y/m/d h:i:s'));
        }
        
        $evento = print_r( $vinculoDaFiliacao , true);
        InserirEvento($connection, $idContato, 'Fil. Resp. Financ.', $evento ); 
        
    }

    $respAcademico = new RespAcademico;
    $codRespAcad = $respAcademico->CriarRespAcademico($ContatatoAluno['tipo_resp_acad'],
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
    InserirEvento($connection, $idContato, 'Resp Academico', $evento ); 
    
    //vincula o pai ou mãe como responsável acadêmico do aluno
    if ($ContatatoAluno['tipo_resp_acad'] == 'Pai' or $ContatatoAluno['tipo_resp_acad'] == 'Mãe' or $ContatatoAluno['tipo_resp_acad']  == "M\u00e3e" ) 
    {
        $filiacao = new Filiacao;
        $vinculoDaFiliacao = $filiacao->VinculoDaFiliacao($codigo_do_aluno, $codRespAcad);
        if ($vinculoDaFiliacao == '0')
        {
            $filiacao = new Filiacao;
            $vinculoDaFiliacao = $filiacao->GetFiliacao($codRespAcad, 
                                                        $codigo_do_aluno, 
                                                        $ContatatoAluno['tipo_resp_acad'], 
                                                        $login_integracao, 
                                                        date( 'Y/m/d h:i:s'));
        }
        
        $evento = print_r( $vinculoDaFiliacao , true);
        InserirEvento($connection, $idContato, 'Fil. Resp Acad.', $evento ); 

    }


        //define grau parentesco do responsavel financeiro
    if ($ContatatoAluno['tipo_resp_financ'] == 'Pai') 
       {
        $codparentcfo = "6";
       } elseif ($ContatatoAluno['tipo_resp_financ'] == 'Mãe')
       {
        $codparentcfo = "7";
       } else{
        $codparentcfo = "9";
       }

    //define grau parentesco do responsavel acadêmico  $tiporespacad 
    if ($ContatatoAluno['tipo_resp_acad'] == 'Pai')
       {
        $codparentraca = "6";
       } elseif ($ContatatoAluno['tipo_resp_acad']== 'Mãe') 
       {
        $codparentraca = "7";
       } else{
        $codparentraca = "9";
       }

    if ($codcfo =='0')   
    {
    sleep(1);
    $cliente = new Cliente;
    $codcfo = $cliente-> ValidaCliente($ContatatoAluno['cpf_resp_financ']);
    }      
    
    $codigos =array( 'ra'=>$ra,
                     'codigo do cli/for '=>$codcfo,
                     'resp finan. '=> $codparentcfo, 
                     'cod resp. acad' => $codRespAcad,
                     'resp acad.'=>$codparentraca
                                                );

    
    //vincula aluno com pai ou mae e responsavel financeiro
    $client= conectasql();

    $params = array(
            'codSentenca' =>'INT.0006', 
            'codColigada'=>1, 
            'codSistema'=>'S', 
            'parameters'=>"RA=$ra;CODCFO=$codcfo;CODPARENTCFO=$codparentcfo;CODPESSOARACA=$codRespAcad;CODPARENTRACA=$codparentraca" 
                
            ); 
        
    $resultSoap = $client->RealizarConsultaSQL($params);
    $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
    $resultArray = json_decode(json_encode($result), true);

    $evento = print_r( $resultArray , true);
    InserirEvento($connection, $idContato, 'vinc. dos respons.', $evento ); 
  
    
    // //------------------------Mandar retorno da integração para o rubeus---------------------------------------
    
    
    $client= conectasql();
    
    $params = array(
                'codSentenca' =>'INT.0007', 
                'codColigada'=>1, 
                'codSistema'=>'S', 
                'parameters'=>"RA=$ra" 
                    
                ); 
            
    $resultSoap = $client->RealizarConsultaSQL($params);
    $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
    $resultArray = json_decode(json_encode($result), true);
    $inf = $resultArray["Resultado"]["DADOS"];
    $req_dump = print_r($inf , true );
    // $fp = file_put_contents( 'valida.json', $req_dump );  
    
    // $REPORT ='O aluno '.$nome.' foi incluído no Totvs coms os seguintes dados: '.' RA: '.$ra.'<br>'.'COD PESSOA MAE: '.$codpessoa_paimae.'<br>'.'COD_PESSOA_ALUNO: '.$codigo_do_aluno.'<br>'.'CODCFO: '.$codcfo.'<br>';
    $REPORT = print_r(mb_strtoupper($inf) , true ); 
    
    $canalEvento = 265;
    $tokenEvento = 'a4e6881d75492fb89e33af63d41485f8';
    $origemRubeus = '229';

    $params = [
            'tipo' =>  $canalEvento,
            //'descricao' => $RA,
            'descricao' => $REPORT,
            'pessoa'               => [
                'id' =>  $ContatatoAluno['Idcontato'],
            ],
            'origem' => $origemRubeus,
            'token' => $tokenEvento
        ];
    
        // Cria o cURL
        
      $curl = curl_init('https://crmthomasjeffersonhomolog.apprubeus.com.br/api/Evento/cadastro' ); //URL APONTANDO PARA AMBIENTE DE TESTE
      //$curl = curl_init('https://crmthomasjefferson.apprubeus.com.br/api/Evento/cadastro' ); //URL APONTANDO PARA AMBIENTE DE PRODUÇÃO
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Envia a requisição e salva a resposta
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        $req_dump = print_r($response, true );
        $fp = file_put_contents( 'console.json', $req_dump, FILE_APPEND);
    
    
        curl_close($curl);
        
        
        if ($err) {
    
            $respostas['erro'] = $err;
        
        }
    
        $evento = print_r( $response , true);
        InserirEvento($connection, $idContato, 'Evento p Rubeus', $evento ); 
               


}

?>