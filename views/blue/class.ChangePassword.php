<?php
/**
 * Implementation of ChangePassword view
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
 * Class which outputs the html page for ChangePassword view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ChangePassword extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$referuri = $this->params['referuri'];
		$hash = $this->params['hash'];

		$this->htmlStartPage(getMLText("change_password"), "login");
		$this->globalBanner();
		$this->pageNavigation(getMLText("change_password"));
		$this->contentContainerStart();
?>
<form action="../op/op.ChangePassword.php" method="post" name="form1" onsubmit="return checkForm();">
<?php
		if ($referuri) {
			echo "<input type='hidden' name='referuri' value='".$referuri."'/>";
		}
		if ($hash) {
			echo "<input type='hidden' name='hash' value='".$hash."'/>";
		}
?>
	<table border="0">
		<tr>
			<td><?php printMLText("password");?></td>
			<td><input class="pwd" type="password" name="newpassword" id="password"> <div id="outerstrength" style="min-width: 100px; height: 14px; display: inline-block; border: 1px solid black; padding: 1px;"><div id="innerstrength" style="width: 0px; height: 14px; display: inline-block; border: 0px; padding: 0px; background-color: red;">&nbsp;</div> <div id="strength" style="display: inline-block;"></div></div></td>
		</tr>
		<tr>
			<td><?php printMLText("password_repeat");?></td>
			<td><input type="password" name="newpasswordrepeat" id="passwordrepeat"></td>
		</tr>
		<tr>
			<td colspan="2"><input type="Submit" value="<?php printMLText("submit_password") ?>"></td>
		</tr>
	</table>
</form>
<?php $this->contentContainerEnd(); ?>
<script language="JavaScript">document.form1.newpassword.focus();</script>
<p><a href="../out/out.Login.php"><?php echo getMLText("login"); ?></a></p>
<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
