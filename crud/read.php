<?php

header("Access-Control-Allow-Origin: *");

$servidor = "localhost";
$usuario = "alunos";
$senha = "alunos";
$db = "feira";

$conecta = mysqli_connect($servidor, $usuario, $senha, $db);

if ($conecta->connect_error){
  die("conexao falhou" . $conecta->connect_error);
}

$sql = "SELECT * FROM trabalho";
$result = mysqli_query($conecta, $sql);

if (mysqli_num_rows($result) > 0){

  $saida = array();
  $saida = $result->fetch_all(MYSQLI_ASSOC);
  echo json_encode($saida);

}else{
  echo json_encode("0 resultados");
}

$conecta->close();

?>
