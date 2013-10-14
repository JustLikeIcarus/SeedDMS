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
include("../inc/inc.Authentication.php");


function getBoolValue($post_name)
{
  $out = false;
  if (isset($_POST[$post_name]))
    if ($_POST[$post_name]=="on")
      $out = true;

  return $out;
}

if (!$user->isAdmin()) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (isset($_POST["action"])) $action=$_POST["action"];
else if (isset($_GET["action"])) $action=$_GET["action"];
else $action=NULL;

// --------------------------------------------------------------------------
if ($action == "saveSettings")
{
  // -------------------------------------------------------------------------
  // get values
  // -------------------------------------------------------------------------
  // SETTINGS - SITE - DISPLAY
	$settings->_siteName = $_POST["siteName"];
  $settings->_footNote = $_POST["footNote"];
  $settings->_printDisclaimer = getBoolValue("printDisclaimer");
  $settings->_language = $_POST["language"];
  $settings->_theme = $_POST["theme"];

  // SETTINGS - SITE - EDITION
  $settings->_strictFormCheck = getBoolValue("strictFormCheck");
  $settings->setViewOnlineFileTypesFromString($_POST["viewOnlineFileTypes"]);
  $settings->_enableConverting = getBoolValue("enableConverting");
  $settings->_enableEmail =getBoolValue("enableEmail");
  $settings->_enableUsersView = getBoolValue("enableUsersView");
  $settings->_enableFullSearch = getBoolValue("enableFullSearch");
  $settings->_enableClipboard = getBoolValue("enableClipboard");
  $settings->_enableFolderTree = getBoolValue("enableFolderTree");
  $settings->_enableRecursiveCount = getBoolValue("enableRecursiveCount");
  $settings->_maxRecursiveCount = intval($_POST["maxRecursiveCount"]);
  $settings->_enableLanguageSelector = getBoolValue("enableLanguageSelector");
  $settings->_expandFolderTree = intval($_POST["expandFolderTree"]);
	$settings->_stopWordsFile = $_POST["stopWordsFile"];
	$settings->_sortUsersInList = $_POST["sortUsersInList"];

  // SETTINGS - SITE - CALENDAR
  $settings->_enableCalendar = getBoolValue("enableCalendar");
  $settings->_calendarDefaultView = $_POST["calendarDefaultView"];
  $settings->_firstDayOfWeek = intval($_POST["firstDayOfWeek"]);

  // SETTINGS - SYSTEM - SERVER
  $settings->_rootDir = $_POST["rootDir"];
  $settings->_httpRoot = $_POST["httpRoot"];
  $settings->_contentDir = $_POST["contentDir"];
  $settings->_cacheDir = $_POST["cacheDir"];
  $settings->_stagingDir = $_POST["stagingDir"];
  $settings->_luceneDir = $_POST["luceneDir"];
  $settings->_extraPath = $_POST["extraPath"];
  $settings->_dropFolderDir = $_POST["dropFolderDir"];
  $settings->_logFileEnable = getBoolValue("logFileEnable");
  $settings->_logFileRotation = $_POST["logFileRotation"];
  $settings->_enableLargeFileUpload = getBoolValue("enableLargeFileUpload");
  $settings->_partitionSize = $_POST["partitionSize"];

  // SETTINGS - SYSTEM - AUTHENTICATION
  $settings->_enableGuestLogin = getBoolValue("enableGuestLogin");
  $settings->_restricted = getBoolValue("restricted");
  $settings->_enableUserImage = getBoolValue("enableUserImage");
  $settings->_disableSelfEdit = getBoolValue("disableSelfEdit");
  $settings->_enablePasswordForgotten = getBoolValue("enablePasswordForgotten");
  $settings->_passwordStrength = intval($_POST["passwordStrength"]);
  $settings->_passwordStrengthAlgorithm = strval($_POST["passwordStrengthAlgorithm"]);
  $settings->_passwordExpiration = intval($_POST["passwordExpiration"]);
  $settings->_passwordHistory = intval($_POST["passwordHistory"]);
  $settings->_loginFailure = intval($_POST["loginFailure"]);
  $settings->_quota = intval($_POST["quota"]);
  $settings->_undelUserIds = strval($_POST["undelUserIds"]);
  $settings->_encryptionKey = strval($_POST["encryptionKey"]);
  $settings->_cookieLifetime = intval($_POST["cookieLifetime"]);

  // TODO Connectors

  // SETTINGS - SYSTEM - DATABASE
  $settings->_dbDriver = $_POST["dbDriver"];
  $settings->_dbHostname = $_POST["dbHostname"];
  $settings->_dbDatabase = $_POST["dbDatabase"];
  $settings->_dbUser = $_POST["dbUser"];
  $settings->_dbPass = $_POST["dbPass"];

  // SETTINGS - SYSTEM - SMTP
  $settings->_smtpServer = $_POST["smtpServer"];
  $settings->_smtpPort = $_POST["smtpPort"];
  $settings->_smtpSendFrom = $_POST["smtpSendFrom"];

  // SETTINGS -ADVANCED - DISPLAY
  $settings->_siteDefaultPage = $_POST["siteDefaultPage"];
  $settings->_rootFolderID = intval($_POST["rootFolderID"]);
  $settings->_titleDisplayHack = getBoolValue("titleDisplayHack");

  // SETTINGS - ADVANCED - AUTHENTICATION
  $settings->_guestID = intval($_POST["guestID"]);
  $settings->_adminIP = $_POST["adminIP"];

  // SETTINGS - ADVANCED - EDITION
  $settings->_versioningFileName = $_POST["versioningFileName"];
  $settings->_workflowMode = $_POST["workflowMode"];
  $settings->_enableAdminRevApp = getBoolValue("enableAdminRevApp");
  $settings->_enableOwnerRevApp = getBoolValue("enableOwnerRevApp");
  $settings->_enableSelfRevApp = getBoolValue("enableSelfRevApp");
  $settings->_enableVersionDeletion = getBoolValue("enableVersionDeletion");
  $settings->_enableVersionModification = getBoolValue("enableVersionModification");
  $settings->_enableDuplicateDocNames = getBoolValue("enableDuplicateDocNames");

  // SETTINGS - ADVANCED - NOTIFICATION
  $settings->_enableOwnerNotification = getBoolValue("enableOwnerNotification");
  $settings->_enableNotificationAppRev = getBoolValue("enableNotificationAppRev");

  // SETTINGS - ADVANCED - SERVER
  $settings->_coreDir = $_POST["coreDir"];
  $settings->_luceneClassDir = $_POST["luceneClassDir"];
  $settings->_contentOffsetDir = intval($_POST["contentOffsetDir"]);
  $settings->_maxDirID = intval($_POST["maxDirID"]);
  $settings->_updateNotifyTime = intval($_POST["updateNotifyTime"]);
  $settings->_maxExecutionTime = intval($_POST["maxExecutionTime"]);

  // SETTINGS - ADVANCED - INDEX CMD
  $settings->_converters = $_POST["converters"];

  // -------------------------------------------------------------------------
  // save
  // -------------------------------------------------------------------------
  if (!$settings->save())
    UI::exitError(getMLText("admin_tools"),getMLText("settings_SaveError"));

	add_log_line(".php&action=savesettings");
}

$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_settings_saved')));


header("Location:../out/out.Settings.php");

?>
