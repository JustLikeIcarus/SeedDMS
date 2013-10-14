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

if (!isset($_POST["documentid"]) || !is_numeric($_POST["documentid"]) || intval($_POST["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$documentid = $_POST["documentid"];
$document = $dms->getDocument($documentid);

if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$folder = $document->getFolder();
$docPathHTML = getFolderPathHTML($folder, true). " / <a href=\"../out/out.ViewDocument.php?documentid=".$documentid."\">".$document->getName()."</a>";

if ($document->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

$name =     $_POST["name"];
$comment =  $_POST["comment"];
$keywords = $_POST["keywords"];
if(isset($_POST['categoryidform1'])) {
	$categories = explode(',', preg_replace('/[^0-9,]+/', '', $_POST["categoryidform1"]));
} elseif(isset($_POST["categories"])) { 
	$categories = $_POST["categories"];
} else {
	$categories = array();
}
$sequence = $_POST["sequence"];
if (!is_numeric($sequence)) {
	$sequence="keep";
}
$attributes = $_POST["attributes"];

if (($oldname = $document->getName()) != $name) {
	if($document->setName($name)) {
		// Send notification to subscribers.
		if($notifier) {
			$notifyList = $document->getNotifyList();
			$folder = $document->getFolder();
/*
			$subject = "###SITENAME###: ".$oldname." - ".getMLText("document_renamed_email");
			$message = getMLText("document_renamed_email")."\r\n";
			$message .= 
				getMLText("old").": ".$oldname."\r\n".
				getMLText("new").": ".$name."\r\n".
				getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
				getMLText("comment").": ".$document->getComment()."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

			$notifier->toList($user, $document->_notifyList["users"], $subject, $message);
			foreach ($document->_notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}
			
			// if user is not owner send notification to owner
			if ($user->getID() != $document->getOwner()->getID()) 
				$notifier->toIndividual($user, $document->getOwner(), $subject, $message);		
*/
			$subject = "document_renamed_email_subject";
			$message = "document_renamed_email_body";
			$params = array();
			$params['name'] = $document->getName();
			$params['old_name'] = $oldname;
			$params['folder_path'] = $folder->getFolderPathPlain();
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

	}
	else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

if (($oldcomment = $document->getComment()) != $comment) {
	if($document->setComment($comment)) {
		// Send notification to subscribers.
		if($notifier) {
			$notifyList = $document->getNotifyList();
			$folder = $document->getFolder();

/*
			$subject = "###SITENAME###: ".$document->getName()." - ".getMLText("comment_changed_email");
			$message = getMLText("document_comment_changed_email")."\r\n";
			$message .= 
				getMLText("document").": ".$document->getName()."\r\n".
				getMLText("folder").": ".$folder->getFolderPathPlain()."\r\n".
				getMLText("comment").": ".$comment."\r\n".
				"URL: ###URL_PREFIX###out/out.ViewDocument.php?documentid=".$document->getID()."\r\n";

			$notifier->toList($user, $document->_notifyList["users"], $subject, $message);
			foreach ($document->_notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message);
			}

			// if user is not owner send notification to owner
			if ($user->getID() != $document->getOwner()) 
				$notifier->toIndividual($user, $document->getOwner(), $subject, $message);		
*/
			$subject = "document_comment_changed_email_subject";
			$message = "document_comment_changed_email_body";
			$params = array();
			$params['name'] = $document->getName();
			$params['folder_path'] = $folder->getFolderPathPlain();
			$params['old_comment'] = $oldcomment;
			$params['new_comment'] = $comment;
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
	}
	else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

$expires = false;
if ($_POST["expires"] != "false") {
	if($_POST["expdate"]) {
		$tmp = explode('-', $_POST["expdate"]);
		$expires = mktime(0,0,0, $tmp[1], $tmp[0], $tmp[2]);
	} else {
		$expires = mktime(0,0,0, $_POST["expmonth"], $_POST["expday"], $_POST["expyear"]);
	}
}

if ($expires) {
	if($document->setExpires($expires)) {
		if($notifier) {
			$notifyList = $document->getNotifyList();
			$folder = $document->getFolder();
			// Send notification to subscribers.
			$subject = "expiry_changed_email_subject";
			$message = "expiry_changed_email_body";
			$params = array();
			$params['name'] = $document->getName();
			$params['folder_path'] = $folder->getFolderPathPlain();
			$params['username'] = $user->getFullName();
			$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID();
			$params['sitename'] = $settings->_siteName;
			$params['http_root'] = $settings->_httpRoot;
			$notifier->toList($user, $notifyList["users"], $subject, $message, $params);
			foreach ($notifyList["groups"] as $grp) {
				$notifier->toGroup($user, $grp, $subject, $message, $params);
			}
		}
	} else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

if (($oldkeywords = $document->getKeywords()) != $keywords) {
	if($document->setKeywords($keywords)) {
	}
	else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

if($categories) {
	$categoriesarr = array();
	foreach($categories as $catid) {
		if($cat = $dms->getDocumentCategory($catid)) {
			$categoriesarr[] = $cat;
		}
		
	}
	$oldcategories = $document->getCategories();
	$oldcatsids = array();
	foreach($oldcategories as $oldcategory)
		$oldcatsids[] = $oldcategory->getID();

	if (count($categoriesarr) != count($oldcategories) ||
			array_diff($categories, $oldcatsids)) {
		if($document->setCategories($categoriesarr)) {
		} else {
			UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
		}
	}
} else {
	if($document->setCategories(array())) {
	} else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

if($attributes) {
	$oldattributes = $document->getAttributes();
	foreach($attributes as $attrdefid=>$attribute) {
		$attrdef = $dms->getAttributeDefinition($attrdefid);
		if($attribute) {
			if($attrdef->getRegex()) {
				if(!preg_match($attrdef->getRegex(), $attribute)) {
					UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("attr_no_regex_match"));
				}
			}
			if(!isset($oldattributes[$attrdefid]) || $attribute != $oldattributes[$attrdefid]->getValue()) {
				if(!$document->setAttributeValue($dms->getAttributeDefinition($attrdefid), $attribute))
					UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
			}
		} elseif(isset($oldattributes[$attrdefid])) {
			if(!$document->removeAttribute($dms->getAttributeDefinition($attrdefid)))
				UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
		}
	}
}

if($sequence != "keep") {
 	if($document->setSequence($sequence)) {
	}
	else {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
	}
}

$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_document_edited')));

add_log_line("?documentid=".$documentid);
header("Location:../out/out.ViewDocument.php?documentid=".$documentid);

?>
