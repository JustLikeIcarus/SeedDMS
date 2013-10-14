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
include("../inc/inc.Utils.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/* Check if the form data comes for a trusted request */
if(!checkFormKey('adddocument')) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_request_token"))),getMLText("invalid_request_token"));
}

if (!isset($_POST["folderid"]) || !is_numeric($_POST["folderid"]) || intval($_POST["folderid"])<1) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

$folderid = $_POST["folderid"];
$folder = $dms->getFolder($folderid);

if (!is_object($folder)) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

$folderPathHTML = getFolderPathHTML($folder, true);

if ($folder->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
}

$comment  = $_POST["comment"];
$version_comment = $_POST["version_comment"];
if($version_comment == "" && isset($_POST["use_comment"]))
	$version_comment = $comment;

$keywords = $_POST["keywords"];
$categories = isset($_POST["categories"]) ? $_POST["categories"] : null;
if(isset($_POST["attributes"]))
	$attributes = $_POST["attributes"];
else
	$attributes = array();
foreach($attributes as $attrdefid=>$attribute) {
	$attrdef = $dms->getAttributeDefinition($attrdefid);
	if($attribute) {
		if($attrdef->getRegex()) {
			if(!preg_match($attrdef->getRegex(), $attribute)) {
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("attr_no_regex_match"));
			}
		}
	}
}

if(isset($_POST["attributes_version"]))
	$attributes_version = $_POST["attributes_version"];
else
	$attributes_version = array();
foreach($attributes_version as $attrdefid=>$attribute) {
	$attrdef = $dms->getAttributeDefinition($attrdefid);
	if($attribute) {
		if($attrdef->getRegex()) {
			if(!preg_match($attrdef->getRegex(), $attribute)) {
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("attr_no_regex_match"));
			}
		}
	}
}


$reqversion = (int)$_POST["reqversion"];
if ($reqversion<1) $reqversion=1;

$sequence = $_POST["sequence"];
if (!is_numeric($sequence)) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("invalid_sequence"));
}

$expires = false;
if (!isset($_POST['expires']) || $_POST["expires"] != "false") {
	if($_POST["expdate"]) {
		$tmp = explode('-', $_POST["expdate"]);
		$expires = mktime(0,0,0, $tmp[1], $tmp[0], $tmp[2]);
	} else {
		$expires = mktime(0,0,0, $_POST["expmonth"], $_POST["expday"], $_POST["expyear"]);
	}
}

// Get the list of reviewers and approvers for this document.
$reviewers = array();
$approvers = array();
$reviewers["i"] = array();
$reviewers["g"] = array();
$approvers["i"] = array();
$approvers["g"] = array();

// Retrieve the list of individual reviewers from the form.
if (isset($_POST["indReviewers"])) {
	foreach ($_POST["indReviewers"] as $ind) {
		$reviewers["i"][] = $ind;
	}
}
// Retrieve the list of reviewer groups from the form.
if (isset($_POST["grpReviewers"])) {
	foreach ($_POST["grpReviewers"] as $grp) {
		$reviewers["g"][] = $grp;
	}
}

// Retrieve the list of individual approvers from the form.
if (isset($_POST["indApprovers"])) {
	foreach ($_POST["indApprovers"] as $ind) {
		$approvers["i"][] = $ind;
	}
}
// Retrieve the list of approver groups from the form.
if (isset($_POST["grpApprovers"])) {
	foreach ($_POST["grpApprovers"] as $grp) {
		$approvers["g"][] = $grp;
	}
}

// add mandatory reviewers/approvers
$docAccess = $folder->getReadAccessList();
$res=$user->getMandatoryReviewers();
foreach ($res as $r){

	if ($r['reviewerUserID']!=0){
		foreach ($docAccess["users"] as $usr)
			if ($usr->getID()==$r['reviewerUserID']){
				$reviewers["i"][] = $r['reviewerUserID'];
				break;
			}
	}
	else if ($r['reviewerGroupID']!=0){
		foreach ($docAccess["groups"] as $grp)
			if ($grp->getID()==$r['reviewerGroupID']){
				$reviewers["g"][] = $r['reviewerGroupID'];
				break;
			}
	}
}
$res=$user->getMandatoryApprovers();
foreach ($res as $r){

	if ($r['approverUserID']!=0){
		foreach ($docAccess["users"] as $usr)
			if ($usr->getID()==$r['approverUserID']){
				$approvers["i"][] = $r['approverUserID'];
				break;
			}
	}
	else if ($r['approverGroupID']!=0){
		foreach ($docAccess["groups"] as $grp)
			if ($grp->getID()==$r['approverGroupID']){
				$approvers["g"][] = $r['approverGroupID'];
				break;
			}
	}
}

if(!$workflow = $user->getMandatoryWorkflow()) {
	if(isset($_POST["workflow"]))
		$workflow = $dms->getWorkflow($_POST["workflow"]);
	else
		$workflow = null;
}


