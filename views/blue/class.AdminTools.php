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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_AdminTools extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$logfileenable = $this->params['logfileenable'];
		$enablefullsearch = $this->params['enablefullsearch'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentContainerStart();
?>
	<ul>
		<li class="first"><a href="../out/out.Statistic.php"><?php echo getMLText("folders_and_documents_statistic")?></a></li>
		<li><a href="../out/out.BackupTools.php"><?php echo getMLText("backup_tools")?></a></li>
<?php		
		if ($logfileenable) echo "<li><a href=\"../out/out.LogManagement.php\">".getMLText("log_management")."</a></li>";
?>
		<li><a href="../out/out.UsrMgr.php"><?php echo getMLText("user_management")?></a></li>
		<li><a href="../out/out.GroupMgr.php"><?php echo getMLText("group_management")?></a></li>
		<li><a href="../out/out.DefaultKeywords.php"><?php echo getMLText("global_default_keywords")?></a></li>
		<li><a href="../out/out.Categories.php"><?php echo getMLText("global_document_categories")?></a></li>
		<li><a href="../out/out.AttributeMgr.php"><?php echo getMLText("global_attributedefinitions")?></a></li>
		<li><a href="../out/out.Info.php"><?php echo getMLText("version_info")?></a></li>
<?php
		if($enablefullsearch) {
?>
		<li><a href="../out/out.Indexer.php"><?php echo getMLText("update_fulltext_index")?></a></li>
		<li><a href="../out/out.CreateIndex.php"><?php echo getMLText("create_fulltext_index")?></a></li>
		<li><a href="../out/out.IndexInfo.php"><?php echo getMLText("fulltext_info")?></a></li>
<?php
		}
?>
	<li><a href="../out/out.ObjectCheck.php"><?php echo getMLText("objectcheck")?></a></li>
	<li><a href="../out/out.Settings.php"><?php echo getMLText("settings")?></a></li>
	</ul>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
