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
include("../inc/inc.Utils.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
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

$folder = $document->getFolder();

if ($document->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

if (is_uploaded_file($_FILES["userfile"]["tmp_name"]) && $_FILES["userfile"]["size"] > 0 && $_FILES['userfile']['error']!=0){
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("uploading_failed"));
}

$name     = $_POST["name"];
$comment  = $_POST["comment"];

if($_FILES["userfile"]["error"]) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
}

$userfiletmp = $_FILES["userfile"]["tmp_name"];
$userfiletype = $_FILES["userfile"]["type"];
$userfilename = $_FILES["userfile"]["name"];

$lastDotIndex = strrpos(basename($userfilename), ".");
if (is_bool($lastDotIndex) && !$lastDotIndex)
	$fileType = ".";
else
	$fileType = substr($userfilename, $lastDotIndex);

$res = $document->addDocumentFile($name, $comment, $user, $userfiletmp, 
                                  basename($userfilename),$fileType, $userfiletype );
                                
if (is_bool($res) && !$res) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
} else {
	// Send notification to subscribers.
	if($notifier) {
		$notifyList = $document->getNotifyList();

/*
		$subject = "###SITENAME###: ".$document->getName()." - ".getMLText("new_file_email");
		$message = getMLText("new_file_email")."\r\n";
		$message .= 
			getMLText("name").": ".$name."\r\n".
			getMLText("comment").": ".$comment."\r\n".
			getMLText("user").": ".$user->getFullName()." <". $user->getEmail() .">\r\n".	
			"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

		$subject=$subject;
		$message=$message;
		
		$notifier->toList($user, $document->_notifyList["users"], $subject, $message);
		foreach ($document->_notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message);
		}
*/

		$subject = "new_file_email_subject";
		$message = "new_file_email_body";
		$params = array();
		$params['name'] = $name;
		$params['document'] = $document->getName();
		$params['username'] = $user->getFullName();
		$params['comment'] = $comment;
		$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID();
		$params['sitename'] = $settings->_siteName;
		$params['http_root'] = $settings->_httpRoot;
		$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
		foreach ($notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message, $params);
		}
	}
}

add_log_line("?name=".$name."&documentid=".$documentid);

header("Location:../out/out.ViewDocument.php?documentid=".$documentid);


?>
