<?php
/**
 * Implementation of WorkflowSummary view
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
 * Class which outputs the html page for WorkflowSummary view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_WorkflowSummary extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$cachedir = $this->params['cachedir'];

		$this->htmlStartPage(getMLText("my_documents"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("my_documents"), "my_documents");

		$this->contentHeading(getMLText("workflow_user_summary"));
		$this->contentContainerStart();

		// Get document list for the current user.
		$workflowStatus = $user->getWorkflowStatus();

		$previewer = new SeedDMS_Preview_Previewer($cachedir, 40);

		$printheader=true;
		$iRev = array();
		foreach ($workflowStatus["u"] as $st) {
			$document = $dms->getDocument($st['document']);
			if($document)
				$version = $document->getContentByVersion($st['version']);
			$workflow = $dms->getWorkflow($st['workflow']);
			$state = $dms->getWorkflowState($st['state']);
			$owner = $document->getOwner();
			$moduser = $dms->getUser($st['userid']);

			if ($document && $version) {
			
				if ($printheader){
					print "<table class=\"table table-condensed\">";
					print "<thead>\n<tr>\n";
					print "<th></th>\n";
					print "<th>".getMLText("name")."</th>\n";
					print "<th>".getMLText("version")."</th>\n";
					print "<th>".getMLText("owner")."</th>\n";
					print "<th>".getMLText("workflow")."</th>\n";
					print "<th>".getMLText("workflow_state")."</th>\n";
					print "<th>".getMLText("last_update")."</th>\n";
					print "<th>".getMLText("expires")."</th>\n";
					print "</tr>\n</thead>\n<tbody>\n";
					$printheader=false;
				}
			
				$previewer->createPreview($version);
				print "<tr>\n";
				print "<td><a href=\"../op/op.Download.php?documentid=".$document->getID()."&version=".$st['version']."\">";
				if($previewer->hasPreview($version)) {
					print "<img class=\"mimeicon\" width=\"40\"src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$version->getVersion()."&width=40\" title=\"".htmlspecialchars($version->getMimeType())."\">";
				} else {
					print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($version->getFileType())."\" title=\"".htmlspecialchars($version->getMimeType())."\">";
				}
				print "</a></td>";
				print "<td><a href=\"out.DocumentVersionDetail.php?documentid=".$st["document"]."&version=".$st["version"]."\">".htmlspecialchars($document->getName());
				print "</a></td>";
				print "<td>".$st["version"]."</td>";
				print "<td>".htmlspecialchars($owner->getFullName())."</td>";
				print "<td>".$workflow->getName()."</td>";
				print "<td>".$state->getName()."</td>";
				print "<td>".$st["date"]." ". htmlspecialchars($moduser->getFullName()) ."</td>";
				print "<td>".(!$document->expires() ? "-":getReadableDate($document->getExpires()))."</td>";				
				print "</tr>\n";
			}
		}
		if (!$printheader) {
			echo "</tbody>\n</table>";
		} else {
			printMLText("no_docs_to_look_at");
		}

		$this->contentContainerEnd();
		$this->contentHeading(getMLText("workflow_group_summary"));
		$this->contentContainerStart();

		$printheader=true;
		foreach ($workflowStatus["g"] as $st) {
			$document = $dms->getDocument($st['document']);
			if($document)
				$version = $document->getContentByVersion($st['version']);
			$workflow = $dms->getWorkflow($st['workflow']);
			$state = $dms->getWorkflowState($st['state']);
			$owner = $document->getOwner();
			$modgroup = $dms->getGroup($st['groupid']);

			if (!in_array($st["document"], $iRev) && $document && $version) {
			
				if ($printheader){
					print "<table class=\"table table-condensed\">";
					print "<thead>\n<tr>\n";
					print "<th></th>\n";
					print "<th>".getMLText("name")."</th>\n";
					print "<th>".getMLText("version")."</th>\n";
					print "<th>".getMLText("owner")."</th>\n";
					print "<th>".getMLText("workflow")."</th>\n";
					print "<th>".getMLText("last_update")."</th>\n";
					print "<th>".getMLText("expires")."</th>\n";
					print "</tr>\n</thead>\n<tbody>\n";
					$printheader=false;
				}		
			
				$previewer->createPreview($version);
				print "<tr>\n";
				print "<td><a href=\"../op/op.Download.php?documentid=".$document->getID()."&version=".$st['version']."\">";
				if($previewer->hasPreview($version)) {
					print "<img class=\"mimeicon\" width=\"40\"src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$version->getVersion()."&width=40\" title=\"".htmlspecialchars($version->getMimeType())."\">";
				} else {
					print "<img class=\"mimeicon\" src=\"".$this->getMimeIcon($version->getFileType())."\" title=\"".htmlspecialchars($version->getMimeType())."\">";
				}
				print "</a></td>";
				print "<td><a href=\"out.DocumentVersionDetail.php?documentid=".$st["document"]."&version=".$st["version"]."\">".htmlspecialchars($document->getName())."</a></td>";
				print "<td>".$st["version"]."</td>";
				print "<td>".htmlspecialchars($owner->getFullName())."</td>";
				print "<td>".$workflow->getName()."</td>";
				print "<td>".$state->getName()."</td>";
				print "<td>".$st["date"]." ". htmlspecialchars($modgroup->getName()) ."</td>";
				print "<td>".(!$document->expires() ? "-":getReadableDate($document->getExpires()))."</td>";				
				print "</tr>\n";
			}
		}
		if (!$printheader) {
			echo "</tbody>\n</table>";
		}else{
			printMLText("no_docs_to_look_at");
		}


		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

