<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

/* deprecated! use SeedDMS_Core_File::format_filesize() instead */
function formatted_size($size_bytes) { /* {{{ */
	if ($size_bytes>1000000000) return number_format($size_bytes/1000000000,1,".","")." GBytes";
	else if ($size_bytes>1000000) return number_format($size_bytes/1000000,1,".","")." MBytes";
	else if ($size_bytes>1000) return number_format($size_bytes/1000,1,".","")." KBytes";
	return number_format($size_bytes,0,"","")." Bytes";
} /* }}} */

function getReadableDate($timestamp) {
	return date("d/m/Y", $timestamp);
}

function getLongReadableDate($timestamp) {
	return date("d/m/Y H:i", $timestamp);
}

function getReadableDuration($secs) {
	$s = "";
	foreach ( getReadableDurationArray($secs) as $k => $v ) {
		if ( $v ) $s .= $v." ".($v==1? substr($k,0,-1) : $k).", ";
	}

	return substr($s, 0, -2);
}

function getReadableDurationArray($secs) {
	$units = array(
					getMLText("weeks")   => 7*24*3600,
					getMLText("days")    =>   24*3600,
					getMLText("hours")   =>      3600,
					getMLText("minutes") =>        60,
					getMLText("seconds") =>         1,
	);

	foreach ( $units as &$unit ) {
		$quot  = intval($secs / $unit);
		$secs -= $quot * $unit;
		$unit  = $quot;
	}

	return $units;
}

//
// The original string sanitizer, kept for reference.
//function sanitizeString($string) {
//	$string = str_replace("'",  "&#0039;", $string);
//	$string = str_replace("--", "", $string);
//	$string = str_replace("<",  "&lt;", $string);
//	$string = str_replace(">",  "&gt;", $string);
//	$string = str_replace("/*", "", $string);
//	$string = str_replace("*/", "", $string);
//	$string = str_replace("\"", "&quot;", $string);
//	
//	return $string;
//}

/* Deprecated, do not use anymore */
function sanitizeString($string) { /* {{{ */

	$string = (string) $string;
	if (get_magic_quotes_gpc()) {
		$string = stripslashes($string);
	}

	// The following three are against sql injection. They are not
	// needed anymore because strings are quoted propperly when saved into
	// the database.
//	$string = str_replace("\\", "\\\\", $string);
//	$string = str_replace("--", "\-\-", $string);
//	$string = str_replace(";", "\;", $string);
	// Use HTML entities to represent the other characters that have special
	// meaning in SQL. These can be easily converted back to ASCII / UTF-8
	// with a decode function if need be.
	$string = str_replace("&", "&amp;", $string);
	$string = str_replace("%", "&#0037;", $string); // percent
	$string = str_replace("\"", "&quot;", $string); // double quote
	$string = str_replace("/*", "&#0047;&#0042;", $string); // start of comment
	$string = str_replace("*/", "&#0042;&#0047;", $string); // end of comment
	$string = str_replace("<", "&lt;", $string);
	$string = str_replace(">", "&gt;", $string);
	$string = str_replace("=", "&#0061;", $string);
	$string = str_replace(")", "&#0041;", $string);
	$string = str_replace("(", "&#0040;", $string);
	$string = str_replace("'", "&#0039;", $string);
	$string = str_replace("+", "&#0043;", $string);

	return trim($string);
} /* }}} */

/* Deprecated, do not use anymore, but keep it for upgrading
 * older versions
 */
function mydmsDecodeString($string) { /* {{{ */

	$string = (string)$string;

	$string = str_replace("&amp;", "&", $string);
	$string = str_replace("&#0037;", "%", $string); // percent
	$string = str_replace("&quot;", "\"", $string); // double quote
	$string = str_replace("&#0047;&#0042;", "/*", $string); // start of comment
	$string = str_replace("&#0042;&#0047;", "*/", $string); // end of comment
	$string = str_replace("&lt;", "<", $string);
	$string = str_replace("&gt;", ">", $string);
	$string = str_replace("&#0061;", "=", $string);
	$string = str_replace("&#0041;", ")", $string);
	$string = str_replace("&#0040;", "(", $string);
	$string = str_replace("&#0039;", "'", $string);
	$string = str_replace("&#0043;", "+", $string);

	return $string;
} /* }}} */

