<?php
//pego os dados enviados pelo formulario
$titulo = $_POST["nome"];
$sala = $_POST["sala"];
$email = $_POST["email"];


$servidor = "localhost";
$usuario = "jimi";
$senha = "341322";
$db = "feira";

$conecta = mysqli_connect($servidor, $usuario, $senha, $db);

echo $titulo;
echo $sala;
echo $email;

if ($conecta->connect_error){
  die("conexao falhou" . $conecta->connect_error);
}

$sql = "INSERT INTO trabalho (titulo, integrantes, obs)
VALUES ('$titulo', '$sala', '$email')";

if (mysqli_query($conecta, $sql)) {
    echo "Gravou no BD";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conecta);
}

mysqli_close($conecta);

$uploaddir = '/var/www/html/crud/pdfs/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "Arquivo válido e enviado com sucesso.\n";
} else {
    echo "Possível ataque de upload de arquivo!\n";
}

echo 'Aqui está mais informações de debug:';
print_r($_FILES);

print "</pre>";

?>
