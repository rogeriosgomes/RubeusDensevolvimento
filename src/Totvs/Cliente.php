<?php

namespace App\Totvs;

class Cliente
{
   // Propriedades
   public $idContato;
   public $tipoCliente;
   public $codcfo; 
   public $nome;
   public $cgccfo;
   public $rua;
   public $numero;
   public $bairro; 
   public $cidade;
   public $codetd;
   public $cep;
   public $telefone;
   public $email;
   public $dtnascimento;
   public $estadocivil; 
   public $loginIntegracao;
   public $dataAtual;

   // Métodos

   function GetCliente($idContato,
                       $codcfo,
                       $nome,
                       $cgccfo,
                       $rua,
                       $numero,
                       $bairro,
                       $cidade,
                       $codetd,
                       $cep,
                       $telefone,
                       $email,
                       $dtnascimento,
                       $estadocivil,
                       $loginIntegracao,
                       $dataAtual)
    {
        $conect = new ConnectTotvs;
        $client =$conect->conectaDataServer();

        $xml_cliente = <<<XML
                        <FinCFOBR>
                        <FCFO>
                        <CODCOLIGADA>1</CODCOLIGADA>
                        <CODCFO>$codcfo</CODCFO>
                        <NOMEFANTASIA>$nome</NOMEFANTASIA>
                        <NOME>$nome</NOME>
                        <CGCCFO>$cgccfo</CGCCFO>
                        <ATIVO>1</ATIVO>
                        <PAGREC>3</PAGREC>
                        <PESSOAFISOUJUR>F</PESSOAFISOUJUR>
                        <RUA>$rua</RUA>
                        <NUMERO>$numero</NUMERO>
                        <BAIRRO>$bairro</BAIRRO>
                        <CIDADE>$cidade</CIDADE>
                        <CODETD>$codetd</CODETD>
                        <CEP>$cep</CEP>
                        <TELEFONE>$telefone</TELEFONE>
                        <TELEX>$telefone</TELEX>
                        <CODMUNICIPIO>00108</CODMUNICIPIO>
                        <TIPORUA>1</TIPORUA>
                        <TIPOBAIRRO>1</TIPOBAIRRO>
                        <EMAIL>$email</EMAIL>
                        <VALOROP1>0.00</VALOROP1>
                        <VALOROP2>0.00</VALOROP2>
                        <VALOROP3>0.00</VALOROP3>
                        <LIMITECREDITO>0.00</LIMITECREDITO>
                        <IDCFO>-1</IDCFO>
                        <USUARIOALTERACAO>$login_integracao</USUARIOALTERACAO>
                        <USUARIOCRIACAO>$login_integracao</USUARIOCRIACAO>
                        <DATAULTALTERACAO>$data_atual</DATAULTALTERACAO>
                        <DATACRIACAO>$data_atual</DATACRIACAO>
                        <EMAILFISCAL>$email</EMAILFISCAL>
                        <CODRECEITA>0000</CODRECEITA>
                        <TIPORENDIMENTO>000</TIPORENDIMENTO>
                        <FORMATRIBUTACAO>00</FORMATRIBUTACAO>
                        <SITUACAONIF>0</SITUACAONIF>
                        <INOVAR_AUTO>0</INOVAR_AUTO>
                        <CODCFOCOLINTEGRACAO>0</CODCFOCOLINTEGRACAO>
                        <ENTIDADEEXECUTORAPAA>0</ENTIDADEEXECUTORAPAA>
                        <APOSENTADOOUPENSIONISTA>0</APOSENTADOOUPENSIONISTA>
                        <RECCREATEDBY>$login_integracao</RECCREATEDBY>
                        <RECCREATEDON>$data_atual</RECCREATEDON>
                        <RECMODIFIEDBY>$login_integracao</RECMODIFIEDBY>
                        <RECMODIFIEDON>$data_atual</RECMODIFIEDON>
                        <DTNASCIMENTO>$dtnascimento</DTNASCIMENTO>
                        <ESTADOCIVIL>$estadocivil</ESTADOCIVIL>
                        </FCFO>
                        </FinCFOBR>
                        XML;

        $xml_Cliente_ = array('DataServerName' =>'FinCFODataBR', 'XML'=>$xml_cliente,'Contexto'=>'CODCOLIGADA=1');

        $result = $client->SaveRecord($xml_Cliente_);
        $req_dump = json_decode(json_encode($result), true) ;
        $codCliente = $req_dump["SaveRecordResult"];//$Resposta_pessoa["SaveRecordResult"]; 
        
        if(strlen(substr($codCliente, 2))> 9)
        {
                $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação do Cliente/fornecedor";
               
                $evento = print_r( $erro , true);
                $conn = new \App\Connection\Conn;
                $conn->InserirEvento($idContato, 'cliente', $evento);

                $mensagem = new \App\Rubeus\Mensagem;
                $mensagem->EnvioEvento($idContato, $erro);
            
            
                exit();
        }
        
        return substr($codCliente,2);
    }

    // valida se o responsável financeiro do aluno já existe na tabela de cliente/fornecedor do totvs
    function ValidaCliente($idContato,
                           $cpf)
    {
        try
        {
            $conect = new ConnectTotvs;
            $client =$conect->conectasql();

            $params = array(
                    'codSentenca' =>'INT.0005', 
                    'codColigada'=>1, 
                    'codSistema'=>'P', 
                    'parameters'=>"CPF=$cpf" 
                        
                    ); 
                
            $resultSoap = $client->RealizarConsultaSQL($params);
            $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
            $resultArray = json_decode(json_encode($result), true);
            $codcfo = $resultArray["Resultado"]["CODCFO"];
        
            return $codcfo;
        } catch (\SoapFault $e)
        {
            $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na validação do cliente";
            $evento = print_r( $erro , true);
            $conn = new \App\Connection\Conn;
            $conn->InserirEvento($idContato, 'valida cliente', $evento);
          
            $mensagem = new \App\Rubeus\Mensagem;
            $mensagem->EnvioEvento($idContato, $erro);
          
           
            exit();
        
        }
  }
   
