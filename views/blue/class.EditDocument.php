<?php
/**
 * Implementation of EditDocument view
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
 * Class which outputs the html page for EditDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditDocument extends SeedDMS_Blue_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$attrdefs = $this->params['attrdefs'];
		$strictformcheck = $this->params['strictformcheck'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->pageNavigation(getFolderPathHTML($folder, true, $document), "view_document");

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
	if (document.form1.keywords.value == "") msg += "<?php printMLText("js_no_keywords");?>\n";
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
		$this->contentHeading(getMLText("edit_document_props"));
		$this->contentContainerStart();
?>
<form action="../op/op.EditDocument.php" name="form1" onsubmit="return checkForm();" method="POST">
	<input type="hidden" name="documentid" value="<?php echo $document->getID() ?>">
	<table cellpadding="3">
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input name="name" value="<?php print htmlspecialchars($document->getName());?>" size="60"></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="4" cols="80"><?php print htmlspecialchars($document->getComment());?></textarea></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("keywords");?>:</td>
			<td class="standardText">
				<textarea name="keywords" rows="2" cols="80"><?php print htmlspecialchars($document->getKeywords());?></textarea><br>
				<a href="javascript:chooseKeywords('form1.keywords');"><?php printMLText("use_default_keywords");?></a>
				<script language="JavaScript">
					var openDlg;
					
					function chooseKeywords(target) {
						openDlg = open("out.KeywordChooser.php?target="+target, "openDlg", "width=500,height=400,scrollbars=yes,resizable=yes");
					}
				</script>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td><?php $this->printCategoryChooser("form1", $document->getCategories());?></td>
		</tr>
<?php
		if ($folder->getAccessMode($user) > M_READ) {
			print "<tr>";
			print "<td class=\"inputDescription\">" . getMLText("sequence") . ":</td>";
			print "<td>";
			$this->printSequenceChooser($folder->getDocuments(), $document->getID());
			print "</td></tr>";
		}
		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
?>
		<tr>
			<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
			<td><?php $this->printAttributeEditField($attrdef, $document->getAttributeValue($attrdef)) ?></td>
		</tr>
<?php
			}
		}
?>
		<tr>
			<td colspan="2"><br><input type="Submit" value="<?php printMLText("save") ?>"></td>
		</tr>
	</table>
</form>
<?php
		$this->contentContainerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
