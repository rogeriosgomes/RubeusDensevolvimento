<?php

// -- validação do usuario no Totvs


  ini_set('soap.wsdl_cache_enabled', '0');
  
 $login_integracao ="integracao";
 $password =  "int%2*45";
 $urltotvs = "https://ws.thomas.org.br:8051";


  $soapParams = array(
            'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
            'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
            'authentication' => SOAP_AUTHENTICATION_BASIC, 
            'trace' => '1', 
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        );
        $wsdl = $urltotvs."/wsDataServer/MEX?wsd";
         /*"env("URL_WEBSERVICE_TOTVS") ". "/wsDataServer/MEX?wsd";*/
        $client = new SoapClient($wsdl, $soapParams);

    
function conectasql(){
     $login_integracao ="integracao";
     $password =  "int%2*45";
     $urltotvs = "https://ws.thomas.org.br:8051";
    $soapParams = array(
            'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
            'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
            'authentication' => SOAP_AUTHENTICATION_BASIC, 
            'trace' => '1', 
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        );
        $wsdl =$urltotvs."/wsConsultaSQL/MEX?wsdl";
         /*"env("URL_WEBSERVICE_TOTVS") ". "/wsDataServer/MEX?wsd";*/
        $client = new SoapClient($wsdl, $soapParams);

    return $client;
}

function cvf_convert_object_to_array ( $data ) {

    if ( is_object ( $data ) ) {
        $data = get_object_vars ( $data ) ;
    }

    if ( is_array ( $data ) ) {
        return array_map ( __FUNCTION__ , $data ) ;
    }
    else {
        return $data ;
    }
}

function get_parts($text, $start, $end){ //função para pegar dados dos resp. acadêmico
    
    $text = ''.$text;
    $ini = strpos($text, $start);
    
  
    $ini += strlen($start);
    $len  = strpos($text, $end, $ini) - $ini;
    return substr($text, $ini, $len);
  
  } 
 
// valida se o responsável financeiro do aluno já existe na tabela de cliente/fornecedor do totvs
 function validaclientefornecedor($cpf){
    
    $client= conectasql();

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
 }
 
// valida se o aluno já tem cadastro na tabela de pessoa e aluno do totvs
 function validaaluno($nome1, $datanascimento1, $estado_natal1, $naturalidade1, $cpf1){
    
    $client= conectasql();

    $params = array(
             'codSentenca' =>'INT.0002', 
             'codColigada'=>1, 
             'codSistema'=>'P', 
             'parameters'=>"NOME=$nome1;DTNASCIMENTO=$datanascimento1;ESTADONATAL=$estado_natal1;NATURALIDADE=$naturalidade1;CPF=$cpf1"                  
              ); 
    $resultSoap = $client->RealizarConsultaSQL($params);
    $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
    $resultArray = json_decode(json_encode($result), true);
   

  return  $resultArray ;
 }
 


function data($data){
   
    $data_ajustada = date("d-m-Y", strtotime($data));

 	return $data_ajustada;
 }

function cidade ($estadouf){
      
      list($cidade, $estado_natal) = explode('-', $estadouf);
      return $cidade;
    }

function estado ($estadouf){
      
      list($cidade, $estado_natal) = explode('-', $estadouf);
      return trim($estado_natal);
    }

function estadocivil($value){
   
 switch($value){
    case  "Casado(a)":
        $estadocivil = 'C';
        break;
    case   "Desquitado(a)" :
        $estadocivil = 'D';
        break;
    case   "Uni\u00e3o Est\u00e1vel" :
        $estadocivil = 'E';
        break;
    case   "Uni\u00e3o Est\u00e1vel" :
    $estadocivil = 'E';
    break;
    case   "União Estável" :
        $estadocivil = 'E';
        break;
    case   "Divorciado(a)" :
        $estadocivil = 'I';
        break;
    case   "Separado(a)" :
        $estadocivil = 'P';
        break;
    case   "Solteiro(a)" :
    $estadocivil = 'S';
    break;
    case   "Vi\u00favo(a)" :
        $estadocivil = 'V';
        break;
    case   "Viúvo(a)" :
        $estadocivil = 'V';
        break;
    case   "Outros" :
        $estadocivil = 'O';
        break;
 }
  
 return $estadocivil;    
    
   
}


