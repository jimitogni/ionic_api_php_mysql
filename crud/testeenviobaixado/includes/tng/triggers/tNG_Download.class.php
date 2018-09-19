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
 * Handle the file download.
 * Only for PRO version
 * @access public
 */
class tNG_Download {
	/**
	 * tng_error object
	 * @var object
	 * @access public
	 */
	var $error;
	/**
	 * relpath to site root
	 * @var string
	 * @access public
	 */
	var $relPath;
	/**
	 * object starter
	 * @var string
	 * @access public
	 */
	var $reference;
	/**
	 * adodb connection object
	 * @var object
	 * @access public
	 */
	var $conn;
	/**
	 * adodb connection string name
	 * @var object
	 * @access public
	 */
	var $connName;
	/**
	 * folder
	 * @var string
	 * @access public
	 */
	var $folder = '';
	/**
	 * filename
	 * @var string
	 * @access public
	 */
	var $renameRule = '';
	/**
	 * hash with the prepared values: filename, folder, pk value ...
	 * retrieved from session
	 * @var array
	 * @access public
	 */
	var $downloadHash = array();
	/**
	 * table name
	 * @var string
	 * @access public
	 */
	var $table;
	/**
	 * hash with primary key values
	 * @var array
	 * @access public
	 */
	var $pk = array();
	/**
	 * counter field in the same table
	 * @var string
	 * @access public
	 */
	var $counterField;
	 /**
	 * may to many table name
	 * @var string
	 * @access public
	 */
	var $tableMtm;
	/**
	 * hash with primary key information (in fact the pk from table)
	 * @var array
	 * @access public
	 */
	var $pkMtm = array();
	 /**
	 * hash foreign key to the user
	 * @var array
	 * @access public
	 */
	var $fkMtm = array();
	/**
	 * counter for the many-to-many table
	 * @var string
	 * @access public
	 */
	var $counterFieldMtm;
	/**
	 * max counter value for many-to-many table
	 * @var string
	 * @access public
	 */
	var $maxCounterFieldMtm;
	/**
	 * max counter value for many-to-many table
	 * @var int
	 * @access public
	 */
	var $maxCounterValueMtm;
	/**
	 * flag if common properties were setted or not;
	 * @var boolean
	 * @access public
	 */
	var $isSetted;
	
	/**
	 * url for the back link
	 * @var string
	 * @access public
	 */
	var $backUri;
	
	/**
	 * base folder
	 * @var string
	 * @access private
	 */
	var $preparedFolder;
	
