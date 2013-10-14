<?php
/**
 * Implementation of AddEvent view
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
 * Class which outputs the html page for AddEvent view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddEvent extends SeedDMS_Blue_Style {

	function show() { /* {{{ */

		$this->htmlStartPage(getMLText("calendar"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("calendar"), "calendar");

		$this->contentHeading(getMLText("add_event"));
		$this->contentContainerStart();
?>
<script language="JavaScript">
function checkForm()
{
	msg = "";
	if (document.form1.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
<?php
	if (isset($settings->_strictFormCheck) && $settings->_strictFormCheck) {
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

<form action="../op/op.AddEvent.php" name="form1" onsubmit="return checkForm();" method="POST">
	<table>
		<tr>
			<td><?php printMLText("from");?>:</td>
			<td><?php $this->printDateChooser(-1, "from");?></td>
		</tr>
		<tr>
			<td><?php printMLText("to");?>:</td>
			<td><?php $this->printDateChooser(-1, "to");?></td>
		</tr>
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input name="name" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"></textarea></td>
		</tr>
		<tr>
			<td colspan="2"><br><input type="submit" value="<?php printMLText("add_event");?>"></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
