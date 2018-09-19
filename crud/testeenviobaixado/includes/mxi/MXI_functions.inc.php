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
include a PHP page (containing HTML/HEAD/BODY and strips away the outer HTML
then eval the code
- overcome the SBs can't be applied on a separate page thing
**/
ob_start();
$GLOBALS['KT_MXI_parse_css'] = true;
$GLOBALS['KT_dir_depth'] = array();
$GLOBALS['KT_dir_depth'][] = array(
	'relpath'=> '', 'dir'=>getcwd().DIRECTORY_SEPARATOR, 'dirrelpath'=>''
);

function mxi_getBaseURL() {
	return KT_getUriFolder();
} 

function mxi_includes_start($fName) {
	if (!isset($GLOBALS['KT_REL_PATH'])) {
		$GLOBALS['KT_REL_PATH'] = "";
	}
	$tmpArr = array();
	$tmpArr['dirRelPath'] = dirname($fName)."/";
	if ($tmpArr['dirRelPath'] == "./") {
		$tmpArr['dirRelPath'] = "";
	}
	$tmpArr['relpath'] = KT_CanonizeRelPath($GLOBALS['KT_REL_PATH'].$tmpArr['dirRelPath']);
	$tmpArr['relpath'] = ($GLOBALS['KT_REL_PATH'].$tmpArr['dirRelPath']);
	$tmpArr['dir'] = getcwd().DIRECTORY_SEPARATOR;
	$GLOBALS['KT_dir_depth'][] = $tmpArr;
	$GLOBALS['KT_REL_PATH'] = $tmpArr['relpath'];
	if ($tmpArr['dirRelPath'] != "") {
		$chk = @chdir($tmpArr['dirRelPath']);
		if ($chk === false) {
			die(KT_getResource('PHP_CHDIR_FAILED', 'MXI', array($tmpArr['dirRelPath'])));
		}
	}
	ob_start();
	if (isset($GLOBALS['tNGs'])) {
		if (!isset($GLOBALS['arrTNGs'])) {
			$GLOBALS['arrTNGs'] = array();
		}
		$GLOBALS['arrTNGs'][] = $GLOBALS['tNGs'];
	}
}

function mxi_includes_end() {
    $content = ob_get_contents();
    ob_end_clean();
    $tmpArr = array_pop($GLOBALS['KT_dir_depth']);
    $GLOBALS['KT_REL_PATH'] = $tmpArr['relpath'];
   
    // dirty hack IIS
		$chk = true;
    if (substr($tmpArr['dir'],0,strlen($tmpArr['dir'])-1) !== realpath(dirname(__FILE__).'/../../')){
                $chk = chdir($tmpArr['dir']);
    } elseif (getcwd() !== realpath(dirname(__FILE__).'/../../')){
                chdir (realpath(dirname(__FILE__)));
                $chk = chdir ('../../');
    }
    if ($chk === false) {
        die(KT_getResource('PHP_CHDIR_FAILED', 'MXI', array($tmpArr['dirRelPath'])));
    }
    $content = mxi_ParseHtml($content, $tmpArr['relpath']);
    $GLOBALS['KT_REL_PATH'] = $GLOBALS['KT_dir_depth'][count($GLOBALS['KT_dir_depth'])-1]['relpath'];
    echo $content;
		if (isset($GLOBALS['arrTNGs']) && count($GLOBALS['arrTNGs']) > 0) {
			$GLOBALS['tNGs'] = array_pop($GLOBALS['arrTNGs']);
		}
}

function mxi_ParseHtml($text, $relPath) {
	if (!isset($GLOBALS['mxi_scripts_hash'])) {
		$GLOBALS['mxi_scripts_hash'] = array();
	}
	if (!isset($GLOBALS['mxi_links_hash'])) {
		$GLOBALS['mxi_links_hash'] = array();
	}
	if (!isset($GLOBALS['mxi_styles_hash'])) {
		$GLOBALS['mxi_styles_hash'] = array();
	}
	$ret = KT_transformsPaths($relPath, $text, false);

	// get the <body>
	$body = $ret;
	$body = preg_replace("/^[\w\W]*<body[^>]*>/i", "", $body);
	$body = preg_replace("/<\/body>[\w\W]*$/i", "", $body);

	// get the <head>
	$head = $ret;
	$head = preg_replace("/^[\w\W]*<head[^>]*>/i", "", $head);
	$head = preg_replace("/<\/head>[\w\W]*$/i", "", $head);
	
	$links = "";
	$styles = "";
	if (isset($GLOBALS['KT_MXI_parse_css']) && $GLOBALS['KT_MXI_parse_css'] == true) {
		// get the external CSS
		preg_match_all("/<link[^>]*>[\n\r]*/i", $head, $links);
		if (sizeof($links) == 1) {
			$links = $links[0];
			foreach ($links as $k => $link) {
				$md5_link = md5($link);
				if (in_array($md5_link, $GLOBALS['mxi_links_hash'])) {
					unset($links[$k]);
				} else {
					array_push($GLOBALS['mxi_links_hash'], $md5_link);
				}
			}
			$links = implode("", $links);
		}
		
		// get the inline CSS
		preg_match_all("/<style[^>]*>[\w\W]*?<\/style>[\n\r]*/i", $head, $styles);
		if (sizeof($styles) == 1) {
			$styles = $styles[0];
			foreach ($styles as $k => $style) {
				$md5_style = md5($style);
				if (in_array($md5_style, $GLOBALS['mxi_styles_hash'])) {
					unset($styles[$k]);
				} else {
					array_push($GLOBALS['mxi_styles_hash'], $md5_style);
				}
			}
			$styles = implode("", $styles);
		}
	}

	// get the JavaScripts
	preg_match_all("/<script[^>]*>[\w\W]*?<\/script>[\n\r]*/i", $head, $scripts);
	if (sizeof($scripts) == 1) {
		$scripts = $scripts[0];
		foreach ($scripts as $k => $script) {
			$md5_script = md5($script);
			if (in_array($md5_script, $GLOBALS['mxi_scripts_hash'])) {
				unset($scripts[$k]);
			} else {
				array_push($GLOBALS['mxi_scripts_hash'], $md5_script);
			}
		}
		$scripts = implode("", $scripts);
	} else {
		$scripts = "";
	}
	
	$ret = $links.$styles.$scripts.$body;
	return $ret;
}
?>