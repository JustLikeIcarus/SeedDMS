<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2011 Uwe Steinmann
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
include("../inc/inc.Utils.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassAccessOperation.php");
include("../inc/inc.Authentication.php");

/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

function getTime() {
	if (function_exists('microtime')) {
		$tm = microtime();
		$tm = explode(' ', $tm);
		return (float) sprintf('%f', $tm[1] + $tm[0]);
	}
	return time();
}

// Redirect to the search page if the navigation search button has been
// selected without supplying any search terms.
if (isset($_GET["navBar"])) {
	if (!isset($_GET["folderid"]) || !is_numeric($_GET["folderid"]) || intval($_GET["folderid"])<1) {
		$folderid=$settings->_rootFolderID;
	} else {
		$folderid = $_GET["folderid"];
	}
	/*
	if(strlen($_GET["query"])==0) {
		header("Location: ../out/out.SearchForm.php?folderid=".$folderid);
	} else {
		if(isset($_GET["fullsearch"]) && $_GET["fullsearch"]) {
			header("Location: ../op/op.SearchFulltext.php?folderid=".$folderid."&query=".$_GET["query"]);
		}
	}
	*/
}

if(isset($_GET["fullsearch"]) && $_GET["fullsearch"]) {
// Search in Fulltext {{{
	if (isset($_GET["query"]) && is_string($_GET["query"])) {
		$query = $_GET["query"];
	}
	else {
		$query = "";
	}

	// category
	$categories = array();
	$categorynames = array();
	if(isset($_GET['categoryids']) && $_GET['categoryids']) {
		foreach($_GET['categoryids'] as $catid) {
			if($catid > 0) {
				$category = $dms->getDocumentCategory($catid);
				$categories[] = $category;
				$categorynames[] = $category->getName();
			}
		}
	}

	//
	// Get the page number to display. If the result set contains more than
	// 25 entries, it is displayed across multiple pages.
	//
	// This requires that a page number variable be used to track which page the
	// user is interested in, and an extra clause on the select statement.
	//
	// Default page to display is always one.
	$pageNumber=1;
	if (isset($_GET["pg"])) {
		if (is_numeric($_GET["pg"]) && $_GET["pg"]>0) {
			$pageNumber = (integer)$_GET["pg"];
		}
		else if (!strcasecmp($_GET["pg"], "all")) {
			$pageNumber = "all";
		}
	}


	// --------------- Suche starten --------------------------------------------

	// Check to see if the search has been restricted to a particular
	// document owner.
	$owner = null;
	if (isset($_GET["ownerid"]) && is_numeric($_GET["ownerid"]) && $_GET["ownerid"]!=-1) {
		$owner = $dms->getUser($_GET["ownerid"]);
		if (!is_object($owner)) {
			UI::exitError(getMLText("search_results"),getMLText("unknown_owner"));
		}
	}

	$pageNumber=1;
	if (isset($_GET["pg"])) {
		if (is_numeric($_GET["pg"]) && $_GET["pg"]>0) {
			$pageNumber = (integer)$_GET["pg"];
		}
		else if (!strcasecmp($_GET["pg"], "all")) {
			$pageNumber = "all";
		}
	}

	$startTime = getTime();
	if($settings->_enableFullSearch) {
		if(!empty($settings->_luceneClassDir))
			require_once($settings->_luceneClassDir.'/Lucene.php');
		else
			require_once('SeedDMS/Lucene.php');
	}

	Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
	if(strlen($query) < 4 && strpos($query, '*')) {
		$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('splash_invalid_searchterm')));
		$resArr = array();
		$resArr['totalDocs'] = 0;
		$resArr['totalFolders'] = 0;
		$resArr['totalPages'] = 0;
		$entries = array();
		$searchTime = 0;
	} else {
		$index = Zend_Search_Lucene::open($settings->_luceneDir);
		$lucenesearch = new SeedDMS_Lucene_Search($index);
		$hits = $lucenesearch->search($query, $owner ? $owner->getLogin() : '', '', $categorynames);
		if($hits === false) {
			$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('splash_invalid_searchterm')));
			$resArr = array();
			$resArr['totalDocs'] = 0;
			$resArr['totalFolders'] = 0;
			$resArr['totalPages'] = 0;
			$entries = array();
			$searchTime = 0;
		} else {
			$limit = 20;
			$resArr = array();
			$resArr['totalDocs'] = count($hits);
			$resArr['totalFolders'] = 0;
			if($pageNumber != 'all' && count($hits) > $limit) {
				$resArr['totalPages'] = (int) (count($hits) / $limit);
				if ((count($hits)%$limit) > 0)
					$resArr['totalPages']++;
				$hits = array_slice($hits, ($pageNumber-1)*$limit, $limit);
			} else {
				$resArr['totalPages'] = 1;
			}

			$entries = array();
			if($hits) {
				foreach($hits as $hit) {
					if($tmp = $dms->getDocument($hit['document_id'])) {
						if($tmp->getAccessMode($user) >= M_READ) {
							$entries[] = $tmp;
						}
					}
				}
			}
		}
		$searchTime = getTime() - $startTime;
		$searchTime = round($searchTime, 2);
	}
	// }}}
} else {
	// Search in Database {{{
	if (isset($_GET["query"]) && is_string($_GET["query"])) {
		$query = $_GET["query"];
	}
	else {
		$query = "";
	}

	/* Select if only documents (0x01), only folders (0x02) or both (0x03)
	 * are found
	 */
	$resultmode = 0x03;

	$mode = "AND";
	if (isset($_GET["mode"]) && is_numeric($_GET["mode"]) && $_GET["mode"]==0) {
			$mode = "OR";
	}

	$searchin = array();
	if (isset($_GET['searchin']) && is_array($_GET["searchin"])) {
		foreach ($_GET["searchin"] as $si) {
			if (isset($si) && is_numeric($si)) {
				switch ($si) {
					case 1: // keywords
					case 2: // name
					case 3: // comment
					case 4: // attributes
						$searchin[$si] = $si;
						break;
				}
			}
		}
	}

	// if none is checkd search all
	if (count($searchin)==0) $searchin=array(1, 2, 3, 4);

	// Check to see if the search has been restricted to a particular sub-tree in
	// the folder hierarchy.
	if (isset($_GET["targetidform1"]) && is_numeric($_GET["targetidform1"]) && $_GET["targetidform1"]>0) {
		$targetid = $_GET["targetidform1"];
		$startFolder = $dms->getFolder($targetid);
	}
	else {
		$targetid = $settings->_rootFolderID;
		$startFolder = $dms->getFolder($targetid);
	}
	if (!is_object($startFolder)) {
		UI::exitError(getMLText("search_results"),getMLText("invalid_folder_id"));
	}

	// Check to see if the search has been restricted to a particular
	// document owner.
	$owner = null;
	if (isset($_GET["ownerid"]) && is_numeric($_GET["ownerid"]) && $_GET["ownerid"]!=-1) {
		$owner = $dms->getUser($_GET["ownerid"]);
		if (!is_object($owner)) {
			UI::htmlStartPage(getMLText("search_results"));
			UI::contentContainer(getMLText("unknown_owner"));
			UI::htmlEndPage();
			exit;
		}
	}

	// Is the search restricted to documents created between two specific dates?
	$startdate = array();
	$stopdate = array();
	if (isset($_GET["creationdate"]) && $_GET["creationdate"]!=null) {
		$creationdate = true;
	} else {
		$creationdate = false;
	}

	if(isset($_GET["createstart"])) {
		$tmp = explode("-", $_GET["createstart"]);
		$startdate = array('year'=>(int)$tmp[2], 'month'=>(int)$tmp[1], 'day'=>(int)$tmp[0], 'hour'=>0, 'minute'=>0, 'second'=>0);
	} else {
		if(isset($_GET["createstartyear"]))
			$startdate = array('year'=>$_GET["createstartyear"], 'month'=>$_GET["createstartmonth"], 'day'=>$_GET["createstartday"], 'hour'=>0, 'minute'=>0, 'second'=>0);
	}
	if ($startdate && !checkdate($startdate['month'], $startdate['day'], $startdate['year'])) {
		UI::htmlStartPage(getMLText("search_results"));
		UI::contentContainer(getMLText("invalid_create_date_start"));
		UI::htmlEndPage();
		exit;
	}
	if(isset($_GET["createend"])) {
		$tmp = explode("-", $_GET["createend"]);
		$stopdate = array('year'=>(int)$tmp[2], 'month'=>(int)$tmp[1], 'day'=>(int)$tmp[0], 'hour'=>0, 'minute'=>0, 'second'=>0);
	} else {
		if(isset($_GET["createendyear"]))
			$stopdate = array('year'=>$_GET["createendyear"], 'month'=>$_GET["createendmonth"], 'day'=>$_GET["createendday"], 'hour'=>23, 'minute'=>59, 'second'=>59);
	}
	if ($stopdate && !checkdate($stopdate['month'], $stopdate['day'], $stopdate['year'])) {
		UI::htmlStartPage(getMLText("search_results"));
		UI::contentContainer(getMLText("invalid_create_date_end"));
		UI::htmlEndPage();
		exit;
	}

	$expstartdate = array();
	$expstopdate = array();
	if (isset($_GET["expirationdate"]) && $_GET["expirationdate"]!=null) {
		$expirationdate = true;
	} else {
		$expirationdate = false;
	}

	if(isset($_GET["expirationstart"]) && $_GET["expirationstart"]) {
		$tmp = explode("-", $_GET["expirationstart"]);
		$expstartdate = array('year'=>(int)$tmp[2], 'month'=>(int)$tmp[1], 'day'=>(int)$tmp[0], 'hour'=>0, 'minute'=>0, 'second'=>0);
		if (!checkdate($expstartdate['month'], $expstartdate['day'], $expstartdate['year'])) {
			UI::exitError(getMLText("search"),getMLText("invalid_expiration_date_start"));
		}
	} else {
//		$expstartdate = array('year'=>$_GET["expirationstartyear"], 'month'=>$_GET["expirationstartmonth"], 'day'=>$_GET["expirationstartday"], 'hour'=>0, 'minute'=>0, 'second'=>0);
		$expstartdate = array();
	}
	if(isset($_GET["expirationend"]) && $_GET["expirationend"]) {
		$tmp = explode("-", $_GET["expirationend"]);
		$expstopdate = array('year'=>(int)$tmp[2], 'month'=>(int)$tmp[1], 'day'=>(int)$tmp[0], 'hour'=>0, 'minute'=>0, 'second'=>0);
		if (!checkdate($expstopdate['month'], $expstopdate['day'], $expstopdate['year'])) {
			UI::exitError(getMLText("search"),getMLText("invalid_expiration_date_end"));
		}
	} else {
		//$expstopdate = array('year'=>$_GET["expirationendyear"], 'month'=>$_GET["expirationendmonth"], 'day'=>$_GET["expirationendday"], 'hour'=>23, 'minute'=>59, 'second'=>59);
		$expstopdate = array();
	}

	// status
	$status = array();
	if (isset($_GET["pendingReview"])){
		$status[] = S_DRAFT_REV;
	}
	if (isset($_GET["pendingApproval"])){
		$status[] = S_DRAFT_APP;
	}
	if (isset($_GET["inWorkflow"])){
		$status[] = S_IN_WORKFLOW;
	}
	if (isset($_GET["released"])){
		$status[] = S_RELEASED;
	}
	if (isset($_GET["rejected"])){
		$status[] = S_REJECTED;
	}
	if (isset($_GET["obsolete"])){
		$status[] = S_OBSOLETE;
	}
	if (isset($_GET["expired"])){
		$status[] = S_EXPIRED;
	}

	/* Do not search for folders if result shall be filtered by status.
	 * If this is not done, unexplainable results will be delivered.
	 * e.g. a search for expired documents of a given user will list
	 * also all folders of that user because the status doesn't apply
	 * to folders.
	 */
	if($status)
		$resultmode = 0x01;

	// category
	$categories = array();
	if(isset($_GET['categoryids']) && $_GET['categoryids']) {
		foreach($_GET['categoryids'] as $catid) {
			if($catid > 0)
				$categories[] = $dms->getDocumentCategory($catid);
		}
	}

	/* Do not search for folders if result shall be filtered by categories. */
	if($categories)
		$resultmode = 0x01;

	if (isset($_GET["attributes"]))
		$attributes = $_GET["attributes"];
	else
		$attributes = array();

	//
	// Get the page number to display. If the result set contains more than
	// 25 entries, it is displayed across multiple pages.
	//
	// This requires that a page number variable be used to track which page the
	// user is interested in, and an extra clause on the select statement.
	//
	// Default page to display is always one.
	$pageNumber=1;
	$limit = 15;
	if (isset($_GET["pg"])) {
		if (is_numeric($_GET["pg"]) && $_GET["pg"]>0) {
			$pageNumber = (int) $_GET["pg"];
		}
		elseif (!strcasecmp($_GET["pg"], "all")) {
			$limit = 0;
		}
	}


	// ---------------- Start searching -----------------------------------------
	$startTime = getTime();
	$resArr = $dms->search($query, $limit, ($pageNumber-1)*$limit, $mode, $searchin, $startFolder, $owner, $status, $creationdate ? $startdate : array(), $creationdate ? $stopdate : array(), array(), array(), $categories, $attributes, $resultmode, $expirationdate ? $expstartdate : array(), $expirationdate ? $expstopdate : array());
	$searchTime = getTime() - $startTime;
	$searchTime = round($searchTime, 2);

	$entries = array();
	if($resArr['folders']) {
		foreach ($resArr['folders'] as $entry) {
			if ($entry->getAccessMode($user) >= M_READ) {
				$entries[] = $entry;
			}
		}
	}
	if($resArr['docs']) {
		foreach ($resArr['docs'] as $entry) {
			if ($entry->getAccessMode($user) >= M_READ) {
				$entries[] = $entry;
			}
		}
	}
// }}}
}

