<?php
/**
 * Implementation of EditFolder view
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
 * Class which outputs the html page for EditFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditFolder extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$attrdefs = $this->params['attrdefs'];
		$rootfolderid = $this->params['rootfolderid'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "view_folder", $folder);
?>

<script language="JavaScript">
function checkForm()
{
	msg = "";
	if (document.form1.name.value == "") msg += "<?php printMLText("js_no_name");?>\n";
<?php
	if ($strictformcheck) {
	?>
	if (document.form1.comment.value == "") msg += "<?php printMLText("js_no_comment");?>\n";
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
</script>

<?php
		$this->contentHeading(getMLText("edit_folder_props"));
		$this->contentContainerStart();
?>
<form action="../op/op.EditFolder.php" name="form1" onsubmit="return checkForm();" method="POST">
<input type="Hidden" name="folderid" value="<?php print $folder->getID();?>">
<input type="Hidden" name="showtree" value="<?php echo showtree();?>">
<table>
<tr>
<td><?php printMLText("name");?>:</td>
<td><input type="text" name="name" value="<?php print htmlspecialchars($folder->getName());?>" size="60"></td>
</tr>
<tr>
<td><?php printMLText("comment");?>:</td>
<td><textarea name="comment" rows="4" cols="80"><?php print htmlspecialchars($folder->getComment());?></textarea></td>
</tr>
<?php
		$parent = ($folder->getID() == $rootfolderid) ? false : $folder->getParent();
		if ($parent && $parent->getAccessMode($user) > M_READ) {
			print "<tr>";
			print "<td>" . getMLText("sequence") . ":</td>";
			print "<td>";
			$this->printSequenceChooser($parent->getSubFolders(), $folder->getID());
			print "</td></tr>\n";
		}

		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
?>
<tr>
	<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
	<td><?php $this->printAttributeEditField($attrdef, $folder->getAttributeValue($attrdef)) ?></td>
</tr>
<?php
			}
		}
?>
<tr>
<td colspan="2"><input type="submit" value="<?php printMLText("save"); ?>"></td>
</tr>
</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
