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
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!isset($_GET["folderid"]) || !is_numeric($_GET["folderid"]) || intval($_GET["folderid"])<1) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}
$folderid = $_GET["folderid"];
$folder = $dms->getFolder($folderid);
if (!is_object($folder)) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

if ($folder->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))),getMLText("access_denied"));
}

$remain = checkQuota();
if ($remain < 0) {
	UI::exitError(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))),getMLText("quota_exceeded", array('bytes'=>SeedDMS_Core_File::format_filesize(abs($remain)))));
}

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'folder'=>$folder, 'strictformcheck'=>$settings->_strictFormCheck, 'enablelargefileupload'=>$settings->_enableLargeFileUpload, 'enableadminrevapp'=>$settings->_enableAdminRevApp, 'enableownerrevapp'=>$settings->_enableOwnerRevApp, 'enableselfrevapp'=>$settings->_enableSelfRevApp, 'dropfolderdir'=>$settings->_dropFolderDir, 'workflowmode'=>$settings->_workflowMode));
if($view) {
	$view->show();
	exit;
}

?>
