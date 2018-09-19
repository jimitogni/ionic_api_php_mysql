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
*	Create thumbnails
* Only for PRO version
* @access public
*/
class tNG_DynamicThumbnail {
	/**
	 * relpath to siteroot
	 * @var string
	 * @access public
	 */
	var $relpath;
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
	 * width to resize
	 * @var int
	 * @access public
	 */
	var $width;
	/**
	 * height to resize
	 * @var int
	 * @access public
	 */
	var $height;
	/**
	 * to keep the proportions or not
	 * @var boolean
	 * @access public
	 */
	var $keepProportion;
	/**
	 * path to watermarg image
	 * @var string
	 * @access public
	 */
	var $watermarkImage;
	/**
	 * id unique for thumbnail
	 * @var string
	 * @access public
	 */
	var $id;
	/**
	 * popup width
	 * @var int
	 * @access public
	 */
	var $popupWidth;
	/**
	 * popup height
	 * @var int
	 * @access public
	 */
	var $popupHeight;
	/**
	 * if the popup page has navigation
	 * @var boolean
	 * @access public
	 */
	var $popupNavigation;
	/**
	 * flag if common properties were setted or not;
	 * @var boolean
	 * @access public
	 */
	var $isSetted;
	/**
	 * flag if common properties were setted or not;
	 * @var boolean
	 * @access public
	 */
	var $fitToWindow;
	/**
	 * apply watermark on thumbnail;
	 * @var boolean
	 * @access public
	 */
	var $watermark;
	/**
	 * watermark alpha 0-100;
	 * @var integer
	 * @access public
	 */
	var $watermarkAlpha;
	/**
	 * alignment for watermark;
	 * @var array
	 * @access public
	 */
	var $watermarkAlignment;
	/**
	 * resiza mode for watermark;
	 * @var array
	 * @access public
	 */
	var $watermarkResize;
	/**
	 * apply watermark on popupimage;
	 * @var boolean
	 * @access public
	 */
	var $popupWatermark;
	/**
	 * current working folder (folder in which the calling script resides);
	 * @var string
	 * @access public
	 */
	var $currentfolder;
	/**
	 * Constructor. Sets the relative path to siteroot
	 * @param string
	 * @access public
	 */	
    function tNG_DynamicThumbnail($relpath, $id) {
    	$this->watermarkImage = '';
    	$this->relpath = $relpath;
    	$this->id = $id;
    	$this->folder = '';
    	$this->renameRule = '';
    	$this->popupNavigation = false;
    	$this->isSetted = false;
    	$this->fitToWindow = false;
    	$this->watermark = false;
    	$this->popupWatermark = false;
    	$this->watermarkAlpha = 20;
    	$this->watermarkResize = array();
    	$this->watermarkAlignment = array();
    	$this->currentfolder = KT_TransformToUrlPath(getcwd());
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
	 * Setter. Sets the resize arguments;
	 * @param int width
	 * @param int height
	 * @param boolean keep proportion
	 * @access public
	 */
    function setResize($width, $height, $keepProportion) {
    	$this->width = (int)$width;
    	$this->height = (int)$height;
    	$this->keepProportion = $keepProportion;
    }
    /**
	 * Setter. Apply watermark on thumbnail;
	 * @param boolean 
	 * @access public
	 */
    function setWatermark($watermark) {
    	$this->watermark = $watermark;
    }
    /**
	 * Setter. Sets watermark alpha;
	 * @param integer 
	 * @access public
	 */
    function setWatermarkAlpha($watermarkAlpha) {
    	$this->watermarkAlpha = $watermarkAlpha;
    }
    /**
	 * Setter. Sets alignments for watermark;
	 * @param string horizontal 
	 * @param string vertical
	 * @access public
	 */
    function setWatermarkAlignment($vertical, $horizontal) {
    	$this->watermarkAlignment['vertical'] = strtolower($vertical);
    	$this->watermarkAlignment['horizontal'] = strtolower($horizontal);
    }
    /**
	 * Setter. Sets watermark resize mode;
	 * @param string  none|stretch|resize
	 * @param string  width for resize
	 * @param string  height for resize
	 * @access public
	 */
    function setWatermarkResize($watermarkResize) {
    	$this->watermarkResize['mode'] = strtolower($watermarkResize);
    	$this->watermarkResize['width'] = 0;
    	$this->watermarkResize['height'] = 0;
    }
    /**
	 * Setter. Sets watermark resize mode;
	 * @param string  none|stretch|resize
	 * @param string  width for resize
	 * @param string  height for resize
	 * @access public
	 */
    function setWatermarkSize($width, $height) {
    	$this->watermarkResize['width'] = (int)$width;
    	$this->watermarkResize['height'] = (int)$height;
    }
    /**
	 * Setter. Apply watermark on popupimage;
	 * @param boolean 
	 * @access public
	 */
    function setPopupWatermark($popupWatermark) {
    	$this->popupWatermark = $popupWatermark;
    }
    /**
	 * Setter. Sets the popup size;
	 * @param int width
	 * @param int height
	 * @param boolean fit image to popup box 
	 * @access public
	 */
    function setPopupSize($popupWidth, $popupHeight, $fitToWindow) {
		// add to session to allow display popup image;
		if (!isset($_SESSION['tng_popup'])) {
			$_SESSION['tng_popup'] = array();
		}
		$_SESSION['tng_popup'][$this->id] = array();

    	$this->popupWidth = (int)$popupWidth;
    	$this->popupHeight = (int)$popupHeight;
    	$this->fitToWindow = $fitToWindow;
    }
    /**
	 * Setter. Sets if the navigation is used;
	 * @param string 
	 * @access public
	 */
    function setPopupNavigation($popupNavigation) {
    	$this->popupNavigation = $popupNavigation;
    }    
    /**
	 * Setter. Sets the watermark image to used;
	 * @param string 
	 * @access public
	 */
    function setWatermarkImage($watermarkImage) {
    	$this->watermarkImage = $watermarkImage;
    }
    
    /**
	 * Get the Link for the Image Popup
	 * @return string url for the popup page
	 * @access public
	 */	
	function getPopupLink() {
		$this->garbageCollector();
		$ret = "#";
		if ($this->popupWidth == '' || $this->popupHeight == '') {
			return $ret;
		}
		$fileName = $this->getFileName();
		if ($fileName !== false) {
			$folder = $this->folder;
			$folder = KT_TransformToUrlPath($folder, true);
			if (!isset($_SESSION['tng_popup'])) {
				$_SESSION['tng_popup'] = array();
			}
			if (!isset($_SESSION['tng_popup'][$this->id])) {
				$_SESSION['tng_popup'][$this->id] = array();
			}
			if (!isset($_SESSION['tng_popup'][$this->id]['files'])) {
				$_SESSION['tng_popup'][$this->id]['files'] = array();
			}
			
			$ret = $this->relpath . 'includes/tng/pub/popup_image.php?id=' . rawurlencode($this->id) . '&n=' . count($_SESSION['tng_popup'][$this->id]['files']);
			
			$siteRootFilename = $folder . $fileName;
			//$siteRootFilename = substr($siteRootFilename, strlen($this->relpath));
			$siteRootFilename = substr($this->currentfolder, strlen(KT_getSiteRoot().'/'))  . $siteRootFilename;
				
			if (!$this->isSetted) {	
				$arrP = array();
				if ($this->watermarkImage != '') {
					$arrP['watermark'] = KT_realpath($this->watermarkImage,false);
					$arrP['watermarkAlpha'] = $this->watermarkAlpha;
					$arrP['watermarkAlignment'] = $this->watermarkAlignment;
					$arrP['watermarkResize'] = $this->watermarkResize;
				}
				if ($this->popupWidth != '' && $this->popupHeight != '') {
					$arrP['popupwidth'] = $this->popupWidth;
					$arrP['popupheight'] = $this->popupHeight;
					$arrP['fitToWindow'] = $this->fitToWindow;
				}
				$arrP['popupNavigation'] = $this->popupNavigation;
				$arrP['popupWatermark'] = $this->popupWatermark;
				$arrP['time'] = time();
				$_SESSION['tng_popup'][$this->id]['properties'] = $arrP;
				$this->isSetted = true;
			}
			$_SESSION['tng_popup'][$this->id]['files'][]= array('fullfilename' => $siteRootFilename, 'filename' =>$fileName);
		}
		return $ret;
	}
    /**
	 * Garbage collector. Clean the hash from session where the creation time is older than 30 minutes;
	 * @return nothing
	 * @access public
	 */ 
    function garbageCollector() {
    	if (!isset($_SESSION['tng_popup'])) {
			return ;
		}
    	// clear old session values;
		foreach ($_SESSION['tng_popup'] as $id => $hash) {
			if (isset($hash['properties']) && $hash['properties']['time'] < time()-60*5) {
				unset($_SESSION['tng_popup'][$id]);
			}
		}	
    }
    /**
	 * Get the onclick action for the Image Popup
	 * @return string action for the popup page
	 * @access public
	 */		
	function getPopupAction() {
		if ($this->popupWidth == '' || $this->popupHeight == '') {
			return '';
		}
		$additional = '';
		if (!$this->fitToWindow) {
			$additional = ', scrollbars=yes';
		}
		$ret = "if (window.screen) { var extraPopUp = ',left=' + (screen.availHeight - ".$this->popupHeight.") / 2 + ',screenX=' + (screen.availHeight - ".$this->popupHeight.") / 2; extraPopUp += ',top=' + (screen.availHeight - ".$this->popupHeight.") / 2 + ',screenY=' + (screen.availHeight - ".$this->popupHeight.") / 2; } else { var extraPopUp = '' }; window.open(this.href, 'ImagePopup', 'width=".$this->popupWidth.", height=".$this->popupHeight.$additional."' + extraPopUp); return false;";
		if (!$this->getFileName()) {
			$ret = "return false;";
		}
		return $ret;
	}

	
	/**
	 * returns the relative filename, returns false if file does not exist
	 * @return mix filename or false
	 * @access public
	 */	
	function getFileName() {
		$ret = false;
		$relpath = $this->relpath;
		$folder = KT_TransformToUrlPath($this->folder);
		$fileName = KT_DynamicData($this->renameRule, null);
		$fileName = KT_TransformToUrlPath($fileName, false);
		$fullFileName = KT_realpath($folder . $fileName, false);
		// security
		$base = KT_realpath($folder, true);
		if (substr($fullFileName, 0, strlen($base)) != $base) {
			return false;
		}
		if(file_exists($fullFileName) && is_file($fullFileName)) {
			$ret = $fileName;
			if (substr($ret,0,1) == '/') {
				$ret = substr($ret,1);
			}
		}
		return $ret;
	}
	
    /**
	 * Main class method. Resize the image and apply the watermark;
	 * @return string error string or url to thumbnail
	 * @access public
	 */
    function Execute() {
    	$ret = "";
		$relpath = $this->relpath;
		$folder = KT_TransformToUrlPath($this->folder);
		$fileName = KT_DynamicData($this->renameRule, null);
		$fileName = KT_TransformToUrlPath($fileName, false);
		$fullFolder = KT_realpath($folder, true);
		$fullFileName = KT_realpath($fullFolder . $fileName, false);
		$path_info = KT_pathinfo($fullFileName);
        $thumbnailFolder = $path_info['dirname'] . '/thumbnails/';

		if (substr($fullFileName, 0, strlen($fullFolder)) != $fullFolder) {
			if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
				$baseFileName = dirname($fullFileName);
				$errorMsg = KT_getResource("FOLDER_DEL_SECURITY_ERROR_D", "tNG", Array($baseFileName, $fullFolder));
				$ret = $relpath . "includes/tng/styles/cannot_thumbnail.gif\" />" . $errorMsg . "<img style=\"display:none\" src=\"".$relpath."includes/tng/styles/cannot_thumbnail.gif";
			} else {
				$ret = $relpath . "includes/tng/styles/cannot_thumbnail.gif";
			}
		} else {
			if ($this->getFileName() !== false) {
				// make the resize
				$proportional = $this->keepProportion;
				$width = $this->width;
				$height = $this->height;
				if (!$this->watermark) {
					$thumbnailName = $path_info['filename'].'_'.$width.'x'.$height.(isset($path_info['extension'])?'.'.$path_info['extension']:'');
				} else {
					$hash = tNG_watermarkHash(KT_realpath($this->watermarkImage, false), $this->watermarkAlpha, $this->watermarkResize, $this->watermarkAlignment);
					$thumbnailName = $path_info['filename'].'_'.$width.'x'.$height.'_w_'.$hash.(isset($path_info['extension'])?'.'.$path_info['extension']:'');
				}
				$thumbnailFullName = $thumbnailFolder . $thumbnailName;
				if (!file_exists(KT_realpath($thumbnailFullName, false)) ) {
					$imageObj = new KT_image();
					$imageObj->setPreferedLib($GLOBALS['tNG_prefered_image_lib']);
					$imageObj->addCommand($GLOBALS['tNG_prefered_imagemagick_path']);
					$imageObj->thumbnail($fullFileName, $thumbnailFolder, $thumbnailName, (int)$width, (int)$height, $proportional);
					if ($imageObj->hasError()) {
						$errorArr = $imageObj->getError();
						if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
							$errMsg = $errorArr[1];
							$ret = $relpath . "includes/tng/styles/cannot_thumbnail.gif\" />".$errMsg."<img style=\"display:none\" src=\"".$relpath."includes/tng/styles/cannot_thumbnail.gif";
						} else {
							$ret = $relpath . "includes/tng/styles/cannot_thumbnail.gif";
						}
						return $ret;
					} else {
						// apply watermark
						if ($this->watermark) {
							// delete other watermarks for same picture
							tNG_deleteThumbnails($thumbnailFolder, $path_info['filename'].'_'.$width.'x'.$height, $hash);
							$imageObj = new KT_image();
							$imageObj->setPreferedLib($GLOBALS['tNG_prefered_image_lib']);
							$imageObj->addCommand($GLOBALS['tNG_prefered_imagemagick_path']);
							$imageObj->watermark($thumbnailFullName, $thumbnailFullName, KT_realpath($this->watermarkImage, false), $this->watermarkAlpha, $this->watermarkResize, $this->watermarkAlignment);
							if ($imageObj->hasError()) {
								@unlink($thumbnailFullName);
								$arrError = $imageObj->getError();
								$errObj = new tNG_error('IMG_WATERMARK', array(), array($arrError[1]));
								if ($GLOBALS['tNG_debug_mode'] == 'DEVELOPMENT') {
									$errMsg = $arrError[1];								
									$ret = $relpath . "includes/tng/styles/cannot_thumbnail.gif\" />".$errMsg."<img style=\"display:none\" src=\"".$relpath."includes/tng/styles/cannot_thumbnail.gif";
								} else {
									$ret = $relpath . "includes/tng/styles/cannot_thumbnail.gif";
								}
								
								return $ret;
							}
						}	
                                        }
                                        $thumbnailURL = $this->folder . KT_DynamicData($this->renameRule, null);
                                        $thumbnailURL = dirname($thumbnailURL) . "/thumbnails/" . $thumbnailName;
                                        $ret = KT_CanonizeRelPath($thumbnailURL);
					if (!$imageObj->hasError()) {
						//$ret .= '?' . md5(filectime($ret)); 
					}
                                } else {
                                  $thumbnailURL = $this->folder . KT_DynamicData($this->renameRule, null);
                                  $thumbnailURL = dirname($thumbnailURL) . "/thumbnails/" . $thumbnailName;
                                  $ret = KT_CanonizeRelPath($thumbnailURL);
				}
			} else {
				$ret = $relpath . "includes/tng/styles/img_not_found.gif";
			}
		}
		return $ret;
    }
    
}
?>