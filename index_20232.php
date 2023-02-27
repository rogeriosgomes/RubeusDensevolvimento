<?php

include 'functions.php';


//Dados para conectar no Totvs

$login_integracao ="integracao";
$password =  "int%2*45";
$urltotvs = "https://ws.thomas.org.br:8051";

// -- validação do usuario no Totvs

 $soapParams = array(
            'login' => $login_integracao/*env("USERNAME_WEBSERVICE_TOTVS")*/,
            'password' => $password/*env("PASSWORD_WEBSERVICE_TOTVS")*/,
            'authentication' => SOAP_AUTHENTICATION_BASIC, 
            'trace' => '1', 
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        );
        $wsdl =$urltotvs."/wsDataServer/MEX?wsd";
         /*"env("URL_WEBSERVICE_TOTVS") ". "/wsDataServer/MEX?wsd";*/
        $client = new SoapClient($wsdl, $soapParams);

//Primeira parte recupera as requisições do Rubeus

         $CANAL_RUBEUS = 265;
         $TOKEN_RUBEUS = 'ec3d8e2077780c54b4d4a3612d8367b1';
         $ORIGEM_RUBEUS = '275';
         
         date_default_timezone_set('America/Sao_Paulo'); 

         header('Content-Type: application/json');
         $request = file_get_contents('php://input'); //recebe json do webhook do Rubeus
        
         $date_event = date( 'd-m-Y H:i:s' );
         $log_events = array(
            'DATA DO EVENTO' => $date_event,
            'EVENTO_JSON' => $request
            );
        
         $req_dump = print_r($log_events, true );
         $fp = file_put_contents(  'events.json', $req_dump, FILE_APPEND );
          
      //RECEBE WEBHOOK COM NOTIFICAÇÃO DE EVENTO "ENTROU NA ETAPA PRÉ MATRICUL" E EXTRAI ID DO USUARIO PARA CONSULTA NA API 
        $json_data = json_decode($request, true); // decodifica variavel $request em um objeto PHP
      
        // $req_dump_contato = print_r($json_data[0]["contato"], true );
        // $fp = file_put_contents(  'contato.json',  $req_dump_contato, FILE_APPEND );
        
        function estrutura($json_data) //pega apenas o array "contato"
        {
          return $json_data[0]["contato"];
        }
        $dados_contato = $json_data[0]["contato"];
        
            foreach ($dados_contato as $key => $value) {
                if($key == 'id'){
                    $id_contato = $value;
                    //echo "$id_contato <br>";
                }
                
                $fp = file_put_contents( 'contato.json', $req_dump );    
           
            }

