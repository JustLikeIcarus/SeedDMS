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

$_tmp = dirname($_SERVER['SCRIPT_FILENAME']);
if(is_link($_tmp)) {
	$_arr = preg_split('/\//', $_tmp);
	array_pop($_arr);

	$_configDir = implode('/', $_arr).'/conf';
//	include(implode('/', $_arr)."/conf/conf.Settings.php");
} else {
//	include("../conf/conf.Settings.php");
}

// ----------------------------
// Update previous version <3.0
// ----------------------------
if (file_exists("../inc/inc.Settings.old.php")) {
	// Change class name
	$str = file_get_contents("../inc/inc.Settings.old.php");
	$str = str_replace("class Settings" , "class OLDSettingsOLD", $str);
	$str = str_replace("Settings()" , "OLDSettingsOLD()", $str);
	file_put_contents("../inc/inc.Settings.old.php", $str);

	include "inc.Settings.old.php";

	$settingsOLD = $settings;
} else {
	$settingsOLD = null;
}

require_once('inc.ClassSettings.php');
$settings = new Settings();
if(!defined("SEEDDMS_INSTALL") && file_exists(dirname($settings->_configFilePath)."/ENABLE_INSTALL_TOOL")) {
	die("SeedDMS won't run unless your remove the file ENABLE_INSTALL_TOOL from your configuration directory.");
}

// ----------------------------
// Update previous version <3.0
// ----------------------------
if (isset($settingsOLD)) {
	$class_vars = get_class_vars(get_class($settingsOLD));
	foreach ($class_vars as $name => $value) {
		if (property_exists ("Settings", $name))
			$settings->$name = $value;
	}

	$settings->save();
	echo "Update finish, you must delete " . realpath("../inc/inc.Settings.old.php") . " file";
	exit;
}

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

if(isset($settings->_maxExecutionTime))
	ini_set('max_execution_time', $settings->_maxExecutionTime);

?>
