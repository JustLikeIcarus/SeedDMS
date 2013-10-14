<?php
/**
 * Implementation of CreateIndex view
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
 * Class which outputs the html page for CreateIndex view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_CreateIndex extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText('admin_tools'), 'admin_tools');
		$this->contentHeading(getMLText("create_fulltext_index"));
		$this->contentContainerStart();

		echo '<p>'.getMLText('create_fulltext_index_warning').'</p>';
		echo '<a href="out.Indexer.php?create=1&confirm=1" class="btn">'.getMLText('confirm_create_fulltext_index').'</a>';

		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

