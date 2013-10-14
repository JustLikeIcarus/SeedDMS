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
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removedocument')) {
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

if ($document->getAccessMode($user) < M_ALL) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("access_denied"));
}

$folder = $document->getFolder();

/* Get the notify list before removing the document */
$nl =	$document->getNotifyList();
$docname = $document->getName();
if (!$document->remove()) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("error_occured"));
} else {

	/* Remove the document from the fulltext index */
	if($settings->_enableFullSearch) {
		if(!empty($settings->_luceneClassDir))
			require_once($settings->_luceneClassDir.'/Lucene.php');
		else
			require_once('SeedDMS/Lucene.php');

		$index = SeedDMS_Lucene_Indexer::open($settings->_luceneDir);
		if($index && $hits = $index->find('document_id:'.$documentid)) {
			$hit = $hits[0];
			$index->delete($hit->id);
			$index->commit();
		}
	}

	if ($notifier){
/*
		$path = "";
		$folderPath = $folder->getPath();
		for ($i = 0; $i  < count($folderPath); $i++) {
			$path .= $folderPath[$i]->getName();
			if ($i +1 < count($folderPath))
				$path .= " / ";
		}
	
		$subject = "###SITENAME###: ".$document->getName()." - ".getMLText("document_deleted_email");
		$message = getMLText("document_deleted_email")."\r\n";
		$message .= 
			getMLText("document").": ".$document->getName()."\r\n".
			getMLText("folder").": ".$path."\r\n".
			getMLText("comment").": ".$document->getComment()."\r\n".
			getMLText("user").": ".$user->getFullName()." <". $user->getEmail() ."> ";

		// Send notification to subscribers.
		$notifier->toList($user, $nl["users"], $subject, $message);
		foreach ($nl["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message);
		}
*/
		$subject = "document_deleted_email_subject";
		$message = "document_deleted_email_body";
		$params = array();
		$params['name'] = $docname;
		$params['folder_path'] = $folder->getFolderPathPlain();
		$params['username'] = $user->getFullName();
		$params['sitename'] = $settings->_siteName;
		$params['http_root'] = $settings->_httpRoot;
		$notifier->toList($user, $nl["users"], $subject, $message, $params);
		foreach ($nl["groups"] as $grp) {
			$notifier->toGroup($user, $grp, $subject, $message, $params);
		}
	}
}

add_log_line("?documentid=".$documentid);

header("Location:../out/out.ViewFolder.php?folderid=".$folder->getID());

?>
