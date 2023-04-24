<?php

namespace App\Rubeus;

Class Evento 
{
    public function  RecebeEvento($request)
    {
    
    $data = date( 'Y/m/d h:i:s' );
    $evento = json_decode($request, true);
    $idContato =  $evento[0]["contato"]["id"];

    $conn = new \App\Connection\Conn;
    $inserirEvento = $conn->InserirEvento($idContato, 'Evento', $request);
    
    // $stmt = $connection ->prepare("INSERT INTO int_rubeus_totvs.EVENTOS(CONTATO, DATAEVENTO,  TIPOEVENTO, EVENTO) VALUES (:CONTATO, :DATAEVENTO, 'EVENTO', :EVENTO)");
    // $stmt ->bindParam(":DATAEVENTO", $data);
    // $stmt ->bindParam(":EVENTO", $request );
    // $stmt ->bindParam(":CONTATO", $idContato );
    // $stmt -> execute();
    
    return   json_decode($request, true);
    //$json_data["tipo"]["codigo"];
    
    }
}







?>