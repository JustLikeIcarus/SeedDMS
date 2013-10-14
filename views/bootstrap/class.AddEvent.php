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
require_once("class.Bootstrap.php");

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
class SeedDMS_View_AddEvent extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */

		$this->htmlStartPage(getMLText("calendar"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation("", "calendar");

		$this->contentHeading(getMLText("add_event"));
		$this->contentContainerStart();

		$expdate = date('d-m-Y');
?>
<script language="JavaScript">
function checkForm()
{
	msg = new Array();
	if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
<?php
	if (isset($settings->_strictFormCheck) && $settings->_strictFormCheck) {
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

<form action="../op/op.AddEvent.php" name="form1" onsubmit="return checkForm();" method="POST">
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("from");?>:</td>
			<td><?php //$this->printDateChooser(-1, "from");?>
    		<span class="input-append date span12" id="fromdate" data-date="<?php echo $expdate; ?>" data-date-format="dd-mm-yyyy">
      		<input class="span6" size="16" name="from" type="text" value="<?php echo $expdate; ?>">
      		<span class="add-on"><i class="icon-calendar"></i></span>
    		</span>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("to");?>:</td>
			<td><?php //$this->printDateChooser(-1, "to");?>
    		<span class="input-append date span12" id="todate" data-date="<?php echo $expdate; ?>" data-date-format="dd-mm-yyyy">
      		<input class="span6" size="16" name="to" type="text" value="<?php echo $expdate; ?>">
      		<span class="add-on"><i class="icon-calendar"></i></span>
    		</span>
			</td>
		</tr>
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"></textarea></td>
		</tr>
		<tr>
			<td></td><td><input class="btn" type="submit" value="<?php printMLText("add_event");?>"></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
