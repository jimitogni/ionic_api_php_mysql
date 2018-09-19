<?php require_once('../Connections/conexao.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_conexao = new KT_connection($conexao, $database_conexao);

//start Trigger_CheckPasswords trigger
//remove this line if you want to edit the code by hand
function Trigger_CheckPasswords(&$tNG) {
  $myThrowError = new tNG_ThrowError($tNG);
  $myThrowError->setErrorMsg("Passwords do not match.");
  $myThrowError->setField("user_senha");
  $myThrowError->setFieldErrorMsg("The two passwords do not match.");
  return $myThrowError->Execute();
}
//end Trigger_CheckPasswords trigger

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("user_email", true, "text", "", "", "", "");
$formValidation->addField("user_senha", true, "text", "", "", "", "");
$tNGs->prepareValidation($formValidation);
// End trigger

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_conexao, $conexao);
$query_Rsli = "SELECT * FROM nivel";
$Rsli = mysql_query($query_Rsli, $conexao) or die(mysql_error());
$row_Rsli = mysql_fetch_assoc($Rsli);
$totalRows_Rsli = mysql_num_rows($Rsli);

// Make an insert transaction instance
$ins_usuarios = new tNG_insert($conn_conexao);
$tNGs->addTransaction($ins_usuarios);
// Register triggers
$ins_usuarios->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_usuarios->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_usuarios->registerTrigger("END", "Trigger_Default_Redirect", 99, "lista_user.php");
$ins_usuarios->registerConditionalTrigger("{POST.user_senha} != {POST.re_user_senha}", "BEFORE", "Trigger_CheckPasswords", 50);
// Add columns
$ins_usuarios->setTable("usuarios");
$ins_usuarios->addColumn("user_nome", "STRING_TYPE", "POST", "user_nome");
$ins_usuarios->addColumn("user_email", "STRING_TYPE", "POST", "user_email");
$ins_usuarios->addColumn("user_senha", "STRING_TYPE", "POST", "user_senha");
$ins_usuarios->addColumn("lev_id", "NUMERIC_TYPE", "POST", "lev_id", "{Rsli.lev_id}");
$ins_usuarios->setPrimaryKey("user_id", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsusuarios = $tNGs->getRecordset("usuarios");
$row_rsusuarios = mysql_fetch_assoc($rsusuarios);
$totalRows_rsusuarios = mysql_num_rows($rsusuarios);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sem título</title>
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script src="../includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>
</head>

<body>
<?php
	echo $tNGs->getErrorMsg();
?>
<form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
  <table cellpadding="2" cellspacing="0" class="KT_tngtable">
    <tr>
      <td class="KT_th"><label for="user_nome">Nome:</label></td>
      <td><input type="text" name="user_nome" id="user_nome" value="<?php echo KT_escapeAttribute($row_rsusuarios['user_nome']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("user_nome");?> <?php echo $tNGs->displayFieldError("usuarios", "user_nome"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="user_email">E-mail:</label></td>
      <td><input type="text" name="user_email" id="user_email" value="<?php echo KT_escapeAttribute($row_rsusuarios['user_email']); ?>" size="32" />
        <?php echo $tNGs->displayFieldHint("user_email");?> <?php echo $tNGs->displayFieldError("usuarios", "user_email"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="user_senha">Senha:</label></td>
      <td><input type="password" name="user_senha" id="user_senha" value="" size="32" />
        <?php echo $tNGs->displayFieldHint("user_senha");?> <?php echo $tNGs->displayFieldError("usuarios", "user_senha"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="re_user_senha">Rep. Senha:</label></td>
      <td><input type="password" name="re_user_senha" id="re_user_senha" value="" size="32" /></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="lev_id">Nivel:</label></td>
      <td><select name="lev_id" id="lev_id">
        <?php 
do {  
?>
        <option value="<?php echo $row_Rsli['lev_id']?>"<?php if (!(strcmp($row_Rsli['lev_id'], $row_rsusuarios['lev_id']))) {echo "SELECTED";} ?>><?php echo $row_Rsli['lev_nome']?></option>
        <?php
} while ($row_Rsli = mysql_fetch_assoc($Rsli));
  $rows = mysql_num_rows($Rsli);
  if($rows > 0) {
      mysql_data_seek($Rsli, 0);
	  $row_Rsli = mysql_fetch_assoc($Rsli);
  }
?>
      </select>
        <?php echo $tNGs->displayFieldError("usuarios", "lev_id"); ?></td>
    </tr>
    <tr class="KT_buttons">
      <td colspan="2"><input type="submit" name="KT_Insert1" id="KT_Insert1" value="Inserir registro" /></td>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($Rsli);
?>