// -------------- Output results --------------------------------------------

if(count($entries) == 1) {
	$entry = $entries[0];
	if(get_class($entry) == 'SeedDMS_Core_Document') {
		header('Location: ../out/out.ViewDocument.php?documentid='.$entry->getID());
		exit;
	} elseif(get_class($entry) == 'SeedDMS_Core_Folder') {
		header('Location: ../out/out.ViewFolder.php?folderid='.$entry->getID());
		exit;
	}
} else {
	$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
	$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'query'=>$query, 'searchhits'=>$entries, 'totalpages'=>$resArr['totalPages'], 'pagenumber'=>$pageNumber, 'searchtime'=>$searchTime, 'urlparams'=>$_GET, 'cachedir'=>$settings->_cacheDir));
	if($view) {
		$view->setParam('totaldocs', $resArr['totalDocs']);
		$view->setParam('totalfolders', $resArr['totalFolders']);
		$view->setParam('fullsearch', (isset($_GET["fullsearch"]) && $_GET["fullsearch"]) ? true : false);
		$view->setParam('mode', isset($mode) ? $mode : '');
		$view->setParam('searchin', isset($searchin) ? $searchin : array());
		$view->setParam('startfolder', isset($startFolder) ? $startFolder : null);
		$view->setParam('owner', $owner);
		$view->setParam('startdate', isset($startdate) ? $startdate : array());
		$view->setParam('stopdate', isset($stopdate) ? $stopdate : array());
		$view->setParam('expstartdate', isset($expstartdate) ? $expstartdate : array());
		$view->setParam('expstopdate', isset($expstopdate) ? $expstopdate : array());
		$view->setParam('creationdate', isset($creationdate) ? $creationdate : '');
		$view->setParam('expirationdate', isset($expirationdate) ? $expirationdate: '');
		$view->setParam('status', isset($status) ? $status : array());
		$view->setParam('categories', isset($categories) ? $categories : '');
		$view->setParam('attributes', isset($attributes) ? $attributes : '');
		$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_document, SeedDMS_Core_AttributeDefinition::objtype_documentcontent/*, SeedDMS_Core_AttributeDefinition::objtype_all*/));
		$view->setParam('attrdefs', $attrdefs);
		$allCats = $dms->getDocumentCategories();
		$view->setParam('allcategories', $allCats);
		$allUsers = $dms->getAllUsers($settings->_sortUsersInList);
		$view->setParam('allusers', $allUsers);
		$view->setParam('workflowmode', $settings->_workflowMode);
		$view->setParam('enablefullsearch', $settings->_enableFullSearch);
		$view->show();
		exit;
	}
}
?>
