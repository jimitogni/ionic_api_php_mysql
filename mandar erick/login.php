<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
  }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

      exit(0);
    }

    $servidor = "localhost";
    $usuario = "jimi";
    $senha = "341322";
    $db = "feira";

    $con = mysqli_connect($servidor, $usuario, $senha, $db);

    if ($conecta->connect_error){
      die("conexao falhou" . $conecta->connect_error);
    }

    $data = file_get_contents("php://input");

    if (isset($data)) {
        $request = json_decode($data);
        $username = $request->username;
        $password = $request->password;
    }

    $username = mysqli_real_escape_string($con,$username);
    $password = mysqli_real_escape_string($con,$password);
    $username = stripslashes($username);
    $password = stripslashes($password);

    $sql = "SELECT id FROM login WHERE usuario='$username' and senha = '$password'";

    $result = mysqli_query($con,$sql);

    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);

    $active = $row[‘active’];

    $count = mysqli_num_rows($result);

    // Se encontrar usuário retorna 1, se não retorna erro
    if($count>0) {
      $response = "1";
    }else{
      $response= "erro";
    }

 echo json_encode($response);

?>
