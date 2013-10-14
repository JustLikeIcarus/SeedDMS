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
if ($action == "addworkflowstate") {
	
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('addworkflowstate')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	$name    = $_POST["name"];
	$docstatus = $_POST["docstatus"];

	if (is_object($dms->getWorkflowStateByName($name))) {
		UI::exitError(getMLText("admin_tools"),getMLText("workflow_state_exists"));
	}

	$newWorkflowstate = $dms->addWorkflowState($name, $docstatus);
	if (!$newWorkflowstate) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}

	$workflowstateid = $newWorkflowstate->getID();
	add_log_line(".php&action=addworkflowstate&name=".$name);
}

// delete user ------------------------------------------------------------
else if ($action == "removeworkflowstate") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('removeworkflowstate')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (isset($_POST["workflowstateid"])) {
		$workflowstateid = $_POST["workflowstateid"];
	}

	if (!isset($workflowstateid) || !is_numeric($workflowstateid) || intval($workflowstateid)<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}

	$workflowStateToRemove = $dms->getWorkflowState($workflowstateid);
	if (!is_object($workflowStateToRemove)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}

	if (!$workflowStateToRemove->remove()) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
		
	add_log_line(".php&action=removeworkflowstate&workflowstateid=".$workflowstateid);
	
	$workflowstateid=-1;
}

// modify workflow ---------------------------------------------------------
else if ($action == "editworkflowstate") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('editworkflowstate')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["workflowstateid"]) || !is_numeric($_POST["workflowstateid"]) || intval($_POST["workflowstateid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}
	
	$workflowstateid=$_POST["workflowstateid"];
	$editedWorkflowState = $dms->getWorkflowState($workflowstateid);
	
	if (!is_object($editedWorkflowState)) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_workflow_id"));
	}
	
	$name = $_POST["name"];
	$docstatus = $_POST["docstatus"];
	
	if ($editedWorkflowState->getName() != $name)
		$editedWorkflowState->setName($name);
	if ($editedWorkflowState->getDocumentStatus() != $docstatus)
		$editedWorkflowState->setDocumentStatus($docstatus);
	
	add_log_line(".php&action=editworkflowstate&workflowstateid=".$workflow);

}
else UI::exitError(getMLText("admin_tools"),getMLText("unknown_command"));

header("Location:../out/out.WorkflowStatesMgr.php?workflowstateid=".$workflowstateid);

?>
