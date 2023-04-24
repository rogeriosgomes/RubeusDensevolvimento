<?php
namespace App\Totvs;

class ConnectTotvs{
 

//conexão com DataServer
function conectaDataServer()
   { 
    ini_set('soap.wsdl_cache_enabled', '0');
     $login_integracao ="integracao";
     $password =  "int%2*45";
     $urltotvs = "https://ws2.thomas.org.br:8051";
     $soapParams = array(
        'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
        'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
        'authentication' => SOAP_AUTHENTICATION_BASIC, 
        'trace' => '1', 
        'exceptions' => true,
        'cache_wsdl' => WSDL_CACHE_NONE
                            );
        $wsdl = $urltotvs."/wsDataServer/MEX?wsd";
        $client = new \SoapClient($wsdl, $soapParams);

        return $client;

    }

//consexãp com DataServer SQL
function conectasql()
{
    ini_set('soap.wsdl_cache_enabled', '0');
   $login_integracao ="integracao";
   $password =  "int%2*45";
   $urltotvs = "https://ws2.thomas.org.br:8051";
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
       $client = new \SoapClient($wsdl, $soapParams);

   return $client;
}
    function Valida()
    {
         $teste = conectasql();
        return 'função esta funcionando';
    }
}

?>