function verificaAnoEscolar($param1){
    
    $String_Ano_Escolar = $param1;

    
    if($String_Ano_Escolar == "INFANTIL 1"){
        $Cod_Ano_Escolar = '00';
        
    }elseif($String_Ano_Escolar == "INFANTIL 2"){
        $Cod_Ano_Escolar = '01';
        
    }elseif($String_Ano_Escolar == "INFANTIL 3"){
        $Cod_Ano_Escolar = '02';
        
    }elseif($String_Ano_Escolar == "INFANTIL 4"){
        $Cod_Ano_Escolar = '03';
        
    }elseif($String_Ano_Escolar == "INFANTIL 5"){
        $Cod_Ano_Escolar = '04';
        
    }elseif($String_Ano_Escolar == "1° ANO EF"){
        $Cod_Ano_Escolar = '05';
        
    }elseif($String_Ano_Escolar == "2° ANO EF"){
        $Cod_Ano_Escolar = '06';
        
    }elseif($String_Ano_Escolar == "3° ANO EF"){
        $Cod_Ano_Escolar = '07';
        
    }elseif($String_Ano_Escolar == "4° ANO EF"){
        $Cod_Ano_Escolar = '08';
        
    }elseif($String_Ano_Escolar == "5° ANO EF"){
        $Cod_Ano_Escolar = '09';
        
    }elseif($String_Ano_Escolar == "6° ANO EF"){
        $Cod_Ano_Escolar = '10';
        
    }elseif($String_Ano_Escolar == "7° ANO EF"){
        $Cod_Ano_Escolar = '11';
        
    }elseif($String_Ano_Escolar == "8° ANO EF"){
        $Cod_Ano_Escolar = '12';
        
    }elseif($String_Ano_Escolar == "9° ANO EF"){
        $Cod_Ano_Escolar = '13';
        
    }elseif($String_Ano_Escolar == "1° ANO EM"){
        $Cod_Ano_Escolar = '14';
        
    }elseif($String_Ano_Escolar == "2° ANO EM"){
        $Cod_Ano_Escolar = '15';
        
    }elseif($String_Ano_Escolar == "3° ANO EM"){
        $Cod_Ano_Escolar = '16';
        
    }elseif($String_Ano_Escolar == "CONCLUÍDO"){
        $Cod_Ano_Escolar = '17';
        
    }
    
    return $Cod_Ano_Escolar;
    
}








function vincula_pessoa_aluno($ra, $codpessoa, $nome, $Cod_Ano_Escolar , $login_integracao, $data_atual, $password, $urltotvs){

  
    $soapParams = array(
    'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
    'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
    'authentication' => SOAP_AUTHENTICATION_BASIC, 
    'trace' => '1', 
    'exceptions' => true,
    'cache_wsdl' => WSDL_CACHE_NONE
                        );
    $wsdl =$urltotvs."/wsDataServer/MEX?wsd";
    /*"env("URL_WEBSERVICE_TOTVS") ". "/wsDataServer/MEX?wsd";*/
    $client = new SoapClient($wsdl, $soapParams);
    
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

// print_r(criar_aluno('0', '0', 'artuhrd reedd', 'emaffdsd@gmail.com', '', '2010-06-06', 'DF', 'BRASÍLIA', 
//               '72015605' , 'M', 'QNN DSDgggggD', '10', 'COMP', 'TAG SUL', 'DF', 'BRASÍLIA', '6199999999',
//               '10', 'INTEGRACAO', '2021-08-16' ));

// print_r(vincula_pessoa_aluno('0', '67546', 'artuhrd reedd', '10', 'INTEGRACAO', '2021-08-16'  ));


function cliente_fornecedor($codcfo, $nome, $cgccfo, $rua, $numero, $bairro, $cidade, $codetd, $cep, $telefone, $email,
                            $login_integracao, $data_atual, $dtnascimento, $estadocivil, $password, $urltotvs ){
   
   
    $soapParams = array(
    'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
    'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
    'authentication' => SOAP_AUTHENTICATION_BASIC, 
    'trace' => '1', 
    'exceptions' => true,
    'cache_wsdl' => WSDL_CACHE_NONE
                        );
    $wsdl =$urltotvs."/wsDataServer/MEX?wsd";
    /*"env("URL_WEBSERVICE_TOTVS") ". "/wsDataServer/MEX?wsd";*/
    $client = new SoapClient($wsdl, $soapParams);
   
    $xml_cliente_fornecedor = <<<XML
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
                                      XML
                                      ;

        $params_cliente_fornecedor = array('DataServerName' =>'FinCFODataBR', 'XML'=>$xml_cliente_fornecedor,'Contexto'=>'CODCOLIGADA=1');

                $result = $client->SaveRecord( $params_cliente_fornecedor); //Salvar usuario
                $req_dump_console = print_r($result, true );
                $fp = file_put_contents( 'console.json', $req_dump_console, FILE_APPEND );  
            
                
                $req_dump = json_encode($result);
                $cod_cfo = get_parts($req_dump,';', '"}');

                return $cod_cfo;

                
}


// print_r(cliente_fornecedor('0', 'sao jose', '882.458.180-38', 'fdfdfdfd', '12', 'tag sul', 'BRASÍLIA', 'DF', 
//                    '72015605', '61992602678', 'TESTEDD@GMAIL.COM', 'INTEGRACAO', '2021-08-16'));

?>