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

// includes business logic
require_once('multiple_upload.inc.php');

if (!isset($uploadHash)) {
	die('Internal Error. Session expired.');
}

// Multiple Upload Helper Object
$muploadHelper = new tNG_MuploadHelper('../../../', 95);
$muploadHelper->setMaxSize($uploadHash['maxSize']);
$muploadHelper->setMaxNumber($uploadHash['maxFiles']);
$muploadHelper->setExistentNumber($totalRows_rsFiles);
$muploadHelper->setAllowedExtensions(implode(',', $uploadHash['allowedExtensions']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link href="../../../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
	<link href="multiple_upload.css" rel="stylesheet" type="text/css" media="all" />
	<title>Multiple Upload</title>
    <script src="../../../includes/common/js/base.js" type="text/javascript"></script>
    <script src="../../../includes/common/js/utility.js" type="text/javascript"></script>
	<?php echo $muploadHelper->getScripts(); ?>
</head>
<body>

<div class="top">
<table border="0" width="100%" cellpadding="0" cellspacing="0" class="KT_tngtable">
<tr><th>
    <h1><?php echo $listFolder->getTotalRecords(); ?> <?php echo KT_getResource('FILES','tNG'); ?> 
        <?php
        // Show IF Conditional region1 
        if ($uploadHash['maxFiles'] != '' && $uploadHash['maxFiles'] != 0) {
        ?>
        (<?php echo KT_getResource('MAXFILES','tNG'); ?> <?php echo $uploadHash['maxFiles']; ?> <?php echo KT_getResource('FILES','tNG'); ?>)
        <?php } 
        // endif Conditional region1
        ?>
    </h1>
    </th></tr>
<tr>
<td align="right">
<?php
// Show IF Conditional region3 
if (!isset($uploadHash['maxFiles']) || $uploadHash['maxFiles']==0 || $uploadHash['maxFiles'] > $totalRows_rsFiles) {
?>
<?php
echo $muploadHelper->Execute();
?>
 <?php } 
// endif Conditional region3
?>
</td>
</tr>
</table>
</div>
<div class="middle">
	    <?php 
    // Show IF Conditional region2 
    if (isset($err)) {
    ?>	
        <div id="KT_tngerror"><label><?php echo KT_getResource('ERROR_LABEL','tNG'); ?></label>
            <div><?php echo ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT' ? $err[1] : $err[0]); ?></div>
        </div>
      <?php } 
    // endif Conditional region2
    ?>
    <?php 
    // Show IF Conditional region6 
    if (isset($_SESSION['tng_upload']['errorForFlash']) && $_SESSION['tng_upload']['errorForFlash']!='' && !isset($_GET['isFlash'])) {
    ?>	
        <div id="KT_tngerror"><label><?php echo KT_getResource('ERROR_LABEL','tNG'); ?></label>
            <div><?php echo $_SESSION['tng_upload']['errorForFlash']; ?></div>
        </div>
        <?php unset($_SESSION['tng_upload']['errorForFlash']); ?>	
      <?php } 
    // endif Conditional region6
    ?>
		<table border="0" width="90%" cellpadding="0" cellspacing="0" class="KT_tngtable" >
		<thead>
		<tr class="KT_row_order">
			<?php 
			// Show IF Conditional region90
			if (isset($uploadHash['thumbnail']['popupWidth']) && isset($uploadHash['thumbnail']['popupHeight'])) {
			?>				
				<th>Preview</th>
						 <?php } 
// endif Conditional region90
?>		
			<th>Name</th>
			<th>Date</th>
			<th>Size</th>
			<th>&nbsp;</th>
		</tr>
		</thead>

				<?php if ($totalRows_rsFiles == 0) { // Show if recordset empty ?>
					<tr  style="height:350px;"><td colspan="5" align="center" valign="center" class="emptyTable"><?php echo KT_getResource('EMPTY_MUP_POPUP','tNG'); ?></td></tr>
	
  <?php } // Show if recordset empty ?>
		
		<?php if ($totalRows_rsFiles > 0) { // Show if recordset not empty ?>
<?php 
$i = 1;
 while (!$rsFiles->EOF) { 
?>
			<tr class="<?php echo ( ($i%2 == 0) ? 'KT_even' : 'KT_odd'); ?>">
				
				<td>
					<?php 
// Show IF Conditional region4 
if (isset($uploadHash['thumbnail']['popupWidth']) && isset($uploadHash['thumbnail']['popupHeight'])) {
?>				
						<a href="<?php echo $objDynamicThumb1->getPopupLink(); ?>" onclick="<?php echo $objDynamicThumb1->getPopupAction(); ?>" target="_blank">
					 <?php } 
// endif Conditional region4
?>
					<?php 
// Show IF Conditional region5 
if (isset($uploadHash['thumbnail']['width']) && isset($uploadHash['thumbnail']['height'])) {
?>									
						<img src="<?php echo $objDynamicThumb1->Execute();?>" alt="<?php echo KT_getResource('CLICK_ENLARGE','tNG'); ?>" title="<?php echo KT_getResource('CLICK_ENLARGE','tNG'); ?>" border="0" />
					  <?php 
// else Conditional region5
} else { ?>
						<?php echo $rsFiles->Fields('name'); ?>
					<?php } 
// endif Conditional region5
?>
							<?php 
// Show IF Conditional region6 
if (isset($uploadHash['thumbnail']['popupWidth']) && isset($uploadHash['thumbnail']['popupHeight'])) {
?>	
						</a>
					<?php } 
// endif Conditional region6
?>
				</td>
				<?php
// Show IF Conditional region99 
if (isset($uploadHash['thumbnail']['width']) && isset($uploadHash['thumbnail']['height'])) {
?>				
				<td>
				<?php echo $rsFiles->Fields('name'); ?>
				</td>
<?php } 
// endif Conditional region99
?>				
				<td><?php echo $rsFiles->Fields('date'); ?></td><td> <?php echo number_format($rsFiles->Fields('size'), 0, '', ',' ); ?> bytes</td>
				<td>
					<form method="post" action="<?php echo KT_getFullUri(); ?>" enctype="multipart/form-data" class="tNG_deleteBtnForm">
						<input type="hidden" name="delete" value="<?php $_SESSION['tng_upload_delete'][] = $rsFiles->Fields('name'); echo (count($_SESSION['tng_upload_delete']) - 1); ?>" />
      					<input type="submit" name="KT_del" value="<?php echo KT_getResource('DELETE','tNG'); ?>" class="KT_delete_link button_smallest" onClick = "if (!confirm('Are you sure you want to delete?')) return false;" />
					</form>
					</td>
			</tr>
<?php
    $rsFiles->MoveNext(); 
  }
?>
  <?php } // Show if recordset not empty ?>
		</table>

</div>
<div class="bottom" >
<table width="100%" class="KT_tngtable" cellpadding="0" cellspacing="0">
<tr class="KT_buttons">
        <th>
            <input type="button" value="<?php echo KT_getResource('CLOSE','tNG'); ?>" onClick = "window.close()" />
        </th>
    </tr>
</table>
</div>
</body>
</html>