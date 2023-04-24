<?php

namespace App\Connection;

define('HOST', '54.172.6.0');  
define('DBNAME', 'int_rubeus_totvs');  
define('CHARSET', 'utf8');  
define('USER', 'root');  
define('PASSWORD', '5tJ2bcbfSwl7');  

class Conn
{ 
   /*  
    * Atributo estático para instância do PDO  
    */  
    public static $pdo;

   //    /*  
   //  * Escondendo o construtor da classe  
   //  */ 
   // private function __construct() {  
   //    //  
   //  } 

    static function GetInstance() {  
      if (!isset(self::$pdo)) {  
        try {  
          $opcoes = array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8', \PDO::ATTR_PERSISTENT => TRUE);  
          self::$pdo = new \PDO("mysql:host=" . HOST . "; dbname=" . DBNAME . "; charset=" . CHARSET . ";", USER, PASSWORD, $opcoes);  
        } catch (\PDOException $e) {  
          print "Erro: " . $e->getMessage();  
        }  
      }  
      return self::$pdo;  
    }  


   
   public function InserirEvento($idContato,$tipoEvento, $evento)
   {
      $connection = $this->GetInstance();
      $stmt = $connection ->prepare("INSERT INTO int_rubeus_totvs.EVENTOS(CONTATO, DATAEVENTO,  TIPOEVENTO, EVENTO) VALUES (:CONTATO, :DATAEVENTO, :TIPOEVENTO, :EVENTO)");
      $stmt ->bindParam(":DATAEVENTO", date( 'Y/m/d h:i:s' ));
      $stmt ->bindParam(":CONTATO"   , $idContato);
      $stmt ->bindParam(":TIPOEVENTO", $tipoEvento);
      $stmt ->bindParam(":EVENTO"    , $evento);
      $stmt -> execute();
   }
   
   
}




?>