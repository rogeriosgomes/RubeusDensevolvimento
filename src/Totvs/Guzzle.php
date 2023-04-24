<?php

namespace App\Totvs;
require_once "src/Totvs/ConnectTotvs.php";





// function conectasql2()
// {
//    $login_integracao ="integracao";
//    $password =  "int%2*45";
//    $urltotvs = "https://ws2.thomas.org.br:8051";
//    $soapParams = array(
//            'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
//            'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
//            'authentication' => SOAP_AUTHENTICATION_BASIC, 
//            'trace' => '1', 
//            'exceptions' => true,
//            'cache_wsdl' => WSDL_CACHE_NONE
//        );
//        $wsdl =$urltotvs."/wsConsultaSQL/MEX?wsdl";
//         /*"env("URL_WEBSERVICE_TOTVS") ". "/wsDataServer/MEX?wsd";*/
//        $client = new SoapClient($wsdl, $soapParams);

//    return $client;
// }

$ws1 = new ConnectTotvs;

$client =$ws1->conectasql();

            $params = array(
                    'codSentenca' =>'INT.0002', 
                    'codColigada'=>1, 
                    'codSistema'=>'P', 
                    'parameters'=>"NOME=Rogerio Silveira Gomes;DTNASCIMENTO=1982-05-04;ESTADONATAL=DF;NATURALIDADE=Rio Verde;CPF=88631443115"                  
                    ); 
            $resultSoap = $client->RealizarConsultaSQL($params);
            $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
            $resultArray = json_decode(json_encode($result), true);

 var_dump($resultArray);
?>