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
 * This the File List recordset class; 
 * Only for PRO version
 * @access public
 */
class tNG_FileListRecordset {
	/**
	 * relpath to site root
	 * @var string 
	 * @access private
	 */
	var $relPath;
	/**
	 * connection object
	 * @var object
	 * @access private
	 */
	var $conn;
	/**
	 * base folder to be read
	 * @var string 
	 * @access private
	 */
	var $baseFolder;
	/**
	 * allowed file extensions
	 * @var array
	 * @access private
	 */
	var $allowedExtensions;
	/**
	 * folder to be read
	 * @var string 
	 * @access private
	 */
	var $folder;
	/**
	 * absolute path to folder
	 * @var string 
	 * @access private
	 */
	var $path;
	/**
	 * the sorting field for files
	 * @var string 
	 * @access private
	 */
	var $orderField;
	/**
	 * the sorting direction
	 * @var string 
	 * @access private
	 */
	var $orderDir;
	/**
	 * holding the files
	 * @var array 
	 * @access private
	 */
	var $filesArr;
	/**
	 * current page
	 * @var int 
	 * @access private
	 */
	var $page;
	/**
	 * nomber of records per page;
	 * @var int 
	 * @access private
	 */
	var $recordsPerPage;
	/**
	 * total nomber of records;
	 * @var int 
	 * @access private
	 */
	var $totalNo;
	/**
	 * error string;
	 * @var string 
	 * @access public
	 */
	var $error;
	/**
	 * Constructor. Sets relpath and connection and initialize some other class members
	 * @param string relpath
	 * @param object connection
	 * @access public
	 */
    function tNG_FileListRecordset($relPath, &$conn) {
    	$this->relPath = $relPath;
    	$this->filesArr = array();
    	$this->conn = &$conn;
    	$this->page = 0;
    	$this->recordsPerPage = 0;
    	$this->totalNo = 0;
    	$this->folder = "";
    	$this->allowedExtensions = array();
    	$this->error = '';
    }
    /**
    * Setter. Sets the page number
    * @param integer page to return 
	 * @access public
	 */
    function setPage($page) {
    	$this->page = $page;
	 }
    /**
    * Setter. Sets no of records per page
    * @param integer no records per page 
	 * @access public
	 */
    function setRecordsPerPage($recordsPerPage) {
    	$this->recordsPerPage = $recordsPerPage;
    }
    /**
	 * Setter. Sets the base folder name;
	 * @param string base folder name
	 * @access public
	 */
    function setBaseFolder($baseFolder) {
    	$pos = strpos($baseFolder,'{');
    	if ($pos !== false) {
    		if ($this->folder == "") {
    			$this->folder = substr($baseFolder,$pos);
    		} else {
    			$this->folder = KT_TransformToUrlPath(substr($baseFolder,$pos),true).$this->folder;
    		}
    		$this->folder = KT_TransformToUrlPath($this->folder, true);
    		$baseFolder = substr($baseFolder,0,$pos);
    	}
    	$this->baseFolder = KT_realpath($baseFolder, true);
	}
    /**
	 * Setter. sets the allowed extensions to read
	 * @param string extension list coma separated
	 * @access public
	 */
    function setAllowedExtensions($allowedExtensions) {
    	$this->allowedExtensions = explode(",",strtolower($allowedExtensions));
    	for($i=0;$i<count($this->allowedExtensions);$i++) {
    		$this->allowedExtensions[$i] = trim($this->allowedExtensions[$i]); 
    	}
	}
    /**
	 * Setter. Sets the folder name and calculate the real path;
	 * @param string base folder name
	 * @access public
	 */
    function setFolder($folder) {
    	if ($this->folder == "") {
    		$this->folder = KT_TransformToUrlPath($folder, true);
    	} else {
    		$this->folder .= $folder;
    	}
	}
    /**
	 * Setter. Sets order field and order direction to be used; 
	 * @param string field name 
	 * @param string direction ASC|DESC 
	 * @access private
	 */
    function setOrder($orderField, $orderDir) {
    	$this->orderField = strtolower($orderField);
    	$this->orderDir = strtolower($orderDir);
    	if (!in_array((string)$this->orderField,array("name","date","size","extension"))) {
    		die("Internal error: unsuported sort column.");
    	} 
    }
    /**
	 * Main class method. Return a fake recordset.
	 * @var string 
	 * @access private
	 */
    function Execute() {
		$relFolder = KT_DynamicData($this->folder,  '', '', false, array(), false);
		$relFolder = KT_TransformToUrlPath($relFolder, true);
		if (substr($relFolder,0,1) == '/') {
			$relFolder = substr($relFolder,1);
		}
    	$fullFolderPath = KT_realpath($this->baseFolder.$relFolder, true);
    	if (substr($fullFolderPath,0,strlen($this->baseFolder)) != $this->baseFolder) {
    		if (isset($GLOBALS['tNG_debug_mode']) && $GLOBALS['tNG_debug_mode'] == "DEVELOPMENT") {
    			die("Security error. The folder '".$fullFolderPath."' is out of base folder '".$this->baseFolder."'");
    		} else {
    			die("Security error. Access to this folder is forbidden.");
    		}
    	}
    	$this->path = $fullFolderPath;
		$noOfEntries = 0;
		$startCountEntries = $this->page * $this->recordsPerPage;
		$this->totalNo = 0;
		
		if (file_exists($this->path)) {
			//read folders
			$folder = new KT_folder();
			$entries = $folder->readFolder($this->path, true);
			if ($folder->hasError()) {
				$err = $folder->getError();
	    		if (isset($GLOBALS['tNG_debug_mode']) && $GLOBALS['tNG_debug_mode'] == "DEVELOPMENT") {
					$this->error = $err[1];
	    		} else {
	    			$this->error = $err[0];
	    		}
			}
			$this->filesArr = $entries['files'];
			$tmpFilesArr = array();
			$tmpArr = array();
			for ($i=0;$i<count($this->filesArr);$i++) {
				$this->filesArr[$i]['fullname'] = $relFolder.$this->filesArr[$i]['name'];
				$path_info = KT_pathinfo($this->filesArr[$i]['name']);
				$this->filesArr[$i]['extension'] = $path_info['extension'];
				$filetime = filectime($this->path . $this->filesArr[$i]['name']);
				$this->filesArr[$i]['date'] =$filetime;
				if (in_array(strtolower($this->filesArr[$i]['extension']),$this->allowedExtensions) || in_array("*", $this->allowedExtensions)) {
					$tmpArr[]= $this->filesArr[$i][$this->orderField];
					$tmpFilesArr[] = $this->filesArr[$i];
				}
			}
			$this->filesArr = $tmpFilesArr;
			$this->Sort($tmpArr);
			$this->totalNo = count($this->filesArr);
			if ($this->recordsPerPage > 0) {
				$from = $this->page * $this->recordsPerPage;
				$this->filesArr = array_slice($this->filesArr, $from, $this->recordsPerPage);
			}
			for($i=0;$i<count($this->filesArr);$i++) {
				$this->filesArr[$i]['date'] = KT_convertDate(date("Y-m-d H:i:s", $this->filesArr[$i]['date']), "yyyy-mm-dd HH:ii:ss", $GLOBALS['KT_screen_date_format'] . ' ' . $GLOBALS['KT_screen_time_format_internal']);
			}
			// create fake recordset
			$this->filesArr = $this->formatData($this->filesArr);
    	} 
		
		$KT_FakeRecordset = new KT_FakeRecordset($this->conn);
		$ret = $KT_FakeRecordset->getFakeRecordset($this->filesArr);
		if ($ret === NULL) {
			if (isset($GLOBALS['tNG_debug_mode']) && $GLOBALS['tNG_debug_mode'] == "DEVELOPMENT") {
				die("Internal error: cannot create fake recordset. ".$KT_FakeRecordset->getError());
			} else {
				die("Internal error: cannot create fake recordset.");
			}
			
		}
		return $ret;
    }
    /**
	 * Getter. Gets total records  
	 * @return int
	 * @access private
	 */
    function getTotalRecords() {
    	return $this->totalNo;	
    }
    /**
	 * Getter. Gets total records  
	 * @return int
	 * @access private
	 */
    function RecordCount() {
    	return $this->totalNo;	
    }
    /**
	 * Sort the fields and return the new order
	 * @param array  
	 * @return none
	 * @access private
	 */
    function Sort(&$arrKeys) {
    	$tmpArr = array(); 
    	if ($this->orderField == "name" || $this->orderField == "extension") {
    		$sortOrder = SORT_STRING;
    	} else{
    		$sortOrder = SORT_NUMERIC;
    	}
    	if ($this->orderDir == 'asc') {
    		asort($arrKeys, $sortOrder);
    	} else {
    		arsort($arrKeys,$sortOrder);
    	}
    	foreach($arrKeys as $i=>$value) {
    		$tmpArr[] =$this->filesArr[$i];
    	}
    	$this->filesArr = $tmpArr;
    }
    /**
	 * Format the receiving data in order to be used with kt fake recordset
	 * @param array 
	 * @return array
	 * @access private
	 */
    function formatData(&$arr) {
    	$arrTmp = array();
    	reset ($arr);
    	foreach ($arr as $i=>$arrVal) {
    		foreach ($arrVal as $j=>$val) {
    			$arrTmp[$j][$i] = $val;
    		}
    	}
    	return $arrTmp;
    }
    /**
	 * Getter. Gets error string
	 * @return string
	 * @access public
	 */
    function getError() {
    	return $this->error;	
    }
}
?>