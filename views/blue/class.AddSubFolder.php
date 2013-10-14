<?php
/**
 * Implementation of AddSubFolder view
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
 * Class which outputs the html page for AddSubFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddSubFolder extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true), "view_folder", $folder);
		$this->contentHeading(getMLText("add_subfolder"));
		$this->contentContainerStart();
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

<form action="../op/op.AddSubFolder.php" name="form1" onsubmit="return checkForm();" method="POST">
	<?php echo createHiddenFieldWithKey('addsubfolder'); ?>
	<input type="Hidden" name="folderid" value="<?php print $folder->getId();?>">
	<input type="Hidden" name="showtree" value="<?php echo showtree();?>">
	<table>
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"></textarea></td>
		</tr>
		<tr>
			<td class="inputDescription"><?php printMLText("sequence");?>:</td>
			<td><?php $this->printSequenceChooser($folder->getSubFolders());?></td>
		</tr>
<?php
	$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_folder, SeedDMS_Core_AttributeDefinition::objtype_all));
	if($attrdefs) {
		foreach($attrdefs as $attrdef) {
?>
<tr>
	<td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
	<td><?php $this->printAttributeEditField($attrdef, '') ?></td>
</tr>
<?php
		}
	}
?>
		<tr>
			<td colspan="2"><br><input type="Submit" value="<?php printMLText("add_subfolder");?>"></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
