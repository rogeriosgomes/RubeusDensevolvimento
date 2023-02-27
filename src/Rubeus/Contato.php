<?php

class Contato{

// propriedades

public $nome;
public $cpf;
public $rg;
public $dataNascimento;
public $naturalidade;
public $estadoNatal;
public $cep;
public $numero;
public $endereco;
public $bairro;
public $email;
public $telefone;
public $sexo;
public $cidade;
public $uf;
public $anoEscolar;
     
//métodos
function RecebeIdContato($requisicao)
{
   if ($requisicao["tipo"]["id"] == 1298)
   {
      $idContato = $requisicao["contato"]["id"];
   } else
   {
      $idContato = "0";
   }

   return $idContato;
}

function RecebeContato($requisicao,$connection)
{  
    $idContato = $requisicao[0]["contato"]["id"];
   
    $tokenContato = '4af096b1b76386bda5c54fccfe6c806f';
    $origemContato = 9;
    $url = 'https://crmthomasjeffersonhomolog.apprubeus.com.br/api/Contato/dadosPessoa/';

    $params = [
        //'codigo' => '',
        'id' => $idContato,
        'origem' => $origemContato ,
        'token' => $tokenContato 
        
    ];

    $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
             ]);
    
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // Envia a requisição e salva a resposta
    $response = curl_exec($curl);
    // $err = curl_error($curl);

    curl_close($curl);

    $data = date( 'Y/m/d h:i:s' );
    $json_data_user = json_decode($response, true);
    
    $evento = print_r( $response , true);
    InserirEvento($connection, $idContato, 'Contato', $evento ); 
       

   //  $stmt = $connection ->prepare("INSERT INTO int_rubeus_totvs.EVENTOS(DATAEVENTO,  TIPOEVENTO, EVENTO) VALUES (:DATAEVENTO, 'CONTATO', :EVENTO)");
   //  $stmt ->bindParam(":DATAEVENTO", $data);
   //  $stmt ->bindParam(":EVENTO", $response );
   //  $stmt -> execute();

    // retorna os dados dos responsavel financeiro e academico

    $contato =$requisicao[0]["contato"]["id"];
    $codigo  =$requisicao[0]["contato"]["codigo"];

    $stmtRespFinanc = $connection ->prepare("SELECT * FROM RESPFINANCEIRO 
                                   WHERE idcontato =:IDCONTATO 
                                     AND codigo=:CODIGO 
                                     AND idevento IN (SELECT MAX(idevento) 
                                                      FROM RESPFINANCEIRO 
                                                      WHERE idcontato =:IDCONTATO 
                                                        AND codigo=:CODIGO ) ");
    
       
    $stmtRespFinanc->bindParam(":IDCONTATO", $contato);
    $stmtRespFinanc->bindParam(":CODIGO",    $codigo);
    
    $stmtRespFinanc ->execute();
    
    $dadosFinanceiro = $stmtRespFinanc -> fetchAll();

   // retorna os dados dos responsavel financeiro

     $stmtRespAcad = $connection ->prepare("SELECT * FROM RESPACADEMICO
                                             WHERE idcontato =:IDCONTATO 
                                                AND codigo=:CODIGO 
                                                AND idevento IN (SELECT MAX(idevento) 
                                                                  FROM RESPACADEMICO 
                                                                  WHERE idcontato =:IDCONTATO 
                                                                  AND codigo=:CODIGO ) ");


      $stmtRespAcad->bindParam(":IDCONTATO", $contato);
      $stmtRespAcad->bindParam(":CODIGO",   $codigo);

      $stmtRespAcad ->execute();

      $dadosAcad = $stmtRespAcad -> fetchAll();
    
    


    //Abaixo acessando campos complementares
    $Array_campos_personalizados = $json_data_user["dados"]["camposPersonalizados"];
 
     foreach ($Array_campos_personalizados as $row)
      {
         
         if($row['coluna'] == 'anoescolar_compl_cont'){
         $anoescolar_compl_cont = $row['valor'];
         
         verificaAnoEscolar($anoescolar_compl_cont);
         $retorno_ano_escolar = verificaAnoEscolar($anoescolar_compl_cont);
         $COD_ANO_ESCOLAR = $retorno_ano_escolar;
         
         }
         
         if($row['coluna'] == 'naturalidadeinscricao_compl_cont'){
         $naturalidade_aluno = cidade($row['valor']);
            $estado_natal_aluno = estado($row['valor']);
         }
         if($row['coluna'] == 'tipoderesponsavel_compl_cont'){
         $tipoderesponsavel_compl_cont = $row['valor'];
         }
         
      }
 
     if($json_data_user["dados"]["sexoNome"] == "Masculino"){
         $sexo = "M";
      }elseif($json_data_user["dados"]["sexoNome"] == "Feminino"){
            $sexo = "F";
      }else{  $sexo = "";
      }
      $sigla_sexo = $sexo ;

      $dados = array(
      'Idcontato' => $contato,
      'nome'=> mb_strtoupper($json_data_user["dados"]["nome"]),
      'cpf' => $json_data_user["dados"]["cpf"] ,
      'data_nascimento_aluno' =>$json_data_user["dados"]["datanascimento"] ,
      'naturalidade_aluno' => $naturalidade_aluno ,
      'estado_natal_aluno' =>strtoupper($estado_natal_aluno),
      'cep' => $json_data_user["dados"]["cep"] ,
      'numero_casa'=> 'SN',
      'endereco_casa' => mb_strtoupper($json_data_user["dados"]["endereco"]) ,
      'bairro' => mb_strtoupper($json_data_user["dados"]["bairro"]),
      'email' => mb_strtoupper($json_data_user["dados"]["emails"]["principal"]["email"] ),
      'telefone' => substr($json_data_user["dados"]["telefones"]["principal"]["telefone"], 3, 12),
      'sexo' => $sigla_sexo,
      'cidade' => (cidade($json_data_user["dados"]["cidadeNome"])),
      'uf' => strtoupper(estado($json_data_user["dados"]["cidadeNome"])),
      'ano_escolar' => $COD_ANO_ESCOLAR,
      'tipo_resp_financ' => $dadosFinanceiro[0]['tipo'],
      'nome_resp_financ' => mb_strtoupper($dadosFinanceiro[0]['nome']),
      'email_resp_financ' => strtoupper($dadosFinanceiro[0]['email']),
      'dt_nasc_resp_financ' => $dadosFinanceiro[0]['dtnascimento'],
      'cpf_resp_financ' => preg_replace('#[^0-9]#', '',$dadosFinanceiro[0]['cpf']),
      'fone_resp_financ' => $dadosFinanceiro[0]['telefone'],
      'cidade_natal_resp_financ' => (cidade($dadosFinanceiro[0]['naturalidade'])),
      'estado_natal_resp_financ' => strtoupper(estado($dadosFinanceiro[0]['naturalidade'])),
      'tipo_resp_acad' => $dadosAcad[0]['tipo'],
      'nome_resp_acad' => mb_strtoupper($dadosAcad[0]['nome']),
      'email_resp_acad' =>mb_strtoupper($dadosAcad[0]['email']),
      'dt_nasc_resp_acad' => $dadosAcad[0]['dtnascimento'],
      'cpf_resp_acad' =>  preg_replace('#[^0-9]#', '',$dadosAcad[0]['cpf']), 
      'fone_resp_acad' => $dadosAcad[0]['telefone'],
      'cidade_natal_resp_acad' => (cidade($dadosAcad[0]['naturalidade'])),
      'estado_natal_resp_acad' => strtoupper(estado($dadosAcad[0]['naturalidade'])),

       );
    
    return $dados;

}


}

?>