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
include("../inc/inc.LogInit.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removefolderfiles')) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
}

function removeFolderFiles($folder) {
	global $dms;

	$documents = $folder->getDocuments();
	foreach ($documents as $document)
		SeedDMS_Core_File::removeDir($dms->contentDir . $document->getDir());

	$subFolders = $folder->getSubFolders();

	foreach ($subFolders as $folder)
		removeFolderFiles($folder);

	return true;
}


if (!isset($_POST["folderid"]) || !is_numeric($_POST["folderid"]) || intval($_POST["folderid"])<1) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_folder_id"));
}
$folderid = $_POST["folderid"];
$folder = $dms->getFolder($folderid);

if (!is_object($folder)) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_folder_id"));
}

if (!removeFolderFiles($folder)) {
	UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
}

add_log_line();

header("Location:../out/out.BackupTools.php");
?>
