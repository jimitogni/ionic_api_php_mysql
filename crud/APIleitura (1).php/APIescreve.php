<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Origin, Content-Type');

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$codigo = $_POST["codigo"];
$nota = $_POST["nota"];
$obs = $_POST["obs"];

$servidor = "localhost";
$usuario = "jimi";
$senha = "341322";
$db = "feira";

$conecta = mysqli_connect($servidor, $usuario, $senha, $db);

if ($conecta->connect_error){
  die("conexao falhou" . $conecta->connect_error);
}

$sql = 'INSERT INTO notas (codigo, nota, obs) VALUES ("'. $codigo .'","'. $nota .'", "'. $obs .'")';
$result = mysqli_query($conecta, $sql);

if ($conecta->query($sql) === TRUE){

  $saida = "Nota ". $nota . " para o trabalho ". $codigo . " Enviada com sucesso!";
  echo json_encode($saida);

}else{
  echo json_encode("Erro". $sql . "<br>". $conecta->error);
}

$conecta->close();

?>
