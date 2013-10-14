<?php
/**
 * Implementation of RemoveEvent view
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
 * Class which outputs the html page for RemoveEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RemoveEvent extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$event = $this->params['event'];

		$this->htmlStartPage(getMLText("calendar"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("calendar"), "calendar");

		$this->contentHeading(getMLText("edit_event"));
		$this->contentContainerStart();

?>
<form action="../op/op.RemoveEvent.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('removeevent'); ?>
	<input type="hidden" name="eventid" value="<?php echo intval($event["id"]); ?>">
	<p><?php printMLText("confirm_rm_event", array ("name" => htmlspecialchars($event["name"])));?></p>
	<input type="submit" value="<?php printMLText("delete");?>">
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
