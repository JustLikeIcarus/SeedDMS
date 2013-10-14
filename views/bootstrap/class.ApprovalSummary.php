<?php
/**
 * Implementation of ApprovalSummary view
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
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for ApprovalSummary view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ApprovalSummary extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$db = $dms->getDB();

		$this->htmlStartPage(getMLText("approval_summary"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("my_documents"), "my_documents");
		$this->contentHeading(getMLText("approval_summary"));
		$this->contentContainerStart();

		// Get document list for the current user.
		$approvalStatus = $user->getApprovalStatus();

		// reverse order
		$approvalStatus["indstatus"]=array_reverse($approvalStatus["indstatus"],true);
		$approvalStatus["grpstatus"]=array_reverse($approvalStatus["grpstatus"],true);
		
		$iRev = array();	
		$printheader = true;
		foreach ($approvalStatus["indstatus"] as $st) {
			$document = $dms->getDocument($st['documentID']);
			if($document)
				$version = $document->getContentByVersion($st['version']);
			$owner = $document->getOwner();
			$moduser = $dms->getUser($st['required']);

			if ($document && $version) {
			
				if ($printheader){
					print "<table class=\"table-condensed\">";
					print "<thead>\n<tr>\n";
					print "<th>".getMLText("name")."</th>\n";
					print "<th>".getMLText("owner")."</th>\n";
					print "<th>".getMLText("status")."</th>\n";
					print "<th>".getMLText("version")."</th>\n";
					print "<th>".getMLText("last_update")."</th>\n";
					print "<th>".getMLText("expires")."</th>\n";
					print "</tr>\n</thead>\n<tbody>\n";
					$printheader = false;
				}
			
				print "<tr>\n";
				print "<td><a href=\"out.DocumentVersionDetail.php?documentid=".$st["documentID"]."&version=".$st["version"]."\">".htmlspecialchars($document->getName())."</a></td>";
				print "<td>".htmlspecialchars($owner->getFullName())."</td>";
				print "<td>".getOverallStatusText($st["status"])."</td>";
				print "<td>".$st["version"]."</td>";
				print "<td>".$st["date"]." ". htmlspecialchars($moduser->getFullName()) ."</td>";
				print "<td>".(!$document->expires() ? "-":getReadableDate($document->getExpires()))."</td>";				
				print "</tr>\n";
			}
			if ($st["status"]!=-2) {
				$iRev[] = $st["documentID"];
			}
		}
		if (!$printheader) {
			echo "</tbody>\n</table>\n";
		}else{
			printMLText("no_approval_needed");
		}

		$this->contentContainerEnd();
		$this->contentHeading(getMLText("group_approval_summary"));
		$this->contentContainerStart();

		$printheader = true;
		foreach ($approvalStatus["grpstatus"] as $st) {
			$document = $dms->getDocument($st['documentID']);
			if($document)
				$version = $document->getContentByVersion($st['version']);
			$owner = $document->getOwner();
			$modgroup = $dms->getGroup($st['required']);

			if (!in_array($st["documentID"], $iRev) && $document && $version) {
			
				if ($printheader){
					print "<table class=\"table-condensed\">";
					print "<thead>\n<tr>\n";
					print "<th>".getMLText("name")."</th>\n";
					print "<th>".getMLText("owner")."</th>\n";
					print "<th>".getMLText("status")."</th>\n";
					print "<th>".getMLText("version")."</th>\n";
					print "<th>".getMLText("last_update")."</th>\n";
					print "<th>".getMLText("expires")."</th>\n";
					print "</tr>\n</thead>\n<tbody>\n";
					$printheader = false;
				}	
			
				print "<tr>\n";
				print "<td><a href=\"out.DocumentVersionDetail.php?documentid=".$st["documentID"]."&version=".$st["version"]."\">".htmlspecialchars($document->getName())."</a></td>";
				print "<td>".htmlspecialchars($owner->getFullName())."</td>";
				print "<td>".getOverallStatusText($st["status"])."</td>";
				print "<td>".$st["version"]."</td>";
				print "<td>".$st["date"]." ". htmlspecialchars($modgroup->getName()) ."</td>";
				print "<td>".(!$document->expires() ? "-":getReadableDate($document->getExpires()))."</td>";				
				print "</tr>\n";
			}
		}
		if (!$printheader) {
			echo "</tbody>\n</table>\n";
		}else{
			printMLText("empty_notify_list");
		}

		$this->contentContainerEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>
