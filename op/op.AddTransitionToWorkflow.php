<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
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
if(!checkFormKey('addtransitiontoworkflow')) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_request_token"));
}

if (!isset($_POST["workflow"]) || !is_numeric($_POST["workflow"])) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_id"));
}
$workflow = $dms->getWorkflow($_POST["workflow"]);
if (!is_object($workflow)) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_id"));
}

if (!isset($_POST["state"]) || !is_numeric($_POST["state"])) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_state_id"));
}
$state = $dms->getWorkflowState($_POST['state']);
if (!is_object($state)) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_state_id"));
}

if (!isset($_POST["nextstate"]) || !is_numeric($_POST["nextstate"])) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_state_id"));
}
$nextstate = $dms->getWorkflowState($_POST['nextstate']);
if (!is_object($nextstate)) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_state_id"));
}

if($state->getID() == $nextstate->getID()) {
	UI::exitError(getMLText("workflow_title"),getMLText("equal_transition_states"));
}

if (!isset($_POST["action"]) || !is_numeric($_POST["action"])) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_action_id"));
}
$action = $dms->getWorkflowAction($_POST['action']);
if (!is_object($action)) {
	UI::exitError(getMLText("workflow_title"),getMLText("invalid_workflow_action_id"));
}

$users = array();
if (isset($_POST["users"]) && is_array($_POST["users"])) {
	foreach($_POST["users"] as $userid) {
		$users[] = $dms->getUser($userid);
	}
}

$groups = array();
if (isset($_POST["groups"]) && is_array($_POST["groups"])) {
	foreach($_POST["groups"] as $groupid) {
		$groups[] = $dms->getGroup($groupid);
	}
}

if(!$users && !$groups) {
	UI::exitError(getMLText("workflow_title"),getMLText("missing_transition_user_group"));
}

if($transition = $workflow->addTransition($state, $action, $nextstate, $users, $groups)) {
}

add_log_line("?workflow=".$workflow->getID()."&transition".$transition->getID());

header("Location:../out/out.WorkflowMgr.php?workflowid=".$workflow->getID());
