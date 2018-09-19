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
 * Handle the multimpe file upload. Base class for tNG_MultipleImageUpload
 * Only for PRO version	 
 * @access public
 */
class tNG_MFileUpload {
	/**
	 * relpath to site root
	 * @var string 
	 * @access public
	 */
	var $relPath; 
	/**
	 * reference unique per page
	 * @var string 
	 * @access public
	 */
	var $reference;
	/**
	 * connection name
	 * @var string 
	 * @access public
	 */
	var $connName;
	/**
	 * baseFolder name
	 * @var string 
	 * @access public
	 */
	var $baseFolder;
	/**
	 * folder name
	 * @var string 
	 * @access public
	 */
	var $folder;
	/**
	 * pk column name
	 * @var string 
	 * @access public
	 */
	var $pk;
	/**
	 * pk rename rule
	 * @var string 
	 * @access public
	 */
	var $pkRenameRule;
	/**
	 * upload popup width
	 * @var integer
	 * @access public
	 */
	var $popupUploadWidth;
	/**
	 * upload popup height
	 * @var integer
	 * @access public
	 */
	var $popupUploadHeight;
	/**
	 * max file size in KB
	 * @var integer
	 * @access public
	 */
	var $maxSize;
	/**
	 * max number of files permited for upload
	 * @var integer
	 * @access public
	 */
	var $maxFiles;
	/**
	 * allowed extensions for upload;
	 * @var array 
	 * @access public
	 */
	var $allowedExtensions = array();
	/**
	 * number of calls of the method getUploadLink;
	 * @var int
	 * @access public
	 */
	var $noOfCalls;
    
