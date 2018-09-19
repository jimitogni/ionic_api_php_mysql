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
* Provides functionalities for deleting files.
* @access public
*/
class tNG_FileDelete {
	/**
	 * The tNG object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * name of the field from database wich helds the file name
	 * @var string 
	 * @access public
	 */
	var $dbFieldName = '';
	/**
	 * basefolder name
	 * @var string
	 * @access public
	 */
	var $baseFolder = '';
	/**
	 * folder name
	 * @var string
	 * @access public
	 */
	var $folder = '';
	/**
	 * if it is used rename
	 * @var boolean
	 * @access public
	 */
	var $rename = false;
	/**
	 * the rename rule
	 * @var string
	 * @access public
	 */
	var $renameRule = '';
	
	/**
	 * Constructor. Sets the reference to transaction.
	 * @param object tNG 
	 * @access public
	 */
	function tNG_FileDelete(&$tNG) {
		$this->tNG = &$tNG;
	}
	
	/**
	 * setter. set the db field name
	 * @var string
	 * @access public
	 */
	function setDbFieldName($dbFieldName) {
		$this->dbFieldName = $dbFieldName;
	}
	/**
	 * setter. set the folder name
	 * @var object tNG
	 * @access public
	 */
	function setFolder($folder) {
		$folder = KT_TransformToUrlPath($folder, true);
		$pos = strpos($folder, '{');
		if ($pos !== false) {
			$this->folder = substr($folder, $pos);
    		$this->baseFolder = substr($folder, 0, $pos);
		} else {
			$this->folder = '';
    		$this->baseFolder = $folder;
		}
	}
	/**
	 * setter. set the rename to true and the renamarule;
	 * @var object tNG
	 * @access public
	 */
	function setRenameRule($renameRule) {
		$this->rename = true;
		$this->renameRule = $renameRule;
	}
	/**
	 * delete the tumbnails if exists
	 * @var string folder name
	 * @var string name of the file
	 * @return nothing
	 * @access public
	 */
	function deleteThumbnails($folder, $oldName) {
		tNG_deleteThumbnails($folder, $oldName, '');
	}
	/**
	 * the main method, execute the code of the class
	 * return mix null or error object
	 * @access public
	 */
	function Execute() {
		$ret = NULL;
		$baseFolder = KT_realpath($this->baseFolder);
		if ($this->rename == false && $this->dbFieldName != '') {
			$fileName = $this->tNG->getSavedValue($this->dbFieldName);
		} else {
			$fileName = KT_DynamicData($this->renameRule, $this->tNG, '', true);
		}
		$folder = KT_DynamicData($this->folder, $this->tNG, '', true);
		// security
		if (substr(KT_realpath($baseFolder . $folder . $fileName), 0, strlen($baseFolder)) != $baseFolder) {
			$ret = new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array(dirname(KT_realpath($baseFolder . $folder . $fileName, false)), $baseFolder));
			return $ret;
		}
		if ($fileName != "") {
			$fullFileName = $baseFolder . $folder . $fileName;
			if (file_exists($fullFileName)) {
				$delRet = @unlink($fullFileName);
				if ($delRet !== true) {
					$ret = new tNG_error('FILE_DEL_ERROR', array(), array($fullFileName));
					$ret->setFieldError($this->fieldName, 'FILE_DEL_ERROR_D', array($fullFileName));
				} else {
					$path_info = KT_pathinfo($fullFileName);
					$this->deleteThumbnails($path_info['dirname'] . '/thumbnails/', $path_info['basename']);
				}
			}
		}
		return $ret;
	}
}
?>