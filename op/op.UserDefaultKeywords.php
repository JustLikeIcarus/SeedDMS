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
include("../inc/inc.Authentication.php");

if ($user->isGuest()) {
	UI::exitError(getMLText("edit_default_keywords"),getMLText("access_denied"));
}

$action = '';
if (isset($_REQUEST["action"])) {
	$action = $_REQUEST["action"];
}

/* Create new category ------------------------------------------------ */
if ($action == "addcategory") {

	if (isset($_REQUEST["name"]) && $_REQUEST["name"]) {
		$name = $_REQUEST["name"];
	
		$newCategory = $dms->addKeywordCategory($user->getID(), $name);
		if (!$newCategory) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
		}
		$categoryid=$newCategory->getID();
	} else {
		UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
	}
}


/* Delete category ---------------------------------------------------- */
else if ($action == "removecategory") {

	$categoryid = 0;
	if (isset($_REQUEST["categoryid"]) && $_REQUEST["categoryid"]) {
		$categoryid = intval($_REQUEST["categoryid"]);
	}
	$category = $dms->getKeywordCategory($categoryid);
	if (is_object($category)) {
		$owner    = $category->getOwner();
		if ($owner->getID() != $user->getID()) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("access_denied"));
		}
		if (!$category->remove()) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
		}
	} else {
		UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
	}
	$categoryid=-1;
}

/* Edit category: new name -------------------------------------------- */
else if ($action == "editcategory") {

	$categoryid = 0;
	if (isset($_REQUEST["categoryid"]) && $_REQUEST["categoryid"]) {
		$categoryid = intval($_REQUEST["categoryid"]);
	}
	$category = $dms->getKeywordCategory($categoryid);
	if (is_object($category)) {
		$owner = $category->getOwner();
		if ($owner->getID() != $user->getID()) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("access_denied"));
		}
		if (isset($_REQUEST["name"]) && $_REQUEST["name"]) {
			$name = $_REQUEST["name"];

			if (!$category->setName($name)) {
				UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
			}
		} else {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
		}
	}
	else UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
}

/* Edit category: new keyword list ----------------------------------- */
else if ($action == "newkeywords") {

	$categoryid = 0;
	if (isset($_REQUEST["categoryid"]) && $_REQUEST["categoryid"]) {
		$categoryid = intval($_REQUEST["categoryid"]);
	}
	$category = $dms->getKeywordCategory($categoryid);
	if (is_object($category)) {
		$owner    = $category->getOwner();
		if ($owner->getID() != $user->getID()) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("access_denied"));
		}

		$keywords = $_REQUEST["keywords"];
		if (!$category->addKeywordList($keywords)) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
		}
	}
	else UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
}

/* Edit category: edit keyword list ----------------------------------*/
else if ($action == "editkeywords") {

	$categoryid = 0;
	if (isset($_REQUEST["categoryid"]) && $_REQUEST["categoryid"]) {
		$categoryid = intval($_REQUEST["categoryid"]);
	}
	
	$category = $dms->getKeywordCategory($categoryid);
	if (is_object($category)) {
		$owner = $category->getOwner();
		if ($owner->getID() != $user->getID()) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("access_denied"));
		}

		$keywordsid = intval($_REQUEST["keywordsid"]);
		if (!is_numeric($keywordsid)) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("unknown_keyword_category"));
		}
		
		if (!$category->editKeywordList($keywordsid, $_REQUEST["keywords"])) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
		}
	}
	else UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
}

/* Edit category: delete keyword list -------------------------------- */
else if ($action == "removekeywords") {

	$categoryid = 0;
	if (isset($_REQUEST["categoryid"]) && $_REQUEST["categoryid"]) {
		$categoryid = intval($_REQUEST["categoryid"]);
	}
	$category = $dms->getKeywordCategory($categoryid);
	if (is_object($category)) {
		$owner    = $category->getOwner();
		if ($owner->getID() != $user->getID()) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("access_denied"));
		}
		$keywordsid = intval($_REQUEST["keywordsid"]);
		if (!is_numeric($keywordsid)) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("unknown_keyword_category"));
		}
		if (!$category->removeKeywordList($keywordsid)) {
			UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
		}
	}
	else UI::exitError(getMLText("personal_default_keywords"),getMLText("error_occured"));
}

header("Location:../out/out.UserDefaultKeywords.php?categoryid=".$categoryid);

?>
