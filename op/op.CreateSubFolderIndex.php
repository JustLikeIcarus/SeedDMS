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
include("../inc/inc.DBInit.php");
include("../inc/inc.Language.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassEmail.php");
include("../inc/inc.Authentication.php");

UI::htmlStartPage("Create Document Folder Index");
UI::globalNavigation();
UI::pageNavigation("Create Document Folder Index");
UI::contentHeading("Indexing Documents...");

if (!$user->isAdmin()) {
	UI::contentContainer("<p>Permission denied.</p>");
	UI::htmlPageEnd();
	exit;
}

function getTime() {
	if (function_exists('microtime')) {
		$tm = microtime();
		$tm = explode(' ', $tm);
		return (float) sprintf('%f', $tm[1] + $tm[0]);
	}
	return time();
}


// ------------------------------------- Suche starten --------------------------------------------

UI::contentContainerStart();

$startTime = getTime();
$results = array();
//searchInFolder($startFolder);

//
// Construct the SQL query that will be used to search the database.
//

// Create the keyword search string. This search spans up to three columns
// in the database: keywords, name and comment.

//
// The base query.
//
$searchQuery = "SELECT `tblDocuments`.* FROM `tblDocuments` ";

// Send the search query to the database.
$resArr = $db->getResultArray($searchQuery);
// Assemble the results into an array of MyDMS Document objects.
if (!is_bool($resArr) && count($resArr)>0) {
	echo "<ol>";
	foreach($resArr as $docArr) {
		$doc = new SeedDMS_Core_Document($docArr["id"],
												$docArr["name"],
												$docArr["comment"],
												$docArr["date"],
												$docArr["expires"],
												$docArr["owner"],
												$docArr["folder"],
												$docArr["inheritAccess"],
												$docArr["defaultAccess"],
												$docArr["lockUser"],
												$docArr["keywords"],
												$docArr["sequence"]);
		// Make sure that the folder search path is also updated.
		$folder = $doc->getFolder();
		$path = $folder->getPath();
		$flist = "";
		foreach ($path as $f) {
			$flist .= ":".$f->getID();
		}
		if (strlen($flist)>1) {
			$flist .= ":";
		}
		$queryStr = "UPDATE tblDocuments SET folderList = '" . $flist . "' WHERE id = ". $doc->getID();
		$db->getResult($queryStr);
		echo "<li>Updating docID '".$doc->getID()."' -- '".$flist."'</li>";
	}
	echo "</ol>";
}

$searchTime = getTime() - $startTime;
$searchTime = round($searchTime, 2);
echo "<p>";
printMLText("search_time", array("time" => $searchTime));

UI::contentContainerEnd();
UI::htmlEndPage();
?>
