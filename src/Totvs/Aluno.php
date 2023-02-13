<?php
require_once "ConnectTotvs.php";

class Aluno
{
  // Propriedades
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

  function GetAluno($ra, 
                       $codpessoa,  
                       $nome, 
                       $email, 
                       $cpf, 
                       $dtnascimento, 
                       $estadonatal, 
                       $naturalidade, 
                       $cep, 
                       $sexo, 
                       $endereco, 
                       $numero, 
                       $complemento, 
                       $bairro, 
                       $uf, 
                       $cidade, 
                       $telefone, 
                       $Cod_Ano_Escolar , 
                       $login_integracao, 
                       $data_atual
                       )
    {
 
        $client =conectaDataServer();
        
        // insere os dados do aluno no cadastro de aluno e pessoa
        $xml_Aluno = <<<XML
            <EduAluno>
            <SAluno>
            <CODCOLIGADA>1</CODCOLIGADA>
            <RA>$ra</RA>
            <CODPESSOA>$codpessoa</CODPESSOA>
            <CODTIPOCURSO>1</CODTIPOCURSO>
            <CODIGO>$codpessoa</CODIGO>
            <NOME>$nome</NOME>
            <EMAIL>$email</EMAIL>
            <CPF>$cpf</CPF>
            <DTNASCIMENTO>$dt_nascimento</DTNASCIMENTO>
            <NACIONALIDADE>10</NACIONALIDADE>
            <ESTADONATAL>$estado_natal</ESTADONATAL>
            <NATURALIDADE>$naturalidade</NATURALIDADE>
            <ESTADOCIVIL>S</ESTADOCIVIL> 
            <CEP>$cep</CEP>
            <SEXO>$sexo</SEXO>
            <RUA>$endereco</RUA>
            <NUMERO>$numero</NUMERO>
            <COMPLEMENTO>$complemento</COMPLEMENTO>
            <BAIRRO>$bairro</BAIRRO>
            <ESTADO>$uf</ESTADO>
            <CIDADE>$cidade</CIDADE>
            <PAIS>Brasil</PAIS>
            <TELEFONE2>$telefone</TELEFONE2>
            <GRAUULTIMAINST>$Cod_Ano_Escolar</GRAUULTIMAINST>
            <ESTADOROW>0</ESTADOROW>
            <ROWVALIDA>0</ROWVALIDA>
            <ALUNO>1</ALUNO>
            <CODSTATUS>-1</CODSTATUS>
            <RECCREATEDBY>$login_integracao</RECCREATEDBY>
            <RECCREATEDON>$data_atual</RECCREATEDON>
            <RECMODIFIEDBY>$login_integracao</RECMODIFIEDBY>
            <RECMODIFIEDON>$data_atual</RECMODIFIEDON>
            
            </SAluno>
            <SAlunoCompl>
                <CODCOLIGADA>1</CODCOLIGADA>
                <RA>$ra</RA>
                <RECCREATEDBY>$login_integracao</RECCREATEDBY>
                <RECCREATEDON>$data_atual</RECCREATEDON>
                <RECMODIFIEDBY>$login_integracao</RECMODIFIEDBY>
                <RECMODIFIEDON>$data_atual</RECMODIFIEDON>
            </SAlunoCompl>
            </EduAluno>
        XML;                         

        $xml_Aluno_ = array('DataServerName' =>'EduAlunoData', 'XML'=>$xml_Aluno,'Contexto'=>'CODCOLIGADA=1;CODFILIAL=1;CODTIPOCURSO=1');
  
        $result = $client->SaveRecord($xml_Aluno_);
        $req_dump = json_decode(json_encode($result), true) ;
        $fp = file_put_contents( 'console.json', $req_dump, FILE_APPEND  );  
        $ra_aluno = $req_dump["SaveRecordResult"];//$Resposta_pessoa["SaveRecordResult"]; 
     
        return $ra_aluno;
    }

}

?>