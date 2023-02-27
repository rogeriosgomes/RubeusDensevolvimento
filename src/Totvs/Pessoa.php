<?php

require_once "ConnectTotvs.php";

class Pessoa
{
   public $codpessoa;
   public $nome;
   public $dtnascimento;
   public $cpf;
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
   public $login_integracao;
   public $data_atual;

// Retorna o codigo da pessoa
function ValidaSePessoaExiste($nome,$dtnascimento,$estadonatal,$naturalidade,$cpf){
    
    $client= conectasql();

    $params = array(
             'codSentenca' =>'INT.0003', 
             'codColigada'=>1, 
             'codSistema'=>'P', 
             'parameters'=>"NOME=$nome;DTNASCIMENTO=$dtnascimento;ESTADONATAL=$estadonatal;NATURALIDADE=$naturalidade;CPF=$cpf"                 
              ); 
    $resultSoap = $client->RealizarConsultaSQL($params);
    $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
    $resultArray = json_decode(json_encode($result), true);
    return  $resultArray["Resultado"]["CODPESSOA"] ;
 }

 //Inclui uma nova pessoa

 function IncluiPessoa($codpessoa, 
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
                       $login_integracao,
                       $data_atual)
{
    
    
    $client =conectaDataServer();

    if($codpessoa == '0'){
        $codpessoa = '-1';
    } 

    $xml_pessoa = <<<XML
    <RhuPessoa>
      <PPESSOA>
        <CODIGO>$codpessoa</CODIGO>
        <NOME>$nome</NOME>
        <DTNASCIMENTO>$dtnascimento</DTNASCIMENTO>
        <CPF>$cpf</CPF>
        <ESTADONATAL>$estadonatal</ESTADONATAL>
        <NATURALIDADE>$naturalidade</NATURALIDADE>
        <EMAIL>$email</EMAIL>
        <SEXO>$sexo</SEXO>
        <ESTADOCIVIL>$estadocivil</ESTADOCIVIL>
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
        <RECCREATEDBY>$login_integracao</RECCREATEDBY>
        <RECCREATEDON>$data_atual</RECCREATEDON>
        <RECMODIFIEDBY>$login_integracao</RECMODIFIEDBY>
        <RECMODIFIEDON>$data_atual</RECMODIFIEDON>
      </PPESSOA>
        </RhuPessoa>
    XML
    ;

    $xml_pessoa_ = array('DataServerName' =>'RhuPessoaData', 'XML'=>$xml_pessoa,'Contexto'=>'CODCOLIGADA=1');
    $result = $client->SaveRecord($xml_pessoa_); //Salvar usuarios
    $req_dump = json_decode(json_encode($result), true) ;
    $fp = file_put_contents( 'console.json', print_r($result, true ) );  
    $cod_pessoa = $req_dump["SaveRecordResult"];//$Resposta_pessoa["SaveRecordResult"]; 
    return $cod_pessoa;

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