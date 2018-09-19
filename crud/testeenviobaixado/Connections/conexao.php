<?php
// WWW.GAHOST.COM.BR
//SAC@GAHOST.COM.BR
//FONE: (86)9423-4098
// DESENVOLVIDO POR: GLEIDSON AZEVEDO
// OBS: MANTER OS CREDITOS E MANDAR UM ELEGICIO ACREDECENDO...

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conexao = "localhost";
$database_conexao = "upload";
$username_conexao = "root";
$password_conexao = "";
$conexao = mysql_pconnect($hostname_conexao, $username_conexao, $password_conexao) or trigger_error(mysql_error(),E_USER_ERROR); 
?>