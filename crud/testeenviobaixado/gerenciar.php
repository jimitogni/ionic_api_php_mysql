<?php require_once('Connections/conexao.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');

// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_conexao = new KT_connection($conexao, $database_conexao);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_conexao, "");
//Grand Levels: Any
$restrict->Execute();
//End Restrict Access To Page

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

//start Trigger_FileUpload trigger
//remove this line if you want to edit the code by hand 
function Trigger_FileUpload(&$tNG) {
  $uploadObj = new tNG_FileUpload($tNG);
  $uploadObj->setFormFieldName("arq_nome");
  $uploadObj->setDbFieldName("arq_nome");
  $uploadObj->setFolder("arquivos/");
  $uploadObj->setMaxSize(5000);
  $uploadObj->setAllowedExtensions("pdf, txt, doc, rar, zip, jpg, png");
  $uploadObj->setRename("auto");
  return $uploadObj->Execute();
}
//end Trigger_FileUpload trigger

// Make an insert transaction instance
$ins_arquivos = new tNG_insert($conn_conexao);
$tNGs->addTransaction($ins_arquivos);
// Register triggers
$ins_arquivos->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_arquivos->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_arquivos->registerTrigger("END", "Trigger_Default_Redirect", 99, "arquivos.php");
$ins_arquivos->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_arquivos->setTable("arquivos");
$ins_arquivos->addColumn("arq_nome", "FILE_TYPE", "FILES", "arq_nome");
$ins_arquivos->addColumn("arq_data", "STRING_TYPE", "POST", "arq_data");
$ins_arquivos->addColumn("user_id", "NUMERIC_TYPE", "POST", "user_id", "{SESSION.kt_login_id}");
$ins_arquivos->setPrimaryKey("arq_id", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsarquivos = $tNGs->getRecordset("arquivos");
$row_rsarquivos = mysql_fetch_assoc($rsarquivos);
$totalRows_rsarquivos = mysql_num_rows($rsarquivos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:wdg="http://ns.adobe.com/addt">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sistema de Upload - Enviar Arquivo</title>
<link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="includes/common/js/base.js" type="text/javascript"></script>
<script src="includes/common/js/utility.js" type="text/javascript"></script>
<script src="includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>
<script type="text/javascript" src="includes/common/js/sigslot_core.js"></script>
<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js"></script>
<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js.php"></script>
<script type="text/javascript" src="includes/wdg/classes/Calendar.js"></script>
<script type="text/javascript" src="includes/wdg/classes/SmartDate.js"></script>
<script type="text/javascript" src="includes/wdg/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="includes/wdg/calendar/calendar-setup_stripped.js"></script>
<script src="includes/resources/calendar.js"></script>
</head>

<body>
<?php
	echo $tNGs->getErrorMsg();
?>
<form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data">
  <table cellpadding="2" cellspacing="0" class="KT_tngtable">
    <tr>
      <td class="KT_th"><label for="arq_nome">Arquivos:</label></td>
      <td><input type="file" name="arq_nome" id="arq_nome" size="32" />
        <?php echo $tNGs->displayFieldError("arquivos", "arq_nome"); ?></td>
    </tr>
    <tr>
      <td class="KT_th"><label for="arq_data">Data:</label></td>
      <td><input name="arq_data" id="arq_data" value="<?php echo KT_escapeAttribute($row_rsarquivos['arq_data']); ?>" size="32" wdg:mondayfirst="true" wdg:subtype="Calendar" wdg:mask="<?php echo $KT_screen_date_format; ?>" wdg:type="widget" wdg:singleclick="true" wdg:restricttomask="no" />
        <?php echo $tNGs->displayFieldHint("arq_data");?> <?php echo $tNGs->displayFieldError("arquivos", "arq_data"); ?></td>
    </tr>
    <tr class="KT_buttons">
      <td colspan="2"><input type="submit" name="KT_Insert1" id="KT_Insert1" value="Enviar Arquivo" /></td>
    </tr>
  </table>
  <input type="hidden" name="user_id" id="user_id" value="<?php echo KT_escapeAttribute($row_rsarquivos['user_id']); ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>