<?php
/**
 * Implementation of DocumentAccess view
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
 * Class which outputs the html page for DocumentAccess view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DocumentAccess extends SeedDMS_Blue_Style {

	function printAccessModeSelection($defMode) { /* {{{ */
		print "<select name=\"mode\">\n";
		print "\t<option value=\"".M_NONE."\"" . (($defMode == M_NONE) ? " selected" : "") . ">" . getMLText("access_mode_none") . "</option>\n";
		print "\t<option value=\"".M_READ."\"" . (($defMode == M_READ) ? " selected" : "") . ">" . getMLText("access_mode_read") . "</option>\n";
		print "\t<option value=\"".M_READWRITE."\"" . (($defMode == M_READWRITE) ? " selected" : "") . ">" . getMLText("access_mode_readwrite") . "</option>\n";
		print "\t<option value=\"".M_ALL."\"" . (($defMode == M_ALL) ? " selected" : "") . ">" . getMLText("access_mode_all") . "</option>\n";
		print "</select>\n";
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$document = $this->params['document'];
		$folder = $this->params['folder'];
		$allUsers = $this->params['allusers'];
		$allGroups = $this->params['allgroups'];


		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");

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

		$this->contentHeading(getMLText("edit_document_access"));
		$this->contentContainerStart();

		if ($user->isAdmin()) {

			$this->contentSubHeading(getMLText("set_owner"));
?>
	<form action="../op/op.DocumentAccess.php">
  <?php echo createHiddenFieldWithKey('documentaccess'); ?>
	<input type="Hidden" name="action" value="setowner">
	<input type="Hidden" name="documentid" value="<?php print $document->getId();?>">
	<?php printMLText("owner");?> : <select name="ownerid">
	<?php
	$owner = $document->getOwner();
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
		$this->contentSubHeading(getMLText("access_inheritance"));

		if ($document->inheritsAccess()) {
			printMLText("inherits_access_msg");
?>
  <p>
	<form action="../op/op.DocumentAccess.php" style="display: inline-block;">
  <?php echo createHiddenFieldWithKey('documentaccess'); ?>
	<input type="hidden" name="documentid" value="<?php print $document->getId();?>">
	<input type="hidden" name="action" value="notinherit">
	<input type="hidden" name="mode" value="copy">
	<input type="submit" value="<?php printMLText("inherits_access_copy_msg")?>">
	</form>
	<form action="../op/op.DocumentAccess.php" style="display: inline-block;">
  <?php echo createHiddenFieldWithKey('documentaccess'); ?>
	<input type="hidden" name="documentid" value="<?php print $document->getId();?>">
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
	<form action="../op/op.DocumentAccess.php">
  <?php echo createHiddenFieldWithKey('documentaccess'); ?>
	<input type="hidden" name="documentid" value="<?php print $document->getId();?>">
	<input type="hidden" name="action" value="inherit">
	<input type="submit" value="<?php printMLText("does_not_inherit_access_msg")?>">
	</form>
<?php
	$accessList = $document->getAccessList();

	$this->contentSubHeading(getMLText("default_access"));
?>
<form action="../op/op.DocumentAccess.php">
  <?php echo createHiddenFieldWithKey('documentaccess'); ?>
	<input type="Hidden" name="documentid" value="<?php print $document->getId();?>">
	<input type="Hidden" name="action" value="setdefault">
	<?php $this->printAccessModeSelection($document->getDefaultAccess()); ?>
	<input type="Submit" value="<?php printMLText("save");?>">
</form>

<?php

		$this->contentSubHeading(getMLText("edit_existing_access"));

		/* memorїze users with access rights */
		$memusers = array();
		/* memorize groups with access rights */
		$memgroups = array();
		if (count($accessList["users"]) != 0 || count($accessList["groups"]) != 0) {

			print "<table class=\"defaultView\">";

			foreach ($accessList["users"] as $userAccess) {
				$userObj = $userAccess->getUser();
				$memusers[] = $userObj->getID();
				print "<tr>\n";
				print "<td><img src=\"images/usericon.gif\" class=\"mimeicon\"></td>\n";
				print "<td>". htmlspecialchars($userObj->getFullName()) . "</td>\n";
				print "<form action=\"../op/op.DocumentAccess.php\">\n";
				print "<td>\n";
				$this->printAccessModeSelection($userAccess->getMode());
				print "</td>\n";
				print "<td><span class=\"actions\">\n";
				echo createHiddenFieldWithKey('documentaccess')."\n";
				print "<input type=\"Hidden\" name=\"documentid\" value=\"".$document->getId()."\">\n";
				print "<input type=\"hidden\" name=\"action\" value=\"editaccess\">\n";
				print "<input type=\"hidden\" name=\"userid\" value=\"".$userObj->getID()."\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/save.gif\">".getMLText("save")." ";
				print "</span></td>\n";
				print "</form>\n";
				print "<form action=\"../op/op.DocumentAccess.php\">\n";
				print "<td><span class=\"actions\">\n";
				echo createHiddenFieldWithKey('documentaccess')."\n";
				print "<input type=\"Hidden\" name=\"documentid\" value=\"".$document->getId()."\">\n";
				print "<input type=\"hidden\" name=\"action\" value=\"delaccess\">\n";
				print "<input type=\"hidden\" name=\"userid\" value=\"".$userObj->getID()."\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/del.gif\">".getMLText("delete")." ";
				print "<span></td>\n";
				print "</form>\n";
				print "</tr>\n";
			}

			foreach ($accessList["groups"] as $groupAccess) {
				$groupObj = $groupAccess->getGroup();
				$memgroups[] = $groupObj->getID();
				$mode = $groupAccess->getMode();
				print "<tr>";
				print "<td><img src=\"images/groupicon.gif\" class=\"mimeicon\"></td>";
				print "<td>". htmlspecialchars($groupObj->getName()) . "</td>";
				print "<form action=\"../op/op.DocumentAccess.php\">";
				print "<td>";
				$this->printAccessModeSelection($groupAccess->getMode());
				print "</td>\n";
				print "<td><span class=\"actions\">\n";
				echo createHiddenFieldWithKey('documentaccess')."\n";
				print "<input type=\"Hidden\" name=\"documentid\" value=\"".$document->getId()."\">";
				print "<input type=\"Hidden\" name=\"action\" value=\"editaccess\">";
				print "<input type=\"Hidden\" name=\"groupid\" value=\"".$groupObj->getID()."\">";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/save.gif\">".getMLText("save")." ";
				print "</span></td>\n";
				print "</form>";
				print "<td><span class=\"actions\">\n";
				print "<form action=\"../op/op.DocumentAccess.php\">\n";
				echo createHiddenFieldWithKey('documentaccess')."\n";
				print "<input type=\"Hidden\" name=\"documentid\" value=\"".$document->getId()."\">\n";
				print "<input type=\"Hidden\" name=\"action\" value=\"delaccess\">\n";
				print "<input type=\"Hidden\" name=\"groupid\" value=\"".$groupObj->getID()."\">\n";
				print "<input type=\"Image\" class=\"mimeicon\" src=\"images/del.gif\">".getMLText("delete")." ";
				print "</form>";
				print "</span></td>\n";
				print "</tr>\n";
			}
			
			print "</table><br>";
		}
?>
<form action="../op/op.DocumentAccess.php" name="form1" onsubmit="return checkForm();">
<?php echo createHiddenFieldWithKey('documentaccess'); ?>
<input type="Hidden" name="documentid" value="<?php print $document->getId()?>">
<input type="Hidden" name="action" value="addaccess">
<table>
<tr>
<td><?php printMLText("user");?>:</td>
<td>
<select name="userid">
<option value="-1"><?php printMLText("select_one");?></option>
<?php
		foreach ($allUsers as $userObj) {
			if ($userObj->isGuest() || in_array($userObj->getID(), $memusers)) {
				continue;
			}
			print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "</option>\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("group");?>:</td>
<td>
<select name="groupid">
<option value="-1"><?php printMLText("select_one");?></option>
<?php
		foreach ($allGroups as $groupObj) {
			if(in_array($groupObj->getID(), $memgroups))
				continue;
			print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "</option>\n";
		}
?>
</select>
</td>
</tr>
<tr>
<td><?php printMLText("access_mode");?>:</td>
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