    /**
	 * Constructor. Sets relpath, reference value, connection name
	 * @param string relpath
	 * @param string reference
	 * @param string connection name
	 * @access public
	 */
    function tNG_MFileUpload($relPath, $reference, $connName) {
    	$this->relPath = $relPath;
    	$this->reference = $reference;
    	$this->baseFolder = '';
    	$this->folder = '';
    	$this->connName = $connName;
    	$this->noOfCalls = 0;
    	$this->popupUploadWidth = 640;
    	$this->popupUploadHeight = 480;    	
    }
    /**
	 * Setter. Sets the pk column name and dynamic data
	 * @param string column name
	 * @param string dynamic data
	 * @access public
	 */
    function setPrimaryKey($pk, $pkRenameRule) {
    	$this->pk = $pk;
    	$this->pkRenameRule = $pkRenameRule;
    }
     /**
	 * Setter. Sets base folder path
	 * @param string 
	 * @access public
	 */
    function setBaseFolder($folder) {
    	$this->baseFolder = KT_TransformToUrlPath($folder);	
    }
	 /**
	 * Setter. Sets the folder rename rule
	 * @param string 
	 * @access public
	 */
    function setFolder($folder) {
    	$this->folder = KT_TransformToUrlPath(KT_DynamicData($folder, null));	
    }
     /**
	 * Setter. Sets the max file size allowed
	 * @param int 
	 * @access public
	 */
    function setMaxSize($maxSize) {
    	$this->maxSize = $maxSize;
    }
	 /**
	 * Setter. Sets max number of files
	 * @param int 
	 * @access public
	 */
    function setMaxFiles($maxFiles) {
    	$this->maxFiles = $maxFiles;
    }
	 /**
	 * Setter. Sets size of the upload 
	 * @param int popup width 
	 * @param int popup height
	 * @access public
	 */
    function setUploadPopupSize($popupUploadWidth, $popupUploadHeight ) {
    	$this->popupUploadWidth = $popupUploadWidth;
    	$this->popupUploadHeight = $popupUploadHeight;
    }
     /**
	 * Setter. Sets allowed extensions
	 * @param string 
	 * @access public
	 */
    function setAllowedExtensions($allowedExtensions) {
    	$arrExtensions = explode(',', strtolower($allowedExtensions));
		for($i=0; $i<count($arrExtensions); $i++) {
			$arrExtensions[$i] = trim($arrExtensions[$i]);
		}
		$this->allowedExtensions = $arrExtensions;
    }
    /**
	 * Getter. Gets upload reference
	 * @return string 
	 * @access public
	 */
    function getUploadReference() {
    	return $this->reference."_".$this->noOfCalls;
    }
    /**
	 * Getter. Gets the js code for open the new window
	 * @return string 
	 * @access public
	 */
    function getUploadAction() {
		if (!$this->checkSecurity()) {
    		return 'return false;';	
    	}  
		return "window.open(this.href,'MultipleUpload','width=".$this->popupUploadWidth.",height=".$this->popupUploadHeight."');return false;";
    }
     /**
	 * Getter. Gets the link to the upload page
	 * sets in session the neccesary info
	 * @return string
	 * @access public
	 */
    function getUploadLink() {
    	if (!$this->checkSecurity()) {
    		return '';	
    	}    	
    	
    	$siteroot = KT_realpath($this->relPath, true);
		$uploadFolder = KT_realpath($this->baseFolder, true);
		$this->baseFolder = $this->relPath . substr($uploadFolder, strlen($siteroot));
    	$url = '';
    	if (!isset($_SESSION['tng_upload'])) {
			$_SESSION['tng_upload'] = array();
		}
		$this->noOfCalls++;
		if ($this->noOfCalls == 1) {
			$this->garbageCollector(); 
			if (!isset($_POST[$this->reference.'_'.$this->noOfCalls]) || !isset($_SESSION['tng_upload'][$this->reference]['files'])) {
				$_SESSION['tng_upload'][$this->reference] = array();
				$_SESSION['tng_upload'][$this->reference]['properties'] = array();
				$_SESSION['tng_upload'][$this->reference]['properties']['maxSize'] = $this->maxSize;
				$_SESSION['tng_upload'][$this->reference]['properties']['maxFiles'] = $this->maxFiles;
				$_SESSION['tng_upload'][$this->reference]['properties']['allowedExtensions'] = $this->allowedExtensions;
				$_SESSION['tng_upload'][$this->reference]['properties']['relPath'] = $this->relPath;
				$_SESSION['tng_upload'][$this->reference]['properties']['connName'] = $this->connName;
				$_SESSION['tng_upload'][$this->reference]['properties']['time'] = time();      
				$_SESSION['tng_upload'][$this->reference]['files'] = array();
			}
			if (isset($_SESSION['tng_upload'][$this->reference]['files'])){
				$tmpArr = array();
				for($i=1;$i<=count($_SESSION['tng_upload'][$this->reference]['files']);$i++) {
					if ($_SESSION['tng_upload'][$this->reference]['files'][$i] !== null) {
						$tmpArr[count($tmpArr)+1] = $_SESSION['tng_upload'][$this->reference]['files'][$i];
					}
				}
				$_SESSION['tng_upload'][$this->reference]['files'] = $tmpArr;
			}
		}
		
		$pk = KT_DynamicData($this->pkRenameRule, null);
		if ($pk == $this->pkRenameRule) {
			$pk = '';
		}
	    if (!isset($_POST[$this->reference.'_'.$this->noOfCalls]) || !isset($_SESSION['tng_upload'][$this->reference]['files'][$this->noOfCalls])) {
	      $_SESSION['tng_upload'][$this->reference]['files'][$this->noOfCalls] = array();
	      if ($pk == '') {
	        $_SESSION['tng_upload'][$this->reference]['files'][$this->noOfCalls]['pk'] = str_replace('.', '_', uniqid("", true));
	        if ($this->noOfCalls == 1) {
	          $_SESSION['tng_upload'][$this->reference]['properties']['pkRule'] = $this->pk;
	          $_SESSION['tng_upload'][$this->reference]['properties']['folderRule'] = $this->baseFolder . $this->folder;
	        }
	      } else {
	        $_SESSION['tng_upload'][$this->reference]['files'][$this->noOfCalls]['pk'] = $pk;	
	      }
	      $_SESSION['tng_upload'][$this->reference]['files'][$this->noOfCalls]['folder'] = str_replace('{'.$this->pk.'}', $_SESSION['tng_upload'][$this->reference]['files'][$this->noOfCalls]['pk'], $this->baseFolder . $this->folder);
	    }
		
		$url = $this->relPath . 'includes/tng/pub/multiple_upload.php';
		$url = KT_addReplaceParam($url, $this->reference, $this->noOfCalls);
		return $url;
    }
    /**
	 * verify the upload folder is not out of the base folder;
	 * @return boolean
	 * @access public
	 */
    function checkSecurity() {
    	// security
    	$base = KT_realpath($this->baseFolder, true);
    	$fullFolder = KT_realpath(str_replace('{'.$this->pk.'}', 1, $this->baseFolder . $this->folder));
		if (substr($fullFolder, 0, strlen($base)) != $base) {
			return false;
		}
		if (strpos($fullFolder, '{') !== false) {
			return false;
		}
		return true;	
    }
     /**
	 * garbage collector. remove from session entries older than 30 minutes;
	 * @return string
	 * @access public
	 */
    function garbageCollector() {
     	// cleanup orphan folders
     	$arr = explode('/', $this->baseFolder . $this->folder);
		if (substr($this->folder, -1, 1) == '/' || substr($this->folder, -1, 1) == '\\') {
			array_pop($arr);
		}
		$new = array_pop($arr);
		$f = implode('/', $arr) . '/'; 
		
		$fld = new KT_folder();
		$arr = $fld->readFolder($f, false);
		//print_r($arr); 
		if (isset($arr['folders']) && count($arr['folders'])) {
			foreach ($arr['folders'] as $entry) {
				if (preg_match("/^(.*)\w{14}_\w{8}(.*)/is", $entry['name'])) {
					$fld = new KT_folder();
					$files = $fld->readFolder($f . $entry['name']);
					if (isset($files['files']) && count($files['files']) > 0) {
						foreach ($files['files'] as $file) {
							if (filectime($f . $entry['name'] . '/' . $file['name']) < time()-60*30 ) {
								$fld->deleteFolderNR($f . $entry['name']);
								break;	
							}
						}						
					} else {
						$fld->deleteFolderNR($f . $entry['name']);
					}				
				}
			}
		}				
		if (!isset($_SESSION['tng_upload'])) {
			return ;	
		}
		// clear old session values;
		foreach ($_SESSION['tng_upload'] as $id => $hash) {
			if (isset($hash['properties']) && $hash['properties']['time'] < time()-60*30) {
		  		unset($_SESSION['tng_upload'][$id]);
		    }
		}
    } 
}
?>