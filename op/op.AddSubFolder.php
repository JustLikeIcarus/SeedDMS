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
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

/* Check if the form data comes for a trusted request */
if(!checkFormKey('addsubfolder')) {
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

$sequence = $_POST["sequence"];

if (!is_numeric($sequence)) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("invalid_sequence"));
}

$name = $_POST["name"];
$comment = $_POST["comment"];
if(isset($_POST["attributes"]))
	$attributes = $_POST["attributes"];
else
	$attributes = array();
foreach($attributes as $attrdefid=>$attribute) {
	$attrdef = $dms->getAttributeDefinition($attrdefid);
	if($attribute) {
		if($attrdef->getRegex()) {
			if(!preg_match($attrdef->getRegex(), $attribute)) {
				UI::exitError(getMLText("folder_title", array("foldername" => $document->getName())),getMLText("attr_no_regex_match"));
			}
		}
	}
}

$subFolder = $folder->addSubFolder($name, $comment, $user, $sequence, $attributes);

if (is_object($subFolder)) {
	// Send notification to subscribers.
	if($notifier) {
		$notifyList = $folder->getNotifyList();

/*
		$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("new_subfolder_email");
		$message = getMLText("new_subfolder_email")."\r\n";
		$message .= 
			getMLText("name").": ".$name."\r\n".
			getMLText("folder").": ".$subFolder->getFolderPathPlain()."\r\n".
			getMLText("comment").": ".$comment."\r\n".
			getMLText("user").": ".$user->getFullName()."\r\n".
			"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$subFolder->getID()."\r\n";

		$notifier->toList($user, $folder->_notifyList["users"], $subject, $message);
		foreach ($folder->_notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message);
		}
*/

		$subject = "new_subfolder_email_subject";
		$message = "new_subfolder_email_body";
		$params = array();
		$params['name'] = $subFolder->getName();
		$params['folder_path'] = $folder->getFolderPathPlain();
		$params['username'] = $user->getFullName();
		$params['comment'] = $comment;
		$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewFolder.php?folderid=".$subFolder->getID();
		$params['sitename'] = $settings->_siteName;
		$params['http_root'] = $settings->_httpRoot;
		$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
		foreach ($notifyList["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message, $params);
		}
	}

} else {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
}

add_log_line("?name=".$name."&folderid=".$folderid);

header("Location:../out/out.ViewFolder.php?folderid=".$folderid."&showtree=".$_POST["showtree"]);

?>
