<?php
/**
 * Do authentication of users and session management
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Uwe Steinmann
 * @version    Release: @package_version@
 */

$refer=urlencode($_SERVER["REQUEST_URI"]);
if (!strncmp("/op", $refer, 3)) {
	$refer="";
}
if (!isset($_COOKIE["mydms_session"])) {
	header("Location: " . $settings->_httpRoot . "out/out.Login.php?referuri=".$refer);
	exit;
}

require_once("inc.Utils.php");
require_once("inc.ClassEmail.php");
require_once("inc.ClassSession.php");

/* Load session */
$dms_session = $_COOKIE["mydms_session"];
$session = new SeedDMS_Session($db);
if(!$resArr = $session->load($dms_session)) {
	setcookie("mydms_session", $dms_session, time()-3600, $settings->_httpRoot); //delete cookie
	header("Location: " . $settings->_httpRoot . "out/out.Login.php?referuri=".$refer);
	exit;
}

/* Load user data */
$user = $dms->getUser($resArr["userID"]);
if($user->isAdmin()) {
	if($resArr["su"]) {
		$user = $dms->getUser($resArr["su"]);
	}
}
if (!is_object($user)) {
	setcookie("mydms_session", $dms_session, time()-3600, $settings->_httpRoot); //delete cookie
	header("Location: " . $settings->_httpRoot . "out/out.Login.php?referuri=".$refer);
	exit;
}

$dms->setUser($user);
if($settings->_enableEmail) {
	$notifier = new SeedDMS_Email();
	$notifier->setSender($user);
} else {
	$notifier = null;
}

/* Include the language file as specified in the session. If that is not
 * available use the language from the settings
 */
/*
if(file_exists($settings->_rootDir . "languages/" . $resArr["language"] . "/lang.inc")) {
	include $settings->_rootDir . "languages/" . $resArr["language"] . "/lang.inc";
	$session->setLanguage($resArr["language"]);
} else {
	include $settings->_rootDir . "languages/" . $settings->_language . "/lang.inc";
	$session->setLanguage($settings->_language);
}
*/

$theme = $resArr["theme"];
if(file_exists($settings->_rootDir . "view/".$theme."/languages/" . $resArr["language"] . "/lang.inc")) {
	include $settings->_rootDir . "view/".$theme."/languages/" . $resArr["language"] . "/lang.inc";
}

/* Check if password needs to be changed because it expired. If it needs
 * to be changed redirect to out/out.ForcePasswordChange.php. Do this
 * check only if password expiration is turned on, we are not on the
 * page to change the password or the page that changes the password, and
 * it is not admin */

if (!$user->isAdmin()) {
	if($settings->_passwordExpiration > 0) {
		if(basename($_SERVER['SCRIPT_NAME']) != 'out.ForcePasswordChange.php' && basename($_SERVER['SCRIPT_NAME']) != 'op.EditUserData.php') {
			$pwdexp = $user->getPwdExpiration();
			if(substr($pwdexp, 0, 10) != '0000-00-00') {
				$pwdexpts = strtotime($pwdexp); // + $pwdexp*86400;
				if($pwdexpts > 0 && $pwdexpts < time()) {
					header("Location: ../out/out.ForcePasswordChange.php");
					exit;
				}
			}
		}
	}
}
?>