	/**
	 * Class contructor.
	 * @param string relpath to site root
	 * @param string reference of the starter
	 * @return nothing
	 * @access public
	 */
    function tNG_Download($relPath, $reference) {
    	$this->relPath = $relPath;
    	$this->reference = $reference;
    	$this->isSetted = false;
		$this->error = null;
    }
   /**
	 * Setter. Sets the connection object
	 * @param object
	 * @return nothing
	 * @access public
	 */
    function setConnection(&$conn, $name) {
    	$this->conn = &$conn;	
    	$this->connName = $name;	
    }
    /**
	 * Setter. Sets the folder (could be a dynamic expression)
	 * @param string folder
	 * @return nothing
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
	 * Setter. Sets the filename (could be a dynamic expression)
	 * @param string filename
	 * @return nothing
	 * @access public
	 */ 
    function setRenameRule($renameRule) {
    	$renameRule = KT_TransformToUrlPath($renameRule, false);
    	if ($this->renameRule == "") {
    		$this->renameRule = $renameRule;
    	} else {
    		$this->renameRule .= $renameRule;
    	}
    }
     /**
	 * Setter. Sets the table name
	 * @param string 
	 * @return nothing
	 * @access public
	 */ 
    function setTable($table) {
    	$this->table = $table;
    }
     /**
	 * Setter. Sets the primary key related information
	 * @param string field name
	 * @param string field type
	 * @param string field reference
	 * @return nothing
	 * @access public
	 */ 
    function setPrimaryKey($field, $type, $reference) {
    	$this->pk['field'] = $field;
    	$this->pk['type'] = $type;
    	$this->pk['value'] = '';
    	$this->pk['reference'] = $reference;
    }
     /**
	 * Setter. Sets the counter field name
	 * @param string 
	 * @return nothing
	 * @access public
	 */ 
    function setCounterField($counterField) {
    	$this->counterField = $counterField;	
    }
     /**
	 * Setter. Sets the table many to many
	 * @param string
	 * @return nothing
	 * @access public
	 */ 
    function setTable_MTM($tableMtm) {   	
    	$this->tableMtm = $tableMtm;
    }
     /**
	 * Setter. Sets the primary key related information to many-to-many table
	 * @param string field name
	 * @param string field type
	 * @param string field reference
	 * @return nothing
	 * @access public
	 */ 
    function setPrimaryKey_MTM($field, $type, $reference) {   	
    	$this->pkMtm['field'] = $field;
    	$this->pkMtm['type'] = $type;
    	$this->pkMtm['value'] = '';
    	$this->pkMtm['reference'] = $reference;
    }
     /**
	 * Setter. Sets user foreign key field name
	 * @param string 
	 * @return nothing
	 * @access public
	 */ 
    function setUserForeignKey_MTM($field) {   	
    	$this->fkMtm['field'] = $field;    	
    	$this->fkMtm['type'] = $GLOBALS['tNG_login_config']['pk_type'];    	
    }
     /**
	 * Setter. Sets the counter field for many-to-many
	 * @param string
	 * @return nothing
	 * @access public
	 */ 
    function setCounterField_MTM($field) {   	
    	$this->counterFieldMtm = $field; 
    }
    /**
	 * Setter. Sets the max counter field for many-to-many table
	 * @param string
	 * @return nothing
	 * @access public
	 */ 
    function setMaxCounterField_MTM($field) {   	
    	$this->maxCounterFieldMtm = $field; 
    }
    /**
	 * Setter. Sets the max counter entered value for many-to-many table
	 * @param string 
	 * @return nothing
	 * @access public
	 */ 
    function setMaxCounterValue_MTM($value) {   	
    	$this->maxCounterValueMtm = $value; 
    }
    /**
	 * main class method. Make the download or display the appropiate error.
	 * call the garbagecollector;
	 * @return nothing
	 * @access public
	 */ 
    function Execute() {
    	$this->garbageCollector();		
    	if (!$this->isDownload()) {
    		return;
    	}  
    	
		//check counter	
		if (!$this->checkCounter()) {
			return $this->error;
		}	
		
		$fileHash = $this->downloadHash;
		$folder =  $fileHash['folder'];
		if (substr($folder, 0, strlen($this->relPath))==$this->relPath) {
			$folder = substr($folder, strlen($this->relPath));
		}	
		$fileName = $fileHash['fileName'];
		$folder = KT_realpath('../../../' . $folder);
		$absPath = KT_realpath($folder . $fileName, false);
		
		$fd = @fopen($absPath, "rb");	
		if (!$fd || !is_file($absPath)) {
			$this->setError(new tNG_error('ERR_DOWNLOAD_FILE_NO_READ', array($fileName), array($absPath)));
			return $this->error;
		}
		
		//increment counter;
		$this->incrementCounter();
		if ($this->hasError()) {
			return $this->error;
		}
		
		$nameForDownload = KT_pathInfo($fileName);
		$mime_type = (function_exists('mime_content_type'))? mime_content_type($absPath): 'application/octet-stream';
		header('Content-type: '.$mime_type);
		header('Cache-control: private');
		header('Content-Length: ' . filesize($absPath));
		header('Content-disposition: attachment; filename="' . $nameForDownload['basename'] . '";');
		if (!$fd) {
			$this->setError(new tNG_error('ERR_DOWNLOAD_FILE_NO_READ', array($fileName), array($absPath)));
			return $this->error;
		}
		do { 
		   	echo fread($fd, 8192); 
		   	flush();
			usleep(10000);
		} while(!feof($fd) && connection_status()==0); 
		fclose($fd);
		exit;
    }
     /**
	 * Check the counter
	 * @return boolean true if the download can be done
	 * @access public
	 */ 
    function checkCounter() {
    	if (($this->maxCounterFieldMtm == '' && $this->maxCounterValueMtm == '') || $this->counterFieldMtm == '' || count($this->fkMtm)==0 || count($this->pkMtm)==0 || $this->maxCounterValueMtm == -1) {
			return true;
		}
		$fileHash = $this->downloadHash;
				
		if (!isset($fileHash['fkMtm']) || $fileHash['fkMtm'] == '') {
			$this->setError(new tNG_error('INCREMENTER_ERROR_FK', array(), array($this->fkMtm['field'])));	
			return null;
		}
		$this->fkMtm['value'] = $fileHash['fkMtm'];
		if (!isset($fileHash['pkMtm']) || $fileHash['pkMtm'] == '') {
			$this->setError(new tNG_error('INCREMENTER_ERROR_PK', array(), array($this->pkMtm['field'])));	
			return null;
		}
		$this->pkMtm['value'] = $fileHash['pkMtm'];
			
		$sql = 'SELECT '.KT_escapeFieldName($this->counterFieldMtm).' AS currval ';
		if ($this->maxCounterFieldMtm != '') {
			$sql .= ', '.KT_escapeFieldName($this->maxCounterFieldMtm).' AS maxval ';
		}
		$sql .= ' FROM '.$this->tableMtm.' WHERE '.KT_escapeFieldName($this->pkMtm['field']).' = '.KT_escapeForSql($this->pkMtm['value'], $this->pkMtm['type'], false);
		$sql .= ' AND '. KT_escapeFieldName($this->fkMtm['field']).' = '.KT_escapeForSql($this->fkMtm['value'], $this->fkMtm['type'], false);
		
		$rs = $this->conn->Execute($sql);
		if ($rs === false) {
			$this->setError(new tNG_error('CHECK_COUNTER_ERROR', array(), array($this->conn->ErrorMsg(), $sql)));
			return ;
		}
		$maxCounter = $this->maxCounterValueMtm;
		if ($this->maxCounterFieldMtm != '') {
			$maxCounter = (int)$rs->Fields('maxval');
			if ($maxCounter == -1) {
				return true;
			}
		}
		if ( (int)$rs->Fields('currval') >=  $maxCounter) {
			$this->setError(new tNG_error('CHECK_COUNTER_ERROR_MAX', array($maxCounter), array($maxCounter)));	
			return;		
		}
		return true;
    }
     /**
	 * Increment the counter
	 * @return nothing
	 * @access public
	 */ 
    function incrementCounter() {
    	// increment in the same table
		if ($this->table != '' && count($this->pk)>0 && $this->counterField != '') {
    		$fileHash = $this->downloadHash;
			$this->pk['value'] = $fileHash['pk'];
			
			$sql = 'UPDATE '.$this->table.' SET '.KT_escapeFieldName($this->counterField).' = '.KT_escapeFieldName($this->counterField).'+ 1 WHERE '.KT_escapeFieldName($this->pk['field']).' = '.KT_escapeForSql($this->pk['value'], $this->pk['type'], false);
    		$ret = $this->conn->Execute($sql);
			if ($ret === false) {
				$this->setError(new tNG_error('INCREMENTER_ERROR', array(), array($this->conn->ErrorMsg(), $sql)));
				return;
			}
    	}
    	// increment in the MTM table
    	if ($this->counterFieldMtm != '' && $this->tableMtm != '' && count($this->fkMtm) > 0 && count($this->pkMtm) > 0) {
    		$fileHash = $this->downloadHash;
    		if (!isset($fileHash['fkMtm']) || $fileHash['fkMtm'] == '') {
    			$this->setError(new tNG_error('INCREMENTER_ERROR_FK', array(), array($this->fkMtm['field'])));	
    			return;
    		}
			$this->fkMtm['value'] = $fileHash['fkMtm'];
    		if (!isset($fileHash['pkMtm']) || $fileHash['pkMtm'] == '') {
    			$this->setError(new tNG_error('INCREMENTER_ERROR_FK', array(), array($this->pkMtm['field'])));	
    			return;
    		}
			$this->pkMtm['value'] = $fileHash['pkMtm'];
			
			$sql = 'UPDATE '.$this->tableMtm.' SET '.KT_escapeFieldName($this->counterFieldMtm).' = '.KT_escapeFieldName($this->counterFieldMtm).'+ 1 WHERE '.KT_escapeFieldName($this->pkMtm['field']).' = '.KT_escapeForSql($this->pkMtm['value'], $this->pkMtm['type'], false) . ' AND ' . KT_escapeFieldName($this->fkMtm['field']).' = '.KT_escapeForSql($this->fkMtm['value'], $this->fkMtm['type'], false);
			$ret = $this->conn->Execute($sql);
			if ($ret === false) {
				$this->setError(new tNG_error('INCREMENTER_ERROR', array(), array($this->conn->ErrorMsg(), $sql)));		
				return;		
			}
    	}
		return null;
    }
    /**
	 * Prepare the hash values and store it in the session; Return the calculated link.
	 * @return string url for download the file
	 * @access public
	 */ 
    function getDownloadLink() {
      $this->preparedFolder = $this->folder;

      //security
      $fullFolderPath = KT_realpath($this->preparedFolder, true);
      $fullFilePath = KT_DynamicData($this->renameRule, null);
      $fullFilePath = KT_realpath($this->preparedFolder . $fullFilePath, true);
      if (substr($fullFilePath, 0, strlen($fullFolderPath)) != $fullFolderPath) {
        $this->setError(new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array($fullFolderPath, $fullFilePath)));
        return $this->relPath . 'includes/tng/pub/tNG_download4.php';
      }
    	$url = '';
	    if (!isset($_SESSION['tng_download'])) {
			$_SESSION['tng_download'] = array();
		}
		if (!isset($_SESSION['tng_download'][$this->reference])) {
			$_SESSION['tng_download'][$this->reference] = array();
		}
		// sets the common values;
		if (!$this->isSetted) {
			$_SESSION['tng_download'][$this->reference]['properties'] = array();
			$_SESSION['tng_download'][$this->reference]['properties']['time'] = time();
			$_SESSION['tng_download'][$this->reference]['properties']['table'] = $this->table;
			if (isset($this->pk['field']) && isset($this->pk['type'])) {
				$_SESSION['tng_download'][$this->reference]['properties']['pk_c'] = array('field'=>$this->pk['field'], 'type'=>$this->pk['type']);
			} else {
				$_SESSION['tng_download'][$this->reference]['properties']['pk_c'] = array();
			}
			$_SESSION['tng_download'][$this->reference]['properties']['counterField'] = $this->counterField;
			$_SESSION['tng_download'][$this->reference]['properties']['tableMtm'] = $this->tableMtm;
			if (isset($this->pkMtm['field']) && isset($this->pkMtm['type'])) {
				$_SESSION['tng_download'][$this->reference]['properties']['pkMtm_c'] = array('field'=>$this->pkMtm['field'], 'type'=>$this->pkMtm['type']);
			} else {
				$_SESSION['tng_download'][$this->reference]['properties']['pkMtm_c'] = array();
			}
			if (isset($this->fkMtm['field']) && isset($this->fkMtm['type'])) {
				$_SESSION['tng_download'][$this->reference]['properties']['fkMtm_c'] = array('field'=>$this->fkMtm['field'], 'type'=>$this->fkMtm['type']);
			} else {
				$_SESSION['tng_download'][$this->reference]['properties']['fkMtm_c'] = array();
			}
			$_SESSION['tng_download'][$this->reference]['properties']['counterFieldMtm'] = $this->counterFieldMtm;
			$_SESSION['tng_download'][$this->reference]['properties']['maxCounterFieldMtm'] = $this->maxCounterFieldMtm;
			$_SESSION['tng_download'][$this->reference]['properties']['maxCounterValueMtm'] = $this->maxCounterValueMtm;
			$_SESSION['tng_download'][$this->reference]['properties']['conn'] = $this->connName;
			$_SESSION['tng_download'][$this->reference]['properties']['relPath'] = $this->relPath;
			$_SESSION['tng_download'][$this->reference]['properties']['backUri'] = KT_getFullUri();
			$_SESSION['tng_download'][$this->reference]['files'] = array();
			$this->isSetted = true;
		}
		// set the class members in hash session to use in the download page;
		$hash = md5(uniqid("", true));
		
