<?php

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

function cidade ($estadouf){
      
    list($cidade, $estado_natal) = explode('-', $estadouf);
    return $cidade;
  }

function estado ($estadouf){
    
    list($cidade, $estado_natal) = explode('-', $estadouf);
    return trim($estado_natal);
  }

function verificaAnoEscolar($param1){
    
    $String_Ano_Escolar = $param1;

    //nova estrutura
    if($String_Ano_Escolar == "BERÇÁRIO" ){ 
        $Cod_Ano_Escolar = '00';
        
    }elseif($String_Ano_Escolar == "MATERNAL 1"){
        $Cod_Ano_Escolar = '01';
        
    }elseif($String_Ano_Escolar == "MATERNAL 2"){
        $Cod_Ano_Escolar = '02';
        
    }elseif($String_Ano_Escolar == "INFANTIL 1"){
        $Cod_Ano_Escolar = '03';
        
    }elseif($String_Ano_Escolar == "INFANTIL 2"){
        $Cod_Ano_Escolar = '04';
        
    }
    //Estrutura antiga
    elseif($String_Ano_Escolar == "INFANTIL 1" ){ 
        $Cod_Ano_Escolar = '00';
        
    }elseif($String_Ano_Escolar == "INFANTIL 2"){
        $Cod_Ano_Escolar = '01';
        
    }elseif($String_Ano_Escolar == "INFANTIL 3"){
        $Cod_Ano_Escolar = '02';
        
    }elseif($String_Ano_Escolar == "INFANTIL 4"){
        $Cod_Ano_Escolar = '03';
        
    }elseif($String_Ano_Escolar == "INFANTIL 5"){
        $Cod_Ano_Escolar = '04';
        
    }
    
    
    elseif($String_Ano_Escolar == "1° ANO EF" or $String_Ano_Escolar == "1º ANO EF" or $String_Ano_Escolar == "1\u00ba ANO EF"){
        $Cod_Ano_Escolar = '05';
        
    }elseif($String_Ano_Escolar == "2° ANO EF" or $String_Ano_Escolar == "2º ANO EF"  or $String_Ano_Escolar == "2\u00ba ANO EF"){
        $Cod_Ano_Escolar = '06';
        
    }elseif($String_Ano_Escolar == "3° ANO EF" or $String_Ano_Escolar == "3º ANO EF"  or $String_Ano_Escolar == "3\u00ba ANO EF"){
        $Cod_Ano_Escolar = '07';
        
    }elseif($String_Ano_Escolar == "4° ANO EF" or $String_Ano_Escolar == "4º ANO EF"  or $String_Ano_Escolar == "4\u00ba ANO EF"){
        $Cod_Ano_Escolar = '08';
        
    }elseif($String_Ano_Escolar == "5° ANO EF" or $String_Ano_Escolar == "5º ANO EF" or $String_Ano_Escolar == "5\u00ba ANO EF"){
        $Cod_Ano_Escolar = '09';
        
    }elseif($String_Ano_Escolar == "6° ANO EF" or $String_Ano_Escolar == "6º ANO EF"  or $String_Ano_Escolar == "6\u00ba ANO EF"){
        $Cod_Ano_Escolar = '10';
        
    }elseif($String_Ano_Escolar == "7° ANO EF" or $String_Ano_Escolar == "7º ANO EF"  or $String_Ano_Escolar == "7\u00ba ANO EF"){
        $Cod_Ano_Escolar = '11';
        
    }elseif($String_Ano_Escolar == "8° ANO EF" or $String_Ano_Escolar == "8º ANO EF"  or $String_Ano_Escolar == "8\u00ba ANO EF"){
        $Cod_Ano_Escolar = '12';
        
    }elseif($String_Ano_Escolar == "9° ANO EF" or $String_Ano_Escolar == "9º ANO EF" or $String_Ano_Escolar == "9\u00ba ANO EF"){
        $Cod_Ano_Escolar = '13';
        
    }elseif($String_Ano_Escolar == "1° ANO EM" or $String_Ano_Escolar == "1º ANO EM"  or $String_Ano_Escolar == "1\u00ba ANO EM"){
        $Cod_Ano_Escolar = '14';
        
    }elseif($String_Ano_Escolar == "2° ANO EM"  or $String_Ano_Escolar == "2º ANO EM" or $String_Ano_Escolar == "2\u00ba ANO EM"){
        $Cod_Ano_Escolar = '15';
        
    }elseif($String_Ano_Escolar == "3° ANO EM" or $String_Ano_Escolar == "3º ANO EM" or $String_Ano_Escolar == "3\u00ba ANO EM"){
        $Cod_Ano_Escolar = '16';
        
    }elseif($String_Ano_Escolar == "CONCLUÍDO"){
        $Cod_Ano_Escolar = '17';
        
    }
    
    return $Cod_Ano_Escolar;
    
}
?>
