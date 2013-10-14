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
class SeedDMS_View_SearchFulltext extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$entries = $this->params['searchhits'];
		$totalpages = $this->params['totalpages'];
		$totaldocs = $this->params['totaldocs'];
		$pageNumber = $this->params['pagenumber'];
		$urlparams = $this->params['urlparams'];
		$searchTime = $this->params['searchtime'];

		$this->htmlStartPage(getMLText("search_results"));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "", $folder);
		$this->contentHeading(getMLText("search_results"));

		$this->contentContainerStart();
?>
<table width="100%" style="border-collapse: collapse;">
<tr>
<td align="left" style="padding:0; margin:0;">
<?php
		$numResults = $totaldocs;
		if ($numResults == 0) {
			printMLText("search_no_results");
		}
		else {
			printMLText("search_report_fulltext", array("doccount" => $totaldocs));
		}
?>
</td>
<td align="right"><?php printMLText("search_time", array("time" => $searchTime));?></td>
</tr>
</table>

<?php
		if ($numResults == 0) {
			$this->contentContainerEnd();
			$this->htmlEndPage();
			exit;
		}

		$this->pageList($pageNumber, $totalpages, "../op/op.SearchFulltext.php", $_GET);

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
		foreach ($entries as $document) {
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

		$this->pageList($pageNumber, $totalpages, "../op/op.Search.php", $_GET);

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

