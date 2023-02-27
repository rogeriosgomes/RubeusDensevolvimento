<?php
require_once "ConnectTotvs.php";

class RespAcademico
{
   //propriedades
   public $tipoRespAcademico;
   public $codpessoa;
   public $nome;
   public $dtnascimento;
   public $cpf;
   public $estadonatal;
   public $naturalidade;
   public $email;
   public $sexo;
   public $estadocivil;
   public $cep;
   public $rua;
   public $numero; 
   public $complemento;
   public $bairro;
   public $uf;
   public $cidade; 
   public $telefone; 
   public $loginIntegracao;
   public $dataAtual;

   //Metódo

   function  CriarRespAcademico($tipoRespAcademico,
                                $nome,
                                $dtnascimento,
                                $cpf,
                                $estadoNatal,
                                $naturalidade,
                                $email,
                                $sexo,
                                $estadocivil,
                                $cep,
                                $rua,
                                $numero, 
                                $complemento,
                                $bairro,
                                $uf,
                                $cidade, 
                                $telefone, 
                                $loginIntegracao,
                                $dataAtual)
   {
      
    //retorna o código da pessoa do responsavel acadêmico
    $codRespAcad = '0';

    $codRespAcad = Pessoa::ValidaSePessoaExiste($nome,
                                                $dtnascimento,
                                                $estadoNatal,
                                                $naturalidade,
                                                $cpf);

    if ($tipoRespAcademico == "Pai")
    {
        $sexoresp = "M";
    } elseif ($tipoRespAcademico == "Mãe" or $tipoRespAcademico== "M\u00e3e") 
    {
        $sexoresp = "F";
    } elseif ($tipoRespAcademico == "Próprio Aluno" or $tipoRespAcademico== "Pr\u00f3prio Aluno" ) 
    {
        $sexoresp = $sexo;
    }elseif ($tipoRespAcademico == "Outro" )
    {
        $sexoresp = "M";
    }

    
    $codRespAcad= Pessoa::IncluiPessoa($codRespAcad,
                                      $nome,
                                      $dtnascimento,
                                      $cpf,
                                      $estadoNatal,
                                      $naturalidade,
                                      $email,
                                      $sexoresp , 
                                      'S', 
                                      $cep, 
                                      $rua, 
                                      $numero,
                                      '',
                                      $bairro,
                                      $uf,
                                      $cidade,
                                      $telefone, 
                                      $loginIntegracao,
                                      $dataAtual);

     return $codRespAcad;


   }
}


?>