<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005 Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2009-2013 Uwe Steinmann
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
include("../inc/inc.Utils.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!isset($_POST["documentid"]) || !is_numeric($_POST["documentid"]) || intval($_POST["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}
$documentid = $_POST["documentid"];
$document = $dms->getDocument($documentid);
if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

if ($document->getAccessMode($user) < M_ALL) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

if (!isset($_POST["version"]) || !is_numeric($_POST["version"]) || intval($_POST["version"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
}

$version = $_POST["version"];
$content = $document->getContentByVersion($version);

if (!is_object($content)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
}

if (!isset($_POST["overrideStatus"]) || !is_numeric($_POST["overrideStatus"]) ||
		(intval($_POST["overrideStatus"])<-3 && intval($_POST["overrideStatus"])>2)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_status"));
}

$overallStatus = $content->getStatus();

// status change control
if ($overallStatus["status"] == S_REJECTED || $overallStatus["status"] == S_EXPIRED || $overallStatus["status"] == S_DRAFT_REV || $overallStatus["status"] == S_DRAFT_APP || $overallStatus["status"] == S_IN_WORKFLOW) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("cannot_change_final_states"));
}

$reviewStatus = $content->getReviewStatus();
$approvalStatus = $content->getApprovalStatus();
$overrideStatus = $_POST["overrideStatus"];
$comment = $_POST["comment"];

if ($overrideStatus != $overallStatus["status"]) {

	if (!$content->setStatus($overrideStatus, $comment, $user)) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	} else {
		// Send notification to subscribers.
		if($notifier) {
			$nl = $document->getNotifyList();
			$folder = $document->getFolder();
/*
			$subject = "###SITENAME###: ".$document->getName()." - ".getMLText("document_status_changed_email");
			$message = getMLText("document_status_changed_email")."\r\n";
			$message .= 
				getMLText("document").": ".$document->getName()."\r\n".
				getMLText("status").": ".getOverallStatusText($overrideStatus)."\r\n".
				getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
				getMLText("comment").": ".$document->getComment()."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$content->_version."\r\n";

			$notifier->toList($user, $nl["users"], $subject, $message);
			foreach ($nl["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}
*/
			$subject = "document_status_changed_email_subject";
			$message = "document_status_changed_email_body";
			$params = array();
			$params['name'] = $document->getName();
			$params['folder_path'] = $folder->getFolderPathPlain();
			$params['status'] = getOverallStatusText($overrideStatus);
			$params['username'] = $user->getFullName();
			$params['sitename'] = $settings->_siteName;
			$params['http_root'] = $settings->_httpRoot;
			$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID();
			$notifier->toList($user, $nl["users"], $subject, $message, $params);
			foreach ($nl["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message, $params);
			}

			$notifier->toIndividual($user, $content->getUser(), $subject, $message, $params);
		}
	}
}
add_log_line("?documentid=".$documentid);
header("Location:../out/out.DocumentVersionDetail.php?documentid=".$documentid."&version=".$version);

?>
