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
if(!checkFormKey('editattributes')) {
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

$folder = $document->getFolder();
$docPathHTML = getFolderPathHTML($folder, true). " / <a href=\"../out/out.ViewDocument.php?documentid=".$documentid."\">".$document->getName()."</a>";

if ($document->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

$versionid = $_POST["version"];
$version = $document->getContentByVersion($versionid);

if (!is_object($version)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
}

$attributes = $_POST["attributes"];

if($attributes) {
	$oldattributes = $version->getAttributes();
	foreach($attributes as $attrdefid=>$attribute) {
		$attrdef = $dms->getAttributeDefinition($attrdefid);
		if($attribute) {
			if($attrdef->getRegex()) {
				if(!preg_match($attrdef->getRegex(), $attribute)) {
					UI::exitError(getMLText("folder_title", array("foldername" => $document->getName())),getMLText("attr_no_regex_match"));
				}
			}
			if(!isset($oldattributes[$attrdefid]) || $attribute != $oldattributes[$attrdefid]->getValue()) {
				if(!$version->setAttributeValue($dms->getAttributeDefinition($attrdefid), $attribute)) {
					UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
				} else {
					if($notifier) {
						$notifyList = $document->getNotifyList();
						$subject = "attribute_changed_email_subject";
						$message = "attribute_changed_email_body";
						$params = array();
						$params['name'] = $document->getName();
						$params['version'] = $version->getVersion();
						$params['attribute'] = $attribute;
						$params['folder_path'] = $folder->getFolderPathPlain();
						$params['username'] = $user->getFullName();
						$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewDocument.php?documentid=".$document->getID()."&version=".$version->getVersion();
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
			}
		} elseif(isset($oldattributes[$attrdefid])) {
			if(!$version->removeAttribute($dms->getAttributeDefinition($attrdefid)))
				UI::exitError(getMLText("document_title", array("documentname" => $folder->getName())),getMLText("error_occured"));
		}
	}
}

add_log_line("?documentid=".$documentid);

header("Location:../out/out.DocumentVersionDetail.php?documentid=".$documentid."&version=".$versionid);

?>
