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
include("../inc/inc.ClassEmail.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

UI::htmlStartPage("Create Initial Status Index");
UI::globalNavigation();
UI::pageNavigation("Create Initial Status Index");

UI::contentHeading("Generate a New Document Status Index.");
UI::contentContainerStart();

if (!$user->isAdmin()) {
	print "<p>Error: User must have administrative privileges to create the status index.</p>";
	UI::contentContainerEnd();
	UI::htmlEndPage();
	exit;
}

if (!isset($_GET["genIndex"]) || $_GET["genIndex"]!=1) {
	print "<form method=\"GET\">";
	print "<input type=\"checkbox\" name=\"genIndex\" id=\"genIndex\" value=\"1\"/><label for=\"genIndex\">Generate the Initial Status Index (for MyDMS upgrades only)</label>";
	print "<p><input type=\"submit\" value=\"go\"/></p>";
	print "</form>";
	UI::contentContainerEnd();
	UI::htmlEndPage();
	exit;
}

$queryStr = "SELECT `tblDocumentContent`.`document`, `tblDocumentContent`.`version` FROM `tblDocumentContent`";
$resArr = $db->getResultArray($queryStr);
if (is_bool($resArr)) {
	print "<p>Error: unable to retrieve document content listing.</p>";
	UI::contentContainerEnd();
	UI::htmlEndPage();
	exit;
}

print "<ul>";
foreach ($resArr as $row) {
	echo "<li>Creating status log for: '".$row["document"]."', version: '".$row["version"]."'";
	$queryStr = "INSERT INTO `tblDocumentStatus` (`documentID`, `version`) ".
		"VALUES ('".$row["document"]."', '".$row["version"]."')";
	if (!$db->getResult($queryStr)) {
		print "<p>Error: unable to insert status row.</p>";
		echo "</li>";
		UI::contentContainerEnd();
		UI::htmlEndPage();
		exit;
	}
	$statusID = $db->getInsertID();
	$queryStr = "INSERT INTO `tblDocumentStatusLog` (`statusID`, `status`, `comment`, `date`, `userID`) ".
		"VALUES ('".$statusID."', '2', '-', NOW(), '".$user->getID()."')";
	if (!$db->getResult($queryStr)) {
		print "<p>Error: unable to insert status log entry.</p>";
		echo "</li>";
		UI::contentContainerEnd();
		UI::htmlEndPage();
		exit;
	}
	echo "</li>";
}
print "<ul>";
print "<p>Status Index Generation is complete.</p>";
UI::contentContainerEnd();
UI::htmlEndPage();
?>
