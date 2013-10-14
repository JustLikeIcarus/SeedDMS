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

require_once("../inc/inc.Utils.php");
require_once('../inc/inc.ClassSettings.php');

$configDir = Settings::getConfigDir();
$settings = new Settings();
$settings->load($configDir."/settings.xml");

/**
 * Check if ENABLE_INSTALL_TOOL exists in config dir
 */
if (!file_exists($configDir."/ENABLE_INSTALL_TOOL")) {
	echo "For installation of SeedDMS, you must create the file conf/ENABLE_INSTALL_TOOL";
	exit;
}

$theme = "blue";
require_once("../inc/inc.Language.php");
require_once("../inc/inc.ClassUI.php");

UI::htmlStartPage('Database update');
UI::contentHeading("SeedDMS Installation for version ".$_GET['version']);
UI::contentContainerStart();

$sqlfile = "update.sql";
switch($settings->_dbDriver) {
	case 'mysql':
	case 'mysqli':
	case 'mysqlnd':
		$dsn = $settings->_dbDriver.":dbname=".$settings->_dbDatabase.";host=".$settings->_dbHostname;
		break;
	case 'sqlite':
		$dsn = $settings->_dbDriver.":".$settings->_dbDatabase;
		if(file_exists('update-'.$_GET['version'].'/update-sqlite3.sql'))
			$sqlfile = "update-sqlite3.sql";
		break;
}
$db = new PDO($dsn, $settings->_dbUser, $settings->_dbPass);
if (!$db) {
	die;
}

$errorMsg = '';
$res = $db->query('select * from tblVersion');
if($rec = $res->fetch(PDO::FETCH_ASSOC)) {
	if($_GET['version'] > $rec['major'].'.'.$rec['minor'].'.'.$rec['subminor']) {

		if(file_exists('update-'.$_GET['version'].'/'.$sqlfile)) {
			$queries = file_get_contents('update-'.$_GET['version'].'/'.$sqlfile);
			$queries = explode(";", $queries);

			// execute queries
			if($queries) {
				echo "<h3>Updating database schema</h3>";
				foreach($queries as $query) {
					$query = trim($query);
					if (!empty($query)) {
						echo $query."<br />";
						if(false === $db->exec($query)) {
							$e = $db->ErrorInfo();
							$errorMsg .= $e[2] . "<br/>";
						}
					}
				}
			}
		} else {
			echo "<p>SQL file for update missing!</p>";
		}
	} else {
		echo "<p>Database schema already up to date.</p>";
	}


	if(!$errorMsg) {
		if(file_exists('update-'.$_GET['version'].'/update.php')) {
			echo "<h3>Running update script</h3>";
			include('update-'.$_GET['version'].'/update.php');
		}
	} else {
		echo $errorMsg;
	}
	echo "<p><a href=\"install.php\">Go back to installation and recheck.</a></p>";
} else {
	echo "<p>Could not determine database schema version.</p>";
}
$db = null;

UI::contentContainerEnd();
UI::htmlEndPage();
?>
