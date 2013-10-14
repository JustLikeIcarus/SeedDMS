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
include("../inc/inc.LogInit.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

if (!isset($_GET["documentid"]) || !is_numeric($_GET["documentid"]) || intval($_GET["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$documentid = $_GET["documentid"];
$document = $dms->getDocument($documentid);

if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$oldFolder = $document->getFolder();

if (!isset($_GET["targetidform1"]) || !is_numeric($_GET["targetidform1"]) || $_GET["targetidform1"]<1) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_target_folder"));
}

$targetid = $_GET["targetidform1"];
$targetFolder = $dms->getFolder($targetid);

if (!is_object($targetFolder)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_target_folder"));
}

if (($document->getAccessMode($user) < M_READWRITE) || ($targetFolder->getAccessMode($user) < M_READWRITE)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

if ($targetid != $oldFolder->getID()) {
	if ($document->setFolder($targetFolder)) {
		// Send notification to subscribers.
		if($notifier) {
			$notifyList = $document->getNotifyList();
/*
			$subject = "###SITENAME###: ".$document->getName()." - ".getMLText("document_moved_email");
			$message = getMLText("document_moved_email")."\r\n";
			$message .= 
				getMLText("document").": ".$document->getName()."\r\n".
				getMLText("folder").": ".$oldFolder->getFolderPathPlain()."\r\n".
				getMLText("new_folder").": ".$targetFolder->getFolderPathPlain()."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

			$notifier->toList($user, $document->_notifyList["users"], $subject, $message);
			foreach ($document->_notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}
			
			// if user is not owner send notification to owner
			if ($user->getID()!= $document->getOwner()) 
				$notifier->toIndividual($user, $document->getOwner(), $subject, $message);		
*/
			$subject = "document_moved_email_subject";
			$message = "document_moved_email_body";
			$params = array();
			$params['name'] = $document->getName();
			$params['old_folder_path'] = $oldFolder->getFolderPathPlain();
			$params['new_folder_path'] = $targetFolder->getFolderPathPlain();
			$params['username'] = $user->getFullName();
			$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID();
			$params['sitename'] = $settings->_siteName;
			$params['http_root'] = $settings->_httpRoot;
			$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
			foreach ($notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message, $params);
			}
			// if user is not owner send notification to owner
			if ($user->getID() != $document->getOwner()->getID()) 
				$notifier->toIndividual($user, $document->getOwner(), $subject, $message, $params);
		}

	} else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

add_log_line();

header("Location:../out/out.ViewDocument.php?documentid=".$documentid);

?>
