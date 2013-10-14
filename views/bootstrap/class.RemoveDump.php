<?php
/**
 * Implementation of RemoveDump view
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
 * Class which outputs the html page for RemoveDump view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveDump extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$dumpname = $this->params['dumpfile'];

		$this->htmlStartPage(getMLText("backup_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("dump_remove"));
		$this->contentContainerStart();
?>
<form action="../op/op.RemoveDump.php" name="form1" method="post">
	<input type="Hidden" name="dumpname" value="<?php echo htmlspecialchars($dumpname); ?>">
  <?php echo createHiddenFieldWithKey('removedump'); ?>
	<p><?php printMLText("confirm_rm_dump", array ("dumpname" => htmlspecialchars($dumpname)));?></p>
	<p><button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("dump_remove");?></button></p>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
