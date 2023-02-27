<?php

try {
   $connection = new PDO("mysql:host=54.172.6.0;dbname=int_rubeus_totvs", "root", "5tJ2bcbfSwl7");
} catch (\PDOException $e) {
   echo $e->getMessage();
}

function InserirEvento( $connection, $idContato,$tipoEvento, $evento)
{
   $stmt = $connection ->prepare("INSERT INTO int_rubeus_totvs.EVENTOS(CONTATO, DATAEVENTO,  TIPOEVENTO, EVENTO) VALUES (:CONTATO, :DATAEVENTO, :TIPOEVENTO, :EVENTO)");
   $stmt ->bindParam(":DATAEVENTO", date( 'Y/m/d h:i:s' ));
   $stmt ->bindParam(":CONTATO"   , $idContato);
   $stmt ->bindParam(":TIPOEVENTO", $tipoEvento);
   $stmt ->bindParam(":EVENTO"    , $evento);
   $stmt -> execute();
}





?>