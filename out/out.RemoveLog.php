<?php
//    MyDMS. Document Management System
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

include("../inc/inc.Settings.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (!isset($_GET["logname"])) {
	UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
}

if(isset($_GET["mode"]))
	$mode = $_GET["mode"];
else
	$mode = 'web';

/* $_GET["logname"] can be a single file name or an array of file names */
if(!is_array($_GET["logname"]))
	$lognames = array($_GET["logname"]);
else
	$lognames = $_GET["logname"];
foreach($lognames as $file) {
	if(!file_exists($settings->_contentDir.$file)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
	}
}

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'lognames'=>$lognames, 'mode'=>$mode));
if($view) {
	$view->show();
	exit;
}

?>
