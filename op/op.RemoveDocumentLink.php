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
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/* Check if the form data comes for a trusted request */
if(!checkFormKey('removedocumentlink')) {
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

if (!isset($_POST["linkid"]) || !is_numeric($_POST["linkid"]) || intval($_POST["linkid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_link_id"));
}

$linkid = $_POST["linkid"];
$link = $document->getDocumentLink($linkid);

if (!is_object($link)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("invalid_link_id"));
}

$responsibleUser = $link->getUser();
$accessMode = $document->getAccessMode($user);

if (
	($accessMode < M_READ)
	|| (($accessMode == M_READ) && ($responsibleUser->getID() != $user->getID()))
	|| (($accessMode > M_READ) && (!$user->isAdmin()) && ($responsibleUser->getID() != $user->getID()) && !$link->isPublic())
   )
{
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("access_denied"));
}

if (!$document->removeDocumentLink($linkid)) {
	UI::exitError(getMLText("document_title", array("documentname" => $document->getName())),getMLText("error_occured"));
}

add_log_line("?documentid=".$documentid."&linkid=".$linkid);

header("Location:../out/out.ViewDocument.php?documentid=".$documentid);

?>
