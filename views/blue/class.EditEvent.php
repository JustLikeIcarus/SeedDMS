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
require_once("class.BlueStyle.php");

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
class SeedDMS_View_EditEvent extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$event = $this->params['event'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("calendar"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("calendar"), "calendar");

		$this->contentHeading(getMLText("edit_event"));
		$this->contentContainerStart();
?>
<script language="JavaScript">
function checkForm()
{
	msg = "";
	if (document.form1.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
<?php
	if ($strictformcheck) {
	?>
	if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
<?php
	}
?>
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}
</script>

<form action="../op/op.EditEvent.php" name="form1" onsubmit="return checkForm();" method="POST">
  <?php echo createHiddenFieldWithKey('editevent'); ?>

	<input type="Hidden" name="eventid" value="<?php echo (int) $event["id"]; ?>">

	<table>
		<tr>
			<td><?php printMLText("from");?>:</td>
			<td><?php $this->printDateChooser($event["start"], "from");?></td>
		</tr>
		<tr>
			<td><?php printMLText("to");?>:</td>
			<td><?php $this->printDateChooser($event["stop"], "to");?></td>
		</tr>
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input name="name" value="<?php echo htmlspecialchars($event["name"]);?>" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"><?php echo htmlspecialchars($event["comment"])?></textarea></td>
		</tr>
		<tr>
			<td colspan="2"><br><input type="Submit" value="<?php printMLText("edit_event");?>"></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
