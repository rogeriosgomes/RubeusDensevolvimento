<?php
namespace App\Totvs;
class Filiacao
{  
    //Propriedade
    public $idContato;
    public $codpessoafiliacao;
    public $codpessoafilho;
    public $tiporelac;
    public $login_integracao;
    public $data_atual;
    
    //Métodos
    function VinculoDaFiliacao($idContato,
                               $codpessoafilho, 
                               $codpessoafiliacao)
    {
        try
        {
            $conect = new ConnectTotvs;
            $client =$conect->conectasql();
        
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
        } catch (\SoapFault $e)
        {
            $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na filiação";
            $evento = print_r( $erro , true);
            $conn = new \App\Connection\Conn;
            $conn->InserirEvento($idContato, 'valida pessoa', $evento);
          
            $mensagem = new \App\Rubes\Mensagem;
            $mensagem->EnvioEvento($idContato, $erro);
          
           
            exit();
        
        }
    }


    function GetFiliacao($idContato,
                         $codpessoafiliacao, 
                         $codpessoafilho, 
                         $tipoRelacionamento, 
                         $login_integracao, 
                         $data_atual)
    {

        if ($tipoRelacionamento == "Pai")
        {
           $tiporelac = "P";
        } elseif ($tipoRelacionamento == "Mãe" or $tipoRelacionamento== "M\u00e3e") 
        {
            $tiporelac = "M";
        }
    
        $conect = new ConnectTotvs;
        $client =$conect->conectaDataServer();
    
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
      
      if(is_int($codFiliacao))
        {
        $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na criação da filiação";
                    
                        $evento = print_r( $erro , true);
                        $conn = new \App\Connection\Conn;
                        $conn->InserirEvento($idContato, 'Filiação', $evento);
                
                        $mensagem = new \App\Rubeus\Mensagem;
                        $mensagem->EnvioEvento($idContato, $erro);
                    
                    
                        exit();
        }
        return $codFiliacao;
    
    }
      

}

?>