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

include("../inc/inc.Settings.php");
include("../inc/inc.Utils.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.Authentication.php");
include("../inc/inc.ClassUI.php");

/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user));

if (!isset($_GET["folderid"]) || !is_numeric($_GET["folderid"]) || intval($_GET["folderid"])<1) {
	$folderid = $settings->_rootFolderID;
}
else {
	$folderid = intval($_GET["folderid"]);
}

$folder = $dms->getFolder($folderid);
if (!is_object($folder)) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

$showtree=showtree();

if (isset($_GET["orderby"]) && strlen($_GET["orderby"])==1 ) {
	$orderby=$_GET["orderby"];
} else $orderby="";

if ($folder->getAccessMode($user) < M_READ) {
	UI::exitError(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))),getMLText("access_denied"));
}

if($view) {
	$view->setParam('folder', $folder);
	$view->setParam('orderby', $orderby);
	$view->setParam('enableFolderTree', $settings->_enableFolderTree);
	$view->setParam('enableClipboard', $settings->_enableClipboard);
	$view->setParam('expandFolderTree', $settings->_expandFolderTree);
	$view->setParam('showtree', showtree());
	$view->setParam('cachedir', $settings->_cacheDir);
	$view->setParam('workflowmode', $settings->_workflowMode);
	$view->setParam('enableRecursiveCount', $settings->_enableRecursiveCount);
	$view->setParam('maxRecursiveCount', $settings->_maxRecursiveCount);
	$view->show();
	exit;
}

?>
