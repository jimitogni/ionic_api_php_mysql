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

include_once(dirname(realpath(__FILE__)) . '/../../common/lib/resources/KT_Resources.php');
$d = 'tNG';

KT_session_start();

$uniqueId = @$_GET['id'];
if (!isset($_SESSION['tNG']['download'][$uniqueId])) {
	die(KT_getResource('ERR_DOWNLOAD_FILE', $d));
}
if (!is_array($_SESSION['tNG']['download'][$uniqueId])) {
	die(KT_getResource('ERR_DOWNLOAD_FILE', $d));
}
if (!isset($_SESSION['tNG']['download'][$uniqueId]['realPath']) || !isset($_SESSION['tNG']['download'][$uniqueId]['fileName'])) {
	die(KT_getResource('ERR_DOWNLOAD_FILE', $d));
}
$realPath = $_SESSION['tNG']['download'][$uniqueId]['realPath'];
$fileName = $_SESSION['tNG']['download'][$uniqueId]['fileName'];
if (md5($realPath) != $uniqueId) {
	die(KT_getResource('ERR_DOWNLOAD_FILE_WRONG_HASH', $d));
}

if (!@fopen($realPath, "rb")) {
	die(KT_getResource('ERR_DOWNLOAD_FILE_NO_READ', $d, array($realPath)));
}
//define('MAX_READ',131072);
$mime_type = (function_exists('mime_content_type'))? mime_content_type($realPath): 'application/octet-stream';
header('Content-type: '.$mime_type);
header('Cache-control: private');
header('Content-Length: ' . filesize($realPath));
header('Content-disposition: attachment; filename="' . $fileName . '";');
$fd = fopen ($realPath, "rb"); 
if (!$fd) {
	header('Status-code: 404');
	die(KT_getResource('ERR_DOWNLOAD_FILE_NO_READ', $d, array($realPath)));
}
do { 
   echo fread($fd, 8192); 
   flush();
	 usleep(10000);
} while(!feof($fd) && connection_status()==0); 
fclose($fd);
exit;
?>