//CONSULTA USUARIO VIA API
$params = [
            //'codigo' => '',
            'id' => $id_contato,
            'origem' => '9',
            'token' => '4af096b1b76386bda5c54fccfe6c806f'
            
        ];

        // Cria o cURL 'https://crmthomasjefferson.apprubeus.com.br/api/Contato/dadosPessoa/'
       // $curl = curl_init('https://crmthomasjeffersonhomolog.apprubeus.com.br/api/Contato/dadosPessoa/' ); //URL APONTANDO PARA AMBIENTE DE TESTE
        $curl = curl_init('https://crmthomasjefferson.apprubeus.com.br/api/Contato/dadosPessoa/' ); //URL APONTANDO PARA AMBIENTE DE PRODUÇÃO
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Envia a requisição e salva a resposta
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
                
        if ($err) {
                echo ('Erro');
            } else {

                $respostas = json_encode(json_decode($response), JSON_PRETTY_PRINT);
                
                echo ($respostas);
            }

     $req_dump = print_r( $respostas, true );
     $fp = file_put_contents( 'consulta_api.json', $req_dump );

     //RECEBE DADOS DO ALUNO RETORNADOS PELA CONSULTA DE API DO RUBEUS
     $json_data_user = json_decode($respostas, true);
  
    //RECEBE WEBHOOK COM NOTIFICAÇÃO DE EVENTO 'Completou a quarta etapa' E extrai dados dos responsavel
 
       if($json_data[0]["tipo"]["nome"]== 'Completou a quarta etapa'){
        
        $dados_resp_fin= strip_tags($json_data[0]["descricao"]);
        
        $fp = file_put_contents('consulta_resp_fin_api.json', $dados_resp_fin);
               
      //Recebe os dados dos responsável financeiro
            $idevento      =$json_data[0]["id"];
            $contato =$json_data[0]["contato"]["id"];
            $codigo =$json_data[0]["contato"]["codigo"];
            $tiporespfin = str_replace(': ', '', strrchr($dados_resp_fin,':'));
            $nomerespfin =strtoupper(get_parts($dados_resp_fin, 'Nome do Responsável: ', 'Telefone Celular - Filiação 1:'));
            $emailrespfin = get_parts($dados_resp_fin, 'E-mail - Filiação 1: ', 'Data de Nascimento do Responsável:');
            $datanascrespfin =  get_parts($dados_resp_fin, 'Data de Nascimento do Responsável: ', 'CPF do Responsável:');
            $cpfrespfin = get_parts($dados_resp_fin, 'CPF do Responsável: ', 'Naturalidade - Filiação 1:');
            $rgrespfin = '';
            $fonerespfin = get_parts($dados_resp_fin, 'Telefone Celular - Filiação 1: +55', 'E-mail - Filiação 1');
            $estadocivilrespfin ='';
            $naturalidaderespfin = get_parts($dados_resp_fin, 'Naturalidade - Filiação 1: ', 'Tipo de Responsável:');

                       
            
        $conn = new PDO("mysql:dbname=rubeus;host=54.172.6.0", "root", "5tJ2bcbfSwl7");
        $stmt = $conn ->prepare("INSERT INTO RESPFINANCEIRO(idevento, evento, idcontato, codigo, tipo, nome, email, dtnascimento, 
                                cpf, rg, telefone, estadocivil, naturalidade) VALUES (:IDEVENTO,'Completou a quarta etapa',:CONTATO,
                                :CODIGO,:TIPO,:NOME,:EMAIL,:DTNASCIMENTO,:CPF,:RG,:TELEFONE,:ESTADOCIVIL,:NATURALIDADE)");
        
        $stmt->bindParam(":IDEVENTO", $idevento);
        $stmt->bindParam(":CONTATO", $contato);
        $stmt->bindParam(":CODIGO", $codigo);
        $stmt->bindParam(":TIPO", $tiporespfin);
        $stmt->bindParam(":NOME", $nomerespfin);
        $stmt->bindParam(":EMAIL", $emailrespfin);
        $stmt->bindParam(":DTNASCIMENTO", $datanascrespfin);
        $stmt->bindParam(":CPF", $cpfrespfin);
        $stmt->bindParam(":RG", $rgrespfin);
        $stmt->bindParam(":TELEFONE", $fonerespfin);
        $stmt->bindParam(":ESTADOCIVIL", $estadocivilrespfin);
        $stmt->bindParam(":NATURALIDADE", $naturalidaderespfin);
        
        $stmt -> execute();
                    
        $log_dados_resp_fin = array(
              "TIPO"=>$tiporespfin,
              "NOME"=>$nomerespfin,
              "EMAIL"=>$emailrespfin,
              "DTNASCIMENTO"=>$datanascrespfin,
              "CPF"=>$cpfrespfin,
              "RG"=>$rgrespfin,
              "TELEFONE"=> $fonerespfin,
              "ESTADOCIVIL"=>$estadocivilrespfin,
              "NATURALIDADE"=>$naturalidaderespfin
          );
         
         $req_dump_fin = print_r( $log_dados_resp_fin , true);
         $fpdados = file_put_contents( 'dados_resp_fin_api.json', $req_dump_fin);
    
    }
    
     //RECEBE WEBHOOK COM NOTIFICAÇÃO DE EVENTO "5ª etapa" E extrai dados dos responsavel
     
    if($json_data[0]["tipo"]["nome"]== 'Completou a quinta etapa'){
     
        $dados_resp = strip_tags($json_data[0]["descricao"]);
        $fp = file_put_contents('consulta_resp_api.json', $dados_resp);
        
        //Recebe os dados dos responsável Acadêmico
        
            $idevento      =$json_data[0]["id"];
            $contato =$json_data[0]["contato"]["id"];
            $codigo =$json_data[0]["contato"]["codigo"];

            
            $tiporespacad = str_replace(': ', '', strrchr($dados_resp,':'));
            $nomerespacad =strtoupper(get_parts($dados_resp, 'Nome do Responsável Acadêmico 2: ', 'Telefone do Responsável Acadêmico 2'));
            $emailrespacad = get_parts($dados_resp, 'E-mail do Responsável Acadêmico 2: ', 'Data de Nascimento do Responsável Acadêmico 2');
            $datanascrespacad =  get_parts($dados_resp, 'Data de Nascimento do Responsável Acadêmico 2: ', 'CPF do Responsável Acadêmico 2:');
            $cpfrespacad = get_parts($dados_resp, 'CPF do Responsável Acadêmico 2: ', 'Naturalidade Filiação 2:');
            $rgrespacad = '';
            $fonerespacad = get_parts($dados_resp, 'Telefone do Responsável Acadêmico 2: +55', 'E-mail do Responsável Acadêmico 2:');
            $estadocivilrespacad = '';
            $naturalidaderespacad = get_parts($dados_resp, 'Naturalidade Filiação 2: ', 'Tipo de Responsável Acadêmico:');
            
            
        $conn = new PDO("mysql:dbname=rubeus;host=54.172.6.0", "root", "5tJ2bcbfSwl7");
        $stmt = $conn ->prepare("INSERT INTO RESPFINANCEIRO(idevento, evento, idcontato, codigo, tipo, nome, email, dtnascimento, 
                                cpf, rg, telefone, estadocivil, naturalidade) VALUES (:IDEVENTO,'Completou a quinta etapa',:CONTATO,
                                :CODIGO,:TIPO,:NOME,:EMAIL,:DTNASCIMENTO,:CPF,:RG,:TELEFONE,:ESTADOCIVIL,:NATURALIDADE)");
        
        $stmt->bindParam(":IDEVENTO", $idevento);
        $stmt->bindParam(":CONTATO", $contato);
        $stmt->bindParam(":CODIGO", $codigo);
        $stmt->bindParam(":TIPO", $tiporespacad);
        $stmt->bindParam(":NOME", $nomerespacad);
        $stmt->bindParam(":EMAIL", $emailrespacad);
        $stmt->bindParam(":DTNASCIMENTO", $datanascrespacad);
        $stmt->bindParam(":CPF", $cpfrespacad);
        $stmt->bindParam(":RG", $rgrespacad);
        $stmt->bindParam(":TELEFONE", $fonerespacad);
        $stmt->bindParam(":ESTADOCIVIL", $estadocivilrespacad);
        $stmt->bindParam(":NATURALIDADE", $naturalidaderespacad);
        
        $stmt -> execute();
        
        $log_dados_resp_acad = array(
            "TIPO"=>$tiporespacad,
            "NOME"=>$nomerespacad,
            "EMAIL"=>$emailrespacad,
            "DTNASCIMENTO"=>$datanascrespacad,
            "CPF"=>$cpfrespacad,
            "RG"=>$rgrespacad,
            "TELEFONE"=> $fonerespacad,
            "ESTADOCIVIL"=>$estadocivilrespacad,
            "NATURALIDADE"=>$naturalidaderespacad
        );
         
        
         $req_dump_acad = print_r( $log_dados_resp_acad  , true);
         $fpdados = file_put_contents( 'dados_resp_api.json', $req_dump_acad);
        
    }
        // OBSERVAÇÃO OS DADOS DO RESPONSAVEL ACADEMICO ESTA SENDO RECUPADOS DO EVETNO Completou a quarta etapa NO IF ACIMA
        // OBSERVAÇÃO OS DADOS DO RESPONSAVEL FINANCEIRO ESTA SENDO RECUPADOS DO EVETNO Completou a terceira etapa NO IF ACIMA
      
