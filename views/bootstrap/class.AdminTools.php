<?php
/**
 * Implementation of AdminTools view
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
 * Class which outputs the html page for AdminTools view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AdminTools extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$logfileenable = $this->params['logfileenable'];
		$enablefullsearch = $this->params['enablefullsearch'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
//		$this->contentHeading(getMLText("admin_tools"));
		$this->contentContainerStart();
?>
	<div id="admin-tools">
	<div class="row-fluid">
		<a href="../out/out.UsrMgr.php" class="span3 btn btn-medium"><i class="icon-user"></i><br /><?php echo getMLText("user_management")?></a>
		<a href="../out/out.GroupMgr.php" class="span3 btn btn-medium"><i class="icon-group"></i><br /><?php echo getMLText("group_management")?></a>
	</div>
	<p></p>
	<div class="row-fluid">
		<a href="../out/out.BackupTools.php" class="span3 btn btn-medium"><i class="icon-hdd"></i><br /><?php echo getMLText("backup_tools")?></a>
<?php		
		if ($logfileenable)
			echo "<a href=\"../out/out.LogManagement.php\" class=\"span3 btn btn-medium\"><i class=\"icon-list\"></i><br />".getMLText("log_management")."</a>";
?>
	</div>
	<p></p>
	<div class="row-fluid">
		<a href="../out/out.DefaultKeywords.php" class="span3 btn btn-medium"><i class="icon-reorder"></i><br /><?php echo getMLText("global_default_keywords")?></a>
		<a href="../out/out.Categories.php" class="span3 btn btn-medium"><i class="icon-columns"></i><br /><?php echo getMLText("global_document_categories")?></a>
		<a href="../out/out.AttributeMgr.php" class="span3 btn btn-medium"><i class="icon-tags"></i><br /><?php echo getMLText("global_attributedefinitions")?></a>
	</div>
<?php
	if($this->params['workflowmode'] != 'traditional') {
?>
	<p></p>
	<div class="row-fluid">
		<a href="../out/out.WorkflowMgr.php" class="span3 btn btn-medium"><i class="icon-sitemap"></i><br /><?php echo getMLText("global_workflows"); ?></a>
		<a href="../out/out.WorkflowStatesMgr.php" class="span3 btn btn-medium"><i class="icon-star"></i><br /><?php echo getMLText("global_workflow_states"); ?></a>
		<a href="../out/out.WorkflowActionsMgr.php" class="span3 btn btn-medium"><i class="icon-bolt"></i><br /><?php echo getMLText("global_workflow_actions"); ?></a>
	</div>
<?php
		}
		if($enablefullsearch) {
?>
	<p></p>
	<div class="row-fluid">
		<a href="../out/out.Indexer.php" class="span3 btn btn-medium"><i class="icon-refresh"></i><br /><?php echo getMLText("update_fulltext_index")?></a>
		<a href="../out/out.CreateIndex.php" class="span3 btn btn-medium"><i class="icon-search"></i><br /><?php echo getMLText("create_fulltext_index")?></a>
		<a href="../out/out.IndexInfo.php" class="span3 btn btn-medium"><i class="icon-info-sign"></i><br /><?php echo getMLText("fulltext_info")?></a>
	</div>
<?php
		}
?>
	<p></p>
	<div class="row-fluid">
		<a href="../out/out.Statistic.php" class="span3 btn btn-medium"><i class="icon-tasks"></i><br /><?php echo getMLText("folders_and_documents_statistic")?></a>
		<a href="../out/out.ObjectCheck.php" class="span3 btn btn-medium"><i class="icon-check"></i><br /><?php echo getMLText("objectcheck")?></a>
		<a href="../out/out.Info.php" class="span3 btn btn-medium"><i class="icon-info-sign"></i><br /><?php echo getMLText("version_info")?></a>
	</div>
	<p></p>
	<div class="row-fluid">
		<a href="../out/out.Settings.php" class="span3 btn btn-medium"><i class="icon-cogs"></i><br /><?php echo getMLText("settings")?></a>
	</div>
	</div>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
