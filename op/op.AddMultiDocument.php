<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
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
include("../inc/inc.LogInit.php");
include("../inc/inc.Utils.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.Authentication.php");

$file_param_name = 'file';
$file_name = $_FILES[ $file_param_name ][ 'name' ];
$source_file_path = $_FILES[ $file_param_name ][ 'tmp_name' ];
$target_file_path =$settings->_stagingDir.$_POST['fileId']."-".$_POST['partitionIndex'];
if( move_uploaded_file( $source_file_path, $target_file_path ) ) {
	if($_POST['partitionIndex']+1 == $_POST['partitionCount']) {
		$fpnew = fopen($settings->_stagingDir.$_POST['fileId'], 'w+');
		for($i=0; $i<$_POST['partitionCount']; $i++) {
			$content = file_get_contents($settings->_stagingDir.$_POST['fileId']."-".$i, 'r');
			fwrite($fpnew, $content);
			unlink($settings->_stagingDir.$_POST['fileId']."-".$i);
		}
		fclose($fpnew);

		if (!isset($_REQUEST["folderid"]) || !is_numeric($_REQUEST["folderid"]) || intval($_REQUEST["folderid"])<1) {
			echo getMLText("invalid_folder_id");
		}

		$folderid = $_REQUEST["folderid"];
		$folder = $dms->getFolder($folderid);
		if (!is_object($folder)) {
			echo getMLText("invalid_folder_id");
		}

		$folderPathHTML = getFolderPathHTML($folder, true);

		if ($folder->getAccessMode($user) < M_READWRITE) {
			echo getMLText("access_denied");
		}

		if(isset($_POST["comment"]))
			$comment  = $_POST["comment"];
		else
			$comment = '';
		if(isset($_POST['version_comment']))
			$version_comment = $_POST["version_comment"];
		else
			$version_comment = '';

		if(isset($_POST["keywords"]))
			$keywords = $_POST["keywords"];
		else
			$keywords = '';

		$reqversion = (int)$_POST["reqversion"];
		if ($reqversion<1) $reqversion=1;

		$sequence = $_POST["sequence"];
		if (!is_numeric($sequence)) {
			$sequence = 1;
		}

		$expires = ($_POST["expires"] == "true") ? mktime(0,0,0, intval($_POST["expmonth"]), intval($_POST["expday"]), intval($_POST["expyear"])) : false;

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

		$userfiletmp = $settings->_stagingDir.$_POST['fileId'];;
		$userfiletype = $_FILES[ $file_param_name ]["type"];
		$userfilename = $_FILES[ $file_param_name ]["name"];

		$lastDotIndex = strrpos(basename($userfilename), ".");
		if (is_bool($lastDotIndex) && !$lastDotIndex) $fileType = ".";
		else $fileType = substr($userfilename, $lastDotIndex);

		if($_POST["name"] != "")
			$name = $_POST["name"];
		else
			$name = basename($userfilename);

		$categories = preg_replace('/[^0-9,]+/', '', $_POST["categoryids"]);
		$cats = array();
		if($categories) {
			$catids = explode(',', $categories);
			foreach($catids as $catid) {
				$cats[] = $dms->getDocumentCategory($catid);
			}
		}
		$res = $folder->addDocument($name, $comment, $expires, $user, $keywords,
																$cats, $userfiletmp, basename($userfilename),
																$fileType, $userfiletype, $sequence,
																$reviewers, $approvers, $reqversion,$version_comment);
		unlink($userfiletmp);
		if (is_bool($res) && !$res) {
			echo getMLText("error_occured");
		} else {
			$document = $res[0];
			if(isset($GLOBALS['SEEDDMS_HOOKS']['postAddDocument'])) {
				foreach($GLOBALS['SEEDDMS_HOOKS']['postAddDocument'] as $hookObj) {
					if (method_exists($hookObj, 'postAddDocument')) {
						$hookObj->postAddDocument($document);
					}
				}
			}
			// Send notification to subscribers.
			if($notifier) {
				$notifyList = $folder->getNotifyList();

/*
				$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("new_document_email");
				$message = getMLText("new_document_email")."\r\n";
				$message .= 
					getMLText("name").": ".$name."\r\n".
					getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
					getMLText("comment").": ".$comment."\r\n".
					getMLText("comment_for_current_version").": ".$version_comment."\r\n".
					"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

				$subject=$subject;
				$message=$message;

				$notifier->toList($user, $folder->_notifyList["users"], $subject, $message);
				foreach ($folder->_notifyList["groups"] as $grp) {
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
}
?>