if($json_data[0]["tipo"]["nome"] != 'Completou a quinta etapa' and  $json_data[0]["tipo"]["nome"] !='Completou a quarta etapa'){
    
    
    //Retorna o ultima linha de entrada no evento "Completou a quarta etapa" do contato
     $conn = new PDO("mysql:dbname=rubeus;host=54.172.6.0", "root", "5tJ2bcbfSwl7");
  
    $stmt = $conn ->prepare("SELECT * FROM RESPFINANCEIRO WHERE idcontato =:IDCONTATO AND codigo=:CODIGO AND EVENTO = 'Completou a quarta etapa' AND 
        idevento IN (SELECT MAX(idevento) FROM RESPFINANCEIRO WHERE idcontato =:IDCONTATO AND codigo=:CODIGO AND EVENTO = 'Completou a quarta etapa') ");
    
    $contato =$json_data[0]["contato"]["id"];
    $codigo =$json_data[0]["contato"]["codigo"];
    
    $stmt->bindParam(":IDCONTATO", $contato);
    $stmt->bindParam(":CODIGO", $codigo);
    
    $stmt ->execute();
    
    $result = $stmt -> fetchAll();
    
    $dados = $result;
     
    $tiporespfin =$dados[0]["tipo"];
    $nomerespfin =$dados[0]["nome"];
    $emailrespfin =$dados[0]["email"];
    $datanascrespfin =$dados[0]["dtnascimento"];
    $cpfrespfin =$dados[0]["cpf"]; 
    $rgrespfin =$dados[0]["rg"];
    $fonerespfin =$dados[0]["telefone"];
    $estadocivilrespfin =$dados[0]["estadocivil"];
    $naturalidaderespfin =$dados[0]["naturalidade"];
    
    //Retorna ultima de entrada no evento "Completou a quinta etapa" do contato
    $conn = new PDO("mysql:dbname=rubeus;host=54.172.6.0", "root", "5tJ2bcbfSwl7");
  
    $stmt = $conn ->prepare("SELECT * FROM RESPFINANCEIRO WHERE idcontato =:IDCONTATO AND codigo=:CODIGO AND EVENTO = 'Completou a quinta etapa'AND 
        idevento IN (SELECT MAX(idevento) FROM RESPFINANCEIRO WHERE idcontato =:IDCONTATO AND codigo=:CODIGO AND EVENTO = 'Completou a quinta etapa') ");
    
    $contato =$json_data[0]["contato"]["id"];
    $codigo =$json_data[0]["contato"]["codigo"];
    
    $stmt->bindParam(":IDCONTATO", $contato);
    $stmt->bindParam(":CODIGO", $codigo);
    
    $stmt ->execute();
    
    $result = $stmt -> fetchAll();
    
    $dados = $result;
     
    $tiporespacad =$dados[0]["tipo"];
    $nomerespacad =$dados[0]["nome"];
    $emailrespacad =$dados[0]["email"];
    $datanascrespacad =$dados[0]["dtnascimento"];
    $cpfrespacad =$dados[0]["cpf"]; 
    $rgrespacad =$dados[0]["rg"];
    $fonerespacad =$dados[0]["telefone"];
    $estadocivilrespacad =$dados[0]["estadocivil"];
    $naturalidaderespacad=$dados[0]["naturalidade"];
        
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

	    if($row['coluna'] == 'nomedoresponsavel_compl_cont'){
	    $nomedoresponsavel_compl_cont = strtoupper($row['valor']); //converte nome para caixa alta
	    }
	    if($row['coluna'] == 'cpfdoresponsavel_compl_cont'){
	    $cpfdoresponsavel_compl_cont = $row['valor'];
     
	    }
	    if($row['coluna'] == 'telefonedoresponsavel1_compl_cont'){
	    $telefonedoresponsavel1_compl_cont = substr($row['valor'], 3, 12);
	    }
	    if($row['coluna'] == 'datadenascimentodoresponsavel_compl_cont'){
	    $datadenascimentodoresponsavel_compl_cont = $row['valor'];
	    }
	    // Retirado por causa da alteração do formulario no mes 04 do ano 2022
    //  if($row['coluna'] == 'naturalidadedoresponsavel_compl_cont'){
	  //$naturalidadedoresponsavel_compl_cont = $row['valor'];
	 // }
	    if($row['coluna'] == 'rg_compl_cont'){
	    $rg_compl_cont = $row['valor'];
	    }
        //dados do responsavél acadêmico
        if($row['coluna'] == 'tipoderesponsavelacademico_compl_cont'){
        $tipo_resp_academico = $row['valor'];
        }
	  
	}
	
	//Abaixo acessando campos principais
    $id_contato           = $json_data_user["dados"]["id"]; 
    $nome                 = strtoupper($json_data_user["dados"]["nome"]); //converte nome para caixa alta
	    if ($tipoderesponsavel_compl_cont == "Próprio Aluno") {
	    	$cpf_aluno = preg_replace('#[^0-9]#', '', $cpfdoresponsavel_compl_cont);
	      } else{
	        $cpf_aluno = $json_data_user["dados"]["cpf"];
	      }
    $cpf                   = $cpf_aluno;
    $data_nascimento_aluno = $json_data_user["dados"]["datanascimento"];
  
    $cep                   = $json_data_user["dados"]["cep"];
    $numero_casa           = 'S/N' ;//$json_data_user["dados"]["numero"];
    $endereco_casa         = $json_data_user["dados"]["endereco"];
    $email                 = $json_data_user["dados"]["emails"]["principal"]["email"];
	    if(!isset($json_data_user["dados"]["emails"]["secundarios"][0]["email"])){
	       $va_email_resp =  $email ;
	    } else {
	        $va_email_resp = $json_data_user["dados"]["emails"]["secundarios"][0]["email"];
	    }
    $email_resp            = $va_email_resp;
    
	    if($json_data_user["dados"]["sexoNome"] == "Masculino"){
	                         $sexo = "M";
	                }elseif($json_data_user["dados"]["sexoNome"] == "Feminino"){
	                        $sexo = "F";
	                }else{  $sexo = "";
	               }
    $sigla_sexo           = $sexo ;
    $cidade                = cidade($json_data_user["dados"]["cidadeNome"]);
    $uf                    = estado($json_data_user["dados"]["cidadeNome"]);
    $bairro                = $json_data_user["dados"]["bairro"];
    $data_atual            = date('Y/m/d'); //Data atual
    if(!isset($json_data_user["dados"]["telefones"]["principal"]["telefone"])){
      $telefone = substr( $json_data_user["dados"]["telefones"]["principal"]["telefone"], 3, 12);
    } else{
      $telefone = $telefonedoresponsavel1_compl_cont;
    }
    $telefone_aluno        = substr( $json_data_user["dados"]["telefones"]["principal"]["telefone"], 3, 12);
 
    //Pega sigla do estado civil do responsavel
	foreach ($Array_campos_personalizados as $row){
	    if($row['coluna'] == 'estadocivilresponsavel_compl_cont'){
	    $estadocivilresponsavel_compl_cont = $row['valor'];
	    }                     
		}

     $sigla_estado_civil_responsavel = estadocivil($estadocivilresponsavel_compl_cont);
     
    // log de dados dos alunos
    $log_dados = array(
     'NOME'=> $nome,
     'CPF' => $cpf ,
     'data_nascimento_aluno' => $data_nascimento_aluno ,
     'naturalidade_aluno' => $naturalidade_aluno ,
     'estado_natal_aluno' =>$estado_natal_aluno,
     'cep' => $cep ,
     'numero_casa'=> $numero_casa,
     'endereco_casa' => $endereco_casa ,
     'bairro' => $bairro,
     'email' => $email ,
     'telefone' => $telefone_aluno,
     'email_resp' => $email_resp ,
     'sexo' => $sexo ,
     'cidade' => $cidade,
     'uf' => $uf,
     'ano escolar' => $COD_ANO_ESCOLAR,
     'tipo do responsavel'=>$tiporespfin ,
     'nome do responsavel'=>$nomerespfin,
     'cpf do responsavel' =>$cpfrespfin,
     'telefone do responsavel' =>$fonerespfin,
     'dt de nascimento resp' => $datanascrespfin,
     'estado civil resp' => $sigla_estado_civil_responsavel,
     'naturalidade do resp financeiro' => $naturalidaderespfin,
     'cidade natal do resp financeiro' => Cidade($naturalidaderespfin),
     'estado natal do resp financeiro' => Estado($naturalidaderespfin),
     'RG do aluno' => $rg_compl_cont,
     'Tipo do responsavel acadêmico' => $tiporespacad,
     'nome resp academico' => $nomerespacad,
     'naturalidade do resp academico' => $naturalidaderespacad,   //$naturalidadedoresponsavel_compl_cont,
     'cidade natal do resp academico' => Cidade($naturalidaderespacad),
     'estado natal do resp academico'=> Estado($naturalidaderespacad),
     'email resp academico' => $emailrespacad
     
      );
     
      $req_dump = print_r($log_dados, true);
      $fpdados = file_put_contents( 'dados.json', $req_dump);

    
     // valida se aluno existe na tabela de pessoa ou aluno
    
    $validaaluno = array(validaaluno( $nome, $data_nascimento_aluno, $estado_natal_aluno, $naturalidade_aluno, $cpf));
    
    // inserindo o aluno no Totvs
    
    $codigo_do_aluno = $validaaluno[0]["Resultado"]['CODPESSOA'];
    $ra_do_aluno     = $validaaluno[0]["Resultado"]['RA'];
    
    // se a pessoa do aluno já esta cadastrado no sistema será atualizado os dados dos aluno
    // verifica os campos: bairro e telefone(esta sendo informado responsavel)
    if($codigo_do_aluno !== '0' ){
    
      $codigo_do_aluno= pessoa($codigo_do_aluno, $nome, $data_nascimento_aluno, $cpf, $estado_natal_aluno, $naturalidade_aluno, 
               $email, $sexo, 'S', $cep, $endereco_casa, $numero_casa, '', $bairro,
               $uf, $cidade, $telefone_aluno, $login_integracao, $data_atual, $password,$urltotvs);
    }

   // se a pessoa do aluno não estiver cadastrada no sistema será inserido ou atualizado os dados dos aluno   
   // Insere o registro do Aluno no cadastro de Aluno quando não existe a pessoa e atualizar os dados dos aluno
   if ($ra_do_aluno == '0') { 
    if ($codigo_do_aluno == '0') {
        $ra = criar_aluno($ra_do_aluno, $codigo_do_aluno, $nome, $email, $cpf, $data_nascimento_aluno,
                              $estado_natal_aluno, $naturalidade_aluno, $cep , $sexo, $endereco_casa, 
                              $numero_casa, $endereco_casa, $bairro, $uf, $cidade, $telefone_aluno,
                              $COD_ANO_ESCOLAR, $login_integracao, $data_atual, $password,$urltotvs);
    }
    else {
      $ra = vincula_pessoa_aluno($ra_do_aluno, $codigo_do_aluno, $nome, $COD_ANO_ESCOLAR, $login_integracao, $data_atual,$password,$urltotvs );
    }
   } else {
    $ra= $ra_do_aluno; 
   }
  
//verifica se o responsavel financeiro é o próprio o aluno

if ($tiporespfin == "Próprio Aluno" or $tiporespfin   == "Pr\u00f3prio Aluno") {

  $codcfo_repons_finan = validaclientefornecedor($cpfrespfin); //verificar depois
  
  $estadocivil ='S';
   
  $codcfo = cliente_fornecedor($codcfo_repons_finan, $nomerespfin, $cpfrespfin,
                        $endereco_casa, $numero_casa, $bairro, $cidade, $uf, $cep, $fonerespfin, 
                        $emailrespfin, $login_integracao, $data_atual, $data_nascimento_aluno, $estadocivil, $password,$urltotvs );

}
//verifica se o responsavel financeiro é o pai ou mãe
if($tiporespfin ==  "Pai" or $tiporespfin ==  "Mãe" or $tiporespfin ==  "M\u00e3e")
{

  $codpessoa_pais = validapaimae(preg_replace('#[^0-9]#', '', $cpfrespfin));

  $cpf_tratado = preg_replace('#[^0-9]#', '', $cpfrespfin);

  if ($tiporespfin == "Pai") {
    $sexoresp = "M";
    $tiporelac = "P";
  } elseif ($tiporespfin == "Mãe" or $tiporespfin == "M\u00e3e") {
    $sexoresp = "F";
    $tiporelac = "M";
  }

  $sigla_estado_civil_responsavel = estadocivil($estadocivilrespfin);
  
  $codpessoa_resp = pessoa($codpessoa_pais, $nomerespfin, $datanascrespfin, 
                           $cpf_tratado, Estado($naturalidaderespfin), Cidade($naturalidaderespfin), $emailrespfin, $sexoresp, $sigla_estado_civil_responsavel,
                           $cep, $endereco_casa, $numero_casa, $endereco_casa, $bairro, $uf, $cidade, 
                           $fonerespfin, $login_integracao, $data_atual, $password,$urltotvs);
 
//   inclui ou atualizar  pai ou mãe no cadastrado na tabela de cliente/fornecedor e retornar o codcfo
  $codcfo_repons_finan = validaclientefornecedor($cpfrespfin);
            
  $codcfo = cliente_fornecedor($codcfo_repons_finan, $nomerespfin, $cpfrespfin,
                              $endereco_casa, $numero_casa, $bairro, $cidade, $uf, $cep, $fonerespfin, 
                              $emailrespfin, $login_integracao, $data_atual, $datanascrespfin, $sigla_estado_civil_responsavel, $password,$urltotvs );

  // vincula pai ou mae ao aluno

  $validaaluno = array(validaaluno( $nome, $data_nascimento_aluno, $estado_natal_aluno, $naturalidade_aluno, $cpf));
  $codigo_do_aluno = $validaaluno[0]["Resultado"]['CODPESSOA'];
 
  // vincula pai ou mae ao aluno no cadastros de filiação
  $vinculo_filiacao = vinculo_filiacao($codigo_do_aluno, $codpessoa_resp); 

  if( $vinculo_filiacao == '0'){
    vinculapaimae($codpessoa_resp, $codigo_do_aluno, $tiporelac, $login_integracao, $data_atual, $password,$urltotvs);
  } 

}

//verifica se o responsavel financeiro é outra pessoa

if ($tiporespfin == "Outros") {

  $codcfo_repons_finan = validaclientefornecedor($cpfrespfin); //verificar depois
  
  $sigla_estado_civil_responsavel = estadocivil($estadocivilrespfin);
   
  $codcfo = cliente_fornecedor($codcfo_repons_finan, $nomerespfin, $cpfrespfin,
                        $endereco_casa, $numero_casa, $bairro, $cidade, $uf, $cep, $fonerespfin, 
                        $emailrespfin, $login_integracao, $data_atual, $datanascrespfin, $sigla_estado_civil_responsavel, $password, $urltotvs);

}

//retorna o código da pessoa do responsavel acadêmico
$cod_resp_academico = '0';

//vincula o aluno responsável acadêmico dele mesmo
if ($tiporespacad == 'Próprio Aluno' or $tiporespacad == "Pr\u00f3prio Aluno" ) {
    
  $validaaluno = array(validaaluno( $nome, $data_nascimento_aluno, $estado_natal_aluno, $naturalidade_aluno, $cpf));
    
  // inserindo o aluno no Totvs
  
  $codigo_do_aluno = $validaaluno[0]["Resultado"]['CODPESSOA'];
  
  $cod_resp_academico = $codigo_do_aluno ;
  
} 
// vincula a pessoa com resp acad. quando  é igual o responsável financeiro e ele é pai ou mae
if  ($tiporespacad == 'Responsável Financeiro (o mesmo)' and ($tiporespfin ==  "Pai" or $tiporespfin ==  "Mãe" or $tiporespfin ==  "M\u00e3e")) {
  $cod_resp_academico = $codpessoa_resp;    
}  
// vincula a pessoa com resp acad. quando  é igual o responsável financeiro e ele é aluno
if  ($tiporespacad == 'Responsável Financeiro (o mesmo)' and ($tiporespfin ==  "Próprio Aluno" or $tiporespfin ==  "Pr\u00f3prio Aluno" )) {
  $cod_resp_academico = $codpessoa_resp;
}
//vincula o pai ou mãe como responsável acadêmico do aluno
if ($tiporespacad == 'Pai' or $tiporespacad == 'Mãe' or $tiporespacad  == "M\u00e3e" ) {
  
  $codpessoa_resp_acad = validapaimae(preg_replace('#[^0-9]#', '', $cpfrespacad));
  $cpf_tratado_resp_acad = preg_replace('#[^0-9]#', '', $cpfrespacad);
  $estadocivilacad = estadocivil($estadocivilrespacad);
  $nome_resp_acad = strtoupper($nomerespacad);
  if ($tiporespacad == "Pai") {
    $sexoresp = "M";
    $tiporelac = "P";
  } elseif ($tiporespacad == "Mãe" or $tiporespacad  == "M\u00e3e") {
    $sexoresp = "F";
    $tiporelac = "M";
  }

  $cod_resp_academico = pessoa($codpessoa_resp_acad , $nome_resp_acad, $datanascrespacad, $cpf_tratado_resp_acad,
                               Estado($naturalidaderespacad),  Cidade($naturalidaderespacad), $emailrespacad, $sexoresp, $estadocivilacad, $cep, 
                               $endereco_casa, $numero_casa, $endereco_casa, $bairro,
                               $uf, $cidade, $fonerespacad, $login_integracao, $data_atual, $password,$urltotvs);

  $vinculo_filiacao = vinculo_filiacao($codigo_do_aluno, $cod_resp_academico);    
  
  if ($vinculo_filiacao == '0') {
    
    vinculapaimae($cod_resp_academico, $codigo_do_aluno, $tiporelac, $login_integracao, $data_atual,$password,$urltotvs );
           
        }

} 

//vincula outra pessoa como responsável acadêmico do aluno
if ($tiporespacad == 'Outro' ){

  $codpessoa_resp_acad = validapaimae(preg_replace('#[^0-9]#', '', $cpfrespacad));
  $cpf_tratado_resp_acad = preg_replace('#[^0-9]#', '', $cpfrespacad);
  $estadocivilacad = estadocivil($estadocivilrespacad);
  $nome_resp_acad = strtoupper($nomerespacad);
  
  $cod_resp_academico = pessoa($codpessoa_mae, $nomemae, $datanascrespacad, $cpf_tratado_mae, 'DF', 
                              'Brasília', $emailrespacad, $sexoresp, $estadocivilacad, $cep, 
                               $endereco_casa, $numero_casa, $endereco_casa, $bairro,
                               $uf, $cidade, $fonerespacad, $login_integracao, $data_atual, $password, $urltotvs);
}

//define grau parentesco do responsavel financeiro
if ($tiporespfin == 'Pai') {
              $codparentcfo = "6";
              } elseif ($tiporespfin == 'Mãe') {
              $codparentcfo = "7";
                           } else{
                                  $codparentcfo = "9";
                                                    }

//define grau parentesco do responsavel acadêmico  $tiporespacad 
if ($tiporespacad  == 'Pai') {
              $codparentraca = "6";
              } elseif ($tiporespacad == 'Mãe') {
              $codparentraca = "7";
                           } else{
                                  $codparentraca = "9";
                                                    }

 if ($codcfo =='0')   {
  sleep(1);
  $codcfo = validaclientefornecedor($cpfdoresponsavel_compl_cont);
  
 }                                                

$codigos =array( 'ra'=>$ra,
                 'codigo do cli/for '=>$codcfo,
                 'resp finan. '=> $codparentcfo, 
                 'cod resp. acad' => $cod_resp_academico,
                 'resp acad.'=>$codparentraca
                                                );

//vincula aluno com pai ou mae e responsavel financeiro
$client= conectasql();

$params = array(
         'codSentenca' =>'INT.0006', 
         'codColigada'=>1, 
         'codSistema'=>'S', 
         'parameters'=>"RA=$ra;CODCFO=$codcfo;CODPARENTCFO=$codparentcfo;CODPESSOARACA=$cod_resp_academico;CODPARENTRACA=$codparentraca" 
              
          ); 
      
$resultSoap = $client->RealizarConsultaSQL($params);
$result = simplexml_load_string($resultSoap->RealizarConsultaSQLResult);     
$resultArray = json_decode(json_encode($result), true);

//------------------------Mandar retorno da integração para o rubeus---------------------------------------


$client= conectasql();

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
$req_dump = print_r($inf , true );
// $fp = file_put_contents( 'valida.json', $req_dump );  

// $REPORT ='O aluno '.$nome.' foi incluído no Totvs coms os seguintes dados: '.' RA: '.$ra.'<br>'.'COD PESSOA MAE: '.$codpessoa_paimae.'<br>'.'COD_PESSOA_ALUNO: '.$codigo_do_aluno.'<br>'.'CODCFO: '.$codcfo.'<br>';
$REPORT = print_r($inf , true ); 

$params = [
        'tipo' => $CANAL_RUBEUS,
        //'descricao' => $RA,
        'descricao' => $REPORT,
        'pessoa'               => [
            'id' => $id_contato,
        ],
        'origem' => $ORIGEM_RUBEUS,
        'token' => $TOKEN_RUBEUS
    ];

    // Cria o cURL
    
    //$curl = curl_init('https://crmthomasjeffersonhomolog.apprubeus.com.br/api/Evento/cadastro' ); //URL APONTANDO PARA AMBIENTE DE TESTE
    $curl = curl_init('https://crmthomasjefferson.apprubeus.com.br/api/Evento/cadastro' ); //URL APONTANDO PARA AMBIENTE DE PRODUÇÃO
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // Envia a requisição e salva a resposta
    $response = curl_exec($curl);
    $err = curl_error($curl);

    $req_dump = print_r($response, true );
    $fp = file_put_contents( 'console.json', $req_dump, FILE_APPEND);


    curl_close($curl);
    
    
    if ($err) {

        $respostas['erro'] = $err;
    
  }

}
  
    ?>

    