  function CriarCliente($idContato,
                        $tipoCliente,
                        $nome,
                        $cgccfo,
                        $rua,
                        $numero,
                        $bairro,
                        $cidade,
                        $codetd,
                        $cep,
                        $telefone,
                        $email,
                        $dtnascimento,
                        $estadocivil,
                        $estadoNatal,
                        $cidadeNatal,
                        $loginIntegracao,
                        $dataAtual)
  {

    
    //verifica se o responsavel financeiro é o próprio o aluno
   
    if ($tipoCliente == "Próprio Aluno (acima de 18 anos)" or $tipoCliente == "Próprio Aluno" or $tipoCliente == "Pr\u00f3prio Aluno") 
    {
        $codcfo_repons_finan = $this->ValidaCliente($idContato,
                                                    $cgccfo); //verificar depois
        
        $estadocivil ='S';
        
        $codcfo = $this->GetCliente($idContato,
                                    $codcfo_repons_finan,
                                    $nome,
                                    $cgccfo,
                                    $rua,
                                    $numero,
                                    $bairro,
                                    $cidade,
                                    $codetd,
                                    $cep,
                                    $telefone,
                                    $email,
                                    $dtnascimento,
                                    $estadocivil,
                                    $loginIntegracao,
                                    $dataAtual);
    }

    
    //verifica se o responsavel financeiro é o pai ou mãe
    if($tipoCliente  ==  "Pai" or $tipoCliente ==  "Mãe" or $tipoCliente  ==  "M\u00e3e")
    {
       
        $pessoa = new Pessoa;
        $codPessoa = $pessoa->ValidaSePessoaExiste($idContato,
                                                   $nome,
                                                   $dtnascimento,
                                                   $estadoNatal,
                                                   $cidadeNatal,
                                                   $cgccfo);
        
        
    
       
        if ($tipoCliente == "Pai") {
            $sexoresp = "M";
            $tiporelac = "P";
        } elseif ($tipoCliente== "Mãe" or $tipoCliente == "M\u00e3e") {
            $sexoresp = "F";
            $tiporelac = "M";
        }
        
        $pessoa = new Pessoa;
        $codPessoa= $pessoa->GetPessoa( $idContato,
                                        $codPessoa,
                                        $nome,
                                        $dtnascimento,
                                        $cgccfo,
                                        $estadoNatal,
                                        $cidadeNatal,
                                        $email,
                                        $sexoresp , 
                                        'S', 
                                        $cep, 
                                        $rua, 
                                        $numero,
                                        '',
                                        $bairro,
                                        $codetd, 
                                        $cidade,
                                        $telefone, 
                                        $loginIntegracao,
                                        $dataAtual);
        
        //   inclui ou atualizar  pai ou mãe no cadastrado na tabela de cliente/fornecedor e retornar o codcfo
       
        $codcfo_repons_finan = $this->ValidaCliente($idContato,
                                                    $cgccfo); //verificar depois
                    
        $codcfo =  $this->GetCliente($idContato,
                                     $codcfo_repons_finan ,
                                     $nome,
                                     $cgccfo,
                                     $rua,
                                     $numero,
                                     $bairro,
                                     $cidade,
                                     $codetd,
                                     $cep,
                                     $telefone,
                                     $email,
                                     $dtnascimento,
                                     $estadocivil,
                                     $loginIntegracao,
                                     $dataAtual);

    }   
    //verifica se o responsavel financeiro é outra pessoa

    if ($tipoCliente  == "Outro" or $tipoCliente  == "Responsável Legal") 
    {

       $codcfo_repons_finan  = $this->ValidaCliente($idContato,
                                                    $cgccfo ); //verificar depois
        
        $codcfo =  $this->GetCliente($idContato,
                                     $codcfo_repons_finan,
                                     $nome,
                                     $cgccfo,
                                     $rua,
                                     $numero,
                                     $bairro,
                                     $cidade,
                                     $codetd,
                                     $cep,
                                     $telefone,
                                     $email,
                                     $dtnascimento,
                                     $estadocivil,
                                     $loginIntegracao,
                                     $dataAtual);                                  
    
    }
    
    if(strlen(substr($codcfo, 2))> 9 or is_null($codcfo))
    {
            $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação do Cliente/fornecedor";
           
            $evento = print_r( $erro , true);
            $conn = new \App\Connection\Conn;
            $conn->InserirEvento($idContato, 'cliente', $evento);

            $mensagem = new \App\Rubeus\Mensagem;
            $mensagem->EnvioEvento($idContato, $erro);
        
        
            exit();
    }
  

   return $codcfo; 
  }

}
/*int.0005
WITH VERIFICA AS (
SELECT CODCFO
FROM FCFO
WHERE REPLACE(REPLACE(CGCCFO, '.', ''), '-', '')=:CPF)
  
  
 SELECT ISNULL((SELECT CODCFO FROM VERIFICA), 0) AS CODCFO 
 FROM GCOLIGADA
 WHERE CODCOLIGADA = 1 
*/
?>