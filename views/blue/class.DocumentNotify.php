<?php
/**
 * Implementation of DocumentNotify view
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
 * Class which outputs the html page for DocumentNotify view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DocumentNotify extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$sortusersinlist = $this->params['sortusersinlist'];

		$notifyList = $document->getNotifyList();

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
		$this->contentHeading(getMLText("edit_existing_notify"));
		$this->contentContainerStart();

		$userNotifyIDs = array();
		$groupNotifyIDs = array();

		print "<table class=\"defaultView\">\n";
		if ((count($notifyList["users"]) == 0) && (count($notifyList["groups"]) == 0)) {
			print "<tr><td>".getMLText("empty_notify_list")."</td></tr>";
		}
		else {
			foreach ($notifyList["users"] as $userNotify) {
				print "<tr>";
				print "<td><img src=\"images/usericon.gif\" class=\"mimeicon\"></td>";
				print "<td>" . htmlspecialchars($userNotify->getLogin() . " - " . $userNotify->getFullName()) . "</td>";
				if ($user->isAdmin() || $user->getID() == $userNotify->getID()) {
					print "<td><a href=\"../op/op.DocumentNotify.php?documentid=". $document->getID() . "&action=delnotify&userid=".$userNotify->getID()."\"><img src=\"images/del.gif\" class=\"mimeicon\"></a>".getMLText("delete")."</td>";
				}else print "<td></td>";
				print "</tr>";
				$userNotifyIDs[] = $userNotify->getID();
			}
			foreach ($notifyList["groups"] as $groupNotify) {
				print "<tr>";
				print "<td><img src=\"images/groupicon.gif\" width=16 height=16 border=0></td>";
				print "<td>" . htmlspecialchars($groupNotify->getName()) . "</td>";
				if ($user->isAdmin() || $groupNotify->isMember($user,true)) {
					print "<td><a href=\"../op/op.DocumentNotify.php?documentid=". $document->getID() . "&action=delnotify&groupid=".$groupNotify->getID()."\"><img src=\"images/del.gif\" class=\"mimeicon\"></a>".getMLText("delete")."</td>";
				}else print "<td></td>";
				print "</tr>";
				$groupNotifyIDs[] = $groupNotify->getID();
			}
		}
		print "</table>\n";

?>
<br>

<form action="../op/op.DocumentNotify.php" name="form1" onsubmit="return checkForm();">
<input type="hidden" name="documentid" value="<?php print $document->getID()?>">
<input type="hidden" name="action" value="addnotify">
<table>
	<tr>
		<td><?php printMLText("user");?>:</td>
		<td>
			<select name="userid">
				<option value="-1"><?php printMLText("select_one");?>
				<?php
					if ($user->isAdmin()) {
						$allUsers = $dms->getAllUsers($sortusersinlist);
						foreach ($allUsers as $userObj) {
							if (!$userObj->isGuest() && ($document->getAccessMode($userObj) >= M_READ) && !in_array($userObj->getID(), $userNotifyIDs))
								print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "\n";
						}
					}
					elseif (!$user->isGuest() && !in_array($user->getID(), $userNotifyIDs)) {
						print "<option value=\"".$user->getID()."\">" . htmlspecialchars($user->getLogin() . " - " . $user->getFullName()) . "\n";
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
					$allGroups = $dms->getAllGroups();
					foreach ($allGroups as $groupObj) {
						if (($user->isAdmin() || $groupObj->isMember($user,true)) && $document->getGroupAccessMode($groupObj) >= M_READ && !in_array($groupObj->getID(), $groupNotifyIDs)) {
							print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
						}
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="Submit" value="<?php printMLText("add") ?>"></td>
	</tr>
</table>
</form>

<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
