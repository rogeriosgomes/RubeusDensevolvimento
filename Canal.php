
<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rubeus X Totvs</title>
    <script type="text/javascript" language="JavaScript">
        function Fechar()
        {
        window.close();
        }
    </script>
    <style>
        header{
            text-align: center;
            background-color: rgb(19,151,150)
            

        }
        header h1{
            color: white;
            font-family: 'Poppins', sans-serif ;
            font-size: 30px;
            padding: 15px;
           
        }
        body{
            background-color:#f0f0f0;
            margin: 10%;
            margin-top: 0;
        }
        .contato{
            width: 40%;
        }
    </style>



<body>

    <header>
        <h1>
            Integrar contato do Rubeus no Totvs Educacional
        </h1>
    </header>

   <section class="contato">
    <form action="Canal.php" method="POST">
      <fieldset>
        <legend>Dados do Contato</legend>

        <label>Informe o Id do Contato:</label>
        <input type="text" required name="idContato"> <br><br>

        <input type="submit" value="Enviar">
        
        <input type="button" value="Fechar" name="fechar" onClick="Fechar()">
      

      </fieldset>
     
    </form>
   </section>
   <section>
     <?php

        require_once "src/Rubeus/ConnectRubeus.php";
        require_once "src/Rubeus/Evento.php";
        require_once "src/Rubeus/Contato.php";
        require_once "src/Rubeus/Mensagem.php";
        require_once "Connection/Connection.php";
        
        
        $idContato = $_POST["idContato"];

      
        $contato = new \App\Rubeus\Contato;
        $valida = $contato->ValidaIdContato($idContato);

      
    
        
        if($valida == 0)
        {   
       
            $retorno = "O contato informado não existe Rubeus.";
        }
        else if($valida == 1)
        {
            $evento = new \App\Rubeus\Mensagem;
            $evento->EnvioEventoContato($idContato, 1687, "Realizando o cadastro do Aluno no Totvs");
            $retorno = "O contato está sendo cadastrado no Totvs, por favor verificar no hisotrico de eventos do contato.";
        }
        else
        {
            $retorno = '';
        }

     ?>

    <br><br>
    <fieldset>
        <legend>Resultado: </legend>
        <br>
         <?php
          echo $retorno;
         ?>
        <br>
    </fieldset>



   </section> 


   
</body>
</html>