function createVersionigFile($document) { /* {{{ */
	global $settings, $dms;
	
	// if directory has been removed recreate it
	if (!file_exists($dms->contentDir . $document->getDir()))
		if (!SeedDMS_Core_File::makeDir($dms->contentDir . $document->getDir())) return false;
	
	$handle = fopen($dms->contentDir . $document->getDir() .$settings-> _versioningFileName , "wb");
	
	if (is_bool($handle)&&!$handle) return false;
	
	$tmp = $document->getName()." (ID ".$document->getID()."\n\n";
	fwrite($handle, $tmp);

	$owner = $document->getOwner();
	$tmp = getMLText("owner")." = ".$owner->getFullName()." <".$owner->getEmail().">\n";
	fwrite($handle, $tmp);
	
	$tmp = getMLText("creation_date")." = ".getLongReadableDate($document->getDate())."\n";
	fwrite($handle, $tmp);
	
	$latestContent = $document->getLatestContent();
	$tmp = "\n### ".getMLText("current_version")." ###\n\n";
	fwrite($handle, $tmp);
	
	$tmp = getMLText("version")." = ".$latestContent->getVersion()."\n";
	fwrite($handle, $tmp);	
	
	$tmp = getMLText("file")." = ".$latestContent->getOriginalFileName()." (".$latestContent->getMimeType().")\n";
	fwrite($handle, $tmp);
	
	$tmp = getMLText("comment")." = ". $latestContent->getComment()."\n";
	fwrite($handle, $tmp);
	
	$status = $latestContent->getStatus();
	$tmp = getMLText("status")." = ".getOverallStatusText($status["status"])."\n";
	fwrite($handle, $tmp);
	
	$reviewStatus = $latestContent->getReviewStatus();
	$tmp = "\n### ".getMLText("reviewers")." ###\n";
	fwrite($handle, $tmp);
	
	foreach ($reviewStatus as $r) {
	
		switch ($r["type"]) {
			case 0: // Reviewer is an individual.
				$required = $dms->getUser($r["required"]);
				if (!is_object($required)) $reqName = getMLText("unknown_user")." = ".$r["required"];
				else $reqName =  getMLText("user")." = ".$required->getFullName();
				break;
			case 1: // Reviewer is a group.
				$required = $dms->getGroup($r["required"]);
				if (!is_object($required)) $reqName = getMLText("unknown_group")." = ".$r["required"];
				else $reqName = getMLText("group")." = ".$required->getName();
				break;
		}

		$tmp = "\n".$reqName."\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("status")." = ".getReviewStatusText($r["status"])."\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("comment")." = ". $r["comment"]."\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("last_update")." = ".$r["date"]."\n";
		fwrite($handle, $tmp);

	}
	
	
	$approvalStatus = $latestContent->getApprovalStatus();
	$tmp = "\n### ".getMLText("approvers")." ###\n";
	fwrite($handle, $tmp);

	foreach ($approvalStatus as $r) {
	
		switch ($r["type"]) {
			case 0: // Reviewer is an individual.
				$required = $dms->getUser($r["required"]);
				if (!is_object($required)) $reqName = getMLText("unknown_user")." = ".$r["required"];
				else $reqName =  getMLText("user")." = ".$required->getFullName();
				break;
			case 1: // Reviewer is a group.
				$required = $dms->getGroup($r["required"]);
				if (!is_object($required)) $reqName = getMLText("unknown_group")." = ".$r["required"];
				else $reqName = getMLText("group")." = ".$required->getName();
				break;
		}

		$tmp = "\n".$reqName."\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("status")." = ".getApprovalStatusText($r["status"])."\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("comment")." = ". $r["comment"]."\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("last_update")." = ".$r["date"]."\n";
		fwrite($handle, $tmp);
	
	}

	$versions = $document->getContent();	
	$tmp = "\n### ".getMLText("previous_versions")." ###\n";
	fwrite($handle, $tmp);

	for ($i = count($versions)-2; $i >= 0; $i--){
	
		$version = $versions[$i];
		$status = $version->getStatus();		
		
		$tmp = "\n".getMLText("version")." = ".$version->getVersion()."\n";
		fwrite($handle, $tmp);	
		
		$tmp = getMLText("file")." = ".$version->getOriginalFileName()." (".$version->getMimeType().")\n";
		fwrite($handle, $tmp);
		
		$tmp = getMLText("comment")." = ". $version->getComment()."\n";
		fwrite($handle, $tmp);
		
		$status = $latestContent->getStatus();
		$tmp = getMLText("status")." = ".getOverallStatusText($status["status"])."\n";
		fwrite($handle, $tmp);
			
	}
	
	fclose($handle);
	return true;
} /* }}} */

// funcion by shalless at rubix dot net dot au (php.net)
function dskspace($dir) { /* {{{ */
   $s = stat($dir);
   $space = $s["blocks"]*512;
   if (is_dir($dir)) {
     $dh = opendir($dir);
     while (($file = readdir($dh)) !== false)
       if ($file != "." and $file != "..")
         $space += dskspace($dir."/".$file);
     closedir($dh);
   }
   return $space;
} /* }}} */

