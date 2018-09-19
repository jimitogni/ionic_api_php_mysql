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

class MXI_Includes {
	var $urlParamName = 'mod';
	var $urlParamValue = '';
	var $pages = array();
	var $includedFile = NULL;
	
	function MXI_Includes($urlParamName = "") {
		$this->urlParamName = $urlParamName;
		$this->urlParamValue = '';
		$this->pages = array();
		$this->includedFile = NULL;
		
		if ($urlParamName != "" && isset($_GET[$urlParamName])) {
			$this->urlParamValue = $_GET[$urlParamName];
		} elseif ($urlParamName == "" && isset($_SERVER['PATH_INFO'])) {
			$this->urlParamValue = $_SERVER['PATH_INFO'];
		}
	}
	
	function IncludeStatic($url, $file, $title, $keywords, $description) {
		$this->pages[$url] = array(
				'file' => $file,
				'title' => $title,
				'keywords' => $keywords,
				'description' => $description
			);
	}
	
	function IncludeDynamicRecordset(&$rs, $urlField, $fileField, $titleField, $keywordsField, $descriptionField) {
		if (is_resource($rs)) {
			$localRs = new KT_Recordset($rs); 
		} else {
			$localRs = $rs;
		}
		$res_errorMsg = '';
		
		if ($localRs->EOF) {
			return;
		}
		
		
		$fieldCount = $localRs->FieldCount();
		$arr_fields = array();
		for ($i=0; $i < $fieldCount; $i++) {
			$meta = $localRs->FetchField($i);
			if ($meta) {
				$arr_fields[$meta->name] = $meta->name;
			}
		}
		if (!isset($arr_fields[$urlField])) {
			$res_errorMsg = KT_getResource('MISSING_FIELD', 'MXI', array($urlField));
		}
		if (!isset($arr_fields[$fileField])) {
			$res_errorMsg = KT_getResource('MISSING_FIELD', 'MXI', array($fileField));
		}
		if ($titleField != "") {
			if (!isset($arr_fields[$titleField])) {
				$res_errorMsg = KT_getResource('MISSING_FIELD', 'MXI', array($titleField));
			}
		}
		if ($keywordsField != "" ) {
			if (!isset($arr_fields[$keywordsField])) {
				$res_errorMsg = KT_getResource('MISSING_FIELD', 'MXI', array($keywordsField));
			}
		}
		if ($descriptionField != "") {
			if (!isset($arr_fields[$descriptionField])) {
				$res_errorMsg = KT_getResource('MISSING_FIELD', 'MXI', array($descriptionField));
			}
		}
		
		if ($res_errorMsg != '') {
			die($res_errorMsg);
		}
		
		while (!$localRs->EOF) {
			$url = $localRs->Fields($urlField);
			$file = $localRs->Fields($fileField);
			$title = "";
			if ($titleField != "") {
				$title = $localRs->Fields($titleField);
			}
			$keywords = "";
			if ($keywordsField != "") {
				$keywords = $localRs->Fields($keywordsField);
			}
			$description = "";
			if ($descriptionField != "") {
				$description = $localRs->Fields($descriptionField);
			}
			$this->IncludeStatic($url, $file, $title, $keywords, $description);
			$localRs->MoveNext();
		}
	}
	
	function IncludeDynamic(&$connection, $tableName, $urlField, $fileField, $titleField, $keywordsField, $descriptionField) {
		KT_setDbType($connection);
		$sql = "SELECT ".KT_escapeFieldName($urlField).",".KT_escapeFieldName($fileField);
		if ($titleField != "") {
			$sql .= ",".KT_escapeFieldName($titleField);
		}
		if ($keywordsField != "") {
			$sql .= ",".KT_escapeFieldName($keywordsField);
		}
		if ($descriptionField != "") {
			$sql .= ",".KT_escapeFieldName($descriptionField);
		}
		$sql .= " FROM " . $tableName;
		$localRs = $connection->Execute($sql);
		if (!$localRs) {
			$res_errorMsg = KT_getResource('SQL_ERROR', 'MXI', array($connection->ErrorMsg(), $sql));
			die($res_errorMsg);
		}
		$this->IncludeDynamicRecordset($localRs, $urlField, $fileField, $titleField, $keywordsField, $descriptionField);
	}
	
	function getKeywords() {
		$ret = "";
		if ($this->getCurrentInclude() !== NULL) {
			$ret = $this->pages[$this->urlParamValue]['keywords'];
		}
		return KT_escapeAttribute($ret);
	}
	
	function getDescription() {
		$ret = "";
		if ($this->getCurrentInclude() !== NULL) {
			$ret = $this->pages[$this->urlParamValue]['description'];
		}
		return KT_escapeAttribute($ret);
	}
	function getTitle() {
		$ret = "";
		if ($this->getCurrentInclude() !== NULL) {
			$ret = $this->pages[$this->urlParamValue]['title'];
		}
		return $ret;
	}
	
	function getCurrentInclude() {
		if ($this->includedFile === NULL) {
			$ret = NULL;
			if (isset($this->pages[$this->urlParamValue])) {
				$ret = $this->pages[$this->urlParamValue]['file'];
				if ($ret == "" || !file_exists($ret)) {
					$ret = NULL;
				}
			}
			if ($ret === NULL) {
				if ($this->urlParamName == "") {
					$param404 = "/404";
				} else {
					$param404 = "404";
				}
				if (isset($this->pages[$param404])) {
					if ($this->pages[$param404]['file'] != "" && file_exists($this->pages[$param404]['file'])) {
						$this->urlParamValue = $param404;
						$ret = $this->pages[$param404]['file'];
					}
				}
				
			}
			$this->includedFile = $ret;
		} else {
			$ret = $this->includedFile;
		}
		return $ret;
	}
}

?>