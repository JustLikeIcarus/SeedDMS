<?php
/**
 * Implementation of EditUserData view
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
 * Class which outputs the html page for EditUserData view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditUserData extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$enableuserimage = $this->params['enableuserimage'];
		$passwordstrength = $this->params['passwordstrength'];
		$httproot = $this->params['httproot'];

		$this->htmlStartPage(getMLText("edit_user_details"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("my_account"), "my_account");

?>

<script language="JavaScript">

function checkForm()
{
	msg = "";
	if (document.form1.pwd.value != document.form1.pwdconf.value) msg += "<?php printMLText("js_pwd_not_conf");?>\n";
	if (document.form1.fullname.value == "") msg += "<?php printMLText("js_no_name");?>\n";
	if (document.form1.email.value == "") msg += "<?php printMLText("js_no_email");?>\n";
//	if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}
</script>

<?php
		$this->contentHeading(getMLText("edit_user_details"));
		$this->contentContainerStart();
?>
<form action="../op/op.EditUserData.php" enctype="multipart/form-data" method="post" name="form1" onsubmit="return checkForm();">
<table>
	<tr>
		<td><?php printMLText("current_password");?>:</td>
		<td><input id="currentpwd" type="password" name="currentpwd" size="30"></td>
	</tr>
	<tr>
		<td><?php printMLText("new_password");?>:</td>
		<td><input class="pwd" rel="outerstrength" type="password" name="pwd" size="30"> <div id="outerstrength" style="min-width: 100px; height: 14px; display: inline-block; border: 1px solid black; padding: 1px;"><div id="innerstrength" style="width: 0px; height: 14px; display: inline-block; border: 0px; padding: 0px; background-color: red;">&nbsp;</div> <div id="strength" style="display: inline-block;"></div></div></td>
	</tr>
	<tr>
		<td><?php printMLText("confirm_pwd");?>:</td>
		<td><input id="pwdconf" type="Password" name="pwdconf" size="30"></td>
	</tr>
	<tr>
		<td><?php printMLText("name");?>:</td>
		<td><input type="text" name="fullname" value="<?php print htmlspecialchars($user->getFullName());?>" size="30"></td>
	</tr>
	<tr>
		<td><?php printMLText("email");?>:</td>
		<td><input type="text" name="email" value="<?php print htmlspecialchars($user->getEmail());?>" size="30"></td>
	</tr>
	<tr>
		<td><?php printMLText("comment");?>:</td>
		<td><textarea name="comment" rows="4" cols="80"><?php print htmlspecialchars($user->getComment());?></textarea></td>
	</tr>

<?php	
		if ($enableuserimage){	
?>	
	<tr>
		<td><?php printMLText("user_image");?>:</td>
		<td>
<?php
			if ($user->hasImage())
				print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$user->getId()."\">";
			else printMLText("no_user_image");
?>
		</td>
	</tr>
	<tr>
		<td><?php printMLText("new_user_image");?>:</td>
		<td><input type="file" name="userfile" accept="image/jpeg" size="30"></td>
	</tr>
	<tr>
		<td><?php printMLText("language");?>:</td>
		<td>
			<select name="language">
<?php
			$languages = getLanguages();
			foreach ($languages as $currLang) {
				print "<option value=\"".$currLang."\" ".(($user->getLanguage()==$currLang) ? "selected" : "").">".getMLText($currLang)."</option>";
			}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php printMLText("theme");?>:</td>
		<td>
			<select name="theme">
<?php
			$themes = UI::getStyles();
			foreach ($themes as $currTheme) {
				print "<option value=\"".$currTheme."\" ".(($user->getTheme()==$currTheme) ? "selected" : "").">".$currTheme."</option>";
			}
?>
			</select>
		</td>
	</tr>
<?php	} ?>

	<tr>
		<td colspan="2"><input type="submit" value="<?php printMLText("submit_userinfo") ?>"></td>
	</tr>
</table>
</form>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
