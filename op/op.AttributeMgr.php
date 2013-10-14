<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005 Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2009-2012 Uwe Steinmann
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
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (isset($_POST["action"])) $action=$_POST["action"];
else $action=NULL;

// add new attribute definition ---------------------------------------------
if ($action == "addattrdef") {
	
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('addattrdef')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	$name = trim($_POST["name"]);
	$type = intval($_POST["type"]);
	$objtype = intval($_POST["objtype"]);
	if(isset($_POST["multiple"]))
		$multiple = trim($_POST["multiple"]);
	else
		$multiple = 0;
	$minvalues = intval($_POST["minvalues"]);
	$maxvalues = intval($_POST["maxvalues"]);
	$valueset = trim($_POST["valueset"]);
	$regex = trim($_POST["regex"]);

	if($name == '') {
		UI::exitError(getMLText("admin_tools"),getMLText("attrdef_noname"));
	}
	if (is_object($dms->getAttributeDefinitionByName($name))) {
		UI::exitError(getMLText("admin_tools"),getMLText("attrdef_exists"));
	}
	$newAttrdef = $dms->addAttributeDefinition($name, $objtype, $type, $multiple, $minvalues, $maxvalues, $valueset, $regex);
	if (!$newAttrdef) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	$attrdefid=$newAttrdef->getID();

	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_add_attribute')));

	add_log_line("&action=addattrdef&name=".$name);
}

// delet attribute definition -----------------------------------------------
else if ($action == "removeattrdef") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('removeattrdef')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["attrdefid"]) || !is_numeric($_POST["attrdefid"]) || intval($_POST["attrdefid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_attrdef"));
	}
	$attrdefid = $_POST["attrdefid"];
	$attrdef = $dms->getAttributeDefinition($attrdefid);
	if (!is_object($attrdef)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_attrdef"));
	}

	if (!$attrdef->remove()) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_rm_attribute')));

	add_log_line("&action=removeattrdef&attrdefid=".$attrdefid);

	$attrdefid=-1;
}

// edit attribute definition -----------------------------------------------
else if ($action == "editattrdef") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('editattrdef')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["attrdefid"]) || !is_numeric($_POST["attrdefid"]) || intval($_POST["attrdefid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_attrdef"));
	}
	$attrdefid = $_POST["attrdefid"];
	$attrdef = $dms->getAttributeDefinition($attrdefid);
	if (!is_object($attrdef)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_attrdef"));
	}

	$name = $_POST["name"];
	$type = intval($_POST["type"]);
	$objtype = intval($_POST["objtype"]);
	if(isset($_POST["multiple"]))
		$multiple = trim($_POST["multiple"]);
	else
		$multiple = 0;
	$minvalues = intval($_POST["minvalues"]);
	$maxvalues = intval($_POST["maxvalues"]);
	$valueset = trim($_POST["valueset"]);
	$regex = trim($_POST["regex"]);
	if (!$attrdef->setName($name)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setType($type)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setObjType($objtype)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setMultipleValues($multiple)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setMinValues($minvalues)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setMaxValues($maxvalues)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setValueSet($valueset)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	if (!$attrdef->setRegex($regex)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}

	$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_edit_attribute')));

	add_log_line("&action=editattrdef&attrdefid=".$attrdefid);
} else {
	UI::exitError(getMLText("admin_tools"),getMLText("unknown_command"));
}

header("Location:../out/out.AttributeMgr.php?attrdefid=".$attrdefid);

?>

