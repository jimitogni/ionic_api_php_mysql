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
* Provides functionalities for handling tNG based file uploads.	
* it is also de base class for tNG_ImageUpload class;
* @access public
*/
class tNG_FileUpload{
	/**
	 * The tNG object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * error object
	 * @var object  
	 * @access public
	 */
	var $errObj;
 	/**
	 * Constructor. Sets the reference to transaction. initialize some vars;
	 * @param object tNG 
	 * @access public
	 */	
	function tNG_FileUpload(&$tNG) {
		$this->tNG = &$tNG;
		$this->formFieldName = "";
		$this->dbFieldName = "";
		$this->folder = "";
		$this->maxSize = 0;
		$this->allowedExtensions = array();
		$this->rename  = "none";
		$this->renameRule  = "";
		$this->uploadedFileName = "";
		$this->errObj = null;
	}
	
	/**
	 * setter. set formFieldName
	 * @var string 
	 * @access public
	 */
	function setFormFieldName($formFieldName) {
		$this->formFieldName = $formFieldName;
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
	 * setter. set folder name
	 * @var string
	 * @access public
	 */
	function setFolder($folder) {
		$this->folder = $folder;
	}
	/**
	 * setter. set the file max size allowed
	 * @var integer
	 * @access public
	 */
	function setMaxSize($maxSize) {
		$this->maxSize = $maxSize;
	}
	/**
	 * setter. set the extensions allowed. 
	 * @var string extensions separated by coma
	 * @access public
	 */
	function setAllowedExtensions($allowedExtensions) {
		$arrExtensions = explode(',',$allowedExtensions);
		for($i=0;$i<count($arrExtensions);$i++) {
			$arrExtensions[$i] = trim($arrExtensions[$i]);
		}
		$this->allowedExtensions = $arrExtensions;
	}
	/**
	 * setter. set if is rename or not rule
	 * @var boolean
	 * @access public
	 */
	function setRename($rename) {
		$this->rename = $rename;
	}
	/**
	 * setter. set rename rule to be used
	 * @var string
	 * @access public
	 */
	function setRenameRule($renameRule) {
		$this->renameRule = $renameRule;
	}
	/**
	 * rollback the operation (delete the file)
	 * @return nothing
	 * @access public
	 */
	function RollBack() {
		@unlink($this->dynamicFolder . $this->uploadedFileName);
	}
	/**
	 * abstract method; to be implemented by the sub classes;
	 * @var string forlder name
	 * @var string file name
	 * @access public
	 */
	function deleteThumbnails($folder, $oldName) {
	}

	/**
	 * the main method, execute the code of the class;
	 * Upload the file, set the file name in transaction;
	 * return mix null or error object
	 * @access public
	 */
	function Execute() {
		if ($this->tNG->getTransactionType() == "_import") {
			$this->tNG->uploadObj = &$this;
		}
		$ret = null;
		if ($this->dbFieldName != '') {
			$oldFileName = $this->tNG->getSavedValue($this->dbFieldName);
			$saveFileName = $this->tNG->getColumnValue($this->dbFieldName);
			if ($this->tNG->getColumnType($this->dbFieldName) != 'FILE_TYPE') {
				$errObj = new tNG_error('FILE_UPLOAD_WRONG_COLTYPE', array(), array($this->dbFieldName));
				$errObj->addFieldError($this->dbFieldName, 'FILE_UPLOAD_WRONG_COLTYPE_D', array($this->dbFieldName));
				return $errObj;
			}
		} else {
			$oldFileName = KT_DynamicData($this->renameRule, $this->tNG, '', true);
			if (isset($this->tNG->multipleIdx)) {
				$saveFileName = @$_FILES[$this->formFieldName."_".$this->tNG->multipleIdx]['name'];
			} else {
				$saveFileName = @$_FILES[$this->formFieldName]['name'];
			}
		}
		$this->dynamicFolder = KT_DynamicData($this->folder, $this->tNG, '', false);
		
		$arrArgs = array();
		$autoRename = false;
		switch ($this->rename) {
			case 'auto':
				$autoRename = true;
				break;
			case 'none':
				break;
			case 'custom':
				$path_info = KT_pathinfo($saveFileName);
				$arrArgs = array('KT_name' => $path_info['filename'], 'KT_ext' => $path_info['extension']);
				$saveFileName = KT_DynamicData($this->renameRule, $this->tNG, '', false, $arrArgs);
				break;
			default:
				die('INTERNAL ERROR: Unknown upload rename method.');
		}
		
                if(tNG_isFileInsideBaseFolder($this->folder, $saveFileName) === false) {
                  $baseFileName = dirname(KT_realPath($this->dynamicFolder . $saveFileName, false));
                  return new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array($baseFileName, tNG_getBaseFolder($this->folder)));
                }
		
		// Upload File
		$fileUpload = new KT_fileUpload();
		if (isset($this->tNG->multipleIdx)) {
			$fileUpload->setFileInfo($this->formFieldName."_".$this->tNG->multipleIdx);
		} else {
			$fileUpload->setFileInfo($this->formFieldName);
		}
		$fileUpload->setFolder($this->dynamicFolder);
		$fileUpload->setRequired(false);
		$fileUpload->setAllowedExtensions($this->allowedExtensions);
		$fileUpload->setAutoRename($autoRename);
		$fileUpload->setMaxSize($this->maxSize);
		$this->uploadedFileName = $fileUpload->uploadFile($saveFileName, $oldFileName);
		
		$updateDB = basename($this->uploadedFileName);
		if ($fileUpload->hasError()) {
			$arrError = $fileUpload->getError();
			$errObj = new tNG_error('FILE_UPLOAD_ERROR', array($arrError[0]), array($arrError[1]));
			if ($this->dbFieldName != '') {
				$errObj->addFieldError($this->dbFieldName, '%s', array($arrError[0]));
			}
			$ret = $errObj;
		} else {
			$this->dynamicFolder = KT_realpath($this->dynamicFolder);
			if ($this->uploadedFileName == "") {
				//Check if for update we need to rename file
				if ($this->rename == "custom") {
					$path_info = KT_pathinfo($oldFileName);
					$arrArgs['KT_ext'] = $path_info['extension'];
				}
				$tmpFileName = KT_DynamicData($this->renameRule,$this->tNG,'',false, $arrArgs);
				if ($tmpFileName != "" && $oldFileName != "" && $tmpFileName != $oldFileName) {
					if (file_exists($this->dynamicFolder.$oldFileName)) {
						if (@rename($this->dynamicFolder.$oldFileName, $this->dynamicFolder.$tmpFileName) === true) {
							$this->uploadedFileName = $tmpFileName;
							$updateDB = basename($this->uploadedFileName);
						} else {
							$ret = new tNG_error('FILE_UPLOAD_RENAME', array(), array($this->dynamicFolder.$oldFileName, $this->dynamicFolder.$tmpFileName));
						}
					}
				}
			}

			if ($ret === null) {
				if ($this->tNG->getTransactionType() == "_insert" || $this->tNG->getTransactionType() == "_multipleInsert") {
					$this->tNG->registerTrigger('ERROR', 'Trigger_Default_RollBack', 1, $this);
				}
				
				$this->deleteThumbnails($this->dynamicFolder .'thumbnails'.DIRECTORY_SEPARATOR, $oldFileName);
				if ($this->uploadedFileName != '') {
					$this->deleteThumbnails($this->dynamicFolder.'thumbnails'.DIRECTORY_SEPARATOR, $this->uploadedFileName);
				}

				if ($this->dbFieldName != '' && $this->uploadedFileName != "") {
					$ret = $this->tNG->afterUpdateField($this->dbFieldName, $updateDB);
				}
			}
			if ($ret === null && $this->dbFieldName != "") {
				$this->tNG->setRawColumnValue($this->dbFieldName,$updateDB);
			}
		}
		$this->errObj = $ret;
		return $ret;
	}
}
?>