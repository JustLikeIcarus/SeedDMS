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
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

if ($user->isGuest()) {
	UI::exitError(getMLText("my_account"),getMLText("access_denied"));
}

function add_folder_notify($folder,$userid,$recursefolder,$recursedoc) {
	global $dms;

	$folder->addNotify($userid, true);
	
	if ($recursedoc){
	
		// include all folder's document
		
		$documents = $folder->getDocuments();
		$documents = SeedDMS_Core_DMS::filterAccess($documents, $dms->getUser($userid), M_READ);

		foreach($documents as $document)
			$document->addNotify($userid, true);
	}
	
	if ($recursefolder){
	
		// recurse all folder's folders
		
		$subFolders = $folder->getSubFolders();
		$subFolders = SeedDMS_Core_DMS::filterAccess($subFolders, $dms->getUser($userid), M_READ);

		foreach($subFolders as $subFolder)
			add_folder_notify($subFolder,$userid,$recursefolder,$recursedoc);
	}
}

if (!isset($_GET["type"])) UI::exitError(getMLText("my_account"),getMLText("error_occured"));
if (!isset($_GET["action"])) UI::exitError(getMLText("my_account"),getMLText("error_occured"));

$userid=$user->getID();
	
if ($_GET["type"]=="document"){

	if ($_GET["action"]=="add"){
		if (!isset($_POST["docid"])) UI::exitError(getMLText("my_account"),getMLText("error_occured"));
		$documentid = $_POST["docid"];
	}else if ($_GET["action"]=="del"){
		if (!isset($_GET["id"])) UI::exitError(getMLText("my_account"),getMLText("error_occured"));
		$documentid = $_GET["id"];
	
	}else UI::exitError(getMLText("my_account"),getMLText("error_occured"));

	if(!$documentid || !($document = $dms->getDocument($documentid))) {
		UI::exitError(getMLText("my_account"),getMLText("error_no_document_selected"));
	}
	
	if ($document->getAccessMode($user) < M_READ) 
		UI::exitError(getMLText("my_account"),getMLText("error_occured"));

	if ($_GET["action"]=="add") $document->addNotify($userid, true);
	else if ($_GET["action"]=="del") $document->removeNotify($userid, true);
	
} else if ($_GET["type"]=="folder") {

	if ($_GET["action"]=="add"){
		if (!isset($_POST["targetidform1"])) UI::exitError(getMLText("my_account"),getMLText("error_occured"));
		$folderid = $_POST["targetidform1"];
	}else if ($_GET["action"]=="del"){
		if (!isset($_GET["id"])) UI::exitError(getMLText("my_account"),getMLText("error_occured"));
		$folderid = $_GET["id"];
	
	}else UI::exitError(getMLText("my_account"),getMLText("error_occured"));

	if(!$folderid || !($folder = $dms->getFolder($folderid))) {
		UI::exitError(getMLText("my_account"),getMLText("error_no_folder_selected"));
	}
	
	if ($folder->getAccessMode($user) < M_READ) 
		UI::exitError(getMLText("my_account"),getMLText("error_occured"));

	if ($_GET["action"]=="add"){
	
		$recursefolder = isset($_POST["recursefolder"]);
		$recursedoc = isset($_POST["recursedoc"]);
	
		add_folder_notify($folder,$userid,$recursefolder,$recursedoc);
		
	} elseif ($_GET["action"]=="del") {
		if(0 == $folder->removeNotify($userid, true)) {
			if($notifier) {
				$obj = $dms->getUser($userid);
				// Email user / group, informing them of subscription.
/*
				$path="";
				$folderPath = $folder->getPath();
				for ($i = 0; $i  < count($folderPath); $i++) {
					$path .= $folderPath[$i]->getName();
					if ($i +1 < count($folderPath))
						$path .= " / ";
				}

				$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("notify_deleted_email");
				$message = getMLText("notify_deleted_email")."\r\n";
				$message .= 
					getMLText("name").": ".$folder->getName()."\r\n".
					getMLText("folder").": ".$path."\r\n".
					getMLText("comment").": ".$folder->getComment()."\r\n".
					"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

				$notifier->toIndividual($user, $obj, $subject, $message);
*/
				$subject = "notify_deleted_email_subject";
				$message = "notify_deleted_email_body";
				$params = array();
				$params['name'] = $folder->getName();
				$params['folder_path'] = $folder->getFolderPathPlain();
				$params['username'] = $user->getFullName();
				$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewFolder.php?folderid=".$folder->getID();
				$params['sitename'] = $settings->_siteName;
				$params['http_root'] = $settings->_httpRoot;

				$notifier->toIndividual($user, $obj, $subject, $message, $params);
			}
		}
	}
}

header("Location:../out/out.ManageNotify.php");

?>
