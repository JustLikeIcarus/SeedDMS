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
include("../inc/inc.Utils.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

if(!checkFormKey('foldernotify')) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("invalid_request_token"));
}

if (!isset($_POST["folderid"]) || !is_numeric($_POST["folderid"]) || intval($_POST["folderid"])<1) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

$folderid = $_POST["folderid"];
$folder = $dms->getFolder($folderid);

if (!is_object($folder)) {
	UI::exitError(getMLText("folder_title", array("foldername" => getMLText("invalid_folder_id"))),getMLText("invalid_folder_id"));
}

if (!isset($_POST["action"]) || (strcasecmp($_POST["action"], "delnotify") && strcasecmp($_POST["action"], "addnotify"))) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("invalid_action"));
}
$action = $_POST["action"];

if (isset($_POST["userid"]) && (!is_numeric($_POST["userid"]) || $_POST["userid"]<-1)) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("unknown_user"));
}
$userid = isset($_POST["userid"]) ? $_POST["userid"] : -1;

if (isset($_POST["groupid"]) && (!is_numeric($_POST["groupid"]) || $_POST["groupid"]<-1)) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("unknown_group"));
}
$groupid = isset($_POST["groupid"]) ? $_POST["groupid"] : -1;

if (isset($_POST["groupid"])&&$_POST["groupid"]!=-1){
	$group=$dms->getGroup($groupid);
	if (!$group->isMember($user,true) && !$user->isAdmin())
		UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
}

$folderPathHTML = getFolderPathHTML($folder, true);

if ($folder->getAccessMode($user) < M_READ) {
	UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
}

// Delete notification -------------------------------------------------------
if ($action == "delnotify") {

	if ($userid > 0) {
		$res = $folder->removeNotify($userid, true);
		$obj = $dms->getUser($userid);
	}
	elseif ($groupid > 0) {
		$res = $folder->removeNotify($groupid, false);
		$obj = $dms->getGroup($groupid);
	}
	switch ($res) {
		case -1:
			UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),isset($userid) ? getMLText("unknown_user") : getMLText("unknown_group"));
			break;
		case -2:
			UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
			break;
		case -3:
			UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("already_subscribed"));
			break;
		case -4:
			UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("internal_error"));
			break;
		case 0:
			if($notifier) {
				// Email user / group, informing them of subscription.
/*
				$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("notify_deleted_email");
				$message = getMLText("notify_deleted_email")."\r\n";
				$message .= 
					getMLText("name").": ".$folder->getName()."\r\n".
					getMLText("folder").": ".$path."\r\n".
					getMLText("comment").": ".$folder->getComment()."\r\n".
					"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

				if ($userid > 0) {
					$notifier->toIndividual($user, $obj, $subject, $message);
				}
				else {
					$notifier->toGroup($user, $obj, $subject, $message);
				}
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

				if ($userid > 0) {
					$notifier->toIndividual($user, $obj, $subject, $message, $params);
				}
				else {
					$notifier->toGroup($user, $obj, $subject, $message, $params);
				}
			}
			break;
	}
}

// Add notification ----------------------------------------------------------
else if ($action == "addnotify") {

	if ($userid != -1) {
		$res = $folder->addNotify($userid, true);
		switch ($res) {
			case -1:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("unknown_user"));
				break;
			case -2:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
				break;
			case -3:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("already_subscribed"));
				break;
			case -4:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("internal_error"));
				break;
			case 0:
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
					$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("notify_added_email");
					$message = getMLText("notify_added_email")."\r\n";
					$message .= 
						getMLText("name").": ".$folder->getName()."\r\n".
						getMLText("folder").": ".$path."\r\n".
						getMLText("comment").": ".$folder->getComment()."\r\n".
						"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

					$notifier->toIndividual($user, $obj, $subject, $message);
*/
					$subject = "notify_added_email_subject";
					$message = "notify_added_email_body";
					$params = array();
					$params['name'] = $folder->getName();
					$params['folder_path'] = $folder->getFolderPathPlain();
					$params['username'] = $user->getFullName();
					$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewFolder.php?folderid=".$folder->getID();
					$params['sitename'] = $settings->_siteName;
					$params['http_root'] = $settings->_httpRoot;

					$notifier->toIndividual($user, $obj, $subject, $message, $params);
				}

				break;
		}
	}
	if ($groupid != -1) {
		$res = $folder->addNotify($groupid, false);
		switch ($res) {
			case -1:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("unknown_group"));
				break;
			case -2:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("access_denied"));
				break;
			case -3:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("already_subscribed"));
				break;
			case -4:
				UI::exitError(getMLText("folder_title", array("foldername" => $folder->getName())),getMLText("internal_error"));
				break;
			case 0:
				if($notifier) {
					$obj = $dms->getGroup($groupid);
					// Email user / group, informing them of subscription.
/*
					$path="";
					$folderPath = $folder->getPath();
					for ($i = 0; $i  < count($folderPath); $i++) {
						$path .= $folderPath[$i]->getName();
						if ($i +1 < count($folderPath))
							$path .= " / ";
					}
					$subject = "###SITENAME###: ".$folder->getName()." - ".getMLText("notify_added_email");
					$message = getMLText("notify_added_email")."\r\n";
					$message .= 
						getMLText("name").": ".$folder->getName()."\r\n".
						getMLText("folder").": ".$path."\r\n".
						getMLText("comment").": ".$folder->getComment()."\r\n".
						"URL: ###URL_PREFIX###out/out.ViewFolder.php?folderid=".$folder->getID()."\r\n";

					$notifier->toGroup($user, $obj, $subject, $message);
*/
					$subject = "notify_added_email_subject";
					$message = "notify_added_email_body";
					$params = array();
					$params['name'] = $folder->getName();
					$params['folder_path'] = $folder->getFolderPathPlain();
					$params['username'] = $user->getFullName();
					$params['url'] = "http".((isset($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'],'off')!=0)) ? "s" : "")."://".$_SERVER['HTTP_HOST'].$settings->_httpRoot."out/out.ViewFolder.php?folderid=".$folder->getID();
					$params['sitename'] = $settings->_siteName;
					$params['http_root'] = $settings->_httpRoot;

					$notifier->toGroup($user, $obj, $subject, $message, $params);
				}
				break;
		}
	}
}
	
header("Location:../out/out.FolderNotify.php?folderid=".$folderid);

?>
