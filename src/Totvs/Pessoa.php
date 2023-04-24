<?php

namespace App\Totvs;

class Pessoa
{
   public $codPessoa;
   public $nome;
   public $dtNascimento;
   public $cpf;
   public $estadoNatal;
   public $naturalidade;
   public $email;
   public $sexo;
   public $estadoCivil;
   public $cep;
   public $rua;
   public $numero; 
   public $complemento;
   public $bairro;
   public $uf;
   public $cidade; 
   public $telefone; 
   public $loginIntegracao;
   public $dataAtual;

// Retorna o codigo da pessoa
public function ValidaSePessoaExiste($idContato,
                                     $nome,
                                     $dtNascimento,
                                     $estadoNatal,
                                     $naturalidade,
                                     $cpf){
    
    try
    {
      $conect = new ConnectTotvs;
      $client =$conect->conectasql();

      $params = array(
              'codSentenca' =>'INT.0003', 
              'codColigada'=>1, 
              'codSistema'=>'P', 
              'parameters'=>"NOME=$nome;DTNASCIMENTO=$dtNascimento;ESTADONATAL=$estadoNatal;NATURALIDADE=$naturalidade;CPF=$cpf"                 
                ); 
      $resultSoap = $client->RealizarConsultaSQL($params);
      $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
      $resultArray = json_decode(json_encode($result), true);
      return  $resultArray["Resultado"]["CODPESSOA"] ;
    } catch (\SoapFault $e)
    {
        $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na validação da pessoa";
        $evento = print_r( $erro , true);
        $conn = new \App\Connection\Conn;
        $conn->InserirEvento($idContato, 'valida pessoa', $evento);
      
        $mensagem = new \App\Rubes\Mensagem;
        $mensagem->EnvioEvento($idContato, $erro);
      
       
        exit();
    
    }
 }

 //Inclui uma nova pessoa

public function GetPessoa($idContato,
                          $codPessoa, 
                          $nome, 
                          $dtNascimento, 
                          $cpf, 
                          $estadoNatal, 
                          $naturalidade, 
                          $email, 
                          $sexo, 
                          $estadoCivil, 
                          $cep, 
                          $rua, 
                          $numero, 
                          $complemento, 
                          $bairro,
                          $uf, 
                          $cidade, 
                          $telefone,
                          $loginIntegracao,
                          $dataAtual)
{
    
    $conect = new ConnectTotvs;
    $client =$conect->conectaDataServer();

    if($codPessoa == '0'){
        $codPessoa = '-1';
    } 

    $xml_pessoa = <<<XML
    <RhuPessoa>
      <PPESSOA>
        <CODIGO>$codPessoa</CODIGO>
        <NOME>$nome</NOME>
        <DTNASCIMENTO>$dtNascimento</DTNASCIMENTO>
        <CPF>$cpf</CPF>
        <ESTADONATAL>$estadoNatal</ESTADONATAL>
        <NATURALIDADE>$naturalidade</NATURALIDADE>
        <EMAIL>$email</EMAIL>
        <SEXO>$sexo</SEXO>
        <ESTADOCIVIL>$estadoCivil</ESTADOCIVIL>
        <CEP>$cep</CEP>
        <RUA>$rua</RUA>
        <NUMERO>$numero</NUMERO>
        <COMPLEMENTO>$complemento</COMPLEMENTO>
        <BAIRRO>$bairro</BAIRRO>
        <ESTADO>$uf</ESTADO>
        <CIDADE>$cidade</CIDADE>
        <PAIS>Brasil</PAIS>
        <TELEFONE2>$telefone</TELEFONE2>
        <FUNCIONARIO>0</FUNCIONARIO>
        <EXFUNCIONARIO>0</EXFUNCIONARIO>
        <CANDIDATO>0</CANDIDATO>
        <RECCREATEDBY>$loginIntegracao</RECCREATEDBY>
        <RECCREATEDON>$dataAtual</RECCREATEDON>
        <RECMODIFIEDBY>$loginIntegracao</RECMODIFIEDBY>
        <RECMODIFIEDON>$dataAtual</RECMODIFIEDON>
      </PPESSOA>
        </RhuPessoa>
    XML
    ;

    $xml_pessoa_ = array('DataServerName' =>'RhuPessoaData', 'XML'=>$xml_pessoa,'Contexto'=>'CODCOLIGADA=1');
    $result = $client->SaveRecord($xml_pessoa_); //Salvar usuarios
    $req = json_decode(json_encode($result), true) ;
    $codPessoa = $req["SaveRecordResult"];//$Resposta_pessoa["SaveRecordResult"]; 

    if(is_int($codPessoa))
    {
      $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação na pessoa do aluno";
                   
                    $evento = print_r( $erro , true);
                    $conn = new \App\Connection\Conn;
                    $conn->InserirEvento($idContato, 'Pessoa', $evento);
              
                    $mensagem = new \App\Rubeus\Mensagem;
                    $mensagem->EnvioEvento($idContato, $erro);
                
                
                    exit();
    }
    return $codPessoa;

}

}

// // Consulta INT.0003
// WITH VERIFICA AS (
//     SELECT DISTINCT CODIGO
//     FROM (
//     SELECT CODIGO, NOME, DTNASCIMENTO, ESTADONATAL, NATURALIDADE, CPF
//     FROM PPESSOA
//     WHERE NOME =:NOME
//        AND DTNASCIMENTO =:DTNASCIMENTO
//        AND ESTADONATAL  =:ESTADONATAL
//        AND NATURALIDADE =:NATURALIDADE
      
//     UNION ALL
    
//     SELECT CODIGO, NOME, DTNASCIMENTO, ESTADONATAL, NATURALIDADE, CPF
//     FROM PPESSOA
//     WHERE CPF =(CASE WHEN :CPF = '' THEN 'BBBB' ELSE :CPF  END)
//     ) X )
    
//      SELECT ISNULL((SELECT CODIGO FROM VERIFICA), 0) AS CODPESSOA 
//      FROM GCOLIGADA
//      WHERE CODCOLIGADA = 1
    
    


?>