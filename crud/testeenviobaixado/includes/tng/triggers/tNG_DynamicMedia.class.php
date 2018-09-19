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
*	Display dynamic media
* Only for PRO version
* @access public
*/
class tNG_DynamicMedia {
	/**
	 * relpath to siteroot
	 * @var string
	 * @access public
	 */
	var $relPath;
	/**
	 * type of media to display
	 * @var string
	 * @access public
	 */
	var $type;
	/**
	 * folder
	 * @var string
	 * @access public
	 */
	var $folder;
	/**
	 * file name
	 * @var string  
	 * @access public
	 */
	var $renameRule;
		
	/**
	 * Constructor. Sets the relative path to siteroot
	 * @param string
	 * @access public
	 */	
    function tNG_DynamicMedia($relPath) {
    	$this->relPath = $relPath;
    }
	/**
	 * Setter. Sets thre relative path to siteroor
	 * @param string
	 * @access public
	 */	
    function setType($type) {
    	$this->type = strtolower($type);
    }
    /**
	 * Setter. Sets the folder name;
	 * @param string 
	 * @access public
	 */	
    function setFolder($folder) {
    	$folder = KT_TransformToUrlPath($folder, true);
    	$pos = strpos($folder, '{');
    	if ($pos !== false) {
    		if ($this->renameRule == "") {
    			$this->renameRule = substr($folder, $pos);
    		} else {
    			$this->renameRule = substr($folder, $pos) . $this->renameRule;
    		}
    		$this->renameRule = $this->renameRule;
    		$folder = substr($folder, 0, $pos);
    	}
    	$this->folder = $folder;
    }
    /**
	 * Setter. Sets the file name;
	 * @param string 
	 * @access public
	 */
    function setRenameRule($renameRule){
    	$renameRule = KT_TransformToUrlPath($renameRule, false);
    	if ($this->renameRule == "") {
    		$this->renameRule = $renameRule;
    	} else {
    		$this->renameRule .= $renameRule;
    	}
    }
    
     /**
	 * Main class method. 
	 * @return string error string or object string
	 * @access public
	 */
    function Execute() {
    	$ret = '';
    	$folder = $this->folder;
		$fileName = KT_DynamicData($this->renameRule, null);
    	// security
		$base = KT_realpath($folder, true);
		if (substr(KT_realpath($base.$fileName), 0, strlen($base)) != $base) {
			return $ret;
		}
		if ($this->type == 'swf') {
			return  $this->getSwfPath();
		} 
		$ret = $folder . $fileName;
		return $ret;
    }
    /**
	 * Getter.
	 * @return string error string or object string
	 * @access public
	 */
    function getSwfPath() {
    	$ret = '';
    	$folder = $this->folder;
		$fileName = KT_DynamicData($this->renameRule, null);
    	// security
		$base = KT_realpath($folder, true);
		if (substr(KT_realpath($base.$fileName), 0, strlen($base)) != $base) {
			return $ret;
		}
		$path_info = KT_pathinfo($folder . $fileName);
		$ret = $path_info['dirname'] . '/' . $path_info['filename'];
		return $ret;
    }
}
?>