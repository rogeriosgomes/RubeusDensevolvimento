<?php
  
 function carregar(string $nomeClasse)
 {
    $caminhoCompleto =__DIR__."src/../$nomeClasse.php";
   
    
    if(file_exists($caminhoCompleto))
    {
        require_once $caminhoCompleto;
    } 
   
 }

 spl_autoload_register("carregar");

?>