<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2011-2013 Uwe Steinmann
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

if (isset($_GET["version"])) {

	// document download
	
	if (!isset($_GET["documentid"]) || !is_numeric($_GET["documentid"]) || intval($_GET["documentid"])<1) {
		UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
	}

	$documentid = $_GET["documentid"];
	$document = $dms->getDocument($documentid);

	if (!is_object($document)) {
		UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));

	}
	$folder = $document->getFolder();
	$docPathHTML = getFolderPathHTML($folder, true). " / <a href=\"../out/out.ViewDocument.php?documentid=".$documentid."\">".$document->getName()."</a>";

	if ($document->getAccessMode($user) < M_READ) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
	}

	if (!is_numeric($_GET["version"]) || intval($_GET["version"])<1) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
	}
	$version = $_GET["version"];
	$content = $document->getContentByVersion($version);

	if (!is_object($content)) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_version"));
	}
	
	//header("Content-Type: application/force-download; name=\"" . mydmsDecodeString($content->getOriginalFileName()) . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($dms->contentDir . $content->getPath() ));
	header("Content-Disposition: attachment; filename=\"" . $content->getOriginalFileName() . "\"");
	//header("Expires: 0");
	header("Content-Type: " . $content->getMimeType());
	//header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: must-revalidate");
	//header("Pragma: no-cache");

	readfile($dms->contentDir . $content->getPath());

} elseif (isset($_GET["file"])) {

	// file download
	
	if (!isset($_GET["documentid"]) || !is_numeric($_GET["documentid"]) || intval($_GET["documentid"])<1) {
		UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
	}

	$documentid = $_GET["documentid"];
	$document = $dms->getDocument($documentid);

	if (!is_object($document)) {
		UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));

	}
	$folder = $document->getFolder();
	$docPathHTML = getFolderPathHTML($folder, true). " / <a href=\"../out/out.ViewDocument.php?documentid=".$documentid."\">".$document->getName()."</a>";

	if ($document->getAccessMode($user) < M_READ) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
	}

	if (!is_numeric($_GET["file"]) || intval($_GET["file"])<1) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_file_id"));
	}
	$fileid = $_GET["file"];
	$file = $document->getDocumentFile($fileid);

	if (!is_object($file)) {
		UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_file_id"));
	}

	header("Content-Type: application/force-download; name=\"" . $file->getOriginalFileName() . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($dms->contentDir . $file->getPath() ));
	header("Content-Disposition: attachment; filename=\"" . $file->getOriginalFileName() . "\"");
	//header("Expires: 0");
	header("Content-Type: " . $file->getMimeType());
	//header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: must-revalidate");
	//header("Pragma: no-cache");

	readfile($dms->contentDir . $file->getPath());

} elseif (isset($_GET["arkname"])) {

	// backup download
	
	if (!$user->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	if (!isset($_GET["arkname"]) || !file_exists($settings->_contentDir.$_GET["arkname"]) ) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
	}

	header('Content-Description: File Transfer');
	//header("Content-Type: application/force-download; name=\"" . $_GET["arkname"] . "\"");
	//header("Content-Type: application/octet-stream");
	header("Content-Type: application/zip");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($settings->_contentDir . $_GET["arkname"] ));
	header("Content-Disposition: attachment; filename=\"" .$_GET["arkname"] . "\"");
//	header("Expires: 0");
	//header("Content-Type: " . $content->getMimeType());
	//header("Cache-Control: no-cache, must-revalidate");
//	header("Cache-Control: must-revalidate");
	header("Cache-Control: public");
	//header("Pragma: no-cache");	
	
	readfile($settings->_contentDir .$_GET["arkname"] );
	
} elseif (isset($_GET["logname"])) {

	// log download
	
	if (!$user->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	if (!isset($_GET["logname"]) || !file_exists($settings->_contentDir.$_GET["logname"]) ) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
	}

	header("Content-Type: text/plain; name=\"" . $_GET["logname"] . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($settings->_contentDir . $_GET["logname"] ));
	header("Content-Disposition: attachment; filename=\"" .$_GET["logname"] . "\"");
	header("Cache-Control: must-revalidate");
	
	readfile($settings->_contentDir .$_GET["logname"] );
	
} elseif (isset($_GET["vfile"])) {

	// versioning info download
	
	$documentid = $_GET["documentid"];
	$document = $dms->getDocument($documentid);

	if (!is_object($document)) {
		UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));

	}	
	
	// update infos
	createVersionigFile($document);
	
	header("Content-Type: text/plain; name=\"" . $settings->_versioningFileName . "\"");
	//header("Content-Type: application/force-download; name=\"" . $settings->_versioningFileName . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($dms->contentDir.$document->getDir().$settings->_versioningFileName )."\"");
	header("Content-Disposition: attachment; filename=\"". $settings->_versioningFileName . "\"");
	//header("Expires: 0");
	//header("Content-Type: " . $content->getMimeType());
	//header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: must-revalidate");
	//header("Pragma: no-cache");	
	
	readfile($dms->contentDir . $document->getDir() .$settings->_versioningFileName);
	
} elseif (isset($_GET["dumpname"])) {

	// dump file download
	
	if (!$user->isAdmin()) {
		UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
	}

	if (!isset($_GET["dumpname"]) || !file_exists($settings->_contentDir.$_GET["dumpname"]) ) {
		UI::exitError(getMLText("admin_tools"),getMLText("unknown_id"));
	}

	header("Content-Type: application/zip; name=\"" . $_GET["dumpname"] . "\"");
	//header("Content-Type: application/force-download; name=\"" . $_GET["dumpname"] . "\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . filesize($settings->_contentDir . $_GET["dumpname"] ));
	header("Content-Disposition: attachment; filename=\"" .$_GET["dumpname"] . "\"");
	//header("Expires: 0");
	//header("Content-Type: " . $content->getMimeType());
	//header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: must-revalidate");
	//header("Pragma: no-cache");	
	
	readfile($settings->_contentDir .$_GET["dumpname"] );
}

add_log_line();
exit();
?>
