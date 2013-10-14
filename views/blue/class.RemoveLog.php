<?php
/**
 * Implementation of RemoveLog view
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
 * Class which outputs the html page for RemoveLog view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveLog extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$lognames = $this->params['lognames'];

		$this->htmlStartPage(getMLText("backup_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("rm_file"));
		$this->contentContainerStart();
?>
<form action="../op/op.RemoveLog.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('removelog'); ?>
<?php
		foreach($lognames as $logname) {
			echo "<input type=\"hidden\" name=\"lognames[]\" value=\"".$logname."\">\n";

		}
?>
	<p><?php printMLText("confirm_rm_log", array ("logname" => implode(', ', $lognames)));?></p>
	<input type="submit" value="<?php printMLText("rm_file");?>">
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
