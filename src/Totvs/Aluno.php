<?php

 namespace App\Totvs;


class Aluno
{
  // Propriedades

  public $idContato;
  public $ra; 
  public $codpessoa;  
  public $nome;
  public $cpf;
  public $dtnascimento;
  public $estadonatal; 
  public $naturalidade;
  public $email;
  public $sexo;
  public $estadocivil;
  public $cep;
  public $rua;
  public $numero; 
  public $complemento;
  public $bairro;
  public $uf;
  public $cidade; 
  public $telefone; 
  public $Cod_Ano_Escolar;
  public $login_integracao; 
  public $data_atual;

  //Métodos


  public function GetAluno($idContato,
                           $ra, 
                           $codPessoa,  
                           $nome, 
                           $email, 
                           $cpf, 
                           $dtNascimento, 
                           $estadoNatal, 
                           $naturalidade, 
                           $cep, 
                           $sexo, 
                           $rua, 
                           $numero, 
                           $complemento, 
                           $bairro, 
                           $uf, 
                           $cidade, 
                           $telefone, 
                           $AnoEscolar , 
                           $loginIntegracao, 
                           $dataAtual
                            )
    {
        
           
            $conect = new ConnectTotvs;
            $client =$conect->conectaDataServer();
            
            // insere os dados do aluno no cadastro de aluno e pessoa
            $xml_Aluno = <<<XML
                <EduAluno>
                <SAluno>
                <CODCOLIGADA>1</CODCOLIGADA>
                <RA>$ra</RA>
                <CODPESSOA>$codPessoa</CODPESSOA>
                <CODTIPOCURSO>1</CODTIPOCURSO>
                <CODIGO>$codPessoa</CODIGO>
                <NOME>$nome</NOME>
                <EMAIL>$email</EMAIL>
                <CPF>$cpf</CPF>
                <DTNASCIMENTO>$dtNascimento</DTNASCIMENTO>
                <NACIONALIDADE>10</NACIONALIDADE>
                <ESTADONATAL>$estadoNatal</ESTADONATAL>
                <NATURALIDADE>$naturalidade</NATURALIDADE>
                <ESTADOCIVIL>S</ESTADOCIVIL> 
                <CEP>$cep</CEP>
                <SEXO>$sexo</SEXO>
                <RUA>$rua</RUA>
                <NUMERO>$numero</NUMERO>
                <COMPLEMENTO>$complemento</COMPLEMENTO>
                <BAIRRO>$bairro</BAIRRO>
                <ESTADO>$uf</ESTADO>
                <CIDADE>$cidade</CIDADE>
                <PAIS>Brasil</PAIS>
                <TELEFONE2>$telefone</TELEFONE2>
                <GRAUULTIMAINST>$AnoEscolar</GRAUULTIMAINST>
                <ESTADOROW>0</ESTADOROW>
                <ROWVALIDA>0</ROWVALIDA>
                <ALUNO>1</ALUNO>
                <CODSTATUS>-1</CODSTATUS>
                <RECCREATEDBY>$loginIntegracao</RECCREATEDBY>
                <RECCREATEDON>$dataAtual</RECCREATEDON>
                <RECMODIFIEDBY>$loginIntegracao</RECMODIFIEDBY>
                <RECMODIFIEDON>$dataAtual</RECMODIFIEDON>
                
                </SAluno>
                <SAlunoCompl>
                    <CODCOLIGADA>1</CODCOLIGADA>
                    <RA>$ra</RA>
                    <RECCREATEDBY>$loginIntegracao</RECCREATEDBY>
                    <RECCREATEDON>$dataAtual</RECCREATEDON>
                    <RECMODIFIEDBY>$loginIntegracao</RECMODIFIEDBY>
                    <RECMODIFIEDON>$dataAtual</RECMODIFIEDON>
                </SAlunoCompl>
                </EduAluno>
            XML;                         

            $xml_Aluno_ = array('DataServerName' =>'EduAlunoData', 'XML'=>$xml_Aluno,'Contexto'=>'CODCOLIGADA=1;CODFILIAL=1;CODTIPOCURSO=1');
    
            $result = $client->SaveRecord($xml_Aluno_);
            $req_dump = json_decode(json_encode($result), true) ;
            $raAluno= $req_dump["SaveRecordResult"];//$Resposta_pessoa["SaveRecordResult"]; 

            if(strlen(substr($raAluno, 2))> 9)
            {
                    $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação do aluno";
                   
                    $evento = print_r( $erro , true);
                    $conn = new \App\Connection\Conn;
                    $conn->InserirEvento($idContato, 'Criar aluno', $evento);
                    $mensagem = new \App\Rubeus\Mensagem;
                    $mensagem->EnvioEvento($idContato, $erro);
                    exit();
            }
     
             return substr($raAluno, 2);

            
        
    }

