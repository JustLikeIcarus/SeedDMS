<?php
//    MyDMS. Document Management System
//    Copyright (C) 2010 Matteo Lucarelli, 2011 Uwe Steinmann
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


/**
 * Check Update file
 */
if (file_exists("../inc/inc.Settings.old.php")) {
  echo "You can't install SeedDMS, unless you delete " . realpath("../inc/inc.Settings.old.php") . ".";
  exit;
}


/**
 * Check file for installation
 */
if (!file_exists("create_tables-innodb.sql")) {
  echo "Can't install SeedDMS, 'create_tables-innodb.sql' missing";
  exit;
}
if (!file_exists("create_tables-sqlite3.sql")) {
  echo "Can't install SeedDMS, 'create_tables-sqlite3.sql' missing";
  exit;
}
if (!file_exists("settings.xml.template_install")) {
  echo "Can't install SeedDMS, 'settings.xml.template_install' missing";
  exit;
}

/**
 * Functions
 */
function openDBConnection($settings) { /* {{{ */
	switch($settings->_dbDriver) {
		case 'mysql':
		case 'mysqli':
		case 'mysqlnd':
			$dsn = $settings->_dbDriver.":dbname=".$settings->_dbDatabase.";host=".$settings->_dbHostname;
			break;
		case 'sqlite':
			$dsn = $settings->_dbDriver.":".$settings->_dbDatabase;
			break;
	}
	$connTmp = new PDO($dsn, $settings->_dbUser, $settings->_dbPass);
	return $connTmp;
} /* }}} */

function printError($error) { /* {{{ */
	print "<div class=\"install_error\">";
	print "Error<br />";
	print $error;
	print "</div>";
} /* }}} */

function printWarning($error) { /* {{{ */
	print "<div class=\"install_warning\">";
	print "Warning<br />";
	print $error;
	print "</div>";
} /* }}} */

function printCheckError($resCheck) { /* {{{ */
	$hasError = false;
	foreach($resCheck as $keyRes => $paramRes) {
		if(isset($paramRes['type']) && $paramRes['type'] == 'error')
			$hasError = true;
		$errorMes = getMLText("settings_$keyRes"). " : " . getMLText("settings_".$paramRes["status"]);

		if (isset($paramRes["currentvalue"]))
			$errorMes .= "<br/> =&gt; " . getMLText("settings_currentvalue") . " : " . $paramRes["currentvalue"];
		if (isset($paramRes["suggestionvalue"]))
			$errorMes .= "<br/> =&gt; " . getMLText("settings_suggestionvalue") . " : " . $paramRes["suggestionvalue"];
		if (isset($paramRes["suggestion"]))
			$errorMes .= "<br/> =&gt; " . getMLText("settings_".$paramRes["suggestion"]);
		if (isset($paramRes["systemerror"]))
			$errorMes .= "<br/> =&gt; " . $paramRes["systemerror"];

		if(isset($paramRes['type']) && $paramRes['type'] == 'error')
			printError($errorMes);
		else
			printWarning($errorMes);
	}

	return $hasError;
} /* }}} */

function fileExistsInIncludePath($file) { /* {{{ */
	$paths = explode(PATH_SEPARATOR, get_include_path());
	$found = false;
	foreach($paths as $p) {
		$fullname = $p.DIRECTORY_SEPARATOR.$file;
		if(is_file($fullname)) {
			$found = $fullname;
			break;
		}
	}
	return $found;
} /* }}} */

/**
 * Load default settings + set
 */
define("SEEDDMS_INSTALL", "on");
define("SEEDDMS_VERSION", "4.3.1");

require_once('../inc/inc.ClassSettings.php');

$configDir = Settings::getConfigDir();

/**
 * Check if ENABLE_INSTALL_TOOL exists in config dir
 */
if (!$configDir) {
	echo "Fatal error! I could not even find a configuration directory.";
	exit;
}

if (!file_exists($configDir."/ENABLE_INSTALL_TOOL")) {
	echo "For installation of SeedDMS, you must create the file conf/ENABLE_INSTALL_TOOL";
	exit;
}

