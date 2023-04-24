<?php

namespace App\Rubeus;



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
//recebe o idContato
function ValidaIdContato($idContato)
{

   $connectRubeus = new ConnectRubeus;
   $origemContato = $connectRubeus->GetOrigemContato();
   $tokenContato = $connectRubeus->GetTokenContato();
   $url = $connectRubeus->GetUrl();

  
   $data = array( 'id' => $idContato,
                 'origem' => $origemContato ,
                 'token' => $tokenContato  );
   $options = array(
                    'http' => array(
                       'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                       'method'  => 'POST',
                       'content' => http_build_query($data),
                    ),
                   );
  $context  = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
  $json_data_user = json_decode($response, true);

  $erro =  $json_data_user;
  $evento = print_r( $erro , true);


   if($json_data_user["success"] != 1 and !is_null($idContato))
   {
     $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na classe contato";
     $evento = print_r( $erro , true);
     $conn = new \App\Connection\Conn;
     $conn->InserirEvento($idContato, 'ValidaContato', $evento);
     // InserirEvento($connection, $idContato, 'Contato', $evento ); 
     //$mensagem = Mensagem::EnvioEvento($idContato, $erro);
     exit();

   }

   if(is_null($idContato))
   {
      $retorno = 2;
   }
   else
   {
      if($idContato == $json_data_user["dados"]["id"])
      {
         $retorno = 1;
      }
   
      else
      {
         $retorno = 0;
      }
      }

   return $retorno;
}

function RecebeIdContato($requisicao)
{
   if ($requisicao["tipo"]["id"] == 1298 or $requisicao["tipo"]["id"] == 1287)
   {
      $idContato = $requisicao["contato"]["id"];
   } else
   {
      $idContato = "0";
   }

   return $idContato;
}