if($settings->_dropFolderDir) {
	if(isset($_POST["dropfolderfileform1"]) && $_POST["dropfolderfileform1"]) {
		$fullfile = $settings->_dropFolderDir.'/'.$user->getLogin().'/'.$_POST["dropfolderfileform1"];
		if(file_exists($fullfile)) {
			/* Check if a local file is uploaded as well */
			if(isset($_FILES["userfile"]['error'][0])) {
				if($_FILES["userfile"]['error'][0] != 0)
					$_FILES["userfile"] = array();
			}
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = explode(';', finfo_file($finfo, $fullfile));
			$_FILES["userfile"]['tmp_name'][] = $fullfile;
			$_FILES["userfile"]['type'][] = $mimetype[0];
			$_FILES["userfile"]['name'][] = $_POST["dropfolderfileform1"];
			$_FILES["userfile"]['size'][] = filesize($fullfile);
			$_FILES["userfile"]['error'][] = 0;
		}
	}
}

/* Check files for Errors first */
for ($file_num=0;$file_num<count($_FILES["userfile"]["tmp_name"]);$file_num++){
	if ($_FILES["userfile"]["size"][$file_num]==0) {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("uploading_zerosize"));
	}
	if (is_uploaded_file($_FILES["userfile"]["tmp_name"][$file_num]) && $_FILES['userfile']['error'][$file_num]!=0){
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("uploading_failed"));
	}
}

for ($file_num=0;$file_num<count($_FILES["userfile"]["tmp_name"]);$file_num++){
	$userfiletmp = $_FILES["userfile"]["tmp_name"][$file_num];
	$userfiletype = $_FILES["userfile"]["type"][$file_num];
	$userfilename = $_FILES["userfile"]["name"][$file_num];
	
	$lastDotIndex = strrpos(basename($userfilename), ".");
	if (is_bool($lastDotIndex) && !$lastDotIndex) $fileType = ".";
	else $fileType = substr($userfilename, $lastDotIndex);

	if ((count($_FILES["userfile"]["tmp_name"])==1)&&($_POST["name"]!=""))
		$name = $_POST["name"];
	else $name = basename($userfilename);

	/* Check if name already exists in the folder */
	if(!$settings->_enableDuplicateDocNames) {
		if($folder->hasDocumentByName($name)) {
			UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("document_duplicate_name"));
		}
	}

	$cats = array();
	if($categories) {
		foreach($categories as $catid) {
			$cats[] = $dms->getDocumentCategory($catid);
		}
	}

	$res = $folder->addDocument($name, $comment, $expires, $user, $keywords,
															$cats, $userfiletmp, basename($userfilename),
	                            $fileType, $userfiletype, $sequence,
	                            $reviewers, $approvers, $reqversion,
	                            $version_comment, $attributes, $attributes_version, $workflow);

	if (is_bool($res) && !$res) {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
	} else {
		$document = $res[0];
		if(isset($GLOBALS['SEEDDMS_HOOKS']['postAddDocument'])) {
			foreach($GLOBALS['SEEDDMS_HOOKS']['postAddDocument'] as $hookObj) {
				if (method_exists($hookObj, 'postAddDocument')) {
					$hookObj->postAddDocument($document);
				}
			}
		}
		if($settings->_enableFullSearch) {
			if(!empty($settings->_luceneClassDir))
				require_once($settings->_luceneClassDir.'/Lucene.php');
			else
				require_once('SeedDMS/Lucene.php');

			$index = SeedDMS_Lucene_Indexer::open($settings->_luceneDir);
			if($index) {
				SeedDMS_Lucene_Indexer::init($settings->_stopWordsFile);
				$index->addDocument(new SeedDMS_Lucene_IndexedDocument($dms, $document, isset($settings->_convcmd) ? $settings->_convcmd : null, true));
			}
		}

		/* Add a default notification for the owner of the document */
		if($settings->_enableOwnerNotification) {
			$res = $document->addNotify($user->getID(), true);
		}
		// Send notification to subscribers of folder.
		if($notifier) {
			$notifyList = $folder->getNotifyList();
			if($settings->_enableNotificationAppRev) {
				/* Reviewers and approvers will be informed about the new document */
				foreach($reviewers['i'] as $reviewerid) {
					$notifyList['users'][] = $dms->getUser($reviewerid);
				}
				foreach($approvers['i'] as $approverid) {
					$notifyList['users'][] = $dms->getUser($approverid);
				}
				foreach($reviewers['g'] as $reviewergrpid) {
					$notifyList['groups'][] = $dms->getGroup($reviewergrpid);
				}
				foreach($approvers['g'] as $approvergrpid) {
					$notifyList['groups'][] = $dms->getGroup($approvergrpid);
				}
			}
/*
			$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("new_document_email");
			$message = getMLText("new_document_email")."\r\n";
			$message .= 
				getMLText("name").": ".$name."\r\n".
				getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
				getMLText("comment").": ".$comment."\r\n".
				getMLText("comment_for_current_version").": ".$version_comment."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

			
			$notifier->toList($user, $notifyList["users"], $subject, $message);
			foreach ($notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}
*/

			$subject = "new_document_email_subject";
			$message = "new_document_email_body";
			$params = array();
			$params['name'] = $name;
			$params['folder_name'] = $folder->getName();
			$params['folder_path'] = $folder->getFolderPathPlain();
			$params['username'] = $user->getFullName();
			$params['comment'] = $comment;
			$params['version_comment'] = $version_comment;
			$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID();
			$params['sitename'] = $settings->_siteName;
			$params['http_root'] = $settings->_httpRoot;
			$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
			foreach ($notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message, $params);
			}

		}
	}
	
	add_log_line("?name=".$name."&folderid=".$folderid);
}

header("Location:../out/out.ViewFolder.php?folderid=".$folderid."&showtree=".$_POST["showtree"]);

?>
