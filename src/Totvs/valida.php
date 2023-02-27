<?php
require_once"Cliente.php";
$cgccfo = "42850142034";
$cliente = new Cliente;
$codcfo_repons_finan = $cliente->ValidaCliente($cgccfo);

var_dump($codcfo_repons_finan);

?>