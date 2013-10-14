<?php
/**
 * Implementation of Login view
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
 * Class which outputs the html page for Login view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_Login extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$enableguestlogin = $this->params['enableguestlogin'];
		$enablepasswordforgotten = $this->params['enablepasswordforgotten'];
		$refer = $this->params['referrer'];
		$themes = $this->params['themes'];

		$this->htmlStartPage(getMLText("sign_in"), "login");
		$this->globalBanner();
		$this->pageNavigation(getMLText("sign_in"));
?>
<script language="JavaScript">
function checkForm()
{
	msg = "";
	if (document.form1.login.value == "") msg += "<?php printMLText("js_no_login");?>\n";
	if (document.form1.pwd.value == "") msg += "<?php printMLText("js_no_pwd");?>\n";
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}

function guestLogin()
{
	url = "../op/op.Login.php?login=guest" + 
		"&sesstheme=" + document.form1.sesstheme.options[document.form1.sesstheme.options.selectedIndex].value +
		"&lang=" + document.form1.lang.options[document.form1.lang.options.selectedIndex].value;
	if (document.form1.referuri) {
		url += "&referuri=" + escape(document.form1.referuri.value);
	}
	document.location.href = url;
}

</script>
<?php $this->contentContainerStart(); ?>
<form action="../op/op.Login.php" method="post" name="form1" onsubmit="return checkForm();">
<?php
		if ($refer) {
			echo "<input type='hidden' name='referuri' value='".sanitizeString($refer)."'/>";
		}
?>
	<table border="0">
		<tr>
			<td><?php printMLText("user_login");?></td>
			<td><input name="login" id="login"></td>
		</tr>
		<tr>
			<td><?php printMLText("password");?></td>
			<td><input name="pwd" type="Password"></td>
		</tr>
		<tr>
			<td><?php printMLText("language");?></td>
			<td>
<?php
			print "<select name=\"lang\">";
			print "<option value=\"\">-";
			$languages = getLanguages();
			foreach ($languages as $currLang) {
				print "<option value=\"".$currLang."\">".getMLText($currLang)."</option>";
			}
			print "</select>";
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("theme");?></td>
		<td>
<?php
			print "<select name=\"sesstheme\">";
			print "<option value=\"\">-";
			foreach ($themes as $currTheme) {
				print "<option value=\"".$currTheme."\">".$currTheme;
			}
			print "</select>";
?>
		</td>
		</tr>
		<tr>
			<td colspan="2"><input type="Submit" value="<?php printMLText("submit_login") ?>"></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$tmpfoot = array();
		if ($enableguestlogin)
			$tmpfoot[] = "<a href=\"javascript:guestLogin()\">" . getMLText("guest_login") . "</a>\n";
		if ($enablepasswordforgotten)
			$tmpfoot[] = "<a href=\"../out/out.PasswordForgotten.php\">" . getMLText("password_forgotten") . "</a>\n";
		if($tmpfoot) {
			print "<p>";
			print implode(' | ', $tmpfoot);
			print "</p>\n";
		}
?>
<script language="JavaScript">document.form1.login.focus();</script>
<?php
		$this->htmlEndPage();
	} /* }}} */
}
?>
