<?php
/**
 * Implementation of GroupMgr view
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
 * Class which outputs the html page for GroupMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_GroupMgr extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$selgroup = $this->params['selgroup'];
		$allUsers = $this->params['allusers'];
		$allGroups = $this->params['allgroups'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

?>
<script language="JavaScript">

function checkForm1(num) {
	msg = "";
	eval("var formObj = document.form" + num + "_1;");
	
	if (formObj.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
<?php
	if ($strictformcheck) {
	?>
	if (formObj.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
<?php
	}
?>
	if (msg != "")
	{
		alert(msg);
		return false;
	}
	else
		return true;
}

function checkForm2(num) {
	msg = "";
	eval("var formObj = document.form" + num + "_2;");
	
	if (formObj.userid.options[formObj.userid.selectedIndex].value == -1) msg += "<?php printMLText("js_select_user");?>\n";

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
		$this->contentHeading(getMLText("group_management"));
		$this->contentContainerStart();
?>

<table>
<tr>
<td>

<?php echo getMLText("selection")?>:<select onchange="showUser(this)" id="selector">
<option value="-1"><?php echo getMLText("choose_group")?>
<option value="0"><?php echo getMLText("add_group")?>
<?php
		$selected=0;
		$count=2;
		foreach ($allGroups as $group) {
			if ($selgroup && $group->getID()==$selgroup->getID()) $selected=$count;
			print "<option value=\"".$group->getID()."\">" . htmlspecialchars($group->getName());
			$count++;
		}
?>
</select>
&nbsp;&nbsp;
</td>

	<td id="keywords0" style="display : none;">
	
	<form action="../op/op.GroupMgr.php" name="form0_1" method="post" onsubmit="return checkForm1('0');">
  <?php echo createHiddenFieldWithKey('addgroup'); ?>
	<input type="Hidden" name="action" value="addgroup">
	<table>
		<tr>
			<td><?php printMLText("name");?>:</td>
			<td><input name="name"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="50"></textarea></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="<?php printMLText("add_group");?>"></td>
		</tr>
	</table>
	</form>	
	
	</td>
	
<?php	
		foreach ($allGroups as $group) {
			print "<td id=\"keywords".$group->getID()."\" style=\"display : none;\">";
			$this->contentSubHeading(getMLText("group")." : ".htmlspecialchars($group->getName()));
?>
	<a href="../out/out.RemoveGroup.php?groupid=<?php print $group->getID();?>"><img src="images/del.gif" width="15" height="15" border="0" align="absmiddle" alt=""> <?php printMLText("rm_group");?></a>
	<?php	$this->contentSubHeading(getMLText("edit_group"));?>
	<form action="../op/op.GroupMgr.php" name="form<?php print $group->getID();?>_1" method="post" onsubmit="return checkForm1('<?php print $group->getID();?>');">
	<?php echo createHiddenFieldWithKey('editgroup'); ?>
	<input type="Hidden" name="groupid" value="<?php print $group->getID();?>">
	<input type="Hidden" name="action" value="editgroup">
	<table>
		<tr>
			<td><?php printMLText("name");?>:</td>
			<td><input name="name" value="<?php print htmlspecialchars($group->getName());?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="50"><?php print htmlspecialchars($group->getComment());?></textarea></td>
		</tr>
		<tr>
			<td colspan="2"><input type="Submit" value="<?php printMLText("save");?>"></td>
		</tr>
	</table>
	</form>
<?php
			$this->contentSubHeading(getMLText("group_members"));
?>
		<table class="folderView">
<?php
			$members = $group->getUsers();
			if (count($members) == 0)
				print "<tr><td>".getMLText("no_group_members")."</td></tr>";
			else {
			
				foreach ($members as $member) {
				
					print "<tr>";
					print "<td><img src=\"images/usericon.gif\" width=16 height=16></td>";
					print "<td>" . htmlspecialchars($member->getFullName()) . "</td>";
					print "<td>" . ($group->isMember($member,true)?getMLText("manager"):"&nbsp;") . "</td>";
					print "<td align=\"right\"><ul class=\"actions\">";
					print "<li><form action=\"../op/op.GroupMgr.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"rmmember\" /><input type=\"hidden\" name=\"groupid\" value=\"".$group->getID()."\" /><input type=\"hidden\" name=\"userid\" value=\"".$member->getID()."\" />".createHiddenFieldWithKey('rmmember')."<input type=\"submit\" value=\"".getMLText("delete")."\" /></form>";
					print "<li><form action=\"../op/op.GroupMgr.php\" method=\"post\"><input type=\"hidden\" name=\"groupid\" value=\"".$group->getID()."\" /><input type=\"hidden\" name=\"action\" value=\"tmanager\" /><input type=\"hidden\" name=\"userid\" value=\"".$member->getID()."\" />".createHiddenFieldWithKey('tmanager')."<input type=\"submit\" value=\"".getMLText("toggle_manager")."\" /></form>";
					print "</td></tr>";
				}
			}
?>
		</table>
		
<?php
			$this->contentSubHeading(getMLText("add_member"));
?>
		
		<form action="../op/op.GroupMgr.php" method="POST" name="form<?php print $group->getID();?>_2" onsubmit="return checkForm2('<?php print $group->getID();?>');">
		<?php echo createHiddenFieldWithKey('addmember'); ?>
		<input type="Hidden" name="action" value="addmember">
		<input type="Hidden" name="groupid" value="<?php print $group->getID();?>">
		<table width="100%">
			<tr>
				<td>
					<select name="userid">
						<option value="-1"><?php printMLText("select_one");?>
						<?php
							foreach ($allUsers as $currUser)
								if (!$group->isMember($currUser))
									print "<option value=\"".$currUser->getID()."\">" . htmlspecialchars($currUser->getLogin()." - ".$currUser->getFullName()) . "\n";
						?>
					</select>
				</td>
				<td>
					<input type="checkbox" name="manager" value="1"><?php printMLText("manager");?>
				</td>
				<td align="right">
					<input type="Submit" value="<?php printMLText("add");?>">
				</td>
			</tr>
		</table>
		</form>
	</td>
<?php  } ?>

</tr>
</table>

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
