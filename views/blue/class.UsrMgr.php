<?php
/**
 * Implementation of UsrMgr view
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
 * Class which outputs the html page for UsrMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UsrMgr extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$seluser = $this->params['seluser'];
		$users = $this->params['allusers'];
		$groups = $this->params['allgroups'];
		$passwordstrength = $this->params['passwordstrength'];
		$passwordexpiration = $this->params['passwordexpiration'];
		$httproot = $this->params['httproot'];
		$enableuserimage = $this->params['enableuserimage'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

?>
<script language="JavaScript">

function checkForm(num)
{
	msg = "";
	eval("var formObj = document.form" + num + ";");

	if (formObj.login.value == "") msg += "<?php printMLText("js_no_login");?>\n";
	if ((num == '0') && (formObj.pwd.value == "")) msg += "<?php printMLText("js_no_pwd");?>\n";
	if ((formObj.pwd.value != formObj.pwdconf.value)&&(formObj.pwd.value != "" )&&(formObj.pwd.value != "" )) msg += "<?php printMLText("js_pwd_not_conf");?>\n";
	if (formObj.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
	if (formObj.email.value == "") msg += "<?php printMLText("js_no_email");?>\n";
	//if (formObj.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}


obj = -1;
function showUser(selectObj) {
	if (obj != -1)
		obj.style.display = "none";

	id = selectObj.options[selectObj.selectedIndex].value;
	if (id == -1)
		return;

	obj = document.getElementById("keywords" + id);
	obj.style.display = "";
}
</script>
<?php
		$this->contentHeading(getMLText("user_management"));
		$this->contentContainerStart();
?>
<table><tr>

<td><?php echo getMLText("selection")?>:
<select onchange="showUser(this)" id="selector">
<option value="-1"><?php echo getMLText("choose_user")?>
<option value="0"><?php echo getMLText("add_user")?>
<?php
		$selected=0;
		$count=2;
		foreach ($users as $currUser) {
			if ($seluser && $currUser->getID()==$seluser->getID()) $selected=$count;
			print "<option value=\"".$currUser->getID()."\">" . htmlspecialchars($currUser->getLogin() . " - ". $currUser->getFullName());
			$count++;
		}
?>
</select>
&nbsp;&nbsp;
</td>

<td id="keywords0" style="display : none;">

	<form action="../op/op.UsrMgr.php" method="post" enctype="multipart/form-data" name="form0" onsubmit="return checkForm('0');">
  <?php echo createHiddenFieldWithKey('adduser'); ?>
	<input type="Hidden" name="action" value="adduser">
	<table>
		<tr>
			<td><?php printMLText("user_login");?>:</td>
			<td><input type="text" name="login"></td>
		</tr>
		<tr>
			<td><?php printMLText("password");?>:</td>
			<td><input class="pwd" name="pwd" rel="outerstrength" type="Password"> <div id="outerstrength" style="min-width: 100px; height: 14px; display: inline-block; border: 1px solid black; padding: 1px;"><div id="innerstrength" style="width: 0px; height: 14px; display: inline-block; border: 0px; padding: 0px; background-color: red;">&nbsp;</div> <div id="strength" style="display: inline-block;"></div></div></td>
		</tr>
		<tr>
			<td><?php printMLText("confirm_pwd");?>:</td>
			<td><input type="password" name="pwdconf"></td>
		</tr>
<?php
		if($passwordexpiration > 0) {
?>
		<tr>
			<td><?php printMLText("password_expiration");?>:</td>
			<td><select name="pwdexpiration"><option value="<?php echo date('Y-m-d H:i:s'); ?>"><?php printMLText("now");?></option><option value="<?php echo date('Y-m-d H:i:s', time()+$passwordexpiration*86400); ?>"><?php printMLText("according_settings");?></option></select></td>
		</tr>
<?php
		}
?>
		<tr>
			<td><?php printMLText("user_name");?>:</td>
			<td><input type="text" name="name"></td>
		</tr>
		<tr>
			<td><?php printMLText("email");?>:</td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="50"></textarea></td>
		</tr>
		<tr>
			<td><?php printMLText("role");?>:</td>
			<td><select name="role"><option value="<?php echo SeedDMS_Core_User::role_user ?>"><?php printMLText("role_user"); ?></option><option value="<?php echo SeedDMS_Core_User::role_admin ?>"><?php printMLText("role_admin"); ?></option><option value="<?php echo SeedDMS_Core_User::role_guest ?>"><?php printMLText("role_guest"); ?></option></select></td>
		</tr>
		<tr>
			<td><?php printMLText("is_hidden");?>:</td>
			<td><input type="checkbox" name="ishidden" value="1"></td>
		</tr>
		<tr>
			<td><?php printMLText("is_disabled");?>:</td>
			<td><input type="checkbox" name="isdisabled" value="1"></td>
		</tr>

<?php if ($enableuserimage){ ?>

		<tr>
			<td><?php printMLText("user_image");?>:</td>
			<td><input type="File" name="userfile"></td>
		</tr>

<?php } ?>

		<tr>
			<td><?php printMLText("reviewers");?>:</td>
			<td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList"><?php
				foreach ($users as $usr) {

					if ($usr->isGuest()) continue;

					print "<li class=\"cbSelectItem\"><input id='revUsr".$usr->getID()."' type='checkbox' name='usrReviewers[]' value='". $usr->getID() ."'>".htmlspecialchars($usr->getLogin());
				}
?>
				</ul>
				</div>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				foreach ($groups as $grp) {

					print "<li class=\"cbSelectItem\"><input id='revGrp".$grp->getID()."' type='checkbox' name='grpReviewers[]' value='". $grp->getID() ."'>".htmlspecialchars($grp->getName());
				}
?>
				</ul>
				</div>
			</td>
		</tr>

		<tr>
			<td><?php printMLText("approvers");?>:</td>
			<td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				foreach ($users as $usr) {

					if ($usr->isGuest()) continue;

					print "<li class=\"cbSelectItem\"><input id='appUsr".$usr->getID()."' type='checkbox' name='usrApprovers[]' value='". $usr->getID() ."'>".htmlspecialchars($usr->getLogin());
				}
?>
				</ul>
				</div>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				foreach ($groups as $grp) {

					print "<li class=\"cbSelectItem\"><input id='revGrp".$grp->getID()."' type='checkbox' name='grpApprovers[]' value='". $grp->getID() ."'>".htmlspecialchars($grp->getName());
				}
?>
				</ul>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="Submit" value="<?php printMLText("add_user");?>"></td>
		</tr>
	</table>
	</form>

</td>


<?php
		foreach ($users as $currUser) {

			print "<td id=\"keywords".$currUser->getID()."\" style=\"display : none;\">";

			$this->contentSubHeading(getMLText("user")." : ".htmlspecialchars($currUser->getLogin()));
?>

	<a class="standardText" href="../out/out.RemoveUser.php?userid=<?php print $currUser->getID();?>"><img src="images/del.gif" width="15" height="15" border="0" align="absmiddle" alt=""> <?php printMLText("rm_user");?></a>

	<?php	$this->contentSubHeading(getMLText("edit_user"));?>

	<form action="../op/op.UsrMgr.php" method="post" enctype="multipart/form-data" name="form<?php print $currUser->getID();?>" onsubmit="return checkForm('<?php print $currUser->getID();?>');">
	<?php echo createHiddenFieldWithKey('edituser'); ?>
	<input type="hidden" name="userid" value="<?php print $currUser->getID();?>">
	<input type="hidden" name="action" value="edituser">
	<table>
		<tr>
			<td><?php printMLText("user_login");?>:</td>
			<td><input type="text" name="login" value="<?php print htmlspecialchars($currUser->getLogin());?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("password");?>:</td>
			<td><input type="password" class="pwd" rel="outerstrength<?php echo $currUser->getID(); ?>" name="pwd"> <div id="outerstrength<?php echo $currUser->getID(); ?>" style="min-width: 100px; height: 14px; display: inline-block; border: 1px solid black; padding: 1px;"><div id="innerstrength" style="width: 0px; height: 14px; display: inline-block; border: 0px; padding: 0px; background-color: red;">&nbsp;</div> <div id="strength" style="display: inline-block;"></td>
		</tr>
		<tr>
			<td><?php printMLText("confirm_pwd");?>:</td>
			<td><input type="Password" name="pwdconf"></td>
		</tr>
<?php
	if($passwordexpiration > 0) {
?>
		<tr>
			<td><?php printMLText("password_expiration");?>:</td>
			<td><select name="pwdexpiration"><option value="<?php echo date('Y-m-d H:i:s'); ?>"><?php printMLText("now");?></option><option value="<?php echo date('Y-m-d H:i:s', time()+$passwordexpiration*86400); ?>"><?php printMLText("according_settings");?></option></select> <?php echo $currUser->getPwdExpiration(); ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td><?php printMLText("user_name");?>:</td>
			<td><input type="text" name="name" value="<?php print htmlspecialchars($currUser->getFullName());?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("email");?>:</td>
			<td><input type="text" name="email" value="<?php print htmlspecialchars($currUser->getEmail()) ;?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="50"><?php print htmlspecialchars($currUser->getComment());?></textarea></td>
		</tr>
		<tr>
			<td><?php printMLText("role");?>:</td>
			<td><select name="role"><option value="<?php echo SeedDMS_Core_User::role_user ?>"><?php printMLText("role_user"); ?></option><option value="<?php echo SeedDMS_Core_User::role_admin ?>" <?php if($currUser->getRole() == SeedDMS_Core_User::role_admin) echo "selected"; ?>><?php printMLText("role_admin"); ?></option><option value="<?php echo SeedDMS_Core_User::role_guest ?>" <?php if($currUser->getRole() == SeedDMS_Core_User::role_guest) echo "selected"; ?>><?php printMLText("role_guest"); ?></option></select></td>
		</tr>
		<tr>
			<td><?php printMLText("is_hidden");?>:</td>
			<td><input type="checkbox" name="ishidden" value="1"<?php print ($currUser->isHidden() ? " checked='checked'" : "");?>></td>
		</tr>
		<tr>
			<td><?php printMLText("is_disabled");?>:</td>
			<td><input type="checkbox" name="isdisabled" value="1"<?php print ($currUser->isDisabled() ? " checked='checked'" : "");?>></td>
		</tr>

<?php if ($enableuserimage){ ?>

		<tr>
			<td><?php printMLText("user_image");?>:</td>
			<td>
<?php
					if ($currUser->hasImage())
						print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$currUser->getId()."\">";
					else
						printMLText("no_user_image");
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("new_user_image");?>:</td>
			<td><input type="file" name="userfile" accept="image/jpeg"></td>
		</tr>

<?php } ?>


		<tr>
			<td><?php printMLText("reviewers");?>:</td>
			<td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
				<?php

				$res=$currUser->getMandatoryReviewers();

				foreach ($users as $usr) {

					if ($usr->isGuest() || ($usr->getID() == $currUser->getID()))
						continue;

					$checked=false;
					foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $checked=true;

					print "<li class=\"cbSelectItem\"><input id='revUsr".$usr->getID()."' type='checkbox' ".($checked?"checked='checked' ":"")."name='usrReviewers[]' value='". $usr->getID() ."'>".htmlspecialchars($usr->getLogin())."</li>\n";
				}
?>
				</ul>
				</div>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
				<?php
				foreach ($groups as $grp) {

					$checked=false;
					foreach ($res as $r) if ($r['reviewerGroupID']==$grp->getID()) $checked=true;

					print "<li class=\"cbSelectItem\"><input id='revGrp".$grp->getID()."' type='checkbox' ".($checked?"checked='checked' ":"")."name='grpReviewers[]' value='". $grp->getID() ."'>".htmlspecialchars($grp->getName())."</li>\n";
				}
?>
				</ul>
				</div>
			</td>
		</tr>

		<tr>
			<td><?php printMLText("approvers");?>:</td>
			<td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				$res=$currUser->getMandatoryApprovers();
				foreach ($users as $usr) {
					if ($usr->isGuest() || ($usr->getID() == $currUser->getID()))
						continue;

					$checked=false;
					foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $checked=true;

					print "<li class=\"cbSelectItem\"><input id='appUsr".$usr->getID()."' type='checkbox' ".($checked?"checked='checked' ":"")."name='usrApprovers[]' value='". $usr->getID() ."'>".htmlspecialchars($usr->getLogin())."</li>\n";
				}
?>
				</ul>
				</div>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
				<div class="cbSelectContainer">
				<ul class="cbSelectList">
<?php
				foreach ($groups as $grp) {

					$checked=false;
					foreach ($res as $r) if ($r['approverGroupID']==$grp->getID()) $checked=true;

					print "<li class=\"cbSelectItem\"><input id='revGrp".$grp->getID()."' type='checkbox' ".($checked?"checked='checked' ":"")."name='grpApprovers[]' value='". $grp->getID() ."'>".htmlspecialchars($grp->getName())."</li>\n";
				}
?>
				</ul>
				</div>
			</td>
		</tr>

		<tr>
			<td colspan="2"><input type="Submit" value="<?php printMLText("save");?>"></td>
		</tr>
	</table>
	</form>
</td>
<?php  } ?>
</tr></table>

<script language="JavaScript">

sel = document.getElementById("selector");
sel.selectedIndex=<?php print $selected ?>;
showUser(sel);

</script>


<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
