<?php require_once('../Connections/conexao.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Require the MXI classes
require_once ('../includes/mxi/MXI.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_conexao = new KT_connection($conexao, $database_conexao);

// Include Multiple Static Pages
$mxiObj = new MXI_Includes("mod");
$mxiObj->IncludeStatic("Home", "home.php", "", "", "");
$mxiObj->IncludeStatic("Cadastrar Usuario", "cada_user.php", "", "", "");
$mxiObj->IncludeStatic("Listar Usuario", "lista_user.php", "", "", "");
$mxiObj->IncludeStatic("Cadastrar Arquivo", "add_arquivo_user.php", "", "", "");
// End Include Multiple Static Pages

// Make a logout transaction instance
$logoutTransaction = new tNG_logoutTransaction($conn_conexao);
$tNGs->addTransaction($logoutTransaction);
// Register triggers
$logoutTransaction->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "GET", "KT_logout_now");
$logoutTransaction->registerTrigger("END", "Trigger_Default_Redirect", 99, "../index.php");
// Add columns
// End of logout transaction instance

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $mxiObj->getTitle(); ?></title>
<style type="text/css">
<!--
body {
	background-color: #CCC;
}
-->
</style>
<meta name="keywords" content="<?php echo $mxiObj->getKeywords(); ?>" />
<meta name="description" content="<?php echo $mxiObj->getDescription(); ?>" />
<base href="<?php echo mxi_getBaseURL(); ?>" />
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script src="../includes/skins/style.js" type="text/javascript"></script>
</head>

<body>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="100" colspan="2" align="center" bgcolor="#999999"><h2>SISTEMA DE GERENCIAMENTO DE USUARIO E ARQUIVOS</h2></td>
  </tr>
  <tr>
    <td width="188" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="22" align="left"><blockquote>
          <p><a href="index.php?mod=Home">Home</a></p>
        </blockquote></td>
      </tr>
      <tr>
        <td height="22" align="left"><blockquote>
          <p><a href="index.php?mod=Cadastrar Usuario">Cadastrar Usuário</a></p>
        </blockquote></td>
      </tr>
      <tr>
        <td height="22" align="left"><blockquote>
          <p><a href="index.php?mod=Listar Usuario">Listar Usuário</a></p>
        </blockquote></td>
      </tr>
      <tr>
        <td height="22" align="left"><blockquote>
          <p><a href="index.php?mod=Cadastrar Arquivo">Cadastrar Arquivo</a></p>
        </blockquote></td>
      </tr>
      <tr>
        <td height="22" align="left"><blockquote>
          <?php
	echo $tNGs->getErrorMsg();
?>
<a href="<?php echo $logoutTransaction->getLogoutLink(); ?>">Sair do Sistema</a> </blockquote></td>
      </tr>
      <tr>
        <td height="22" align="left" bgcolor="#333333">&nbsp;</td>
      </tr>
    </table></td>
    <td width="512" align="center" valign="top" bgcolor="#FFFFFF">&nbsp;
      <?php
  $incFileName = $mxiObj->getCurrentInclude();
  if ($incFileName !== null)  {
    mxi_includes_start($incFileName);
    require(basename($incFileName)); // require the page content
    mxi_includes_end();
}
?></td>
  </tr>
  <tr>
    <td height="40" colspan="2" align="center" bgcolor="#999999">Produzido por: <a href="http://www.gahost.com.br" target="_blank">Gleidson Azevedo</a></td>
  </tr>
</table>
</body>
</html>