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

// Load the tNG classes
require_once(dirname(realpath(__FILE__)) . '/../tNG.inc.php');

if (!isset($_GET['id']) || !isset($_SESSION['tng_popup'][$_GET['id']])) {
	die('Internal Error. Session expired.');
}
if (!isset($_GET['n']) || !isset($_SESSION['tng_popup'][$_GET['id']]['files'][$_GET['n']])) {
	die('Internal Error. Session expired.');
}

$hash = $_SESSION['tng_popup'][$_GET['id']]['properties'];
$hashFile = $_SESSION['tng_popup'][$_GET['id']]['files'][$_GET['n']];

// check is exists the file;
if (!is_file('../../../' . $hashFile['fullfilename']) || !file_exists('../../../' . $hashFile['fullfilename'])) {
	if ($GLOBALS['tNG_debug_mode'] == "DEVELOPMENT") {
		echo KT_getResource('PHP_FILE_RENAME_NO_FILE_D', 'File', array($hashFile['fullfilename']));
	} else {
		echo KT_getResource('PHP_FILE_RENAME_NO_FILE', 'File', array());
	}
	exit;
}

$n = $_GET['n'];
$firstURL = '';
$nextURL = '';
$prevURL = '';
$lastURL = '';
if ($n > 0) {
	$firstURL = 'popup_image.php?id='.$_GET['id'].'&n=0';
	$prevURL = 'popup_image.php?id='.$_GET['id'].'&n='.($n-1);	
}
if ($n < count($_SESSION['tng_popup'][$_GET['id']]['files'])-1) {
	$nextURL = 'popup_image.php?id='.$_GET['id'].'&n='.($n+1);	
	$lastURL = 'popup_image.php?id='.$_GET['id'].'&n='.(count($_SESSION['tng_popup'][$_GET['id']]['files'])-1);
}
// resize to fit window
$size = getimagesize('../../../' . $hashFile['fullfilename']);
if (($size[0] > $hash['popupwidth'] || $size[1] > $hash['popupheight']) && $hash['fitToWindow'] == true) {
	$arr = explode('/', $hashFile['fullfilename']);
	$fileName = array_pop($arr);
	
	$folder = implode('/', $arr) . '/thumbnails/';
	$path_info = KT_pathinfo($fileName);
	if (isset($hash['popupWatermark']) && $hash['popupWatermark']) {
		$hashThumbnail = tNG_watermarkHash($hash['watermark'], $hash['watermarkAlpha'], $hash['watermarkResize'], $hash['watermarkAlignment']);
		$fileName = $path_info['filename'].'_'.$hash['popupwidth'].'x'.$hash['popupheight']. '_w_' . $hashThumbnail . (isset($path_info['extension'])?'.'.$path_info['extension']:'');
		$thumbnailForDelete = $path_info['filename'].'_'.$hash['popupwidth'].'x'.$hash['popupheight'];
	} else {
		$fileName = $path_info['filename'].'_'.$hash['popupwidth'].'x'.$hash['popupheight'].(isset($path_info['extension'])?'.'.$path_info['extension']:'');
	}
	
	// resize image
	if ($size[0] > $hash['popupwidth'] || $size[1] > $hash['popupheight']) {
		$image = new KT_image();
		$image->setPreferedLib($GLOBALS['tNG_prefered_image_lib']);
		$image->addCommand($GLOBALS['tNG_prefered_imagemagick_path']);
		$image->resize('../../../'.$hashFile['fullfilename'], '../../../'.$folder, $fileName, $hash['popupwidth'], $hash['popupheight'], true);
		
		if ($image->hasError()) {
			$err = $image->getError();			
		}
		$hashFile['fullfilename'] = $fileName;
		$wasResized = true;
	}	
}

