<?php
include("../inc/inc.ClassSettings.php");

function usage() { /* {{{ */
	echo "Usage:\n";
	echo "  seeddms-createfolder [--config <file>] [-c <comment>] [-n <name>] [-s <sequence>] [-h] [-v] -F <parent id>\n";
	echo "\n";
	echo "Description:\n";
	echo "  This program creates a new folder in SeedDMS.\n";
	echo "\n";
	echo "Options:\n";
	echo "  -h, --help: print usage information and exit.\n";
	echo "  -v, --version: print version and exit.\n";
	echo "  --config: set alternative config file.\n";
	echo "  -F <parent id>: id of parent folder\n";
	echo "  -c <comment>: set comment for file\n";
	echo "  -n <name>: set name of the folder\n";
	echo "  -s <sequence>: set sequence of folder\n";
} /* }}} */

$version = "0.0.1";
$shortoptions = "F:c:s:n:hv";
$longoptions = array('help', 'version', 'config:');
if(false === ($options = getopt($shortoptions, $longoptions))) {
	usage();
	exit(0);
}

/* Print help and exit */
if(isset($options['h']) || isset($options['help'])) {
	usage();
	exit(0);
}

/* Print version and exit */
if(isset($options['v']) || isset($options['verѕion'])) {
	echo $version."\n";
	exit(0);
}

/* Set alternative config file */
if(isset($options['config'])) {
	$settings = new Settings($options['config']);
} else {
	$settings = new Settings();
}

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

require_once("SeedDMS/Core.php");

if(isset($options['F'])) {
	$folderid = (int) $options['F'];
} else {
	echo "Missing parent folder ID\n";
	usage();
	exit(1);
}

$comment = '';
if(isset($options['c'])) {
	$comment = $options['c'];
}

$sequence = 0;
if(isset($options['s'])) {
	$sequence = $options['s'];
}

$name = '';
if(isset($options['n'])) {
	$name = $options['n'];
}

$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");
//$db->_conn->debug = 1;

$dms = new SeedDMS_Core_DMS($db, $settings->_contentDir.$settings->_contentOffsetDir);
if(!$dms->checkVersion()) {
	echo "Database update needed.";
	exit;
}

$dms->setRootFolderID($settings->_rootFolderID);
$dms->setMaxDirID($settings->_maxDirID);
$dms->setEnableConverting($settings->_enableConverting);
$dms->setViewOnlineFileTypes($settings->_viewOnlineFileTypes);

/* Create a global user object */
$user = $dms->getUser(1);

$folder = $dms->getFolder($folderid);

if (!is_object($folder)) {
	echo "Could not find specified folder\n";
	exit(1);
}

if ($folder->getAccessMode($user) < M_READWRITE) {
	echo "Not sufficient access rights\n";
	exit(1);
}

if (!is_numeric($sequence)) {
	echo "Sequence must be numeric\n";
	exit(1);
}

$res = $folder->addSubFolder($name, $comment, $user, $sequence);

if (is_bool($res) && !$res) {
	echo "Could not add folder\n";
	exit(1);
}
?>

