<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2010-2013 Uwe Steinmann
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
include("../inc/inc.Utils.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");
include("../inc/inc.ClassPasswordStrength.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (isset($_POST["action"])) $action=$_POST["action"];
else $action=NULL;

// add new workflow ---------------------------------------------------------
if ($action == "addworkflow") {
	
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('addworkflow')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	$name = $_POST["name"];
	if (is_object($dms->getWorkflowByName($name))) {
		UI::exitError(getMLText("admin_tools"),getMLText("workflow_exists"));
	}

	if(isset($_POST["initstate"])) {
		$initstate = $_POST["initstate"];
		$state = $dms->getWorkflowState($initstate);
		if (!$state) {
			UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
		}
	} else {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}

	$newWorkflow = $dms->addWorkflow($name, $state);
	if (!$newWorkflow) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}

	$workflowid = $newWorkflow->getID();

	add_log_line(".php&action=addworkflow&name=".$name);
}

// delete workflow --------------------------------------------------------
else if ($action == "removeworkflow") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('removeworkflow')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (isset($_POST["workflowid"])) {
		$workflowid = $_POST["workflowid"];
	}

	if (!isset($workflowid) || !is_numeric($workflowid) || intval($workflowid)<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}

	$workflowToRemove = $dms->getWorkflow($workflowid);
	if (!is_object($workflowToRemove)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}

	if (!$workflowToRemove->remove()) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
		
	add_log_line(".php&action=removeworkflow&workflowid=".$workflowid);
	
	$workflowid=-1;
}

// modify workflow ---------------------------------------------------------
else if ($action == "editworkflow") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('editworkflow')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["workflowid"]) || !is_numeric($_POST["workflowid"]) || intval($_POST["workflowid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}
	
	$workflowid=$_POST["workflowid"];
	$editedWorkflow = $dms->getWorkflow($workflowid);
	
	if (!is_object($editedWorkflow)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}
	
	$name = $_POST["name"];
	$initstate = $_POST["initstate"];
	
	$state = $dms->getWorkflowState($initstate);
	if (!$state) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}

	if ($editedWorkflow->getName() != $name)
		$editedWorkflow->setName($name);
	if ($editedWorkflow->getInitState()->getID() != $state->getID())
		$editedWorkflow->setInitState($state);
	
	add_log_line(".php&action=editworkflow&workflowid=".$workflowid);

}
else UI::exitError(getMLText("admin_tools"),getMLText("unknown_command"));

header("Location:../out/out.WorkflowMgr.php?workflowid=".$workflowid);

?>