function RecebeContato($requisicao)
{  
   
    $idContato = $requisicao[0]["contato"]["id"];
 
    $connectRubeus = new ConnectRubeus;
    $origemContato = $connectRubeus->GetOrigemContato();
    $tokenContato = $connectRubeus->GetTokenContato();
    $url = $connectRubeus->GetUrl();

   
    $data = array( 'id' => $idContato,
                  'origem' => $origemContato ,
                  'token' => $tokenContato  );
    $options = array(
                     'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                     ),
                    );
   $context  = stream_context_create($options);
   $response = file_get_contents($url, false, $context);
   $json_data_user = json_decode($response, true);

    if($json_data_user["success"] != 1 )
    {
      $erro = "O aluno não foi criado no Totvs: O ocorreu um problema na classe contato";
      $evento = print_r( $erro , true);
      $conn = new \App\Connection\Conn;
      $conn->InserirEvento($idContato, 'Contato', $evento);
      // InserirEvento($connection, $idContato, 'Contato', $evento ); 
      //$mensagem = Mensagem::EnvioEvento($idContato, $erro);
      exit();

    }

     
    $evento = print_r(  $json_data_user   , true);
    $conn = new \App\Connection\Conn;
    $conn->InserirEvento($idContato, 'Contato', $evento);
  
    $contato =$requisicao[0]["contato"]["id"];
    $codigo  =$requisicao[0]["contato"]["codigo"];


    
    //Abaixo acessando campos complementares
   try{
    
    $Array_campos_personalizados = $json_data_user["dados"]["camposPersonalizados"];
    $funcao = new \App\Repositorio\Funcoes;
 
     foreach ($Array_campos_personalizados as $row)
      {
         
         if($row['coluna'] == 'anoescolar_compl_cont'){
         $anoescolar_compl_cont = $row['valor'];
         $retorno_ano_escolar = $funcao->VerificaAnoEscolar($anoescolar_compl_cont);
         $COD_ANO_ESCOLAR =  $retorno_ano_escolar;
         
         }

      
         
         if($row['coluna'] == 'naturalidadeinscricao_compl_cont'){
         $naturalidade_aluno =   $funcao->cidade($row['valor']);
            $estado_natal_aluno =   $funcao->estado($row['valor']);
         }
        

         // Dados do Responsavel financeiro
         if($row['coluna'] == 'tipoderesponsavel_compl_cont'){
            $tipoRespFinanceiro = $row['valor'];
            }
            if($row['coluna'] == 'nomedoresponsavel_compl_cont'){
               $nomeRespFinanceiro = $row['valor'];
               }
            if($row['coluna'] == 'campopersonalizado_14_compl_cont'){
               $foneRespFinanceiro = $row['valor'];
               }
            if($row['coluna'] == 'campopersonalizado_13_compl_cont'){
               $emailRespFinanceiro = $row['valor'];
               }
            if($row['coluna'] == 'datadenascimentodoresponsavel_compl_cont'){
               $dtNascimentoRespFinanceiro = $row['valor'];
               }
            if($row['coluna'] == 'cpfdoresponsavel_compl_cont'){
               $cpfRespFinanceiro = $row['valor'];
               }
            if($row['coluna'] == 'campopersonalizado_10_compl_cont'){
               $naturalidadeRespFinanc =   $funcao->cidade($row['valor']);
               $estadoNatalRespFinanc =   $funcao->estado($row['valor']);
            }
   
            // Dados do Responsavel Academico
            if($row['coluna'] == 'campopersonalizado_47_compl_cont'){
               $tipoRespAcad = $row['valor'];
               }
            if($row['coluna'] == 'nomedoresponsavelacademico2_compl_cont'){
               $nomeRespAcad = $row['valor'];
               }
            if($row['coluna'] == 'telefonedoresponsavelacademico2_compl_cont'){
               $foneRespAcad = $row['valor'];
               }
            if($row['coluna'] == 'emaildoresponsavelacademico2_compl_cont'){
               $emailRespAcad = $row['valor'];
               }
            if($row['coluna'] == 'datadenascimentodoresponsavelacademico2_compl_cont'){
               $dtNascimentoRespAcad = $row['valor'];
            }
            if($row['coluna'] == 'cpfdoresponsavelacademico2_compl_cont'){
               $cpfRespAcad = $row['valor'];
               }
            if($row['coluna'] == 'campopersonalizado_1_compl_cont'){
               $naturalidadeRespAcad =   $funcao->cidade($row['valor']);
               $estadoNatalRespAcad =   $funcao->estado($row['valor']);
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
      'numero_casa'=> $json_data_user["dados"]["numero"] ,
      'endereco_casa' => mb_strtoupper($json_data_user["dados"]["endereco"]) ,
      'bairro' => mb_strtoupper($json_data_user["dados"]["bairro"]),
      'email' => mb_strtoupper($json_data_user["dados"]["emails"]["principal"]["email"] ),
      'telefone' => substr($json_data_user["dados"]["telefones"]["principal"]["telefone"], 3, 12),
      'sexo' => $sigla_sexo,
      'cidade' => (  $funcao->cidade($json_data_user["dados"]["cidadeNome"])),
      'uf' => strtoupper(  $funcao->estado($json_data_user["dados"]["cidadeNome"])),
      'ano_escolar' => $COD_ANO_ESCOLAR,

      'tipo_resp_financ' => $tipoRespFinanceiro,
      'nome_resp_financ' => mb_strtoupper($nomeRespFinanceiro),
      'email_resp_financ' => strtoupper($emailRespFinanceiro),
      'dt_nasc_resp_financ' => $dtNascimentoRespFinanceiro,
      'cpf_resp_financ' => preg_replace('#[^0-9]#', '',$cpfRespFinanceiro),
      'fone_resp_financ' => substr($foneRespFinanceiro,3,12),
      'cidade_natal_resp_financ' => ($naturalidadeRespFinanc),
      'estado_natal_resp_financ' => strtoupper($estadoNatalRespFinanc),

      'tipo_resp_acad' => $tipoRespAcad,
      'nome_resp_acad' => mb_strtoupper($nomeRespAcad),
      'email_resp_acad' =>mb_strtoupper($emailRespAcad),
      'dt_nasc_resp_acad' => $dtNascimentoRespAcad,
      'cpf_resp_acad' =>  preg_replace('#[^0-9]#', '',$cpfRespAcad ), 
      'fone_resp_acad' => substr($foneRespAcad,3,12),
      'cidade_natal_resp_acad' => $naturalidadeRespAcad,
      'estado_natal_resp_acad' => strtoupper($estadoNatalRespAcad)
   

       );

      $evento = print_r(  $dados , true);
      // $evento = print_r(  'teste' , true);
       $conn = new \App\Connection\Conn;
       $conn->InserirEvento($idContato, 'Dados', $evento);

      
      } catch (\Throwable $th)
      {
      $erro = "O aluno não criado no Totvs: O ocorreu um problema na classe contato";
      $evento = print_r( $erro , true);
      $conn = new \App\Connection\Conn;
      $conn->InserirEvento($idContato, 'Dados', $evento);
      //$mensagem = Mensagem::EnvioEvento($idContato,  $erro );
      exit();
      }
 
    
    
    return $dados ;
    

}


}

?>