    // valida se o aluno já tem cadastro na tabela de pessoa e aluno do totvs
   public function ValidaAluno($idContato, 
                               $nome, 
                               $dtNascimento, 
                               $estadoNatal, 
                               $naturalidade, 
                               $cpf)
   {
     try {   
            $conect = new ConnectTotvs;
            $client =$conect->conectasql();

            $params = array(
                    'codSentenca' =>'INT.0002', 
                    'codColigada'=>1, 
                    'codSistema'=>'P', 
                    'parameters'=>"NOME=$nome;DTNASCIMENTO=$dtNascimento;ESTADONATAL=$estadoNatal;NATURALIDADE=$naturalidade;CPF=$cpf"                  
                    ); 
            $resultSoap = $client->RealizarConsultaSQL($params);
            $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
            $resultArray = json_decode(json_encode($result), true);
          
            $evento = print_r( $resultArray, true);
            $conn = new \App\Connection\Conn;
            $conn->InserirEvento($idContato, 'valida aluno', $evento);
         

            return  $resultArray ; 

            
        } catch (\SoapFault $e)
        {
            $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na validação do aluno";
            $evento = print_r( $erro , true);
            $conn = new \App\Connection\Conn;
            $conn->InserirEvento($idContato, 'valida aluno', $evento);
          
            $mensagem = new \App\Rubeus\Mensagem;
            $mensagem->EnvioEvento($idContato, $erro);
            exit();
        
        }      
           
    }
    

    public function VinculaPessoaAluno($ra, 
                                       $codPessoa, 
                                       $nome, 
                                       $CodAnoEscolar , 
                                       $loginIntegracao, 
                                       $dataAtual)
    {
       
        $conect = new ConnectTotvs;
        $client =$conect->conectaDataServer();
        
        // insere os dados do aluno, somente no cadastro de aluno, pois já existe na pessoa
        $xml_Aluno = <<<XML
                    <EduAluno>
                    <SAluno>
                        <CODCOLIGADA>1</CODCOLIGADA>
                        <RA>$ra</RA>
                        <CODPESSOA>$codpessoa</CODPESSOA>
                        <CODTIPOCURSO>1</CODTIPOCURSO>
                        <CODIGO>$codpessoa</CODIGO>
                        <NOME>$nome</NOME>
                        <GRAUULTIMAINST>$Cod_Ano_Escolar</GRAUULTIMAINST>
                        <ALUNO>1</ALUNO>
                        <RECCREATEDBY>$login_integracao</RECCREATEDBY>
                        <RECCREATEDON>$data_atual</RECCREATEDON>
                        <RECMODIFIEDBY>$login_integracao</RECMODIFIEDBY>
                        <RECMODIFIEDON>$data_atual</RECMODIFIEDON>
                        </SAluno> 
                    </EduAluno>
                    XML
                            ;
        $params = array('DataServerName' =>'EduAlunoData', 'XML'=>$xml_Aluno,'Contexto'=>'CODCOLIGADA=1;CODFILIAL=1;CODTIPOCURSO=1');
        $result = $client->SaveRecord($params);
        $RespostaAluno = cvf_convert_object_to_array($result);    
        $req = json_encode($RespostaAluno);
        $raAluno = get_parts($req,';', '"}');

        if(strlen(substr($raAluno, 2))> 9)
        {
                $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação do aluno";
               
                $evento = print_r( $erro , true);
                $conn = new \App\Connection\Conn;
                $conn->InserirEvento($idContato, 'Vincular Aluno', $evento);
                $mensagem = new \App\Rubeus\Mensagem;
                $mensagem->EnvioEvento($idContato, $erro);
                exit();
        }
 
        
        return $raAluno;


    
    }

