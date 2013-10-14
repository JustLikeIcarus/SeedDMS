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

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (isset($_POST["action"])) $action = $_POST["action"];
else $action = null;

// Create new group --------------------------------------------------------
if ($action == "addgroup") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('addgroup')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	$name = $_POST["name"];
	$comment = $_POST["comment"];

	if (is_object($dms->getGroupByName($name))) {
		UI::exitError(getMLText("admin_tools"),getMLText("group_exists"));
	}

	$newGroup = $dms->addGroup($name, $comment);
	if (!$newGroup) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	
	$groupid=$newGroup->getID();
	
	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_add_group')));

	add_log_line("&action=addgroup&name=".$name);
}

// Delete group -------------------------------------------------------------
else if ($action == "removegroup") {
	
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('removegroup')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["groupid"]) || !is_numeric($_POST["groupid"]) || intval($_POST["groupid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}
	
	$group = $dms->getGroup($_POST["groupid"]);
	if (!is_object($group)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}

	if (!$group->remove($user)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	
	$groupid = '';

	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_rm_group')));

	add_log_line("?groupid=".$_POST["groupid"]."&action=removegroup");
}

// Modifiy group ------------------------------------------------------------
else if ($action == "editgroup") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('editgroup')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["groupid"]) || !is_numeric($_POST["groupid"]) || intval($_POST["groupid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}
	
	$groupid=$_POST["groupid"];
	$group = $dms->getGroup($groupid);
	
	if (!is_object($group)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}
	
	$name = $_POST["name"];
	$comment = $_POST["comment"];

	if ($group->getName() != $name)
		$group->setName($name);
	if ($group->getComment() != $comment)
		$group->setComment($comment);
		
	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_edit_group')));

	add_log_line("?groupid=".$_POST["groupid"]."&action=editgroup");
}

// Add user to group --------------------------------------------------------
else if ($action == "addmember") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('addmember')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["groupid"]) || !is_numeric($_POST["groupid"]) || intval($_POST["groupid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}
	
	$groupid=$_POST["groupid"];
	$group = $dms->getGroup($groupid);
	
	if (!is_object($group)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}

	if (!isset($_POST["userid"]) || !is_numeric($_POST["userid"]) || intval($_POST["userid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_user_id"));
	}
	
	$newMember = $dms->getUser($_POST["userid"]);
	if (!is_object($newMember)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_user_id"));
	}

	if (!$group->isMember($newMember)){
		$group->addUser($newMember);
		if (isset($_POST["manager"])) $group->toggleManager($newMember);
	}
	
	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_add_group_member')));

	add_log_line("?groupid=".$groupid."&userid=".$_POST["userid"]."&action=addmember");
}

// Remove user from group --------------------------------------------------
else if ($action == "rmmember") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('rmmember')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["groupid"]) || !is_numeric($_POST["groupid"]) || intval($_POST["groupid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}
	
	$groupid=$_POST["groupid"];
	$group = $dms->getGroup($groupid);
	
	if (!is_object($group)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}

	if (!isset($_POST["userid"]) || !is_numeric($_POST["userid"]) || intval($_POST["userid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_user_id"));
	}
	
	$oldMember = $dms->getUser($_POST["userid"]);
	if (!is_object($oldMember)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_user_id"));
	}

	$group->removeUser($oldMember);
	
	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_rm_group_member')));

	add_log_line("?groupid=".$groupid."&userid=".$_POST["userid"]."&action=rmmember");
}

// toggle manager flag
else if ($action == "tmanager") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('tmanager')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["groupid"]) || !is_numeric($_POST["groupid"]) || intval($_POST["groupid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}
	
	$groupid=$_POST["groupid"];
	$group = $dms->getGroup($groupid);
	
	if (!is_object($group)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_group_id"));
	}

	if (!isset($_POST["userid"]) || !is_numeric($_POST["userid"]) || intval($_POST["userid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_user_id"));
	}
	
	$usertoedit = $dms->getUser($_POST["userid"]);
	if (!is_object($usertoedit)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_user_id"));
	}
	
	$group->toggleManager($usertoedit);
	
	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_toogle_group_manager')));

	add_log_line("?groupid=".$groupid."&userid=".$_POST["userid"]."&action=tmanager");
}

header("Location:../out/out.GroupMgr.php?groupid=".$groupid);

?>
