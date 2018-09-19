<?php
/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

/*
	Copyright (c) InterAKT Online 2000-2006. All rights reserved.
*/
// Load the tNG classes
require_once(dirname(realpath(__FILE__)) . '/../tNG.inc.php');

$reference = null;
foreach ($_GET as $key => $val) {
	if (preg_match("/^KT_download(.*)/is", $key)) {
		$reference = $key;
		$downloadID = $val;
		break;
	}
}
$ret = null;
$backUri = '#';
if ($reference !== null) {
	$dwnldObj1 = new tNG_Download("../../../", $reference);
	$ret = $dwnldObj1->Execute();
	$backUri = $dwnldObj1->backUri;
	
}
if ($ret === null) {
	exit;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Download File</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
<?php
  $output = '';

  if ($ret->details!='') {
    $output .= '<div id="KT_tngerror"><label>' . KT_getResource('ERROR_LABEL','tNG') . '</label><div>' . $ret->details . '</div></div>' . "\r\n";
  }

  if ('DEVELOPMENT' == $GLOBALS['tNG_debug_mode']) {
    if ($ret->devDetails != '') {
      $output .= '<div id="KT_tngdeverror"><label>Developer Details:</label><div>' . $ret->devDetails . '</div></div>';
    }
  }

  echo $output;
?>
<a href="<?php echo $backUri;?>">Back</a>
</body>
</html>