if (!file_exists($configDir."/settings.xml")) {
	if(!copy("settings.xml.template_install", $configDir."/settings.xml")) {
		echo "Could not create initial configuration file from template. Check directory permission of conf/.";
		exit;
	}
}

// Set folders settings
$settings = new Settings();
$settings->load($configDir."/settings.xml");

$rootDir = realpath ("..");
$rootDir = str_replace ("\\", "/" , $rootDir) . "/";
$installPath = realpath ("install.php");
$installPath = str_replace ("\\", "/" , $installPath);
$tmpToDel = str_replace ($rootDir, "" , $installPath);
$httpRoot = str_replace ($tmpToDel, "" , $_SERVER["REQUEST_URI"]);
do {
	$httpRoot = str_replace ("//", "/" , $httpRoot, $count);
} while ($count<>0);

if(!$settings->_rootDir)
	$settings->_rootDir = $rootDir;
//$settings->_coreDir = $settings->_rootDir;
//$settings->_luceneClassDir = $settings->_rootDir;
if(!$settings->_contentDir) {
	$settings->_contentDir = $settings->_rootDir . 'data/';
	$settings->_luceneDir = $settings->_rootDir . 'data/lucene/';
	$settings->_stagingDir = $settings->_rootDir . 'data/staging/';
}
$settings->_httpRoot = $httpRoot;

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

/**
 * Include GUI + Language
 */
$theme = "blue";
include("../inc/inc.Language.php");
include "../languages/en_GB/lang.inc";
include("../inc/inc.ClassUI.php");


UI::htmlStartPage("INSTALL");
UI::contentHeading("SeedDMS Installation for version ".SEEDDMS_VERSION);
UI::contentContainerStart();


/**
 * Show phpinfo
 */
if (isset($_GET['phpinfo'])) {
	echo '<a href="install.php">' . getMLText("back") . '</a>';
  phpinfo();
	UI::contentContainerEnd();
	UI::htmlEndPage();
  exit();
}

/**
 * check if ENABLE_INSTALL_TOOL shall be removed
 */
if (isset($_GET['disableinstall'])) { /* {{{ */
	if(file_exists($configDir."/ENABLE_INSTALL_TOOL")) {
		if(unlink($configDir."/ENABLE_INSTALL_TOOL")) {
			echo getMLText("settings_install_disabled");
			echo "<br/><br/>";
			echo '<a href="' . $httpRoot . '/out/out.Settings.php">' . getMLText("settings_more_settings") .'</a>';
		} else {
			echo getMLText("settings_cannot_disable");
			echo "<br/><br/>";
			echo '<a href="install.php">' . getMLText("back") . '</a>';
		}
	} else {
		echo getMLText("settings_cannot_disable");
		echo "<br/><br/>";
		echo '<a href="install.php">' . getMLText("back") . '</a>';
	}
	UI::contentContainerEnd();
	UI::htmlEndPage();
  exit();
} /* }}} */

/**
 * Check System
 */
if (printCheckError( $settings->checkSystem())) { /* {{{ */
	if (function_exists("apache_get_version")) {
  	echo "<br/>Apache version: " . apache_get_version();
	}

	echo "<br/>PHP version: " . phpversion();

	echo "<br/>PHP include path: " . ini_get('include_path');

	echo '<br/>';
	echo '<br/>';
	echo '<a href="' . $httpRoot . 'install/install.php">' . getMLText("refresh") . '</a>';
	echo ' - ';
	echo '<a href="' . $httpRoot . 'install/install.php?phpinfo">' . getMLText("version_info") . '</a>';

	exit;
} /* }}} */


if (isset($_POST["action"])) $action=$_POST["action"];
else if (isset($_GET["action"])) $action=$_GET["action"];
else $action=NULL;

