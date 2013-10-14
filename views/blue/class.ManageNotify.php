<?php
/**
 * Implementation of ManageNotify view
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
 * Class which outputs the html page for ManageNotify view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ManageNotify extends SeedDMS_Blue_Style {

	// Get list of subscriptions for documents or folders for user or groups
	function getNotificationList($as_group, $folders) { /* {{{ */

		// First, get the list of groups of which the user is a member.
		if ($as_group){
		
			$groups = $this->user->getGroups();
			
			if (count($groups)==0) return NULL;
			
			$grpList = "";
			foreach ($groups as $group) {
				$grpList .= (strlen($grpList)==0 ? "" : ", ") . $group->getID();
			}
			
			$queryStr = "SELECT `tblNotify`.* FROM `tblNotify` ".
			 "WHERE `tblNotify`.`groupID` IN (". $grpList .")";
					
		} else {
			$queryStr = "SELECT `tblNotify`.* FROM `tblNotify` ".
				"WHERE `tblNotify`.`userID` = '". $this->user->getID()."'" ;
		}
		
		$resArr = $this->db->getResultArray($queryStr);
		
		$ret=array();
			
		foreach ($resArr as $res){
			
			if (($res["targetType"] == T_DOCUMENT)&&(!$folders)) $ret[]=$res["target"];
			if (($res["targetType"] == T_FOLDER)&&($folders)) $ret[]=$res["target"];
		}
		
		return $ret;
	} /* }}} */

	function printFolderNotificationList($ret,$deleteaction=true) { /* {{{ */
		if (count($ret)==0) {
			printMLText("empty_notify_list");
		}
		else {

			print "<table class=\"folderView\">";
			print "<thead><tr>\n";
			print "<th></th>\n";
			print "<th>".getMLText("name")."</th>\n";
			print "<th>".getMLText("owner")."</th>\n";
			print "<th>".getMLText("actions")."</th>\n";
			print "</tr></thead>\n<tbody>\n";
			foreach($ret as $ID) {
				$fld = $this->dms->getFolder($ID);
				if (is_object($fld)) {
					$owner = $fld->getOwner();
					print "<tr class=\"folder\">";
					print "<td><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></td>";
					print "<td><a href=\"../out/out.ViewFolder.php?folderid=".$ID."\">" . htmlspecialchars($fld->getName()) . "</a></td>\n";
					print "<td>".htmlspecialchars($owner->getFullName())."</td>";
					print "<td><ul class=\"actions\">";
					if ($deleteaction) print "<li><a href='../op/op.ManageNotify.php?id=".$ID."&type=folder&action=del'>".getMLText("delete")."</a>";
					else print "<li><a href='../out/out.FolderNotify.php?folderid=".$ID."'>".getMLText("edit")."</a>";
					print "</ul></td></tr>";
				}
			}
			print "</tbody></table>";
		}
	} /* }}} */

	function printDocumentNotificationList($ret,$deleteaction=true) { /* {{{ */

		if (count($ret)==0) {
			printMLText("empty_notify_list");
		}
		else {
			print "<table class=\"folderView\">";
			print "<thead>\n<tr>\n";
			print "<th></th>\n";
			print "<th>".getMLText("name")."</th>\n";
			print "<th>".getMLText("owner")."</th>\n";
			print "<th>".getMLText("status")."</th>\n";
			print "<th>".getMLText("version")."</th>\n";
			print "<th>".getMLText("actions")."</th>\n";
			print "</tr></thead>\n<tbody>\n";
			foreach ($ret as $ID) {
				$doc = $this->dms->getDocument($ID);
				if (is_object($doc)) {
					$owner = $doc->getOwner();
					$latest = $doc->getLatestContent();
					$status = $latest->getStatus();
					print "<tr>\n";
					print "<td><img src=\"images/file.gif\" width=18 height=18 border=0></td>";
					print "<td><a href=\"../out/out.ViewDocument.php?documentid=".$ID."\">" . htmlspecialchars($doc->getName()) . "</a></td>\n";
					print "<td>".htmlspecialchars($owner->getFullName())."</td>";
					print "<td>".getOverallStatusText($status["status"])."</td>";
					print "<td class=\"center\">".$latest->getVersion()."</td>";
					print "<td><ul class=\"actions\">";
					if ($deleteaction) print "<li><a href='../op/op.ManageNotify.php?id=".$ID."&type=document&action=del'>".getMLText("delete")."</a>";
					else print "<li><a href='../out/out.DocumentNotify.php?documentid=".$ID."'>".getMLText("edit")."</a>";
					print "</ul></td></tr>\n";
				}
			}
			print "</tbody></table>";
		}
	} /* }}} */

	function show() { /* {{{ */
		$this->dms = $this->params['dms'];
		$this->user = $this->params['user'];
		$this->db = $this->dms->getDB();

		$this->htmlStartPage(getMLText("my_account"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("my_account"), "my_account");

		$this->contentHeading(getMLText("edit_existing_notify"));
		$this->contentContainerStart();

		print "<form method=POST action=\"../op/op.ManageNotify.php?type=folder&action=add\" name=\"form1\">";
		$this->contentSubHeading(getMLText("choose_target_folder"));
		$this->printFolderChooser("form1",M_READ);
		print "<input type=\"checkbox\" name=\"recursefolder\" value=\"1\">";
		print getMLText("include_subdirectories");
		print "<input type=\"checkbox\" name=\"recursedoc\" value=\"1\">";
		print getMLText("include_documents");
		print "&nbsp;&nbsp;<input type='submit' name='' value='".getMLText("add")."'/>";
		print "</form>";

		print "<form method=POST action=\"../op/op.ManageNotify.php?type=document&action=add\" name=\"form2\">";
		$this->contentSubHeading(getMLText("choose_target_document"));
		$this->printDocumentChooser("form2");
		print "&nbsp;&nbsp;<input type=\"Submit\" value=\"".getMLText("add")."\">";
		print "</form>";

		$this->contentContainerEnd();


		//
		// Display the results.
		//
		$this->contentHeading(getMLText("edit_folder_notify"));
		$this->contentContainerStart();
		$this->contentSubHeading(getMLText("user"));
		$ret=$this->getNotificationList(false,true);
		$this->printFolderNotificationList($ret);
		$this->contentSubHeading(getMLText("group"));
		$ret=$this->getNotificationList(true,true);
		$this->printFolderNotificationList($ret,false);
		$this->contentContainerEnd();

		$this->contentHeading(getMLText("edit_document_notify"));
		$this->contentContainerStart();
		$this->contentSubHeading(getMLText("user"));
		$ret=$this->getNotificationList(false,false);
		$this->printDocumentNotificationList($ret);
		$this->contentSubHeading(getMLText("group"));
		$ret=$this->getNotificationList(true,false);
		$this->printDocumentNotificationList($ret,false);
		$this->contentContainerEnd();

		$this->htmlEndPage();
	} /* }}} */
}
?>
