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
foreach ($_GET as $reference => $uploadID) {
	if (preg_match("/^KT_upload(.*)/is", $reference)) {
		break;	
	}	
}

if (!isset($uploadID)) {
	return null;
}
if (!isset($_SESSION['tng_upload'][$reference])) {
	return null;
}
if (!isset($_SESSION['tng_upload'][$reference]['files'][$uploadID])) {
	return null;
}
$uploadHash = $_SESSION['tng_upload'][$reference]['properties'];
$uploadHashFile = $_SESSION['tng_upload'][$reference]['files'][$uploadID];

// recover connection
require_once(dirname(__FILE__) . '/../../../Connections/' . $uploadHash['connName'] . '.php');
if (is_resource($$uploadHash['connName'])) {
    $database = 'database_'.$uploadHash['connName'];
    $KT_conn_mupload = new KT_connection($$uploadHash['connName'], $$database);
    $isMysql = 1;
} else {
	$KT_conn_mupload = $$uploadHash['connName'];	
}

//print_r($uploadHash);
$folder = $uploadHashFile['folder'];
if (substr($folder, -1, 1) != '/' || substr($folder, -1, 1) != '\\') {
	$folder .= '/';
}
if ($uploadHash['relPath'] != '') {
	if (substr($folder, 0, strlen($uploadHash['relPath']))==$uploadHash['relPath']) {
		$folder = substr($folder, strlen($uploadHash['relPath']));
	}	
}
$folder = '../../../'. $folder;

// create the folder if not exists
if (!file_exists($folder)) {
	$folderObj = new KT_folder();
	$folderObj->createFolder($folder);
	if ($folderObj->hasError()) {
		$err = $folderObj->getError();
		echo '<html><head><title>Multilpe Upload</title><link href="../../../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" /></head><body><div id="KT_tngerror"><label>'.KT_getResource('ERROR_LABEL','tNG').'</label><div>';
		echo ((isset($GLOBALS['tNG_debug_mode']) && $GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') ? $err[1] : $err[0]);
		echo '</div></div></body></html>';
		exit;
	}
} 

// delete selected file
if (isset($_POST['delete']) && isset($_SESSION['tng_upload_delete'][$_POST['delete']])) {
	$file = new KT_file();
	$file->deleteFile($folder . $_SESSION['tng_upload_delete'][$_POST['delete']]);
	if ($file->hasError()) {
		$err = $file->getError();		
	} else {
		// delete thumbnails
		tNG_deleteThumbnails($folder.'/thumbnails/', $_SESSION['tng_upload_delete'][$_POST['delete']], '');		
	}
	$_SESSION['tng_upload_delete'] = array();
	KT_redir(KT_getFullUri());
}
$_SESSION['tng_upload_delete'] = array();

// upload the files;
if (isset($_FILES['Filedata'])) {
	$folderObj = new KT_folder();
	$entries = $folderObj->readFolder($folder);
	if ($uploadHash['maxFiles']==0 || (isset($uploadHash['maxFiles']) && isset($entries['files']) && count($entries['files']) < $uploadHash['maxFiles']) || !isset($uploadHash['maxFiles']) || !isset($entries['files'])) {
		$fileUpload = new KT_fileUpload();
		$fileUpload->setFileInfo('Filedata');
		$fileUpload->setFolder($folder);
		$fileUpload->setRequired(true);
		$fileUpload->setAllowedExtensions($uploadHash['allowedExtensions']);
		$fileUpload->setAutoRename(true);
		$fileUpload->setMaxSize($uploadHash['maxSize']);
		$fileName = $fileUpload->uploadFile($_FILES['Filedata']['name'], '');
		if (!isset($_GET['isFlash']) && $fileUpload->hasError()) {
			$err = $fileUpload->getError();		
		}
		if (isset($_GET['isFlash']) && $fileUpload->hasError()) {
			$err = $fileUpload->getError();
			!isset($_SESSION['tng_upload']['errorForFlash']) ? $_SESSION['tng_upload']['errorForFlash'] = '' : '';
			if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
				$_SESSION['tng_upload']['errorForFlash'] .= $err[1] .'<br/>';
			} else {
				$_SESSION['tng_upload']['errorForFlash'] .= $err[0] .'<br/>';
			}			
		}	
		// make the resize
		if (isset($uploadHash['isImage']) && $uploadHash['isImage'] == true  && !$fileUpload->hasError() && isset($uploadHash['resize']) && count($uploadHash['resize']) > 0) {
			$image = new KT_image();
			$image->setPreferedLib($GLOBALS['tNG_prefered_image_lib']);
			$image->addCommand($GLOBALS['tNG_prefered_imagemagick_path']);
			$image->resize($folder.$fileName, $folder, $fileName, $uploadHash['resize']['width'], $uploadHash['resize']['height'], $uploadHash['resize']['keepProportion']);
			if ($image->hasError()) {
				$err = $image->getError();	
				if (isset($_GET['isFlash'])) {
					!isset($_SESSION['tng_upload']['errorForFlash']) ? $_SESSION['tng_upload']['errorForFlash'] = '' : '';
					if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
						$_SESSION['tng_upload']['errorForFlash'] .= $err[1] .'<br/>';
					} else {
						$_SESSION['tng_upload']['errorForFlash'] .= $err[0] .'<br/>';
					}
				}
				// delete picture
				$file = new KT_File();
				$file->deleteFile($folder.$fileName);
				if ($file->hasError()) {
					$arr = $file->getError();
					$err[0] .= '<br/>' . $arr[0];
					$err[1] .= '<br/>' . $arr[1];					
					if (isset($_GET['isFlash'])) {
					!isset($_SESSION['tng_upload']['errorForFlash']) ? $_SESSION['tng_upload']['errorForFlash'] = '' : '';
					if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
						$_SESSION['tng_upload']['errorForFlash'] .= $err[1] .'<br/>';
					} else {
						$_SESSION['tng_upload']['errorForFlash'] .= $err[0] .'<br/>';
					}
				}
				}		
			}
		} 
	} else {
		if (isset($_GET['isFlash'])) {
			!isset($_SESSION['tng_upload']['errorForFlash']) ? $_SESSION['tng_upload']['errorForFlash'] = '' : '';
			$_SESSION['tng_upload']['errorForFlash'] .= KT_getResource('MAX_FILES_NO_REACHED', 'tNG', array($uploadHash['maxFiles'])) . '<br/>';
		}	
	}	
}
// end page execution if we are calling from flash;
if (isset($_GET['isFlash'])) {
	echo " ";
	exit;
}
	