function add_log_line($msg="") { /* {{{ */
	global $logger, $user;

	if(!$logger) return;

	if($user)
		$logger->log($user->getLogin()." (".$_SERVER['REMOTE_ADDR'].") ".basename($_SERVER["REQUEST_URI"], ".php").$msg);
	else
		$logger->log("-- (".$_SERVER['REMOTE_ADDR'].") ".basename($_SERVER["REQUEST_URI"], ".php").$msg);
} /* }}} */

function _add_log_line($msg="") { /* {{{ */
	global $settings,$user;
	
	if ($settings->_logFileEnable!=TRUE) return;

	if ($settings->_logFileRotation=="h") $logname=date("YmdH", time());
	else if ($settings->_logFileRotation=="d") $logname=date("Ymd", time());
	else $logname=date("Ym", time());
	
	if($h = fopen($settings->_contentDir.$logname.".log", "a")) {
		fwrite($h,date("Y/m/d H:i", time())." ".$user->getLogin()." (".$_SERVER['REMOTE_ADDR'].") ".basename($_SERVER["REQUEST_URI"], ".php").$msg."\n");
		fclose($h);
	}
} /* }}} */

	function getFolderPathHTML($folder, $tagAll=false, $document=null) { /* {{{ */
		$path = $folder->getPath();
		$txtpath = "";
		for ($i = 0; $i < count($path); $i++) {
			if ($i +1 < count($path)) {
				$txtpath .= "<a href=\"../out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\">".
					htmlspecialchars($path[$i]->getName())."</a> / ";
			}
			else {
				$txtpath .= ($tagAll ? "<a href=\"../out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\">".
										 htmlspecialchars($path[$i]->getName())."</a>" : htmlspecialchars($path[$i]->getName()));
			}
		}
		if($document)
			$txtpath .= " / <a href=\"../out/out.ViewDocument.php?documentid=".$document->getId()."\">".htmlspecialchars($document->getName())."</a>";

		return $txtpath;
	} /* }}} */
	
function showtree() { /* {{{ */
	global $settings;
	
	if (isset($_GET["showtree"])) return intval($_GET["showtree"]);
	else if ($settings->_enableFolderTree==0) return 0;
	
	return 1;
} /* }}} */

/**
 * Create a unique key which is used for form validation to prevent
 * CSRF attacks. The key is added to a any form that has to be secured
 * as a hidden field. Once the form is submitted the key is compared
 * to the current key in the session and the request is only executed
 * if both are equal. The key is derived from the session id, a configurable
 * encryption key and form identifierer.
 *
 * @param string $formid individual form identifier
 * @return string session key
 */
function createFormKey($formid='') { /* {{{ */
	global $settings, $session;

	if($id = $session->getId()) {
		return md5($id.$settings->_encryptionKey.$formid);
	} else {
		return false;
	}
} /* }}} */

/**
 * Create a hidden field with the name 'formtoken' and set its value
 * to the key returned by createFormKey()
 *
 * @param string $formid individual form identifier
 * @return string input field for html formular
 */
function createHiddenFieldWithKey($formid='') { /* {{{ */
	return '<input type="hidden" name="formtoken" value="'.createFormKey($formid).'" />';	
} /* }}} */

/**
 * Check if the form key in the POST or GET request variable 'formtoken'
 * has the value of key returned by createFormKey(). Request to modify
 * data in the DMS should always use POST because it is harder to run
 * CSRF attacks using POST than GET.
 *
 * @param string $formid individual form identifier
 * @param string $method defines if the form data is pass via GET or
 * POST (default)
 * @return boolean true if key matches otherwise false
 */
function checkFormKey($formid='', $method='POST') { /* {{{ */
	switch($method) {
		case 'GET':
			if(isset($_GET['formtoken']) && $_GET['formtoken'] == createFormKey($formid))
				return true;
			break;
		default:
			if(isset($_POST['formtoken']) && $_POST['formtoken'] == createFormKey($formid))
				return true;
	}
	
	return false;
} /* }}} */

/**
 * Check disk usage of currently logged in user
 *
 * @return boolean/integer true if no quota is set, number of bytes until
 *         quota is reached. Negative values indicate a disk usage above quota.
 */
function checkQuota() { /* {{{ */
	global $settings, $dms, $user;

	$quota = 0;
	$uquota = $user->getQuota();
	if($uquota > 0)
		$quota = $uquota;
	elseif($settings->_quota > 0)
		$quota = $settings->_quota;

	if($quota == 0)
		return true;

	return ($quota - $user->getUsedDiskSpace());
} /* }}} */
?>
