<?php
include("../inc/inc.Settings.php");
include("Log.php");
include("webdav.php");

$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");
$db->getResult("set names 'utf8'");

$dms = new SeedDMS_Core_DMS($db, $settings->_contentDir.$settings->_contentOffsetDir);

if($settings->_logFileEnable) {
	if ($settings->_logFileRotation=="h") $logname=date("YmdH", time());
	else if ($settings->_logFileRotation=="d") $logname=date("Ymd", time());
	else $logname=date("Ym", time());
	$logname = $settings->_contentDir."webdav-".$logname.".log";
	$log = Log::factory('file', $logname);
} else {
	$log = null;
}

$server = new HTTP_WebDAV_Server_SeedDMS();
$server->ServeRequest($dms, $log);
//$files = array();
//$options = array('path'=>'/Test1/subdir', 'depth'=>1);
//echo $server->MKCOL(&$options);

?>
