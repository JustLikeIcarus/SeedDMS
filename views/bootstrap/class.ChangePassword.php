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
require_once("class.Bootstrap.php");

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
class SeedDMS_View_ChangePassword extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$referuri = $this->params['referuri'];
		$hash = $this->params['hash'];
		$passwordstrength = $this->params['passwordstrength'];

		$this->htmlStartPage(getMLText("change_password"), "login");
		$this->globalBanner();
		$this->contentStart();
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
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("password");?>:</td>
			<td><input class="pwd" type="password" rel="strengthbar" name="newpassword" id="password"></td>
		</tr>
<?php
		if($passwordstrength > 0) {
?>
		<tr>
			<td><?php printMLText("password_strength");?>:</td>
			<td>
				<div id="strengthbar" class="progress" style="width: 220px; height: 30px; margin-bottom: 8px;"><div class="bar bar-danger" style="width: 0%;"></div></div>
			</td>
		</tr>
<?php
		}
?>
		<tr>
			<td><?php printMLText("confirm_pwd");?>:</td>
			<td><input type="password" name="newpasswordrepeat" id="passwordrepeat"></td>
		</tr>
		<tr>
			<td></td>
			<td><input class="btn" type="submit" value="<?php printMLText("submit_password") ?>"></td>
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
