<?php
/**
 * Implementation of ForcePasswordChange view
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
 * Class which outputs the html page for ForcePasswordChange view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ForcePasswordChange extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$passwordstrength = $this->params['passwordstrength'];

		$this->htmlStartPage(getMLText("sign_in"), "login");
		$this->globalBanner();
		$this->contentStart();
		echo "<h3>".getMLText('password_expiration')."</h3>";
		echo "<div class=\"alert\">".getMLText('password_expiration_text')."</div>";
		$this->contentContainerStart();
?>
<form action="../op/op.EditUserData.php" method="post" name="form1" onsubmit="return checkForm();">
<table>
	<tr>
		<td><?php printMLText("current_password");?>:</td>
		<td><input id="currentpwd" type="Password" name="currentpwd" size="30"></td>
	</tr>
	<tr>
		<td><?php printMLText("password");?>:</td>
		<td><input class="pwd" type="Password" rel="strengthbar" name="pwd" size="30"></td>
	</tr>
		<tr>
			<td><?php printMLText("password_strength");?>:</td>
			<td>
				<div id="strengthbar" class="progress" style="width: 220px; height: 30px; margin-bottom: 8px;"><div class="bar bar-danger" style="width: 0%;"></div></div>
			</td>
		</tr>
	<tr>
		<td><?php printMLText("confirm_pwd");?>:</td>
		<td><input id="pwdconf" type="Password" name="pwdconf" size="30"></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="btn" type="submit" value="<?php printMLText("submit_userinfo") ?>"></td>
	</tr>
</table>
<input type="hidden" name="fullname" value="<?php print htmlspecialchars($user->getFullName());?>" />
<input type="hidden" name="email" value="<?php print htmlspecialchars($user->getEmail());?>" />
<input type="hidden" name="comment" value="<?php print htmlspecialchars($user->getComment());?>" />
</form>

<?php
		$this->contentContainerEnd();
		$tmpfoot = array();
		$tmpfoot[] = "<a href=\"../op/op.Logout.php\">" . getMLText("logout") . "</a>\n";
		print "<p>";
		print implode(' | ', $tmpfoot);
		print "</p>\n";
		$this->htmlEndPage();
	} /* }}} */
}
?>
