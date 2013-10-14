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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_ApprovalSummary extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$db = $dms->getDB();

		$this->htmlStartPage(getMLText("approval_summary"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("my_documents"), "my_documents");
		$this->contentHeading(getMLText("approval_summary"));
		$this->contentContainerStart();

		if (!$db->createTemporaryTable("ttstatid")) {
			$this->exitError(getMLText("approval_summary"),getMLText("internal_error_exit"));
		}

		// TODO: verificare scadenza
		
		// Get document list for the current user.
		$approvalStatus = $user->getApprovalStatus();

		// reverse order
		$approvalStatus["indstatus"]=array_reverse($approvalStatus["indstatus"],true);
		$approvalStatus["grpstatus"]=array_reverse($approvalStatus["grpstatus"],true);
		
		// Create a comma separated list of all the documentIDs whose information is
		// required.
		$dList = array();
		foreach ($approvalStatus["indstatus"] as $st) {
			if (!in_array($st["documentID"], $dList)) {
				$dList[] = $st["documentID"];
			}
		}
		foreach ($approvalStatus["grpstatus"] as $st) {
			if (!in_array($st["documentID"], $dList)) {
				$dList[] = $st["documentID"];
			}
		}
		$docCSV = "";
		foreach ($dList as $d) {
			$docCSV .= (strlen($docCSV)==0 ? "" : ", ")."'".$d."'";
		}
		
		if (strlen($docCSV)>0) {
		
			$queryStr = "SELECT `tblDocuments`.*, `tblDocumentLocks`.`userID` as `lockUser`, ".
				"`tblDocumentStatus`.*, `tblDocumentStatusLog`.`status`, ".
				"`tblDocumentStatusLog`.`comment` AS `statusComment`, `tblDocumentStatusLog`.`date` as `statusDate`, ".
				"`tblDocumentStatusLog`.`userID`, `oTbl`.`fullName` AS `ownerName`, `sTbl`.`fullName` AS `statusName` ".
				"FROM `tblDocumentStatus` ".
				"LEFT JOIN `tblDocumentStatusLog` USING (`statusID`) ".
				"LEFT JOIN `ttstatid` on `ttstatid`.`maxLogID` = `tblDocumentStatusLog`.`statusLogID` ".
				"LEFT JOIN `tblDocuments` ON `tblDocuments`.`id` = `tblDocumentStatus`.`documentID` ".
				"LEFT JOIN `tblDocumentLocks` ON `tblDocuments`.`id`=`tblDocumentLocks`.`document` ".
				"LEFT JOIN `tblUsers` AS `oTbl` on `oTbl`.`id` = `tblDocuments`.`owner` ".
				"LEFT JOIN `tblUsers` AS `sTbl` on `sTbl`.`id` = `tblDocumentStatusLog`.`userID` ".
				"WHERE `ttstatid`.`maxLogID`=`tblDocumentStatusLog`.`statusLogID` ".
				"AND `tblDocuments`.`id` IN (" . $docCSV . ") ".
				"ORDER BY `statusDate` DESC";
		
			$resArr = $db->getResultArray($queryStr);
			
			if (is_bool($resArr) && !$resArr) {
				$this->exitError(getMLText("approval_summary"),getMLText("internal_error_exit"));
			}
			// Create an array to hold all of these results, and index the array by
			// document id. This makes it easier to retrieve document ID information
			// later on and saves us having to repeatedly poll the database every time
			// new document information is required.
			$docIdx = array();
			foreach ($resArr as $res) {
			
				// verify expiry
				if ( $res["expires"] && time()>$res["expires"]+24*60*60 ){
					if  ( $res["status"]==S_DRAFT_APP || $res["status"]==S_DRAFT_REV ){
						$res["status"]=S_EXPIRED;
					}
				}

				$docIdx[$res["id"]][$res["version"]] = $res;
			}
		}
		
		$iRev = array();	
		$printheader = true;
		foreach ($approvalStatus["indstatus"] as $st) {

			if (isset($docIdx[$st["documentID"]][$st["version"]])) {
			
				if ($printheader){
					print "<table class=\"folderView\">";
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
				print "<td><a href=\"out.DocumentVersionDetail.php?documentid=".$st["documentID"]."&version=".$st["version"]."\">".htmlspecialchars($docIdx[$st["documentID"]][$st["version"]]["name"])."</a></td>";
				print "<td>".htmlspecialchars($docIdx[$st["documentID"]][$st["version"]]["ownerName"])."</td>";
				print "<td>".getOverallStatusText($docIdx[$st["documentID"]][$st["version"]]["status"])."</td>";
				print "<td>".$st["version"]."</td>";
				print "<td>".$st["date"]." ". htmlspecialchars($docIdx[$st["documentID"]][$st["version"]]["statusName"]) ."</td>";
				print "<td>".(!$docIdx[$st["documentID"]][$st["version"]]["expires"] ? "-":getReadableDate($docIdx[$st["documentID"]][$st["version"]]["expires"]))."</td>";				
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

			if (!in_array($st["documentID"], $iRev) && isset($docIdx[$st["documentID"]][$st["version"]])) {
			
				if ($printheader){
					print "<table class=\"folderView\">";
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
				print "<td><a href=\"out.DocumentVersionDetail.php?documentid=".$st["documentID"]."&version=".$st["version"]."\">".htmlspecialchars($docIdx[$st["documentID"]][$st["version"]]["name"])."</a></td>";
				print "<td>".htmlspecialchars($docIdx[$st["documentID"]][$st["version"]]["ownerName"])."</td>";
				print "<td>".getOverallStatusText($docIdx[$st["documentID"]][$st["version"]]["status"])."</td>";
				print "<td>".$st["version"]."</td>";
				print "<td>".$st["date"]." ". htmlspecialchars($docIdx[$st["documentID"]][$st["version"]]["statusName"]) ."</td>";
				print "<td>".(!$docIdx[$st["documentID"]][$st["version"]]["expires"] ? "-":getReadableDate($docIdx[$st["documentID"]][$st["version"]]["expires"]))."</td>";				
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
