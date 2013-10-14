<?php
//    MyDMS. Document Management System
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
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removedocumentfile')) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_request_token"))),getMLText("invalid_request_token"));
}

if (!isset($_POST["documentid"]) || !is_numeric($_POST["documentid"]) || intval($_POST["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$documentid = $_POST["documentid"];
$document = $dms->getDocument($documentid);

if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

if (!isset($_POST["fileid"]) || !is_numeric($_POST["fileid"]) || intval($_POST["fileid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_file_id"));
}

$fileid = $_POST["fileid"];
$file = $document->getDocumentFile($fileid);

if (!is_object($file)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_file_id"));
}


if (($document->getAccessMode($user) < M_ALL)&&($user->getID()!=$file->getUserID())) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

if (!$document->removeDocumentFile($fileid)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
} else {
	// Send notification to subscribers.
	if($notifier) {
		$notifyList = $document->getNotifyList();
/*
		$subject = "###SITENAME###: ".$document->getName()." - ".getMLText("removed_file_email");
		$message = getMLText("removed_file_email")."\r\n";
		$message .= 
			getMLText("name").": ".$document->getName()."\r\n".
			getMLText("file").": ".$file->getOriginalFileName()."\r\n".
			getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".	
			"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

		$notifier->toList($user, $document->_notifyList["users"], $subject, $message);
		foreach ($document->_notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message);
		}
*/
		$subject = "removed_file_email_subject";
		$message = "removed_file_email_body";
		$params = array();
		$params['document'] = $document->getName();
		$params['username'] = $user->getFullName();
		$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID();
		$params['sitename'] = $settings->_siteName;
		$params['http_root'] = $settings->_httpRoot;
		$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
		foreach ($notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message, $params);
		}
	}
}

add_log_line("?documentid=".$documentid."&fileid=".$fileid);

header("Location:../out/out.ViewDocument.php?documentid=".$documentid);

?>
