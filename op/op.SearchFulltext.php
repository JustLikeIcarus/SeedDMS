<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2011 Uwe Steinmann
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
include("../inc/inc.Authentication.php");

function getTime() {
	if (function_exists('microtime')) {
		$tm = microtime();
		$tm = explode(' ', $tm);
		return (float) sprintf('%f', $tm[1] + $tm[0]);
	}
	return time();
}

if (!isset($_GET["folderid"]) || !is_numeric($_GET["folderid"]) || intval($_GET["folderid"])<1) {
	$folderid=$settings->_rootFolderID;
} else {
	$folderid = $_GET["folderid"];
}

$folder = $dms->getFolder($folderid);
if (!is_object($folder)) {
	UI::exitError(getMLText("search_results"),getMLText("invalid_folder_id"));
}

// Create the keyword search string. This search spans up to three columns
// in the database: keywords, name and comment.

if (isset($_GET["query"]) && is_string($_GET["query"])) {
	$query = $_GET["query"];
}
else {
	$query = "";
}

// category
$categories = array();
if(isset($_GET['categoryids']) && $_GET['categoryids']) {
	foreach($_GET['categoryids'] as $catid) {
		if($catid > 0) {
			$category = $dms->getDocumentCategory($catid);
			$categories[] = $category->getName();
		}
	}
}

//
// Get the page number to display. If the result set contains more than
// 25 entries, it is displayed across multiple pages.
//
// This requires that a page number variable be used to track which page the
// user is interested in, and an extra clause on the select statement.
//
// Default page to display is always one.
$pageNumber=1;
if (isset($_GET["pg"])) {
	if (is_numeric($_GET["pg"]) && $_GET["pg"]>0) {
		$pageNumber = (integer)$_GET["pg"];
	}
	else if (!strcasecmp($_GET["pg"], "all")) {
		$pageNumber = "all";
	}
}


// --------------- Suche starten --------------------------------------------

// Check to see if the search has been restricted to a particular
// document owner.
$owner = null;
if (isset($_GET["ownerid"]) && is_numeric($_GET["ownerid"]) && $_GET["ownerid"]!=-1) {
	$owner = $dms->getUser($_GET["ownerid"]);
	if (!is_object($owner)) {
		UI::exitError(getMLText("search_results"),getMLText("unknown_owner"));
	}
}

$pageNumber=1;
if (isset($_GET["pg"])) {
	if (is_numeric($_GET["pg"]) && $_GET["pg"]>0) {
		$pageNumber = (integer)$_GET["pg"];
	}
	else if (!strcasecmp($_GET["pg"], "all")) {
		$pageNumber = "all";
	}
}

$startTime = getTime();
if($settings->_enableFullSearch) {
	if(!empty($settings->_luceneClassDir))
		require_once($settings->_luceneClassDir.'/Lucene.php');
	else
		require_once('SeedDMS/Lucene.php');
}

Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
$index = Zend_Search_Lucene::open($settings->_luceneDir);
$lucenesearch = new SeedDMS_Lucene_Search($index);
$hits = $lucenesearch->search($query, $owner ? $owner->getLogin() : '', '', $categories);
$totalDocs = count($hits);
$limit = 20;
$resArr = array();
if($pageNumber != 'all' && count($hits) > $limit) {
	$resArr['totalPages'] = (int) (count($hits) / $limit);
	if ((count($hits)%$limit) > 0)
		$resArr['totalPages']++;
	$hits = array_slice($hits, ($pageNumber-1)*$limit, $limit);
} else {
	$resArr['totalPages'] = 1;
}

$resArr['docs'] = array();
if($hits) {
	foreach($hits as $hit) {
		if($tmp = $dms->getDocument($hit['document_id'])) {
			$resArr['docs'][] = $tmp;
		}
	}
}
$searchTime = getTime() - $startTime;
$searchTime = round($searchTime, 2);

// -------------- Output results --------------------------------------------

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'folder'=>$folder, 'searchhits'=>$resArr['docs'], 'totalpages'=>$resArr['totalPages'], 'totaldocs'=>$totalDocs, 'pagenumber'=>$pageNumber, 'searchtime'=>$searchTime, 'urlparams'=>$_GET));
if($view) {
	$view->show();
	exit;
}

UI::htmlStartPage(getMLText("search_results"));
UI::globalNavigation($folder);
UI::pageNavigation(getFolderPathHTML($folder, true), "", $folder);
UI::contentHeading(getMLText("search_results"));

UI::contentContainerStart();
?>
<table width="100%" style="border-collapse: collapse;">
<tr>
<td align="left" style="padding:0; margin:0;">
<?php
$numResults = count($resArr['docs']);
if ($numResults == 0) {
	printMLText("search_no_results");
}
else {
	printMLText("search_report_fulltext", array("doccount" => $totalDocs));
}
?>
</td>
<td align="right"><?php printMLText("search_time", array("time" => $searchTime));?></td>
</tr>
</table>

<?php
if ($numResults == 0) {
	UI::contentContainerEnd();
	UI::htmlEndPage();
	exit;
}

UI::pageList($pageNumber, $resArr['totalPages'], "../op/op.SearchFulltext.php", $_GET);

print "<table class=\"folderView\">";
print "<thead>\n<tr>\n";
//print "<th></th>\n";
print "<th>".getMLText("name")."</th>\n";
print "<th>".getMLText("owner")."</th>\n";
print "<th>".getMLText("status")."</th>\n";
print "<th>".getMLText("version")."</th>\n";
print "<th>".getMLText("comment")."</th>\n";
//print "<th>".getMLText("reviewers")."</th>\n";
//print "<th>".getMLText("approvers")."</th>\n";
print "</tr>\n</thead>\n<tbody>\n";

$resultsFilteredByAccess = false;
foreach ($resArr['docs'] as $document) {
	if ($document->getAccessMode($user) < M_READ) {
		$resultsFilteredByAccess = true;
	}
	else {
		$lc = $document->getLatestContent();
		print "<tr>";
		$docName = htmlspecialchars($document->getName());
		print "<td><a class=\"standardText\" href=\"../out/out.ViewDocument.php?documentid=".$document->getID()."\">/";
		$folder = $document->getFolder();
		$path = $folder->getPath();
		for ($i = 1; $i  < count($path); $i++) {
			print htmlspecialchars($path[$i]->getName())."/";
		}
		print $docName;
		print "</a></td>";
		
		$owner = $document->getOwner();
		print "<td>".htmlspecialchars($owner->getFullName())."</td>";
		$display_status=$lc->getStatus();
		print "<td>".getOverallStatusText($display_status["status"]). "</td>";

		print "<td class=\"center\">".$lc->getVersion()."</td>";
		
		$comment = htmlspecialchars($document->getComment());
		if (strlen($comment) > 50) $comment = substr($comment, 0, 47) . "...";
		print "<td>".$comment."</td>";
		print "</tr>\n";
	}
}
if ($resultsFilteredByAccess) {
	print "<tr><td colspan=\"7\">". getMLText("search_results_access_filtered") . "</td></tr>";
}
print "</tbody></table>\n";

UI::pageList($pageNumber, $resArr['totalPages'], "../op/op.Search.php", $_GET);

UI::contentContainerEnd();
UI::htmlEndPage();
?>
