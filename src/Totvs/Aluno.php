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
                    $rua, 
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
                <DTNASCIMENTO>$dtnascimento</DTNASCIMENTO>
                <NACIONALIDADE>10</NACIONALIDADE>
                <ESTADONATAL>$estadonatal</ESTADONATAL>
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
            $ra_aluno = $req_dump["SaveRecordResult"];//$Resposta_pessoa["SaveRecordResult"]; 
     
             return substr($ra_aluno, 2);
        
    }

    // valida se o aluno já tem cadastro na tabela de pessoa e aluno do totvs
   function ValidaAluno($nome, $dtNascimento, $estadoNatal, $naturalidade, $cpf)
   {
    
    $client =conectasql();

    $params = array(
             'codSentenca' =>'INT.0002', 
             'codColigada'=>1, 
             'codSistema'=>'P', 
             'parameters'=>"NOME=$nome;DTNASCIMENTO=$dtNascimento;ESTADONATAL=$estadoNatal;NATURALIDADE=$naturalidade;CPF=$cpf"                  
              ); 
    $resultSoap = $client->RealizarConsultaSQL($params);
    $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
    $resultArray = json_decode(json_encode($result), true);
    return  $resultArray ;
    }


    function VinculaPessoaAluno($ra, 
                                $codPessoa, 
                                $nome, 
                                $CodAnoEscolar , 
                                $loginIntegracao, 
                                $dataAtual)
    {

        $client = conectaDataServer();
        
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
    
        $req_dump_console = print_r($result, true );
        $fp = file_put_contents( 'console.json', $req_dump_console, FILE_APPEND  );  
    
        $Resposta_ = $client->__getLastResponse();
    
        $Resposta_aluno = cvf_convert_object_to_array($result);    
        
        // list($coligada, $ra) = explode(';', $Resposta_aluno["SaveRecordResult"]);
        
        $req_dump = json_encode($Resposta_aluno);
       
        $ra_aluno = get_parts($req_dump,';', '"}');
        
        return $ra_aluno;
    
    }

    function CriarAluno($codigoAluno,
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
        
           
            $codigoAluno= Pessoa::IncluiPessoa($codigoAluno,
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
        }

  
        // se a pessoa do aluno não estiver cadastrada no sistema será inserido ou atualizado os dados dos aluno   
        // Insere o registro do Aluno no cadastro de Aluno quando não existe a pessoa e atualizar os dados dos aluno
        if ($raAluno == '0') { 
            if ($codigoAluno == '0') {
                
                $ra =  $this->GetAluno( $raAluno, 
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
        
       

        return $ra;

    }

   


}

?>