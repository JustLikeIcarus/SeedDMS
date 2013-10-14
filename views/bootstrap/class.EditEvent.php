<?php
/**
 * Implementation of EditEvent view
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
 * Class which outputs the html page for EditEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditEvent extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$event = $this->params['event'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("calendar"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("calendar"), "calendar");

		$this->contentHeading(getMLText("edit_event"));
		$this->contentContainerStart();
?>
<script language="JavaScript">
function checkForm()
{
	msg = new Array()
	if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
<?php
	if ($strictformcheck) {
?>
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
<?php
	}
?>
	if (msg != "") {
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}
</script>

<form action="../op/op.EditEvent.php" name="form1" onsubmit="return checkForm();" method="POST">
  <?php echo createHiddenFieldWithKey('editevent'); ?>

	<input type="Hidden" name="eventid" value="<?php echo (int) $event["id"]; ?>">

	<table class="table-condensed">
		<tr>
			<td><?php printMLText("from");?>:</td>
			<td><?php //$this->printDateChooser($event["start"], "from");?>
    		<span class="input-append date span12" id="fromdate" data-date="<?php echo date('d-m-Y', $event["start"]); ?>" data-date-format="dd-mm-yyyy">
      		<input class="span6" size="16" name="from" type="text" value="<?php echo date('d-m-Y', $event["start"]); ?>">
      		<span class="add-on"><i class="icon-calendar"></i></span>
    		</span>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("to");?>:</td>
			<td><?php //$this->printDateChooser($event["stop"], "to");?>
    		<span class="input-append date span12" id="todate" data-date="<?php echo date('d-m-Y', $event["stop"]); ?>" data-date-format="dd-mm-yyyy">
      		<input class="span6" size="16" name="to" type="text" value="<?php echo date('d-m-Y', $event["stop"]); ?>">
      		<span class="add-on"><i class="icon-calendar"></i></span>
    		</span>
			</td>
		</tr>
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" value="<?php echo htmlspecialchars($event["name"]);?>" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"><?php echo htmlspecialchars($event["comment"])?></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td><button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