$showform = true;
if ($action=="setSettings") {
	/**
	 * Get Parameters
	 */
	$settings->_rootDir = $_POST["rootDir"];
	$settings->_httpRoot = $_POST["httpRoot"];
	$settings->_contentDir = $_POST["contentDir"];
	$settings->_luceneDir = $_POST["luceneDir"];
	$settings->_stagingDir = $_POST["stagingDir"];
	$settings->_extraPath = $_POST["extraPath"];
	$settings->_dbDriver = $_POST["dbDriver"];
	$settings->_dbHostname = $_POST["dbHostname"];
	$settings->_dbDatabase = $_POST["dbDatabase"];
	$settings->_dbUser = $_POST["dbUser"];
	$settings->_dbPass = $_POST["dbPass"];
	$settings->_coreDir = $_POST["coreDir"];
	$settings->_luceneClassDir = $_POST["luceneClassDir"];

	if(isset($settings->_extraPath))
		ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

	/**
	 * Check Parameters, require version 3.3.x
	 */
	$hasError = printCheckError( $settings->check(substr(str_replace('.', '', SEEDDMS_VERSION), 0,2)));

	if (!$hasError) {
		// Create database
		if (isset($_POST["createDatabase"])) {
			$createOK = false;
			$errorMsg = "";

			$connTmp =openDBConnection($settings);
			if ($connTmp) {
				// read SQL file
				if ($settings->_dbDriver=="mysql")
					$queries = file_get_contents("create_tables-innodb.sql");
				elseif($settings->_dbDriver=="sqlite")
					$queries = file_get_contents("create_tables-sqlite3.sql");
				else
					die();

				// generate SQL query
				$queries = explode(";", $queries);

				// execute queries
				foreach($queries as $query) {
				// var_dump($query);
					$query = trim($query);
					if (!empty($query)) {
						$connTmp->exec($query);

						if ($connTmp->errorCode() != 0) {
							$errorMsg .= $connTmp->errorInfo() . "<br/>";
						}
					}
				}
			}

			// error ?
			if (empty($errorMsg))
				$createOK = true;

			$connTmp = null;

			// Show error
			if (!$createOK) {
				echo $errorMsg;
				$hasError = true;
			}
		} // create database

		if (!$hasError) {

			// Save settings
			$settings->save();

			$needsupdate = false;
			$connTmp =openDBConnection($settings);
			if ($connTmp) {
				$res = $connTmp->query('select * from tblVersion');
				if($res) {
					if($rec = $res->fetch(PDO::FETCH_ASSOC)) {
						$updatedirs = array();
						$d = dir(".");
						while (false !== ($entry = $d->read())) {
							if(preg_match('/update-([0-9.]*)/', $entry, $matches)) {
								$updatedirs[] = $matches[1];
							}
						}
						$d->close();

						echo "Your current database schema has version ".$rec['major'].'.'.$rec['minor'].'.'.$rec['subminor'].". Please run all (if any)<br />of the update scripts below in the listed order.<br /><br />";
						$connTmp = null;

						if($updatedirs) {
							asort($updatedirs);
							foreach($updatedirs as $updatedir) {
								if($updatedir > $rec['major'].'.'.$rec['minor'].'.'.$rec['subminor']) {
									$needsupdate = true;
									print "<h3>Database update to version ".$updatedir." needed</h3>";
									if(file_exists('update-'.$updatedir.'/update.txt')) {
										print "<p>Please read the comments on updating this version. <a href=\"update-".$updatedir."/update.txt\" target=\"_blank\">Read now</a></p>";
									}
									print "<p>Run the <a href=\"update.php?version=".$updatedir."\">update script</a>.</p>";
								}
							}
						} else {
							print "<p>Your current database is up to date.</p>";
						}
					}
					if(!$needsupdate) {
						echo getMLText("settings_install_success");
						echo "<br/><br/>";
						echo getMLText("settings_delete_install_folder");
						echo "<br/><br/>";
						echo '<a href="install.php?disableinstall=1">' . getMLText("settings_disable_install") . '</a>';
						echo "<br/><br/>";

						echo '<a href="' . $httpRoot . '/out/out.Settings.php">' . getMLText("settings_more_settings") .'</a>';
						$showform = false;
					}
				} else {
					print "<p>You does not seem to have a valid database. The table tblVersion is missing.</p>";
				}
			}
		}
	}

	// Back link
	echo '<br/>';
	echo '<br/>';
//	echo '<a href="' . $httpRoot . '/install/install.php">' . getMLText("back") . '</a>';

}

