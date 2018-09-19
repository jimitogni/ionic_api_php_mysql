<?php require_once('../Connections/conexao.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Load the required classes
require_once('../includes/tfi/TFI.php');
require_once('../includes/tso/TSO.php');
require_once('../includes/nav/NAV.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_conexao = new KT_connection($conexao, $database_conexao);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_conexao, "../");
//Grand Levels: Any
$restrict->Execute();
//End Restrict Access To Page

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

// Filter
$tfi_listarquivos6 = new TFI_TableFilter($conn_conexao, "tfi_listarquivos6");
$tfi_listarquivos6->addColumn("arquivos.arq_nome", "STRING_TYPE", "arq_nome", "%");
$tfi_listarquivos6->addColumn("arquivos.arq_data", "STRING_TYPE", "arq_data", "%");
$tfi_listarquivos6->addColumn("usuarios.user_id", "STRING_TYPE", "user_id", "%");
$tfi_listarquivos6->Execute();

// Sorter
$tso_listarquivos6 = new TSO_TableSorter("rsarquivos1", "tso_listarquivos6");
$tso_listarquivos6->addColumn("arquivos.arq_nome");
$tso_listarquivos6->addColumn("arquivos.arq_data");
$tso_listarquivos6->addColumn("usuarios.user_nome");
$tso_listarquivos6->setDefault("arquivos.arq_nome DESC");
$tso_listarquivos6->Execute();

// Navigation
$nav_listarquivos6 = new NAV_Regular("nav_listarquivos6", "rsarquivos1", "../", $_SERVER['PHP_SELF'], 10);

//NeXTenesio3 Special List Recordset
$maxRows_rsarquivos1 = $_SESSION['max_rows_nav_listarquivos6'];
$pageNum_rsarquivos1 = 0;
if (isset($_GET['pageNum_rsarquivos1'])) {
  $pageNum_rsarquivos1 = $_GET['pageNum_rsarquivos1'];
}
$startRow_rsarquivos1 = $pageNum_rsarquivos1 * $maxRows_rsarquivos1;

// Defining List Recordset variable
$NXTFilter_rsarquivos1 = "1=1";
if (isset($_SESSION['filter_tfi_listarquivos6'])) {
  $NXTFilter_rsarquivos1 = $_SESSION['filter_tfi_listarquivos6'];
}
// Defining List Recordset variable
$NXTSort_rsarquivos1 = "arquivos.arq_nome DESC";
if (isset($_SESSION['sorter_tso_listarquivos6'])) {
  $NXTSort_rsarquivos1 = $_SESSION['sorter_tso_listarquivos6'];
}
mysql_select_db($database_conexao, $conexao);

$query_rsarquivos1 = "SELECT arquivos.arq_nome, arquivos.arq_data, usuarios.user_nome AS user_id, arquivos.arq_id FROM arquivos LEFT JOIN usuarios ON arquivos.user_id = usuarios.user_id WHERE {$NXTFilter_rsarquivos1} ORDER BY {$NXTSort_rsarquivos1}";
$query_limit_rsarquivos1 = sprintf("%s LIMIT %d, %d", $query_rsarquivos1, $startRow_rsarquivos1, $maxRows_rsarquivos1);
$rsarquivos1 = mysql_query($query_limit_rsarquivos1, $conexao) or die(mysql_error());
$row_rsarquivos1 = mysql_fetch_assoc($rsarquivos1);

if (isset($_GET['totalRows_rsarquivos1'])) {
  $totalRows_rsarquivos1 = $_GET['totalRows_rsarquivos1'];
} else {
  $all_rsarquivos1 = mysql_query($query_rsarquivos1);
  $totalRows_rsarquivos1 = mysql_num_rows($all_rsarquivos1);
}
$totalPages_rsarquivos1 = ceil($totalRows_rsarquivos1/$maxRows_rsarquivos1)-1;
//End NeXTenesio3 Special List Recordset

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

$nav_listarquivos6->checkBoundries();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);
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
<script src="../includes/nxt/scripts/list.js" type="text/javascript"></script>
<script src="../includes/nxt/scripts/list.js.php" type="text/javascript"></script>
<script type="text/javascript">
$NXT_LIST_SETTINGS = {
  duplicate_buttons: true,
  duplicate_navigation: true,
  row_effects: true,
  show_as_buttons: true,
  record_counter: true
}
</script>
<style type="text/css">
  /* Dynamic List row settings */
  .KT_col_arq_nome {width:140px; overflow:hidden;}
  .KT_col_arq_data {width:140px; overflow:hidden;}
  .KT_col_user_id {width:140px; overflow:hidden;}
</style>
</head>

<body>
<div class="KT_tng" id="listarquivos6">
  <h1> Arquivos
    <?php
  $nav_listarquivos6->Prepare();
  require("../includes/nav/NAV_Text_Statistics.inc.php");
?>
  </h1>
  <div class="KT_tnglist">
    <form action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" method="post" id="form1">
      <div class="KT_options"> <a href="<?php echo $nav_listarquivos6->getShowAllLink(); ?>"><?php echo NXT_getResource("Show"); ?>
        <?php 
  // Show IF Conditional region1
  if (@$_GET['show_all_nav_listarquivos6'] == 1) {
?>
          <?php echo $_SESSION['default_max_rows_nav_listarquivos6']; ?>
          <?php 
  // else Conditional region1
  } else { ?>
          <?php echo NXT_getResource("all"); ?>
          <?php } 
  // endif Conditional region1
?>
<?php echo NXT_getResource("records"); ?></a> &nbsp;
        &nbsp;
        <?php 
  // Show IF Conditional region2
  if (@$_SESSION['has_filter_tfi_listarquivos6'] == 1) {
?>
          <a href="<?php echo $tfi_listarquivos6->getResetFilterLink(); ?>"><?php echo NXT_getResource("Reset filter"); ?></a>
          <?php 
  // else Conditional region2
  } else { ?>
          <a href="<?php echo $tfi_listarquivos6->getShowFilterLink(); ?>"><?php echo NXT_getResource("Show filter"); ?></a>
          <?php } 
  // endif Conditional region2
?>
      </div>
      <table cellpadding="2" cellspacing="0" class="KT_tngtable">
        <thead>
          <tr class="KT_row_order">
            <th> <input type="checkbox" name="KT_selAll" id="KT_selAll"/>
            </th>
            <th id="arq_nome" class="KT_sorter KT_col_arq_nome <?php echo $tso_listarquivos6->getSortIcon('arquivos.arq_nome'); ?>"> <a href="<?php echo $tso_listarquivos6->getSortLink('arquivos.arq_nome'); ?>">Nome do Arquivo</a></th>
            <th id="arq_data" class="KT_sorter KT_col_arq_data <?php echo $tso_listarquivos6->getSortIcon('arquivos.arq_data'); ?>"> <a href="<?php echo $tso_listarquivos6->getSortLink('arquivos.arq_data'); ?>">Data</a></th>
            <th id="user_id" class="KT_sorter KT_col_user_id <?php echo $tso_listarquivos6->getSortIcon('usuarios.user_nome'); ?>"> <a href="<?php echo $tso_listarquivos6->getSortLink('usuarios.user_nome'); ?>">Nome de Usuário</a></th>
            <th>&nbsp;</th>
          </tr>
          <?php 
  // Show IF Conditional region3
  if (@$_SESSION['has_filter_tfi_listarquivos6'] == 1) {
?>
            <tr class="KT_row_filter">
              <td>&nbsp;</td>
              <td><input type="text" name="tfi_listarquivos6_arq_nome" id="tfi_listarquivos6_arq_nome" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listarquivos6_arq_nome']); ?>" size="20" maxlength="80" /></td>
              <td><input type="text" name="tfi_listarquivos6_arq_data" id="tfi_listarquivos6_arq_data" value="<?php echo KT_escapeAttribute(@$_SESSION['tfi_listarquivos6_arq_data']); ?>" size="20" maxlength="100" /></td>
              <td><select name="tfi_listarquivos6_user_id" size="1" id="tfi_listarquivos6_user_id">
                <option value="" <?php if (!(strcmp("", $row_rsarquivos1['user_id']))) {echo "selected=\"selected\"";} ?>><?php echo NXT_getResource("None"); ?></option>
              </select></td>
              <td><input type="submit" name="tfi_listarquivos6" value="<?php echo NXT_getResource("Filter"); ?>" /></td>
            </tr>
            <?php } 
  // endif Conditional region3
?>
        </thead>
        <tbody>
          <?php if ($totalRows_rsarquivos1 == 0) { // Show if recordset empty ?>
            <tr>
              <td colspan="5"><?php echo NXT_getResource("The table is empty or the filter you've selected is too restrictive."); ?></td>
            </tr>
            <?php } // Show if recordset empty ?>
          <?php if ($totalRows_rsarquivos1 > 0) { // Show if recordset not empty ?>
            <?php do { ?>
              <tr class="<?php echo @$cnt1++%2==0 ? "" : "KT_even"; ?>">
                <td><input type="checkbox" name="kt_pk_arquivos" class="id_checkbox" value="<?php echo $row_rsarquivos1['arq_id']; ?>" />
                  <input type="hidden" name="arq_id" class="id_field" value="<?php echo $row_rsarquivos1['arq_id']; ?>" /></td>
                <td><div class="KT_col_arq_nome"><?php echo KT_FormatForList($row_rsarquivos1['arq_nome'], 20); ?></div></td>
                <td><div class="KT_col_arq_data"><?php echo KT_FormatForList($row_rsarquivos1['arq_data'], 20); ?></div></td>
                <td><div class="KT_col_user_id"><?php echo KT_FormatForList($row_rsarquivos1['user_id'], 20); ?></div></td>
                <td><a class="KT_edit_link" href="../gerenciar.php?arq_id=<?php echo $row_rsarquivos1['arq_id']; ?>&amp;KT_back=1"><?php echo NXT_getResource("edit_one"); ?></a> <a class="KT_delete_link" href="#delete"><?php echo NXT_getResource("delete_one"); ?></a></td>
              </tr>
              <?php } while ($row_rsarquivos1 = mysql_fetch_assoc($rsarquivos1)); ?>
            <?php } // Show if recordset not empty ?>
        </tbody>
      </table>
      <div class="KT_bottomnav">
        <div>
          <?php
            $nav_listarquivos6->Prepare();
            require("../includes/nav/NAV_Text_Navigation.inc.php");
          ?>
        </div>
      </div>
      <div class="KT_bottombuttons">
        <div class="KT_operations"> <a class="KT_edit_op_link" href="#" onclick="nxt_list_edit_link_form(this); return false;"><?php echo NXT_getResource("edit_all"); ?></a> <a class="KT_delete_op_link" href="#" onclick="nxt_list_delete_link_form(this); return false;"><?php echo NXT_getResource("delete_all"); ?></a></div>
        <span>&nbsp;</span>
        <select name="no_new" id="no_new">
          <option value="1">1</option>
          <option value="3">3</option>
          <option value="6">6</option>
        </select>
        <a class="KT_additem_op_link" href="add_arquivo_user.php?KT_back=1" onclick="return nxt_list_additem(this)"><?php echo NXT_getResource("add new"); ?></a></div>
    </form>
  </div>
  <br class="clearfixplain" />
</div>
<p>&nbsp;</p>
<?php
	echo $tNGs->getErrorMsg();
?>
<a href="<?php echo $logoutTransaction->getLogoutLink(); ?>">Logout</a>
</body>
</html>
<?php
mysql_free_result($rsarquivos1);
?>
