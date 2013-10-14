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

if (!isset($_GET["targetid"]) || !is_numeric($_GET["targetid"]) || $_GET["targetid"]<1) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

$targetid = $_GET["targetid"];
$targetFolder = $dms->getFolder($targetid);

if (!is_object($targetFolder)) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

if ($targetFolder->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
}

$clipboard = $session->getClipboard();
foreach($clipboard['docs'] as $documentid) {
	$document = $dms->getDocument($documentid);
	$oldFolder = $document->getFolder();

	if ($document->getAccessMode($user) < M_READWRITE) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
	}

	if ($targetid != $oldFolder->getID()) {
		if ($document->setFolder($targetFolder)) {
			// Send notification to subscribers.
			if($notifier) {
				$notifyList = $document->getNotifyList();
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
			$session->removeFromClipboard($document);

		} else {
			UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
		}
	} else {
		$session->removeFromClipboard($document);
	}
}

foreach($clipboard['folders'] as $folderid) {
	$folder = $dms->getFolder($folderid);

	if ($folder->getAccessMode($user) < M_READWRITE) {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
	}

	$oldFolder = $folder->getParent();
	if ($folder->setParent($targetFolder)) {
		// Send notification to subscribers.
		if($notifier) {
			$notifyList = $folder->getNotifyList();
			$subject = "folder_moved_email_subject";
			$message = "folder_moved_email_body";
			$params = array();
			$params['name'] = $folder->getName();
			$params['old_folder_path'] = $oldFolder->getFolderPathPlain();
			$params['new_folder_path'] = $targetFolder->getFolderPathPlain();
			$params['username'] = $user->getFullName();
			$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewFolder.php?folderid=".$folder->getID();
			$params['sitename'] = $settings->_siteName;
			$params['http_root'] = $settings->_httpRoot;
			$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
			foreach ($notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message, $params);
			}
			// if user is not owner send notification to owner
			if ($user->getID() != $folder->getOwner()->getID()) 
				$notifier->toIndividual($user, $folder->getOwner(), $subject, $message, $params);

		}
		$session->removeFromClipboard($folder);
	} else {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
	}
}

$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_moved_clipboard')));

add_log_line();

if($_GET['refferer'])
	header("Location:".urldecode($_GET['refferer']));
else
	header("Location:../out/out.ViewFolder.php?folderid=".$targetid);

?>
