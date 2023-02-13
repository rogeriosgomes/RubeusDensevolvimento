<?php
require_once "ConnectTotvs.php";
class Filiacao
{  
    //Propriedade
    public $codpessoafiliacao;
    public $codpessoafilho;
    public $tiporelac;
    public $login_integracao;
    public $data_atual;
    
    //Métodos
    function VinculoDaFiliacao($codpessoafilho, $codpessoafiliacao)
    {

        $client= conectasql();
      
        $params = array(
                 'codSentenca' =>'INT.0004', 
                 'codColigada'=>1, 
                 'codSistema'=>'P', 
                 'parameters'=>"CODPESSOAFILHO=$codpessoafilho;CODPESSOAPAIMAE=$codpessoafiliacao" 
                      
                  ); 
              
        $resultSoap = $client->RealizarConsultaSQL($params);
        $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
        $resultArray = json_decode(json_encode($result), true);
        $vinculo = $resultArray["Resultado"]["CODFILIACAO"];
      
        return $vinculo;
    }


    function GetFiliacao($codpessoafiliacao, $codpessoafilho, $tiporelac, $login_integracao, $data_atual)
    {
    
   
        $client = $client =conectaDataServer();
    
        $xml_filiação_pai_filho = <<<XML
                                            <RhuFiliacao>
                                                <VFILIACAO>
                                                    <CODFILIACAO>-1</CODFILIACAO>
                                                    <CODPESSOAFILIACAO>$codpessoafiliacao</CODPESSOAFILIACAO> 
                                                    <CODPESSOAFILHO>$codpessoafilho</CODPESSOAFILHO>
                                                    <TIPORELACIONAMENTO>$tiporelac</TIPORELACIONAMENTO>
                                                    <RECCREATEDBY>$login_integracao</RECCREATEDBY>
                                                    <RECCREATEDON>$data_atual</RECCREATEDON>
                                                    <RECMODIFIEDBY>$login_integracao</RECMODIFIEDBY>
                                                    <RECMODIFIEDON>$data_atual</RECMODIFIEDON>
                                                </VFILIACAO>
                                            </RhuFiliacao>
                                          XML;
    
      $xml_filiação_pai_filho_ = array('DataServerName' =>'RhuFiliacaoData', 'XML'=>$xml_filiação_pai_filho,'Contexto'=>'?');
      $result = $client->SaveRecord($xml_filiação_pai_filho_); 
      $req_dump = json_decode(json_encode($result), true);
      $fp = file_put_contents( 'console.json', print_r($result, true ) );  
      $codFiliacao = $req_dump["SaveRecordResult"];   
      return $codFiliacao;
    
    }
      

}

?>