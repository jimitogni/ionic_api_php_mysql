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
 * This the tNG_DeleteFolder trigger; 
 * Only for PRO version
 * Delete the related folder.
 * @access public
 */ 
class tNG_DeleteFolder {
	/**
	 * The tNG object
	 * @var object tNG
	 * @access public
	 */
	var $tNG;
	/**
	 * base folder
	 * @var string
	 * @access public
	 */
	var $baseFolder;
	/**
	 * folder to be deleted inside baseFolder;
	 * @var string
	 * @access public
	 */
	var $folder;
	/**
	 * full name folder
	 * @var string
	 * @access public
	 */
        var $fullFolder;

        /**
         * This indicates if all placeholders have been replaced.
         */
        var $validReplacement;

	/**
         * Constructor. Sets the reference to the transaction in which the trigger is used.
         * @param object tNG &$tNG reference to transaction object
         * @access public
         */
        function tNG_DeleteFolder(&$tNG) {
          $this->tNG = &$tNG;
          $this->folder = '';
          $this->validReplacement = true;
        }
        
        /**
         * Setter. Sets base folder
         * @param string 
         * @access public
         */
        function setBaseFolder($baseFolder) {
          $pos = strpos($baseFolder, '{');
          if ($pos !== false) {
            $this->validReplacement = $this->isValidReplacement($baseFolder);

            $this->folder = KT_DynamicData(substr($baseFolder, $pos), $this->tNG, '', false, array(), false);
            $this->baseFolder = substr($baseFolder, 0, $pos);
          } else {
            $this->folder = '';
            $this->baseFolder = $baseFolder;
          }
          $this->baseFolder = KT_realpath($baseFolder);
        }
        
        /**
         * Setter. Sets the dynamic part of the folder
         * @param string 
         * @access public
         */
        function setFolder($folder) {
          $this->validReplacement = $this->isValidReplacement($folder);

          $this->folder .= KT_DynamicData($folder, $this->tNG, '', false, array(), false);
        }

        /**
         * Method that checks if all dynamic data placeholders have been replaced.
         *
         * @param text  The string containing the dynamic data placeholders.
         * 
         * @returns true or false depending on whether all placeholders have been replaced.
         */
        function isValidReplacement($text) {
          if(preg_match_all('/\{([\w\d\.\s\(\)]+)\}/', $text, $matches)) {
            if(isset($matches[1]) && is_array($matches[1])) {
              foreach($matches[1] as $key=>$placeholder) {
                $value = KT_DynamicData('{' . $placeholder . '}', $this->tNG, '', false, array(), false);
                if(empty($value)) {
                  return false;
                }
              }
            }
          }

          return true;
        }
        
        /**
         * Main class methode
         * @return mixt null or error object in case of error;
         * @access public
         */
        function Execute() {
          if($this->validReplacement === false) {
            $ret = new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array($this->fullFolder, $this->baseFolder));
            return $ret;
          }

          $this->fullFolder = KT_realpath($this->baseFolder. $this->folder);
          // security
          if (substr($this->fullFolder, 0, strlen($this->baseFolder)) != $this->baseFolder) {
            $ret = new tNG_error("FOLDER_DEL_SECURITY_ERROR", array(), array($this->fullFolder, $this->baseFolder));
            return $ret;
          }
          
          $ret = null;
          if (!file_exists($this->fullFolder)) {
            return $ret;	
          }
          $folder = new KT_Folder();
          // delete thumbnails;
          $folder->deleteFolderNR($this->fullFolder);
          if ($folder->hasError()) {
            $arr = $folder->getError();
            $ret = new tNG_error("FOLDER_DEL_ERROR", array($arr[0]), array($arr[1]));
            return $ret;
          }
          
          return $ret;	
    }	
}
?>