// apply the watermark
if (isset($hash['popupWatermark']) && $hash['popupWatermark']) {
	if (!isset($wasResized)) {
		$arr = explode('/', $hashFile['fullfilename']);
		$fileName = array_pop($arr);
		$hashThumbnail = tNG_watermarkHash($hash['watermark'], $hash['watermarkAlpha'], $hash['watermarkResize'], $hash['watermarkAlignment']);
		
		$folder = implode('/', $arr) . '/thumbnails/';
		$path_info = KT_pathinfo($fileName);
		$fileName = $path_info['filename'].'_'.$size[0].'x'.$size[1].'_w_' . $hashThumbnail . (isset($path_info['extension'])?'.'.$path_info['extension']:'');
		$thumbnailForDelete = $path_info['filename'].'_'.$size[0].'x'.$size[1];
	}
	$image = new KT_image();
	$image->setPreferedLib($GLOBALS['tNG_prefered_image_lib']);
	$image->addCommand($GLOBALS['tNG_prefered_imagemagick_path']);
	if (!isset($wasResized)) {
		if (!file_exists('../../../' . $folder . $fileName)) {
			$image->watermark('../../../' . $hashFile['fullfilename'], '../../../' . $folder . $fileName, $hash['watermark'], $hash['watermarkAlpha'], $hash['watermarkResize'], $hash['watermarkAlignment']);
			tNG_deleteThumbnails('../../../' . $folder, $thumbnailForDelete, $hashThumbnail);
		}
	} else {
		$image->watermark('../../../' . $folder . $fileName, '../../../' . $folder . $fileName, $hash['watermark'], $hash['watermarkAlpha'], $hash['watermarkResize'], $hash['watermarkAlignment']);
	}
	if ($image->hasError()) {
		$err = $image->getError();			
	}
	$hashFile['fullfilename'] = $fileName;
}
?>
<html>
<head>
	<title>Popup Image</title>
	<link href="../../../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
	<style>
		body {
			overflow:auto;
		}
		.centered {
			text-align:center !important;
		}
	</style>
</head>
<body height="100%" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginheight="0" marginwidth="0">
	<?php 
// Show IF Conditional region1 
if ($hash['popupNavigation']) {
?>
<div class="centered">
	<div class="KT_textnav clearfix">
		<ul>
			<li class="first">
				<?php 
				// Show IF Conditional region4 
				if ($firstURL != "") {
				?>
							<a href="<?php echo $firstURL;?>"><?php echo KT_getResource('First','NAV'); ?></a>
						  <?php } 
				// endif Conditional region4
				?>
			</li>
			<li class="prev">
				<?php 
				// Show IF Conditional region2 
				if ($prevURL != "") {
				?>
							<a href="<?php echo $prevURL;?>"><?php echo KT_getResource('Previous','NAV'); ?></a>
						  <?php } 
				// endif Conditional region2
				?>	
			</li>
			<li class="next">
				<?php 
				// Show IF Conditional region3
				if ($nextURL != "") {
				?>
							<a href="<?php echo $nextURL;?>"><?php echo KT_getResource('Next','NAV'); ?></a>
						<?php } 
				// endif Conditional region3
				?>
			</li>
			<li class="last">
				<?php 
				// Show IF Conditional region4 
				if ($lastURL != "") {
				?>
							<a href="<?php echo $lastURL;?>"><?php echo KT_getResource('Last','NAV'); ?></a>
						  <?php } 
				// endif Conditional region4
				?>
			</li>
		</ul>
	</div>
</div>
  <?php } 
// endif Conditional region1
?>
<?php 
// Show IF Conditional region5 
if (isset($err) && $GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
?>
	<div id="KT_tngerror"><label><?php echo KT_getResource('ERROR_LABEL','tNG'); ?></label>
		<div><?php echo $err[1]; ?></div>
	</div>
<?php } 
// endif Conditional region5
?>
<table width="100%" height="96%">
	<tr>
		<td align="center">
<a href="#" onClick="window.close()" title="Click to Close">
	<?php isset($fileName) ? $src = '../../../'.$folder . $fileName : $src = '../../../'.$hashFile['fullfilename']; ?>
	<img src="<?php echo $src; ?>" title="<?php echo $hashFile['filename'];?>" alt="<?php echo $hashFile['filename'];?>" border="0" />
</a>
		</td>
	</tr>
</table>
</body>
</html>