    function CriarAluno($idContato,
                        $codigoAluno,
                        $raAluno,
                        $nome, 
                        $dtnascimento, 
                        $cpf, 
                        $estadonatal, 
                        $naturalidade, 
                        $email, 
                        $sexo, 
                        $estadocivil, 
                        $cep, 
                        $rua, 
                        $numero, 
                        $complemento, 
                        $bairro,
                        $uf, 
                        $cidade, 
                        $telefone,
                        $CodAnoEscolar , 
                        $loginIntegracao,
                        $dataAtual)
     {
        
        if($codigoAluno !== '0' )
        {
        
            $pessoa = new Pessoa;
            $codigoAluno= $pessoa->GetPessoa($idContato,
                                             $codigoAluno,
                                             $nome, 
                                             $dtnascimento, 
                                             $cpf, 
                                             $estadonatal, 
                                             $naturalidade, 
                                             $email, 
                                             $sexo, 
                                             $estadocivil, 
                                             $cep, 
                                             $rua, 
                                             $numero, 
                                             $complemento, 
                                             $bairro,
                                             $uf, 
                                             $cidade, 
                                             $telefone,
                                             $loginIntegracao,
                                             $dataAtual);

            // $ra =  $this->GetAluno( $raAluno, 
            //                         $codigoAluno,  
            //                         $nome, 
            //                         $email, 
            //                         $cpf, 
            //                         $dtnascimento, 
            //                         $estadonatal, 
            //                         $naturalidade, 
            //                         $cep, 
            //                         $sexo, 
            //                         $rua, 
            //                         $numero, 
            //                         $complemento, 
            //                         $bairro, 
            //                         $uf, 
            //                         $cidade, 
            //                         $telefone, 
            //                         $CodAnoEscolar , 
            //                         $loginIntegracao, 
            //                         $dataAtual);
                                    
        }

  
        // se a pessoa do aluno não estiver cadastrada no sistema será inserido ou atualizado os dados dos aluno   
        // Insere o registro do Aluno no cadastro de Aluno quando não existe a pessoa e atualizar os dados dos aluno
        if ($raAluno == '0') { 
            if ($codigoAluno == '0') {
                
                $ra =  $this->GetAluno( $idContato,
                                        $raAluno, 
                                        $codigoAluno,  
                                        $nome, 
                                        $email, 
                                        $cpf, 
                                        $dtnascimento, 
                                        $estadonatal, 
                                        $naturalidade, 
                                        $cep, 
                                        $sexo, 
                                        $rua, 
                                        $numero, 
                                        $complemento, 
                                        $bairro, 
                                        $uf, 
                                        $cidade, 
                                        $telefone, 
                                        $CodAnoEscolar , 
                                        $loginIntegracao, 
                                        $dataAtual);

            }
            else {
           
            $ra = $this->VinculaPessoaAluno($ra, 
                                            $codigoAluno, 
                                            $nome, 
                                            $CodAnoEscolar , 
                                            $loginIntegracao, 
                                            $dataAtual);
            }
        } else {
            $ra= $raAluno; 
        }  

        if(strlen(substr($ra, 2))> 9)
        {
                $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação do aluno";
               
                $evento = print_r( $erro , true);
                $conn = new \App\Connection\Conn;
                $conn->InserirEvento($idContato, 'Vincular Aluno', $evento);
                $mensagem = new \App\Rubeus\Mensagem;
                $mensagem->EnvioEvento($idContato, $erro);
                exit();
        }

        $evento = print_r(  $ra , true);
        $conn = new \App\Connection\Conn;
        $conn->InserirEvento($idContato, 'Aluno', $evento );      

        return $ra;

    }


    public function VinculaResponsaveis($idContato,
                                        $ra,
                                        $codcfo,
                                        $tipoCliente,
                                        $codRespAcad,
                                        $tipoRespAcademico
                                        )
    {
     
        //define grau parentesco do responsavel financeiro
        if ($tipoCliente == 'Pai') 
        {
            $codparentcfo = "6";
        } elseif ($tipoCliente== 'Mãe')
        {
            $codparentcfo = "7";
        } else{
            $codparentcfo = "9";
        }

        //define grau parentesco do responsavel acadêmico  $tiporespacad 
        if ($tipoRespAcademico == 'Pai')
        {
            $codparentraca = "6";
        } elseif ($tipoRespAcademico== 'Mãe') 
        {
            $codparentraca = "7";
        } else{
            $codparentraca = "9";
        }
        
        try {
           //vincula aluno com pai ou mae e responsavel financeiro
            $conect = new ConnectTotvs;
            $client =$conect->conectasql();
        
            $params = array(
                    'codSentenca' =>'INT.0006', 
                    'codColigada'=>1, 
                    'codSistema'=>'S', 
                    'parameters'=>"RA=$ra;CODCFO=$codcfo;CODPARENTCFO=$codparentcfo;CODPESSOARACA=$codRespAcad;CODPARENTRACA=$codparentraca" 
                        
                    ); 
                
            $resultSoap = $client->RealizarConsultaSQL($params);
            $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
            $resultArray = json_decode(json_encode($result), true);

            return $resultArray;
        } catch (\SoapFault $e)
        {
            $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na validação do aluno";
            $evento = print_r( $erro , true);
            $conn = new \App\Connection\Conn;
            $conn->InserirEvento($idContato, 'valida aluno', $evento);
          
            $mensagem = new \App\Rubeus\Mensagem;
            $mensagem->EnvioEvento($idContato, $erro);
            exit();
        
        }   
        
    }

   


}

?>