if($showform) {

	/**
	 * Set parameters
	 */
	?>
	<form action="install.php" method="post" enctype="multipart/form-data">
	<input type="Hidden" name="action" value="setSettings">
	    <table>
	      <!-- SETTINGS - SYSTEM - SERVER -->
	      <tr ><td><b> <?php printMLText("settings_Server");?></b></td> </tr>
	      <tr title="<?php printMLText("settings_rootDir_desc");?>">
	        <td><?php printMLText("settings_rootDir");?>:</td>
	        <td><input name="rootDir" value="<?php echo $settings->_rootDir ?>" size="100" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_httpRoot_desc");?>">
	        <td><?php printMLText("settings_httpRoot");?>:</td>
	        <td><input name="httpRoot" value="<?php echo $settings->_httpRoot ?>" size="100" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_contentDir_desc");?>">
	        <td><?php printMLText("settings_contentDir");?>:</td>
	        <td><input name="contentDir" value="<?php echo $settings->_contentDir ?>" size="100" style="background:yellow" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_luceneDir_desc");?>">
	        <td><?php printMLText("settings_luceneDir");?>:</td>
	        <td><input name="luceneDir" value="<?php echo $settings->_luceneDir ?>" size="100" style="background:yellow" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_stagingDir_desc");?>">
	        <td><?php printMLText("settings_stagingDir");?>:</td>
	        <td><input name="stagingDir" value="<?php echo $settings->_stagingDir ?>" size="100" style="background:yellow" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_coreDir_desc");?>">
	        <td><?php printMLText("settings_coreDir");?>:</td>
	        <td><input name="coreDir" value="<?php echo $settings->_coreDir ?>" size="100" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_luceneClassDir_desc");?>">
	        <td><?php printMLText("settings_luceneClassDir");?>:</td>
	        <td><input name="luceneClassDir" value="<?php echo $settings->_luceneClassDir ?>" size="100" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_extraPath_desc");?>">
	        <td><?php printMLText("settings_extraPath");?>:</td>
	        <td><input name="extraPath" value="<?php echo $settings->_extraPath ?>" size="100" /></td>
	      </tr>

	 	    <!-- SETTINGS - SYSTEM - DATABASE -->
	      <tr ><td><b> <?php printMLText("settings_Database");?></b></td> </tr>
	      <tr title="<?php printMLText("settings_dbDriver_desc");?>">
	        <td><?php printMLText("settings_dbDriver");?>:</td>
	        <td><input name="dbDriver" value="<?php echo $settings->_dbDriver ?>" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_dbHostname_desc");?>">
	        <td><?php printMLText("settings_dbHostname");?>:</td>
	        <td><input name="dbHostname" value="<?php echo $settings->_dbHostname ?>" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_dbDatabase_desc");?>">
	        <td><?php printMLText("settings_dbDatabase");?>:</td>
	        <td><input name="dbDatabase" value="<?php echo $settings->_dbDatabase ?>" style="background:yellow" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_dbUser_desc");?>">
	        <td><?php printMLText("settings_dbUser");?>:</td>
	        <td><input name="dbUser" value="<?php echo $settings->_dbUser ?>" style="background:yellow" /></td>
	      </tr>
	      <tr title="<?php printMLText("settings_dbPass_desc");?>">
	        <td><?php printMLText("settings_dbPass");?>:</td>
	        <td><input name="dbPass" value="<?php echo $settings->_dbPass ?>" type="password" style="background:yellow" /></td>
	      </tr>
	      <tr><td></td></tr>
	      <tr><td></td></tr>
	      <tr>
	        <td><?php printMLText("settings_createdatabase");?>:</td>
	        <td><input name="createDatabase" type="checkbox" style="background:yellow"/></td>
	      </tr>
	    </table>

	   <input type="Submit" value="<?php printMLText("apply");?>" />
	</form>
	<?php

}

/*

*/

// just remove info for web page installation
$settings->_printDisclaimer = false;
$settings->_footNote = false;
// end of the page
UI::contentContainerEnd();
UI::htmlEndPage();
?>
