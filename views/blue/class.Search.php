<?php
/**
 * Implementation of Search result view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.BlueStyle.php");

/**
 * Class which outputs the html page for Search result view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Search extends SeedDMS_Blue_Style {

	function markQuery($str, $tag = "b") {
		$querywords = preg_split("/ /", $this->query);
		
		foreach ($querywords as $queryword)
			$str = str_ireplace("($queryword)", "<" . $tag . ">\\1</" . $tag . ">", $str);
		
		return $str;
	}

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$this->query = $this->params['query'];
		$entries = $this->params['searchhits'];
		$totalpages = $this->params['totalpages'];
		$pageNumber = $this->params['pagenumber'];
		$searchTime = $this->params['searchtime'];
		$urlparams = $this->params['urlparams'];
		$searchin = $this->params['searchin'];

		$this->htmlStartPage(getMLText("search_results"));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "");
		$this->contentHeading(getMLText("search_results"));
		$this->contentContainerStart();
		$this->pageList($pageNumber, $totalpages, "../op/op.Search.php", $urlparams);

		print "<table class=\"folderView\">";
		print "<thead>\n<tr>\n";
		print "<th></th>\n";
		print "<th>".getMLText("name")."</th>\n";
		print "<th>".getMLText("attributes")."</th>\n";
		print "<th>".getMLText("owner")."</th>\n";
		print "<th>".getMLText("status")."</th>\n";
		print "<th>".getMLText("version")."</th>\n";
		print "<th>".getMLText("comment")."</th>\n";
		//print "<th>".getMLText("reviewers")."</th>\n";
		//print "<th>".getMLText("approvers")."</th>\n";
		print "</tr>\n</thead>\n<tbody>\n";

		$resultsFilteredByAccess = false;
		$foldercount = $doccount = 0;
		foreach ($entries as $entry) {
			if(get_class($entry) == 'SeedDMS_Core_Document') {
				$document = $entry;
					$doccount++;
					$lc = $document->getLatestContent();
					print "<tr>";
					//print "<td><img src=\"../out/images/file.gif\" class=\"mimeicon\"></td>";
					if (in_array(2, $searchin)) {
						$docName = $this->markQuery(htmlspecialchars($document->getName()), "i");
					} else {
						$docName = htmlspecialchars($document->getName());
					}
					print "<td><a class=\"standardText\" href=\"../out/out.ViewDocument.php?documentid=".$document->getID()."\"><img class=\"mimeicon\" src=\"../out/images/icons/".$this->getMimeIcon($lc->getFileType())."\" title=\"".$lc->getMimeType()."\"></a></td>";
					print "<td><a class=\"standardText\" href=\"../out/out.ViewDocument.php?documentid=".$document->getID()."\">/";
					$folder = $document->getFolder();
					$path = $folder->getPath();
					for ($i = 1; $i  < count($path); $i++) {
						print htmlspecialchars($path[$i]->getName())."/";
					}
					print $docName;
					print "</a></td>";

					$attributes = $lc->getAttributes();
					print "<td>";
					print "<ul class=\"documentDetail\">\n";
					$attributes = $lc->getAttributes();
					if($attributes) {
						foreach($attributes as $attribute) {
							$attrdef = $attribute->getAttributeDefinition();
							print "<li>".htmlspecialchars($attrdef->getName()).": ".htmlspecialchars($attribute->getValue())."</li>\n";
						}
					}
					print "</ul>\n";
					print "</td>";

					$owner = $document->getOwner();
					print "<td>".htmlspecialchars($owner->getFullName())."</td>";
					$display_status=$lc->getStatus();
					print "<td>".getOverallStatusText($display_status["status"]). "</td>";

					print "<td class=\"center\">".$lc->getVersion()."</td>";
					
					if (in_array(3, $searchin)) $comment = $this->markQuery(htmlspecialchars($document->getComment()));
					else $comment = htmlspecialchars($document->getComment());
					if (strlen($comment) > 50) $comment = substr($comment, 0, 47) . "...";
					print "<td>".$comment."</td>";
					print "</tr>\n";
			} elseif(get_class($entry) == 'SeedDMS_Core_Folder') {
				$folder = $entry;
					$foldercount++;
					if (in_array(2, $searchin)) {
						$folderName = $this->markQuery(htmlspecialchars($folder->getName()), "i");
					} else {
						$folderName = htmlspecialchars($folder->getName());
					}
					print "<td><a class=\"standardText\" href=\"../out/out.ViewFolder.php?folderid=".$folder->getID()."\"><img src=\"../out/images/folder_closed.gif\" width=18 height=18 border=0></a></td>";
					print "<td><a class=\"standardText\" href=\"../out/out.ViewFolder.php?folderid=".$folder->getID()."\">";
					$path = $folder->getPath();
					print "/";
					for ($i = 1; $i  < count($path)-1; $i++) {
						print htmlspecialchars($path[$i]->getName())."/";
					}
					print $folderName;
					print "</a></td>";
					print "<td></td>";
					
					$owner = $folder->getOwner();
					print "<td>".htmlspecialchars($owner->getFullName())."</td>";
					print "<td></td>";
					print "<td></td>";
					if (in_array(3, $searchin)) $comment = $this->markQuery(htmlspecialchars($folder->getComment()));
					else $comment = htmlspecialchars($folder->getComment());
					if (strlen($comment) > 50) $comment = substr($comment, 0, 47) . "...";
					print "<td>".$comment."</td>";
					print "</tr>\n";
			}
		}
		if (0 && $resultsFilteredByAccess) {
			print "<tr><td colspan=\"7\">". getMLText("search_results_access_filtered") . "</td></tr>";
		}

		print "</tbody></table>\n";
		$numResults = $doccount + $foldercount;
		if ($numResults == 0) {
			print "<p>".getMLText("search_no_results")."</p>";
		} else {
		//	print "<p>".getMLText("search_report", array("doccount" => $doccount, "foldercount" => $foldercount, 'searchtime'=>$searchTime))."</p>";
		}

		$this->pageList($pageNumber, $totalpages, "../op/op.Search.php", $_GET);

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

