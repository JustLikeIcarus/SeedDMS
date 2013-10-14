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

// Create new keyword category ------------------------------------------
if ($action == "addcategory") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('addcategory')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	$name = $_POST["name"];
	if (is_object($dms->getKeywordCategoryByName($name, $user->getID()))) {
		UI::exitError(getMLText("admin_tools"),getMLText("keyword_exists"));
	}
	$newCategory = $dms->addKeywordCategory($user->getID(), $name);
	if (!$newCategory) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	$categoryid=$newCategory->getID();
}

// Delete keyword categorie ---------------------------------------------
else if ($action == "removecategory") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('removecategory')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["categoryid"]) || !is_numeric($_POST["categoryid"]) || intval($_POST["categoryid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}
	$categoryid = $_POST["categoryid"];
	$category = $dms->getKeywordCategory($categoryid);
	if (!is_object($category)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}

	$owner = $category->getOwner();
	if (!$owner->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}
	if (!$category->remove()) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
	$categoryid=-1;
}

// Modify keyword categorie: new name -----------------------------------
else if ($action == "editcategory") {

	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('editcategory')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["categoryid"]) || !is_numeric($_POST["categoryid"]) || intval($_POST["categoryid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}
	$categoryid = $_POST["categoryid"];
	$category = $dms->getKeywordCategory($categoryid);
	if (!is_object($category)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}

	$owner    = $category->getOwner();
	if (!$owner->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	$name = $_POST["name"];
	if (!$category->setName($name)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
}

// Modify keyword categorie: new list of keywords -----------------------
else if ($action == "newkeywords") {
	
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('newkeywords')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	$categoryid = (int) $_POST["categoryid"];
	$category = $dms->getKeywordCategory($categoryid);
	$owner    = $category->getOwner();
	if (!$owner->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	$keywords = $_POST["keywords"];
	if(trim($keywords)) {
		if (!$category->addKeywordList($keywords)) {
			UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
		}
	}
}

// Modify keyword categorie: modify list of keywords -------------------
else if ($action == "editkeywords")
{
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('editkeywords')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["categoryid"]) || !is_numeric($_POST["categoryid"]) || intval($_POST["categoryid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}
	$categoryid = $_POST["categoryid"];
	$category = $dms->getKeywordCategory($categoryid);
	if (!is_object($category)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));

	}

	$owner    = $category->getOwner();
	if (!$owner->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	if (!isset($_POST["keywordsid"]) || !is_numeric($_POST["keywordsid"]) || intval($_POST["keywordsid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
	}
	$keywordsid = $_POST["keywordsid"];

	$keywords = $_POST["keywords"];
	if (!$category->editKeywordList($keywordsid, $keywords)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
}

// Modify keyword categorie: delete list of keywords --------------------
else if ($action == "removekeywords") {
	
	/* Check if the form data comes for a trusted request */
	if(!checkFormKey('removekeywords')) {
		UI::exitError(getMLText("admin_tools"),getMLText("invalid_request_token"));
	}

	if (!isset($_POST["categoryid"]) || !is_numeric($_POST["categoryid"]) || intval($_POST["categoryid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}
	$categoryid = $_POST["categoryid"];
	$category = $dms->getKeywordCategory($categoryid);
	if (!is_object($category)) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_keyword_category"));
	}

	$owner    = $category->getOwner();
	if (!$owner->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	if (!isset($_POST["keywordsid"]) || !is_numeric($_POST["keywordsid"]) || intval($_POST["keywordsid"])<1) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
	}
	$keywordsid = $_POST["keywordsid"];

	if (!$category->removeKeywordList($keywordsid)) {
		UI::exitError(getMLText("admin_tools"),getMLText("error_occured"));
	}
}
else {
	UI::exitError(getMLText("admin_tools"),getMLText("unknown_command"));
}

header("Location:../out/out.DefaultKeywords.php?categoryid=".$categoryid);

?>
