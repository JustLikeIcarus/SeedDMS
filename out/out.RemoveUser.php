<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010-2012 Uwe Steinmann
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

if (!isset($_GET["userid"]) || !is_numeric($_GET["userid"]) || intval($_GET["userid"])<1) {
	UI::exitError(getMLText("rm_user"),getMLText("invalid_user_id"));
}

$rmuser = $dms->getUser(intval($_GET["userid"]));
if (!is_object($rmuser)) {
	UI::exitError(getMLText("rm_user"),getMLText("invalid_user_id"));
}

if(in_array($rmuser->getID(), explode(',', $settings->_undelUserIds))) {
	UI::exitError(getMLText("rm_user"),getMLText("cannot_delete_user"));
}

if ($rmuser->getID()==$user->getID()) {
	UI::exitError(getMLText("rm_user"),getMLText("cannot_delete_yourself"));
}

$allusers = $dms->getAllUsers($settings->_sortUsersInList);

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'rmuser'=>$rmuser, 'allusers'=>$allusers));
if($view) {
	$view->show();
	exit;
}

?>
