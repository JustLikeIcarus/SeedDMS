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
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removearchive')) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
}

if (!isset($_POST["arkname"]) || !file_exists($settings->_contentDir.$_POST["arkname"]) ) {
	UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
}

if (!SeedDMS_Core_File::removeFile($settings->_contentDir.$_POST["arkname"])) {
	UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
}

add_log_line("?arkname=".$_POST["arkname"]);
header("Location:../out/out.BackupTools.php");

?>