//folder recordset
$listFolder =  new tNG_FileListRecordset("../../../", $KT_conn_mupload);
$listFolder->setBaseFolder($folder);
$listFolder->setFolder("");
$listFolder->setAllowedExtensions(implode(',', $uploadHash['allowedExtensions']));
$listFolder->setOrder('name', 'ASC');
//create the fake recordset
if (isset($isMysql)) {
	$tmp = $listFolder->Execute();
	if (!is_resource($tmp)) {
		die('Internal error');
	}
	$rsFiles = new KT_Recordset($tmp);
} else {
	$rsFiles = $listFolder->Execute();
	if (!is_object($rsFiles)) {
		die('Internal error');
	}
}
$totalRows_rsFiles = $rsFiles->RecordCount();
// end folder recordset

if ((isset($uploadHash['thumbnail']['width']) && isset($uploadHash['thumbnail']['height'])) || (isset($uploadHash['thumbnail']['popupWidth']) && isset($uploadHash['thumbnail']['popupHeight']))) {
	$objDynamicThumb1 = new tNG_DynamicThumbnail("../../../", "KT_thumbnail_" . $reference);
	$objDynamicThumb1->setFolder($folder);
	$objDynamicThumb1->setRenameRule("{rsFiles.name}");
	if (isset($uploadHash['thumbnail']['width']) && isset($uploadHash['thumbnail']['height'])) {
		$objDynamicThumb1->setResize($uploadHash['thumbnail']['width'], $uploadHash['thumbnail']['height'], true);
	}
	if (isset($uploadHash['thumbnail']['popupWidth']) && isset($uploadHash['thumbnail']['popupHeight'])) {	
		$objDynamicThumb1->setPopupSize($uploadHash['thumbnail']['popupWidth'], $uploadHash['thumbnail']['popupHeight'], true);
		$objDynamicThumb1->setPopupNavigation(false);
	}
}
?>