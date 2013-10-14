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
include("../inc/inc.LogInit.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

/* Get the group and check if the currently logged in user is a manager */
$ismanager = false;
if($_REQUEST['groupid']) {
	$group = $dms->getGroup($_REQUEST['groupid']);

	$managers = $group->getManagers();
	foreach($managers as $manager) {
		if($manager->getId() == $user->getId()) {
			$ismanager = true;
			break;
		}
	}
}

if($ismanager) {
	$curuser = $dms->getUser($_REQUEST['userid']);
	$members = $group->getUsers();

	// Add user to group
	if ($_REQUEST['action'] == "add") {
		$curuser->joinGroup($group);
	}
	// Delete user from group
	elseif($_REQUEST['action'] == 'del') {
		$curuser->leaveGroup($group);
	}
} else {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

header("Location:../out/out.GroupView.php");

?>
