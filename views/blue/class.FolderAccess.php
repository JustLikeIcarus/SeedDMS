<?php
/**
 * Implementation of FolderAccess view
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
 * Class which outputs the html page for FolderAccess view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_FolderAccess extends SeedDMS_Blue_Style {
	function printAccessModeSelection($defMode) { /* {{{ */
		print "<select name=\"mode\">\n";
		print "\t<option value=\"".M_NONE."\"" . (($defMode == M_NONE) ? " selected" : "") . ">" . getMLText("access_mode_none") . "\n";
		print "\t<option value=\"".M_READ."\"" . (($defMode == M_READ) ? " selected" : "") . ">" . getMLText("access_mode_read") . "\n";
		print "\t<option value=\"".M_READWRITE."\"" . (($defMode == M_READWRITE) ? " selected" : "") . ">" . getMLText("access_mode_readwrite") . "\n";
		print "\t<option value=\"".M_ALL."\"" . (($defMode == M_ALL) ? " selected" : "") . ">" . getMLText("access_mode_all") . "\n";
		print "</select>\n";
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$allUsers = $this->params['allusers'];
		$allGroups = $this->params['allgroups'];
		$rootfolderid = $this->params['rootfolderid'];

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "view_folder", $folder);
?>

<script language="JavaScript">
function checkForm()
{
	msg = "";
	if ((document.form1.userid.options[document.form1.userid.selectedIndex].value == -1) && 
		(document.form1.groupid.options[document.form1.groupid.selectedIndex].value == -1))
			msg += "<?php printMLText("js_select_user_or_group");?>\n";
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
		$this->contentHeading(getMLText("edit_folder_access"));
		$this->contentContainerStart();

		if ($user->isAdmin()) {

			$this->contentSubHeading(getMLText("set_owner"));
?>
	<form action="../op/op.FolderAccess.php">
	<?php echo createHiddenFieldWithKey('folderaccess'); ?>
	<input type="Hidden" name="action" value="setowner">
	<input type="Hidden" name="folderid" value="<?php print $folder->getID();?>">
	<?php printMLText("owner");?> : <select name="ownerid">
<?php
			$owner = $folder->getOwner();
			foreach ($allUsers as $currUser) {
				if ($currUser->isGuest())
					continue;
				print "<option value=\"".$currUser->getID()."\"";
				if ($currUser->getID() == $owner->getID())
					print " selected";
				print ">" . htmlspecialchars($currUser->getLogin() . " - " . $currUser->getFullname()) . "</option>\n";
			}
?>
	</select>
	<input type="submit" value="<?php printMLText("save")?>">
	</form>
	<?php
		}

		if ($folder->getID() != $rootfolderid && $folder->getParent()){

			$this->contentSubHeading(getMLText("access_inheritance"));
			
			if ($folder->inheritsAccess()) {
				printMLText("inherits_access_msg");
?>
  <p>
	<form action="../op/op.FolderAccess.php" style="display: inline-block;">
  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
	<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
	<input type="hidden" name="action" value="notinherit">
	<input type="hidden" name="mode" value="copy">
	<input type="submit" value="<?php printMLText("inherits_access_copy_msg")?>">
	</form>
	<form action="../op/op.FolderAccess.php" style="display: inline-block;">
  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
	<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
	<input type="hidden" name="action" value="notinherit">
	<input type="hidden" name="mode" value="empty">
	<input type="submit" value="<?php printMLText("inherits_access_empty_msg")?>">
	</form>
	</p>
<?php
				$this->contentContainerEnd();
				$this->htmlEndPage();
				return;
			}
?>
	<form action="../op/op.FolderAccess.php">
  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
	<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
	<input type="hidden" name="action" value="inherit">
	<input type="submit" value="<?php printMLText("does_not_inherit_access_msg")?>">
	</form>
<?php
		}

		$accessList = $folder->getAccessList();

		$this->contentSubHeading(getMLText("default_access"));
?>
<form action="../op/op.FolderAccess.php">
  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
	<input type="Hidden" name="folderid" value="<?php print $folder->getID();?>">
	<input type="Hidden" name="action" value="setdefault">
	<?php $this->printAccessModeSelection($folder->getDefaultAccess()); ?>
	<input type="submit" value="<?php printMLText("save");?>">
</form>

<?php

		$this->contentSubHeading(getMLText("edit_existing_access"));

		if ((count($accessList["users"]) != 0) || (count($accessList["groups"]) != 0)) {

			print "<table class=\"defaultView\">";

			foreach ($accessList["users"] as $userAccess) {
				$userObj = $userAccess->getUser();
				print "<tr>\n";
				print "<td><img src=\"images/usericon.gif\" class=\"mimeicon\"></td>\n";
				print "<td>". htmlspecialchars($userObj->getFullName()) . "</td>\n";
				print "<form action=\"../op/op.FolderAccess.php\">\n";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"Hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
				print "<input type=\"Hidden\" name=\"action\" value=\"editaccess\">\n";
				print "<input type=\"Hidden\" name=\"userid\" value=\"".$userObj->getID()."\">\n";
				print "<td>\n";
				$this->printAccessModeSelection($userAccess->getMode());
				print "</td>\n";
				print "<td><span class=\"actions\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/save.gif\">".getMLText("save")." ";
				print "</span></td>\n";
				print "</form>\n";
				print "<td><span class=\"actions\">\n";
				print "<form action=\"../op/op.FolderAccess.php\">\n";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"Hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
				print "<input type=\"Hidden\" name=\"action\" value=\"delaccess\">\n";
				print "<input type=\"Hidden\" name=\"userid\" value=\"".$userObj->getID()."\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/del.gif\">".getMLText("delete")." ";
				print "</form>\n";
				print "<span></td>\n";
				print "</tr>\n";
			}

			foreach ($accessList["groups"] as $groupAccess) {
				$groupObj = $groupAccess->getGroup();
				$mode = $groupAccess->getMode();
				print "<tr>";
				print "<td><img src=\"images/groupicon.gif\" class=\"mimeicon\"></td>";
				print "<td>". htmlspecialchars($groupObj->getName()) . "</td>";
				print "<form action=\"../op/op.FolderAccess.php\">";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"Hidden\" name=\"folderid\" value=\"".$folder->getID()."\">";
				print "<input type=\"Hidden\" name=\"action\" value=\"editaccess\">";
				print "<input type=\"Hidden\" name=\"groupid\" value=\"".$groupObj->getID()."\">";
				print "<td>";
				$this->printAccessModeSelection($groupAccess->getMode());
				print "</td>\n";
				print "<td><span class=\"actions\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/save.gif\">".getMLText("save")." ";
				print "</span></td>\n";
				print "</form>";
				print "<form action=\"../op/op.FolderAccess.php\">\n";
				print "<td><span class=\"actions\">\n";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"Hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
				print "<input type=\"Hidden\" name=\"action\" value=\"delaccess\">\n";
				print "<input type=\"Hidden\" name=\"groupid\" value=\"".$groupObj->getID()."\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/del.gif\">".getMLText("delete")." ";
				print "</span></td>\n";
				print "</form>";
				print "</tr>\n";
			}
			
			print "</table><br>";
		}
?>
<form action="../op/op.FolderAccess.php" name="form1" onsubmit="return checkForm();">
<?php echo createHiddenFieldWithKey('folderaccess'); ?>
<input type="Hidden" name="folderid" value="<?php print $folder->getID()?>">
<input type="Hidden" name="action" value="addaccess">
<table>
<tr>
<td><?php printMLText("user");?>:</td>
<td>
<select name="userid">
<option value="-1"><?php printMLText("select_one");?>
<?php
		foreach ($allUsers as $userObj) {
			if ($userObj->isGuest()) {
				continue;
			}
			print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "</option>\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td class="inputDescription"><?php printMLText("group");?>:</td>
<td>
<select name="groupid">
<option value="-1"><?php printMLText("select_one");?>
<?php
		foreach ($allGroups as $groupObj) {
			print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td class="inputDescription"><?php printMLText("access_mode");?>:</td>
<td>
<?php
		$this->printAccessModeSelection(M_READ);
?>
</td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="<?php printMLText("add");?>"></td>
</tr>
</table>
</form>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
