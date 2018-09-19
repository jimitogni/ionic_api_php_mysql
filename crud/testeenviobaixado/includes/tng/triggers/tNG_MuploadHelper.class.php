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

/** 
*	Object helper for multiple upload with save information. Put the code for flash / form upload in page
* Only for PRO version	 
* @access public
*/
class tNG_MuploadHelper {
	/**
	 * relpath to siteroot
	 * @var string
	 * @access public
	 */
	var $relpath;
	
	/**
	 * file maxsizet
	 * @var integer
	 * @access public
	 */
	var $maxSize;
	
	/**
	 * maxNumber files 
	 * @var integer
	 * @access public
	 */
	var $maxNumber;
	
	/**
	 * Sets existent number of uploaded files;
	 * @var integer
	 * @access public
	 */
	var $existentNumber;
	
	/**
	 * allowed extensions;
	 * @var string
	 * @access public
	 */
	var $allowedExtensions;
	/**
	 * input file size; 
	 * @var integer
	 * @access public
	 */
	var $fileInputSize;

	/**
	 * progress bar color HEX code (e.g. e2e2e2);
	 * @var string
	 * @access publix
	 */
	 var $barColor = "0x102B6F";
	/**
	 * progress area text color in HEX code (e.g. e2e2e2);
	 * @var string
	 * @access publix
	 */
	 var $textColor = "0x102B6F";
	/**
	 * border color in HEX code (e.g. #e2e2e2);
	 * @var string
	 * @access publix
	 */
	var $borderColor = "#e2e2e2";
	/**
	 * Constructor. Sets the relative path to siteroot
	 * @param string
	 * @param integer
	 * @access public
	 */	
    function tNG_MuploadHelper($relpath, $fileInputSize) {
    	$this->relpath = $relpath;
    	$this->fileInputSize = $fileInputSize;
    }
    
    /**
	 * Setter. Sets file maxsize
	 * @param integer
	 * @access public
	 */	
    function setMaxSize($maxSize) {
    	$this->maxSize = $maxSize;
    }
    
    /**
	 * Setter. Sets maxs number of files;
	 * @param integer
	 * @access public
	 */	
    function setMaxNumber($maxNumber) {
    	$this->maxNumber = $maxNumber;
    }
    
    /**
	 * Setter. Sets existent number uploaded files;
	 * @param integer
	 * @access public
	 */	
    function setExistentNumber($existentNumber) {
    	$this->existentNumber = $existentNumber;
    }

    /**
	 * Setter. Sets progress bar color;
	 * @param string
	 * @access public
	 */	    
    function setBarColor($val) {
    	$this->barColor = "0x".$val;
    }
    /**
	 * Setter. Sets border color;
	 * @param string
	 * @access public
	 */	    
    function setBorderColor($val) {
    	$this->borderColor = "0x".$val;
    }
    
    /**
	 * Setter. Sets text color;
	 * @param string
	 * @access public
	 */	
    function setTextColor($val) {
    	$this->textColor = "0x".$val;
    }

    /**
	 * Setter. Sets allowed extensions;
	 * @param string
	 * @access public
	 */	
    function setAllowedExtensions($allowedExtensions) {
		$allowedExtensions = str_replace(" ","",$allowedExtensions);
    	$this->allowedExtensions = $allowedExtensions;
    }
    
    /**
	 * Main class method; return the code in page
	 * @param string
	 * @access public
	 */	
    function Execute() {
  		if (!isset($_GET['isFlash'])) {
  			unset($_SESSION['tng_errors']);
  		}
     	$ret = '';
     	$url = KT_addReplaceParam(KT_getFullUri(), '/^totalRows_.*$/i'); 
  		$url2 = KT_addReplaceParam($url, 'isFlash'); 
  		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
  			$protocol = "https";
  		} else {
  			$protocol = "http";
  		}
  		$ret .= '<div id="uploadComponent" style="display:none;"></div>';
  		$ret .= '<div id="progressComponent" style="display:none;"></div>';
  		$ret .= '<div id="singleIndicator" style="display:none;width=100%; text-align:center;">
        				<h3>Uploading files</h3>
        				<img id="pbar" src="'.$this->relpath.'includes/tng/pub/loading.gif"/>
        				</div>
        				<div id="simple_upload" style="">
        					<form id="singleUpload" method="post" action="'.KT_escapeAttribute($url2).'" enctype="multipart/form-data" onSubmit="tNG_showIndicator()">
        			          <table cellpadding="2" cellspacing="0" class="KT_tngtable">
        			            <tr>
        			              <td><label for="filename_pic">Filename:</label></td>
        			              <td align="right"><input type="file" name="Filedata" id="Filedata" size = "40%" /><input type="submit" name="KT_Insert1" id="KT_Insert1" value="'.KT_getResource('UPLOAD','tNG').'"class="button_big" /></td>
        			            </tr>
        			          </table>
        			        </form>		
        				</div>	
        				<script>
          				tNG_initFileUpload("'.$protocol.'");
        				</script>';
    	return $ret;
    } 
    
    function getScripts() {
    	$includedpath = $this->relpath;
    	if (isset($GLOBALS['KT_REL_PATH'])) {
    		$includedpath = $GLOBALS['KT_REL_PATH'] . $includedpath;
    	}    	
    	$url = KT_addReplaceParam(KT_getFullUri(), '/^totalRows_.*$/i');
  		$flashURL = KT_addReplaceParam($url, 'isFlash'); 
  		$flashURL = KT_addReplaceParam($flashURL, session_name(), session_id());
    	$ret = '';
    	$ret .= '<script src="'.$this->relpath.'includes/tng/pub/flashembed.js" type="text/javascript" language="javascript"></script>'."\n";
	   	$ret .=	'<script type="text/javascript">
          //this is the unique instance of the multiple file upload object
          myupload = new tNG_FlashUpload(\''.$includedpath.'\',\'uploadComponent\', \'progressComponent\', \'myupload\');
          myupload.setColors(\''.$this->barColor.'\',\''.$this->textColor.'\',\''.$this->borderColor.'\');
          myupload.initialize(
					\''.KT_addReplaceParam($flashURL, 'isFlash', 1).'\',
					\''.KT_getResource('UPLOAD','tNG').'\',
					\''.$this->maxSize.'\',
					\''.$this->maxNumber.'\',
					\''.$this->existentNumber.'\',
					\''.$this->allowedExtensions.'\',
					\''.KT_escapeJS(KT_getResource('FLASH_MAX_SIZE_REACHED','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_MAX_FILES_REACHED','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_EMPTY_FILE','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_SKIPPING','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_HTTPERROR','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_HTTPERROR_HEAD','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_IOERROR','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_IOERROR_HEAD','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_COMPLETE_MSG','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_UPLOAD_BATCH','tNG')).'\',
					\''.KT_escapeJS(KT_getResource('FLASH_UPLOAD_SINGLE','tNG')).'\');          
          KT_self_url = "'.KT_addReplaceParam($url, 'isFlash').'";
				</script>'."\n";
		return $ret;
    }  
}

?>