		$_SESSION['tng_download'][$this->reference]['files'][$hash] = array();
		$_SESSION['tng_download'][$this->reference]['files'][$hash]['folder'] = $this->preparedFolder;
		$_SESSION['tng_download'][$this->reference]['files'][$hash]['fileName'] = KT_DynamicData($this->renameRule, null);	
		if (isset($this->pk['reference']) && $this->pk['reference'] != '') {
			$_SESSION['tng_download'][$this->reference]['files'][$hash]['pk'] = KT_DynamicData($this->pk['reference'], null);	
		}
		if (isset($this->pkMtm['reference']) && $this->pkMtm['reference'] != '') {
			$_SESSION['tng_download'][$this->reference]['files'][$hash]['pkMtm'] = KT_DynamicData($this->pkMtm['reference'], null);	
		}
		if (isset($this->fkMtm['field']) && $this->fkMtm['field'] != '' && isset($_SESSION['kt_login_id'])) {
			$_SESSION['tng_download'][$this->reference]['files'][$hash]['fkMtm'] = $_SESSION['kt_login_id'];	
		}
			
		$url = $this->relPath . 'includes/tng/pub/tNG_download4.php';
		$arr = array();
		foreach ($_GET as $key=>$val) {
			if (!preg_match("/^KT_download/is", $key)) {
				$arr[] = $key .'='. $val;
			}
		}
		$url .= '?'. implode('&', $arr); 
		$url = KT_addReplaceParam($url, $this->reference, $hash);
		return $url;
    }
    /**
	 * Garbage collector. Clean the hash from session where the creation time is older than 5 minutes;
	 * @return nothing
	 * @access public
	 */ 
    function garbageCollector() {
    	if (!isset($_SESSION['tng_download'])) {
			return ;
		}
    	// clear old session values;
		foreach ($_SESSION['tng_download'] as $id => $hash) {
			if (isset($hash['properties']) && $hash['properties']['time'] < time()-60*5) {
				unset($_SESSION['tng_download'][$id]);
			}
		}	
    }
    
     /**
	 * Starter for the download operation. Check if we have a download
	 * @return boolean true if we have a download to serve;
	 * @access public
	 */ 
    function isDownload() {
    	$downloadID = KT_getRealValue('GET', $this->reference);
    	if (!isset($downloadID)) {
			return null;
		}
		if (!isset($_SESSION['tng_download'][$this->reference])) {
			return null;
		}
		if (!isset($_SESSION['tng_download'][$this->reference]['files'][$downloadID])) {
			return null;
		}
		// initialize the class members from Hash Session;
		$this->downloadHash = $_SESSION['tng_download'][$this->reference]['files'][$downloadID];
		$this->table = $_SESSION['tng_download'][$this->reference]['properties']['table'];
		$this->pk = $_SESSION['tng_download'][$this->reference]['properties']['pk_c'];
		$this->counterField = $_SESSION['tng_download'][$this->reference]['properties']['counterField'];
		$this->tableMtm = $_SESSION['tng_download'][$this->reference]['properties']['tableMtm'];
		$this->pkMtm = $_SESSION['tng_download'][$this->reference]['properties']['pkMtm_c'];
		$this->fkMtm = $_SESSION['tng_download'][$this->reference]['properties']['fkMtm_c'];
		$this->counterFieldMtm = $_SESSION['tng_download'][$this->reference]['properties']['counterFieldMtm'];
		$this->maxCounterFieldMtm = $_SESSION['tng_download'][$this->reference]['properties']['maxCounterFieldMtm'];
		$this->maxCounterValueMtm = $_SESSION['tng_download'][$this->reference]['properties']['maxCounterValueMtm'];
		if ($_SESSION['tng_download'][$this->reference]['properties']['conn'] != '') {	
			require_once(dirname(__FILE__) . '/../../../Connections/' . $_SESSION['tng_download'][$this->reference]['properties']['conn'] . '.php');
			$this->conn = $$_SESSION['tng_download'][$this->reference]['properties']['conn'];	
			if (is_resource($this->conn)) {
	            $database = 'database_'.$_SESSION['tng_download'][$this->reference]['properties']['conn'];
                $this->conn = new KT_connection($$_SESSION['tng_download'][$this->reference]['properties']['conn'], $$database);
            }		
		}
		$this->relPath = $_SESSION['tng_download'][$this->reference]['properties']['relPath'];
		$this->backUri = $_SESSION['tng_download'][$this->reference]['properties']['backUri'];
		return true;
    }    
     /**
	 * Setter. Sets the tng error object 
	 * @param string folder
	 * @return nothing
	 * @access public
	 */ 
    function setError(&$objError) {
    	$this->error = &$objError;
    }
     /**
	 * Getter. Check if we have error or not
	 * @return boolean true if we have error
	 * @access public
	 */ 
    function hasError() {
    	if (is_object($this->error)) {
    		return true;
    	}
    	return false;
    }
}
?>