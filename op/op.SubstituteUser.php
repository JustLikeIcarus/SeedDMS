<?php
//    MyDMS. Document Management System
//    Copyright (C) 2013 Uwe Steinmann
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

if (!isset($_GET["userid"])) {
	UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
}

$session->setSu($_GET['userid']);

$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_substituted_user')));

add_log_line("?userid=".$_GET["userid"]);
header("Location: ../".(isset($settings->_siteDefaultPage) && strlen($settings->_siteDefaultPage)>0 ? $settings->_siteDefaultPage : "out/out.ViewFolder.php?folderid=".$settings->_rootFolderID));

?>
