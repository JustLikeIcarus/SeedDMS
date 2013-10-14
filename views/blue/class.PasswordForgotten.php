<?php
/**
 * Implementation of PasswordForgotten view
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
 * Class which outputs the html page for PasswordForgotten view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_PasswordForgotten extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$referrer = $this->params['referrer'];

		$this->htmlStartPage(getMLText("password_forgotten"), "login");
		$this->globalBanner();
		$this->pageNavigation(getMLText("password_forgotten"));
?>

<?php $this->contentContainerStart(); ?>
<form action="../op/op.PasswordForgotten.php" method="post" name="form1" onsubmit="return checkForm();">
<?php
		if ($referrer) {
			echo "<input type='hidden' name='referuri' value='".$referrer."'/>";
		}
?>
  <p><?php printMLText("password_forgotten_text"); ?></p>
	<table border="0">
		<tr>
			<td><?php printMLText("login");?>:</td>
			<td><input type="text" name="login" id="login"></td>
		</tr>
		<tr>
			<td><?php printMLText("email");?>:</td>
			<td><input type="text" name="email" id="email"></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="<?php printMLText("submit_password_forgotten") ?>"></td>
		</tr>
	</table>
</form>
<?php $this->contentContainerEnd(); ?>
<script language="JavaScript">document.form1.email.focus();</script>
<p><a href="../out/out.Login.php"><?php echo getMLText("login"); ?></a></p>
<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
