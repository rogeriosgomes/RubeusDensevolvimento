<?php

namespace App\Rubeus;

Class Mensagem
{

    function MensagemConfirmacao($ra)
    {
        $conect = new \App\Totvs\ConnectTotvs;
        $client =$conect->conectasql();
    
        $params = array(
                    'codSentenca' =>'INT.0007', 
                    'codColigada'=>1, 
                    'codSistema'=>'S', 
                    'parameters'=>"RA=$ra" 
                        
                    ); 
                
        $resultSoap = $client->RealizarConsultaSQL($params);
        $result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
        $resultArray = json_decode(json_encode($result), true);
        $inf = $resultArray["Resultado"]["DADOS"];

        return $inf;
    }

    function EnvioEvento($idContato, $mensagem)
    {   
        $connectRubeus = new ConnectRubeus;
        $canal  = $connectRubeus->GetCanalEvento();
        $origem = $connectRubeus->GetOrigemEvento();
        $token  = $connectRubeus->GetTokenEvento();
        $url    = $connectRubeus->GetUrlEvento();

        
        $params = [
            'tipo' =>  $canal,
            'descricao' => $mensagem,
            'pessoa'               => [
                'id' =>  $idContato,
            ],
            'origem' => $origem,
            'token' => $token
        ];
         // Cria o cURL
        
         $curl = curl_init($url);
         
         curl_setopt($curl, CURLOPT_HTTPHEADER, [
             'Content-Type: application/json',
             'Accept: application/json',
         ]);
         curl_setopt($curl, CURLOPT_POST, 1);
         curl_setopt($curl, CURLOPT_MAXREDIRS, -1);
         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         
         // Envia a requisição e salva a resposta
         $response = curl_exec($curl);
         $err = curl_error($curl);
     
         curl_close($curl);
         
         
         if ($err) {
     
             $respostas['erro'] = $err;
         
         }
        

         return  $response;
         
    }

    function EnvioEventoContato($idContato, $canal, $mensagem)
    {   
        $connectRubeus = new ConnectRubeus;
        // $canal  = $connectRubeus->GetCanalEvento();
        $origem = $connectRubeus->GetOrigemEvento();
        $token  = $connectRubeus->GetTokenEvento();
        $url    = $connectRubeus->GetUrlEvento();

        
        $params = [
            'tipo' =>  $canal,
            'descricao' => $mensagem,
            'pessoa'               => [
                'id' =>  $idContato,
            ],
            'origem' => $origem,
            'token' => $token
        ];
         // Cria o cURL
        
         $curl = curl_init($url);
         
         curl_setopt($curl, CURLOPT_HTTPHEADER, [
             'Content-Type: application/json',
             'Accept: application/json',
         ]);
         curl_setopt($curl, CURLOPT_POST, 1);
         curl_setopt($curl, CURLOPT_MAXREDIRS, -1);
         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         
         // Envia a requisição e salva a resposta
         $response = curl_exec($curl);
         $err = curl_error($curl);
     
         curl_close($curl);
         
         
         if ($err) {
     
             $respostas['erro'] = $err;
         
         }
        

         return  $err;
    }
 }

?>