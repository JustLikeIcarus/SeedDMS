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
include("../inc/inc.Utils.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

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

$name    = $_POST["name"];
$comment = $_POST["comment"];
if(isset($_POST["sequence"])) {
	$sequence = $_POST["sequence"];
	if (!is_numeric($sequence)) {
		$sequence = "keep";
	}
} else {
	$sequence = "keep";
}
if(isset($_POST["attributes"]))
	$attributes = $_POST["attributes"];
else
	$attributes = array();

$wasupdated = false;
if(($oldname = $folder->getName()) != $name) {
	if($folder->setName($name)) {
		// Send notification to subscribers.
		if($notifier) {
			$notifyList = $folder->getNotifyList();
/*
			$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("folder_renamed_email");
			$message = getMLText("folder_renamed_email")."\r\n";
			$message .= 
				getMLText("old").": ".$oldname."\r\n".
				getMLText("new").": ".$folder->getName()."\r\n".
				getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
				getMLText("comment").": ".$comment."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

//			$subject=mydmsDecodeString($subject);
//			$message=mydmsDecodeString($message);
			
			$notifier->toList($user, $folder->_notifyList["users"], $subject, $message);
			foreach ($folder->_notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}
*/

			$subject = "folder_renamed_email_subject";
			$message = "folder_renamed_email_body";
			$params = array();
			$params['name'] = $folder->getName();
			$params['old_name'] = $oldname;
			$params['folder_path'] = $folder->getFolderPathPlain();
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
	} else {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));	
	}
}
if(($oldcomment = $folder->getComment()) != $comment) {
	if($folder->setComment($comment)) {
		// Send notification to subscribers.
		if($notifier) {
			$notifyList = $folder->getNotifyList();
/*
			$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("comment_changed_email");
			$message = getMLText("folder_comment_changed_email")."\r\n";
			$message .= 
				getMLText("name").": ".$folder->getName()."\r\n".
				getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
				getMLText("comment").": ".$comment."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

//			$subject=mydmsDecodeString($subject);
//			$message=mydmsDecodeString($message);
			
			$notifier->toList($user, $folder->_notifyList["users"], $subject, $message);
			foreach ($folder->_notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}
*/

			$subject = "folder_comment_changed_email_subject";
			$message = "folder_comment_changed_email_body";
			$params = array();
			$params['name'] = $folder->getName();
			$params['folder_path'] = $folder->getFolderPathPlain();
			$params['old_comment'] = $oldcomment;
			$params['comment'] = $comment;
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
	} else {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));	
	}
}

if($attributes) {
	$oldattributes = $folder->getAttributes();
	foreach($attributes as $attrdefid=>$attribute) {
		$attrdef = $dms->getAttributeDefinition($attrdefid);
		if($attribute) {
			if($attrdef->getRegex()) {
				if(!preg_match($attrdef->getRegex(), $attribute)) {
					UI::exitError(getMLText("folder_title", array("foldername" => $document->getName())),getMLText("attr_no_regex_match"));
				}
			}
			if(!isset($oldattributes[$attrdefid]) || $attribute != $oldattributes[$attrdefid]->getValue()) {
				if(!$folder->setAttributeValue($dms->getAttributeDefinition($attrdefid), $attribute))
					UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
			}
		} elseif(isset($oldattributes[$attrdefid])) {
			if(!$folder->removeAttribute($dms->getAttributeDefinition($attrdefid)))
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));
		}
	}
}

if(strcasecmp($sequence, "keep")) {
	if($folder->setSequence($sequence)) {
	} else {
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("error_occured"));	
	}
}

$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_folder_edited')));

add_log_line("?folderid=".$folderid);

header("Location:../out/out.ViewFolder.php?folderid=".$folderid."&showtree=".$_POST["showtree"]);

?>
