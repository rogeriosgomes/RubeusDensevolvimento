<?php
 
 namespace App\Rubeus;

 class ConnectRubeus
 { 

    //Propiedades

    private $ambiente = '2'; // 1 - producao -- 2 - homologacao
   
    // Dados para receber evento do Rubeus

    public $canalEvento = 265;
    private $tokenEvento = 'ec3d8e2077780c54b4d4a3612d8367b1';
    private $origemEvento = '275';
    private $urlEventoHom = 'https://crmthomasjeffersonhomolog.apprubeus.com.br/api/Evento/cadastro'; //homologação
    private $urlEventoProd = 'https://crmthomasjefferson.apprubeus.com.br/api/Evento/cadastro' ; //produção

    // //Dados para receber o contato
    private $urlHom = 'https://crmthomasjeffersonhomolog.apprubeus.com.br/api/Contato/dadosPessoa/'; //homologação
    private $urlProd = 'https://crmthomasjefferson.apprubeus.com.br/api/Contato/dadosPessoa/' ; //produção
    private $tokenContato = "4af096b1b76386bda5c54fccfe6c806f";
    private  $origemContato = 9;


    public function GetCanalEvento()
    {
        return $this->canalEvento;
    
    }
    
    public function GetTokenEvento()
    {
        return $this->tokenEvento;
    }

    public function GetOrigemEvento()
    {
        return $this->origemEvento;
    }

    public function GetUrl()
    {
        if( $this->ambiente == '1')
        {
            $url = $this->urlProd;
        } else {
            $url = $this->urlHom;
        }

        return $url;
    }

    public function GetUrlEvento()
    {
        if( $this->ambiente == '1')
        {
            $url = $this->urlEventoProd ; 
            ;
        } else {
            $url = $this->urlEventoHom;
        }

        return $url;
    }

    public function GetTokenContato()
    {
        return $this->tokenContato;
    }

    public function GetOrigemContato()
    {
        return $this->origemContato;
    }


 }
 
 
?>