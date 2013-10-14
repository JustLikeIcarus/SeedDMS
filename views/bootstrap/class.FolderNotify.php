<?php
/**
 * Implementation of FolderNotify view
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
 * Class which outputs the html page for FolderNotify view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_FolderNotify extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$allUsers = $this->params['allusers'];
		$allGroups = $this->params['allgroups'];
		$strictformcheck = $this->params['strictformcheck'];

		$notifyList = $folder->getNotifyList();

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true), "view_folder", $folder);

?>

<script language="JavaScript">
function checkForm()
{
	msg = new Array();
	if ((document.form1.userid.options[document.form1.userid.selectedIndex].value == -1) && 
		(document.form1.groupid.options[document.form1.groupid.selectedIndex].value == -1))
			msg.push("<?php printMLText("js_select_user_or_group");?>");
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

<?php
		$this->contentHeading(getMLText("edit_existing_notify"));
		$this->contentContainerStart();

		$userNotifyIDs = array();
		$groupNotifyIDs = array();

		print "<table class=\"table-condensed\">\n";
		if (empty($notifyList["users"]) && empty($notifyList["groups"])) {
			print "<tr><td>".getMLText("empty_notify_list")."</td></tr>";
		}
		else {
			foreach ($notifyList["users"] as $userNotify) {
				print "<tr>";
				print "<td><i class=\"icon-user\"></i></td>";
				print "<td>" . htmlspecialchars($userNotify->getFullName()) . "</td>";
				if ($user->isAdmin() || $user->getID() == $userNotify->getID()) {
					print "<form action=\"../op/op.FolderNotify.php\" method=\"post\">\n";
					echo createHiddenFieldWithKey('foldernotify')."\n";
					print "<input type=\"Hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
					print "<input type=\"Hidden\" name=\"action\" value=\"delnotify\">\n";
					print "<input type=\"Hidden\" name=\"userid\" value=\"".$userNotify->getID()."\">\n";
					print "<td>";
					print "<button type=\"submit\" class=\"btn btn-mini\"><i class=\"icon-remove\"></i> ".getMLText("delete")."</button>";
					print "</td>";
					print "</form>\n";
				}else print "<td></td>";
				print "</tr>";
				$userNotifyIDs[] = $userNotify->getID();
			}

			foreach ($notifyList["groups"] as $groupNotify) {
				print "<tr>";
				print "<td><i class=\"icon-group\"></i></td>";
				print "<td>" . htmlspecialchars($groupNotify->getName()) . "</td>";
				if ($user->isAdmin() || $groupNotify->isMember($user,true)) {
					print "<form action=\"../op/op.FolderNotify.php\" method=\"post\">\n";
					echo createHiddenFieldWithKey('foldernotify')."\n";
					print "<input type=\"Hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
					print "<input type=\"Hidden\" name=\"action\" value=\"delnotify\">\n";
					print "<input type=\"Hidden\" name=\"groupid\" value=\"".$groupNotify->getID()."\">\n";
					print "<td>";
					print "<button type=\"submit\" class=\"btn btn-mini\"><i class=\"icon-remove\"></i> ".getMLText("delete")."</button>";
					print "</td>";
					print "</form>\n";
				}else print "<td></td>";
				print "</tr>";
				$groupNotifyIDs[] = $groupNotify->getID();
			}
		}
		print "</table>\n";

?>
<br>
<form action="../op/op.FolderNotify.php" method="post" name="form1" onsubmit="return checkForm();">
<?php	echo createHiddenFieldWithKey('foldernotify'); ?>
<input type="Hidden" name="folderid" value="<?php print $folder->getID()?>">
<input type="Hidden" name="action" value="addnotify">
<table class="table-condensed">
	<tr>
		<td><?php printMLText("user");?>:</td>
		<td>
			<select name="userid">
				<option value="-1"><?php printMLText("select_one");?>
				<?php
					if ($user->isAdmin()) {
						foreach ($allUsers as $userObj) {
							if (!$userObj->isGuest() && ($folder->getAccessMode($userObj) >= M_READ) && !in_array($userObj->getID(), $userNotifyIDs))
								print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getFullName()) . "\n";
						}
					}
					elseif (!$user->isGuest() && !in_array($user->getID(), $userNotifyIDs)) {
						print "<option value=\"".$user->getID()."\">" . htmlspecialchars($user->getFullName()) . "\n";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php printMLText("group");?>:</td>
		<td>
			<select name="groupid">
				<option value="-1"><?php printMLText("select_one");?>
				<?php
					foreach ($allGroups as $groupObj) {
						if (($user->isAdmin() || $groupObj->isMember($user,true)) && $folder->getGroupAccessMode($groupObj) >= M_READ && !in_array($groupObj->getID(), $groupNotifyIDs)) {
							print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
						}
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" class="btn" value="<?php printMLText("add") ?>"></td>
	</tr>
</table>
</form>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
